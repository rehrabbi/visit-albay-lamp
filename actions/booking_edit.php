<?php
require_once __DIR__ . '/../includes/app.php';

require_login($pdo);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('my-bookings.php');
}

verify_csrf($_POST['csrf_token'] ?? null);

$user = current_user($pdo);
$bookingId = (int) ($_POST['booking_id'] ?? 0);

$stmt = $pdo->prepare('SELECT * FROM bookings WHERE id = ? AND user_id = ?');
$stmt->execute([$bookingId, $user['id']]);
$booking = $stmt->fetch();

if (!$booking) {
    set_flash('error', 'Booking not found.');
    redirect('my-bookings.php');
}

// Splir range string into check-in and check-out dates
$rawDateInput = trim((string) ($_POST['check_in_date'] ?? ''));
if (strpos($rawDateInput, ' to ') !== false) {
    $dateParts = explode(' to ', $rawDateInput);
    $_POST['check_in_date'] = $dateParts[0];
    $_POST['check_out_date'] = $dateParts[1];
} elseif ($rawDateInput !== '') {
    // Fallback if they somehow bypassed the calendar
    $_POST['check_out_date'] = add_days($rawDateInput, max(1, (int)($_POST['nights'] ?? 1)));
}


$fields = [
    'full_name' => 'string',
    'email' => 'email',
    'phone' => 'string',
    'address' => 'string',
    'destination_id' => 'int',
    'hotel_id' => 'int',
    'check_in_date' => 'date',
    'check_out_date' => 'date', 
    'nights' => 'int',
    'guests' => 'int',
    'rooms' => 'int',
    'payment_method' => 'string',
    'special_request' => 'string',
];

$proposed = [];

foreach ($fields as $field => $type) {
    if (!array_key_exists($field, $_POST)) {
        continue;
    }

    $value = trim((string) $_POST[$field]);

    if ($type === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
        set_flash('error', 'Enter a valid email address.');
        redirect('my-bookings.php');
    }

    if ($type === 'int') {
        $value = (string) max(1, (int) $value);
    }

    if ($field === 'destination_id' && !find_destination($pdo, (int) $value)) {
        set_flash('error', 'Choose a valid destination.');
        redirect('my-bookings.php');
    }

    if ($field === 'hotel_id' && !find_hotel($pdo, (int) $value)) {
        set_flash('error', 'Choose a valid place to stay.');
        redirect('my-bookings.php');
    }

    if ($field === 'payment_method' && !in_array($value, ['GCash', 'Credit / Debit Card', 'Cash on Arrival'], true)) {
        set_flash('error', 'Choose a valid payment method.');
        redirect('my-bookings.php');
    }

    if ($value !== (string) ($booking[$field] ?? '')) {
        $proposed[$field] = $type === 'int' ? (int) $value : $value;
    }
}

if (!$proposed) {
    set_flash('error', 'No changes to submit.');
    redirect('my-bookings.php');
}

$pdo->beginTransaction();
$delete = $pdo->prepare("DELETE FROM edit_requests WHERE booking_id = ? AND status = 'pending'");
$delete->execute([$bookingId]);

$insert = $pdo->prepare('INSERT INTO edit_requests (booking_id, proposed) VALUES (?, ?)');
$insert->execute([$bookingId, json_encode($proposed, JSON_THROW_ON_ERROR)]);
$pdo->commit();

set_flash('success', 'Change request submitted for admin approval.');
redirect('my-bookings.php');