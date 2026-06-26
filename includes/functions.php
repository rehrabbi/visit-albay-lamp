<?php
declare(strict_types=1);

function h(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function current_path(): string
{
    $request = $_SERVER['REQUEST_URI'] ?? 'index.php';
    $path = parse_url($request, PHP_URL_PATH) ?: 'index.php';
    $query = parse_url($request, PHP_URL_QUERY);
    $base = base_url();

    if ($base !== '' && str_starts_with($path, $base)) {
        $path = substr($path, strlen($base));
    }

    $path = ltrim($path, '/');
    return $path . ($query ? '?' . $query : '');
}

function base_url(): string
{
    $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
    if (str_ends_with($scriptDir, '/actions')) {
        $scriptDir = dirname($scriptDir);
    }

    return rtrim($scriptDir, '/');
}

function url(string $path = ''): string
{
    $base = base_url();
    return ($base === '' ? '' : $base) . '/' . ltrim($path, '/');
}

function redirect(string $path): never
{
    header('Location: ' . url($path));
    exit;
}

function safe_next(?string $next, string $fallback = 'index.php'): string
{
    $next = trim((string) $next);

    if ($next === '' || preg_match('/^[a-z][a-z0-9+.-]*:/i', $next) || str_starts_with($next, '//')) {
        return $fallback;
    }

    return ltrim($next, '/');
}

function money(mixed $value): string
{
    return '₱' . number_format((float) $value, 0);
}

function json_list(mixed $json): array
{
    if (is_array($json)) {
        return $json;
    }

    $decoded = json_decode((string) $json, true);
    return is_array($decoded) ? $decoded : [];
}

function all_destinations(PDO $pdo): array
{
    return $pdo->query('SELECT * FROM destinations ORDER BY id ASC')->fetchAll();
}

function all_hotels(PDO $pdo): array
{
    $hotels = $pdo->query('SELECT * FROM hotels WHERE active = 1 ORDER BY price_per_night ASC')->fetchAll();
    $stmt = $pdo->query('SELECT hotel_id, destination_id, distance_km FROM hotel_destinations');
    $serves = [];
    $distances = [];

    foreach ($stmt->fetchAll() as $row) {
        $hotelId = (int) $row['hotel_id'];
        $serves[$hotelId][] = (int) $row['destination_id'];
        $distances[$hotelId][(int) $row['destination_id']] = (float) $row['distance_km'];
    }

    foreach ($hotels as &$hotel) {
        $hotelId = (int) $hotel['id'];
        $hotel['destination_ids'] = $serves[$hotelId] ?? [];
        $hotel['distances'] = $distances[$hotelId] ?? [];
    }

    return $hotels;
}

function find_destination(PDO $pdo, int $id): ?array
{
    $stmt = $pdo->prepare('SELECT * FROM destinations WHERE id = ?');
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    return $row ?: null;
}

function find_hotel(PDO $pdo, int $id): ?array
{
    $stmt = $pdo->prepare('SELECT * FROM hotels WHERE id = ? AND active = 1');
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    return $row ?: null;
}

function hotel_serves_destination(PDO $pdo, int $hotelId, int $destinationId): bool
{
    $stmt = $pdo->prepare('SELECT 1 FROM hotel_destinations WHERE hotel_id = ? AND destination_id = ?');
    $stmt->execute([$hotelId, $destinationId]);
    return (bool) $stmt->fetchColumn();
}

function booking_reference(PDO $pdo): string
{
    do {
        $ref = 'ALB-2026-' . random_int(1000, 9999);
        $stmt = $pdo->prepare('SELECT 1 FROM bookings WHERE reference_code = ?');
        $stmt->execute([$ref]);
    } while ($stmt->fetchColumn());

    return $ref;
}

function add_days(string $date, int $days): string
{
    return (new DateTimeImmutable($date))->modify('+' . $days . ' days')->format('Y-m-d');
}

function all_peak_seasons(PDO $pdo): array
{
    static $cache = null;
    if ($cache === null) {
        $cache = $pdo->query(
            'SELECT label, start_date, end_date, surcharge_pct FROM peak_seasons ORDER BY start_date ASC'
        )->fetchAll();
    }
    return $cache;
}

function peak_season_for(PDO $pdo, string $checkIn): ?array
{
    $checkIn = substr(trim($checkIn), 0, 10);
    if ($checkIn === '') {
        return null;
    }
    foreach (all_peak_seasons($pdo) as $season) {
        if ($checkIn >= $season['start_date'] && $checkIn <= $season['end_date']) {
            return $season;
        }
    }
    return null;
}

function peak_multiplier(PDO $pdo, string $checkIn): float
{
    $season = peak_season_for($pdo, $checkIn);
    return $season ? 1 + ((float) $season['surcharge_pct'] / 100) : 1.0;
}

function compute_booking_total(PDO $pdo, float $price, int $nights, int $rooms, string $checkIn): float
{
    $total = 0.0;
    
    // Loop through every single night of the stay
    for ($i = 0; $i < $nights; $i++) {
        $currentDate = add_days($checkIn, $i);
        $multiplier = peak_multiplier($pdo, $currentDate);
        $total += ($price * $rooms * $multiplier);
    }
    
    return round($total, 2);
}


function peak_seasons_for_js(PDO $pdo): array
{
    $out = [];
    foreach (all_peak_seasons($pdo) as $season) {
        $out[] = [
            'start' => $season['start_date'],
            'end' => $season['end_date'],
            'pct' => (float) $season['surcharge_pct'],
        ];
    }
    return $out;
}

function safe_payment_details(array $input): string
{
    $method = $input['payment_method'] ?? '';

    if ($method === 'GCash') {
        return json_encode([
            'gcash_name' => trim((string) ($input['gcash_name'] ?? '')),
            'gcash_number' => trim((string) ($input['gcash_number'] ?? '')),
        ], JSON_THROW_ON_ERROR);
    }

    if ($method === 'Credit / Debit Card') {
        $digits = preg_replace('/\D+/', '', (string) ($input['card_number'] ?? ''));
        return json_encode([
            'card_name' => trim((string) ($input['card_name'] ?? '')),
            'card_last4' => substr($digits, -4),
            'card_expiry' => trim((string) ($input['card_expiry'] ?? '')),
        ], JSON_THROW_ON_ERROR);
    }

    return json_encode(new stdClass(), JSON_THROW_ON_ERROR);
}

function validate_booking(array $input, PDO $pdo): array
{
    $errors = [];

    if (trim((string) ($input['full_name'] ?? '')) === '') {
        $errors['full_name'] = 'Full name is required.';
    }

    if (!filter_var($input['email'] ?? '', FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'A valid email is required.';
    }

    if (trim((string) ($input['phone'] ?? '')) === '') {
        $errors['phone'] = 'Phone number is required.';
    }

    $destinationId = (int) ($input['destination_id'] ?? 0);
    $hotelId = (int) ($input['hotel_id'] ?? 0);

    if (!$destinationId || !find_destination($pdo, $destinationId)) {
        $errors['destination_id'] = 'Choose a destination.';
    }

    if (!$hotelId || !find_hotel($pdo, $hotelId)) {
        $errors['hotel_id'] = 'Choose a place to stay.';
    }

    if ($destinationId && $hotelId && !hotel_serves_destination($pdo, $hotelId, $destinationId)) {
        $errors['hotel_id'] = 'Choose a stay that serves your selected destination.';
    }

    if (empty($input['check_in_date'])) {
        $errors['check_in_date'] = 'Check-in date is required.';
    }

    $guests = (int) ($input['guests'] ?? 0);
    $nights = (int) ($input['nights'] ?? 0);
    $rooms = (int) ($input['rooms'] ?? 0);

    if ($guests < 1 || $guests > 20) {
        $errors['guests'] = 'Guests must be between 1 and 20.';
    }

    if ($nights < 1 || $nights > 30) {
        $errors['nights'] = 'Nights must be between 1 and 30.';
    }

    if ($rooms < 1 || $rooms > 10) {
        $errors['rooms'] = 'Rooms must be between 1 and 10.';
    }

    $payment = (string) ($input['payment_method'] ?? '');
    if (!in_array($payment, ['GCash', 'Credit / Debit Card', 'Cash on Arrival'], true)) {
        $errors['payment_method'] = 'Choose a payment method.';
    }

    if ($payment === 'GCash') {
        $gcashNumber = preg_replace('/\s+/', '', (string) ($input['gcash_number'] ?? ''));
        if (trim((string) ($input['gcash_name'] ?? '')) === '') {
            $errors['gcash_name'] = 'GCash account name is required.';
        }
        if (!preg_match('/^(09\d{9}|\+639\d{9})$/', $gcashNumber)) {
            $errors['gcash_number'] = 'A valid GCash mobile number is required.';
        }
    }

    if ($payment === 'Credit / Debit Card') {
        $cardNumber = preg_replace('/\s+/', '', (string) ($input['card_number'] ?? ''));
        if (trim((string) ($input['card_name'] ?? '')) === '') {
            $errors['card_name'] = 'Name on card is required.';
        }
        if (!preg_match('/^\d{13,19}$/', $cardNumber)) {
            $errors['card_number'] = 'A valid card number is required.';
        }
        if (!preg_match('/^(0[1-9]|1[0-2])\/\d{2}$/', trim((string) ($input['card_expiry'] ?? '')))) {
            $errors['card_expiry'] = 'Card expiry must use MM/YY.';
        }
        if (!preg_match('/^\d{3,4}$/', trim((string) ($input['card_cvv'] ?? '')))) {
            $errors['card_cvv'] = 'A valid CVV is required.';
        }
    }

    return $errors;
}

function ensure_demo_users(PDO $pdo): void
{
    $users = [
        ['admin', 'admin', 'admin'],
        ['user', 'user', 'user'],
    ];

    $select = $pdo->prepare('SELECT id FROM users WHERE username = ?');
    $insert = $pdo->prepare('INSERT INTO users (username, password_hash, role) VALUES (?, ?, ?)');

    foreach ($users as [$username, $password, $role]) {
        $select->execute([$username]);
        if (!$select->fetchColumn()) {
            $insert->execute([$username, password_hash($password, PASSWORD_DEFAULT), $role]);
        }
    }
}

function form_value(string $key, mixed $fallback = ''): string
{
    return h($_POST[$key] ?? $_GET[$key] ?? $fallback);
}

function booking_field_label(string $key): string
{
    return [
        'full_name' => 'Full name',
        'email' => 'Email',
        'phone' => 'Phone',
        'address' => 'Address',
        'destination_id' => 'Destination',
        'hotel_id' => 'Stay',
        'check_in_date' => 'Check-in date',
        'nights' => 'Nights',
        'guests' => 'Guests',
        'rooms' => 'Rooms',
        'payment_method' => 'Payment method',
        'special_request' => 'Special request',
    ][$key] ?? $key;
}
