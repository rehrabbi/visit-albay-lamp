<?php
require_once __DIR__ . '/../includes/app.php';

require_admin($pdo);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('admin.php');
}

verify_csrf($_POST['csrf_token'] ?? null);

$bookingId = (int) ($_POST['booking_id'] ?? 0);
$intent = (string) ($_POST['intent'] ?? '');

$stmt = $pdo->prepare('SELECT * FROM bookings WHERE id = ?');
$stmt->execute([$bookingId]);
$booking = $stmt->fetch();

if (!$booking) {
    set_flash('error', 'Booking not found.');
    redirect('admin.php');
}

if ($intent === 'delete') {
    $stmt = $pdo->prepare('DELETE FROM bookings WHERE id = ?');
    $stmt->execute([$bookingId]);
    set_flash('success', 'Booking deleted.');
    redirect('admin.php');
}

if ($intent !== 'update') {
    set_flash('error', 'Unknown admin action.');
    redirect('admin.php');
}

$fullName = trim((string) ($_POST['full_name'] ?? ''));
$email = trim((string) ($_POST['email'] ?? ''));
$phone = trim((string) ($_POST['phone'] ?? ''));
$destinationId = (int) ($_POST['destination_id'] ?? 0);

// === NEW RANGE SPLIT LOGIC ===
$rawDateInput = trim((string) ($_POST['check_in_date'] ?? ''));
$dateParts = explode(' to ', $rawDateInput);
$checkIn = $dateParts[0];
// =============================

$guests = max(1, min(20, (int) ($_POST['guests'] ?? 1)));

if ($fullName === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $phone === '' || !$destinationId || !$checkIn) {
    set_flash('error', 'Fill in valid booking details before saving.');
    redirect('admin.php');
}

if (!find_destination($pdo, $destinationId)) {
    set_flash('error', 'Choose a valid destination.');
    redirect('admin.php');
}

// === NEW CALCULATION LOGIC ===
// Handle check-out date from range or fallback
if (isset($dateParts[1])) {
    $checkOut = $dateParts[1];
} else {
    $checkOut = add_days($checkIn, (int) $booking['nights']);
}

// Calculate the new nights difference
$datetime1 = new DateTime($checkIn);
$datetime2 = new DateTime($checkOut);
$nights = max(1, $datetime1->diff($datetime2)->days);

// Recalculate the total price based on new nights
$newTotal = $nights * (int) $booking['rooms'] * (float) $booking['hotel_price'];
// =============================

// === UPDATED QUERY TO INCLUDE NEW NIGHTS AND TOTAL ===
$stmt = $pdo->prepare(
    'UPDATE bookings
     SET full_name = ?, email = ?, phone = ?, destination_id = ?, check_in_date = ?, check_out_date = ?, guests = ?, nights = ?, hotel_total = ?
     WHERE id = ?'
);
$stmt->execute([$fullName, $email, $phone, $destinationId, $checkIn, $checkOut, $guests, $nights, $newTotal, $bookingId]);

set_flash('success', 'Booking updated successfully.');
redirect('admin.php');