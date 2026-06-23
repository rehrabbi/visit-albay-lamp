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
$checkIn = trim((string) ($_POST['check_in_date'] ?? ''));
$guests = max(1, min(20, (int) ($_POST['guests'] ?? 1)));

if ($fullName === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $phone === '' || !$destinationId || !$checkIn) {
    set_flash('error', 'Fill in valid booking details before saving.');
    redirect('admin.php');
}

if (!find_destination($pdo, $destinationId)) {
    set_flash('error', 'Choose a valid destination.');
    redirect('admin.php');
}

$checkOut = add_days($checkIn, (int) $booking['nights']);

$stmt = $pdo->prepare(
    'UPDATE bookings
     SET full_name = ?, email = ?, phone = ?, destination_id = ?, check_in_date = ?, check_out_date = ?, guests = ?
     WHERE id = ?'
);
$stmt->execute([$fullName, $email, $phone, $destinationId, $checkIn, $checkOut, $guests, $bookingId]);

set_flash('success', 'Booking updated.');
redirect('admin.php');
