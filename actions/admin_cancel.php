<?php
require_once __DIR__ . '/../includes/app.php';

require_admin($pdo);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('admin.php?tab=cancellations');
}

verify_csrf($_POST['csrf_token'] ?? null);

$requestId = (int) ($_POST['cancellation_request_id'] ?? 0);
$action = (string) ($_POST['action'] ?? '');

if (!in_array($action, ['approve', 'reject'], true)) {
    set_flash('error', 'Unknown cancellation action.');
    redirect('admin.php?tab=cancellations');
}

$stmt = $pdo->prepare(
    "SELECT * FROM cancellation_requests WHERE id = ? AND status = 'pending'"
);
$stmt->execute([$requestId]);
$request = $stmt->fetch();

if (!$request) {
    set_flash('error', 'Pending cancellation request not found.');
    redirect('admin.php?tab=cancellations');
}

$pdo->beginTransaction();

if ($action === 'approve') {
    $stmt = $pdo->prepare("UPDATE bookings SET status = 'cancelled' WHERE id = ?");
    $stmt->execute([$request['booking_id']]);
}

$stmt = $pdo->prepare('UPDATE cancellation_requests SET status = ?, resolved_at = CURRENT_TIMESTAMP WHERE id = ?');
$stmt->execute([$action === 'approve' ? 'approved' : 'rejected', $requestId]);

$pdo->commit();

set_flash('success', $action === 'approve' ? 'Booking cancelled.' : 'Cancellation request rejected.');
redirect('admin.php?tab=cancellations');
