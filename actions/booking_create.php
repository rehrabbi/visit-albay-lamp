<?php
require_once __DIR__ . '/../includes/app.php';

require_login($pdo);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('plan.php');
}

verify_csrf($_POST['csrf_token'] ?? null);

$errors = validate_booking($_POST, $pdo);

if ($errors) {
    set_flash('error', reset($errors));
    redirect('plan.php');
}

$user = current_user($pdo);
$destinationId = (int) $_POST['destination_id'];
$hotelId = (int) $_POST['hotel_id'];
$hotel = find_hotel($pdo, $hotelId);
$nights = (int) $_POST['nights'];
$rooms = (int) $_POST['rooms'];
$price = (float) $hotel['price_per_night'];
$checkIn = (string) $_POST['check_in_date'];
$total = compute_booking_total($pdo, $price, $nights, $rooms, $checkIn);
$checkOut = add_days($checkIn, $nights);

// Collision guard: refuse dates that overlap an existing active booking for the
// same stay + destination (existing.start < new.end AND existing.end > new.start).
$overlap = $pdo->prepare(
    "SELECT COUNT(*) FROM bookings
     WHERE hotel_id = ? AND destination_id = ? AND status = 'active'
       AND check_in_date < ? AND check_out_date > ?"
);
$overlap->execute([$hotelId, $destinationId, $checkOut, $checkIn]);
if ($overlap->fetchColumn() > 0) {
    set_flash('error', 'Those dates are already booked for this stay. Please pick different dates.');
    redirect('plan.php');
}

$reference = booking_reference($pdo);

$stmt = $pdo->prepare(
    'INSERT INTO bookings
      (reference_code, user_id, destination_id, hotel_id, full_name, email, phone, address,
       check_in_date, check_out_date, guests, nights, rooms, hotel_price, hotel_total,
       payment_method, payment_details, special_request)
     VALUES
      (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
);

$stmt->execute([
    $reference,
    $user['id'],
    $destinationId,
    $hotelId,
    trim((string) $_POST['full_name']),
    trim((string) $_POST['email']),
    trim((string) $_POST['phone']),
    trim((string) ($_POST['address'] ?? '')),
    $checkIn,
    $checkOut,
    (int) $_POST['guests'],
    $nights,
    $rooms,
    $price,
    $total,
    (string) $_POST['payment_method'],
    safe_payment_details($_POST),
    trim((string) ($_POST['special_request'] ?? '')),
]);

set_flash('success', 'Booking ' . $reference . ' was created.');
redirect('my-bookings.php');
