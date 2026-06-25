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

if ($booking['status'] === 'cancelled') {
    set_flash('error', 'This booking is already cancelled.');
    redirect('my-bookings.php');
}

$reason = trim((string) ($_POST['reason'] ?? ''));

if (mb_strlen($reason) < 5) {
    set_flash('error', 'Please give a brief reason for cancelling (at least 5 characters).');
    redirect('my-bookings.php');
}

$pdo->beginTransaction();
$delete = $pdo->prepare("DELETE FROM cancellation_requests WHERE booking_id = ? AND status = 'pending'");
$delete->execute([$bookingId]);

$insert = $pdo->prepare('INSERT INTO cancellation_requests (booking_id, reason) VALUES (?, ?)');
$insert->execute([$bookingId, $reason]);
$pdo->commit();

set_flash('success', 'Cancellation request submitted for admin approval.');
redirect('my-bookings.php');
