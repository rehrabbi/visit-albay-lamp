<?php
require_once __DIR__ . '/../includes/app.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('login.php');
}

verify_csrf($_POST['csrf_token'] ?? null);

$username = trim((string) ($_POST['username'] ?? ''));
$password = (string) ($_POST['password'] ?? '');
$next = safe_next($_POST['next'] ?? 'index.php');

if (strlen($username) < 3 || strlen($password) < 3) {
    set_flash('error', 'Username and password must be at least 3 characters.');
    redirect('login.php?next=' . rawurlencode($next));
}

$stmt = $pdo->prepare('SELECT id FROM users WHERE username = ?');
$stmt->execute([$username]);
if ($stmt->fetchColumn()) {
    set_flash('error', 'That username is already taken.');
    redirect('login.php?next=' . rawurlencode($next));
}

$stmt = $pdo->prepare('INSERT INTO users (username, password_hash, role) VALUES (?, ?, ?)');
$stmt->execute([$username, password_hash($password, PASSWORD_DEFAULT), 'user']);

login_user(['id' => (int) $pdo->lastInsertId()]);
set_flash('success', 'Account created. You are now signed in.');
redirect($next);
