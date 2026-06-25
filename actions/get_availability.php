<?php
require_once __DIR__ . '/../includes/app.php';

// Fetch booked dates for a hotel/destination
$hotel_id = (int)($_GET['hotel_id'] ?? 0);
$dest_id = (int)($_GET['destination_id'] ?? 0);

if ($hotel_id > 0 && $dest_id > 0) {
    // Return the start and end dates as a range for the calendar
    $stmt = $pdo->prepare("SELECT check_in_date as 'from', check_out_date as 'to' FROM bookings WHERE hotel_id = ? AND destination_id = ? AND status = 'active'");
    $stmt->execute([$hotel_id, $dest_id]);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
} else {
    echo json_encode([]);
}