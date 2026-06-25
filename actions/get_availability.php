<?php
// Returns the active booked date ranges for a hotel + destination as JSON,
// so the booking calendar can disable dates that are already taken.
// Read-only; used by assets/js/calendar.js.
require_once __DIR__ . '/../includes/app.php';

require_login($pdo);

header('Content-Type: application/json');

$hotelId = (int) ($_GET['hotel_id'] ?? 0);
$destinationId = (int) ($_GET['destination_id'] ?? 0);
$excludeBookingId = (int) ($_GET['exclude_booking_id'] ?? 0);

if ($hotelId <= 0 || $destinationId <= 0) {
    echo json_encode([]);
    exit;
}

$sql = "SELECT check_in_date AS `from`, check_out_date AS `to`
        FROM bookings
        WHERE hotel_id = ? AND destination_id = ? AND status = 'active'";
$params = [$hotelId, $destinationId];

if ($excludeBookingId > 0) {
    $sql .= ' AND id <> ?';
    $params[] = $excludeBookingId;
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);

echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
