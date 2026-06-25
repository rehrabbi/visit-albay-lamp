<?php
require_once __DIR__ . '/../includes/app.php';

require_admin($pdo);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('admin.php?tab=edits');
}

verify_csrf($_POST['csrf_token'] ?? null);

$requestId = (int) ($_POST['edit_request_id'] ?? 0);
$action = (string) ($_POST['action'] ?? '');

if (!in_array($action, ['approve', 'reject'], true)) {
    set_flash('error', 'Unknown edit action.');
    redirect('admin.php?tab=edits');
}

$stmt = $pdo->prepare(
    "SELECT e.*, b.hotel_id AS b_hotel_id, b.nights AS b_nights, b.rooms AS b_rooms, b.check_in_date AS b_check_in
     FROM edit_requests e
     JOIN bookings b ON b.id = e.booking_id
     WHERE e.id = ? AND e.status = 'pending'"
);
$stmt->execute([$requestId]);
$request = $stmt->fetch();

if (!$request) {
    set_flash('error', 'Pending edit request not found.');
    redirect('admin.php?tab=edits');
}

$pdo->beginTransaction();

if ($action === 'approve') {
    $proposed = json_list($request['proposed']);
    $sets = [];
    $values = [];

    // Plain fields applied as-is.
    $allowed = ['full_name', 'email', 'phone', 'address', 'destination_id', 'guests', 'special_request', 'payment_method'];
    foreach ($allowed as $field) {
        if (array_key_exists($field, $proposed)) {
            $sets[] = "{$field} = ?";
            $values[] = $proposed[$field];
        }
    }

    // Changing the payment method invalidates the stored card/GCash details.
    if (array_key_exists('payment_method', $proposed)) {
        $sets[] = 'payment_details = ?';
        $values[] = '{}';
    }

    // Recompute the stay (price, total, check-out) when the hotel, nights,
    // rooms, or check-in date change.
    $touchesStay = array_key_exists('hotel_id', $proposed)
        || array_key_exists('nights', $proposed)
        || array_key_exists('rooms', $proposed)
        || array_key_exists('check_in_date', $proposed);

    if ($touchesStay) {
        $hotelId = (int) ($proposed['hotel_id'] ?? $request['b_hotel_id']);
        $nights = max(1, (int) ($proposed['nights'] ?? $request['b_nights']));
        $rooms = max(1, (int) ($proposed['rooms'] ?? $request['b_rooms']));
        $checkIn = (string) ($proposed['check_in_date'] ?? $request['b_check_in']);
        $hotel = find_hotel($pdo, $hotelId);

        if ($hotel) {
            $price = (float) $hotel['price_per_night'];
            $sets[] = 'hotel_id = ?';        $values[] = $hotelId;
            $sets[] = 'nights = ?';          $values[] = $nights;
            $sets[] = 'rooms = ?';           $values[] = $rooms;
            $sets[] = 'check_in_date = ?';   $values[] = $checkIn;
            $sets[] = 'check_out_date = ?';  $values[] = add_days($checkIn, $nights);
            $sets[] = 'hotel_price = ?';     $values[] = $price;
            $sets[] = 'hotel_total = ?';     $values[] = compute_booking_total($pdo, $price, $nights, $rooms, $checkIn);
        }
    }

    if ($sets) {
        $values[] = $request['booking_id'];
        $sql = 'UPDATE bookings SET ' . implode(', ', $sets) . ' WHERE id = ?';
        $stmt = $pdo->prepare($sql);
        $stmt->execute($values);
    }
}

$stmt = $pdo->prepare('UPDATE edit_requests SET status = ?, resolved_at = CURRENT_TIMESTAMP WHERE id = ?');
$stmt->execute([$action === 'approve' ? 'approved' : 'rejected', $requestId]);

$pdo->commit();

set_flash('success', $action === 'approve' ? 'Edit approved and applied.' : 'Edit rejected.');
redirect('admin.php?tab=edits');
