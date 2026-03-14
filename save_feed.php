<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Not logged in', 'redirect' => 'login.php']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['name']) || !isset($data['url']) || empty($data['name']) || empty($data['url'])) {
    echo json_encode(['success' => false, 'error' => 'Name and URL required']);
    exit();
}

if (!filter_var($data['url'], FILTER_VALIDATE_URL)) {
    echo json_encode(['success' => false, 'error' => 'Invalid URL']);
    exit();
}

// Check for duplicate
$check = $pdo->prepare("SELECT id FROM feeds WHERE user_id = ? AND feed_url = ?");
$check->execute([$_SESSION['user_id'], $data['url']]);
if ($check->fetch()) {
    echo json_encode(['success' => false, 'error' => 'Feed already exists']);
    exit();
}

$stmt = $pdo->prepare("INSERT INTO feeds (user_id, feed_name, feed_url) VALUES (?, ?, ?)");
$success = $stmt->execute([$_SESSION['user_id'], $data['name'], $data['url']]);

if ($success) {
    echo json_encode(['success' => true, 'feed_id' => $pdo->lastInsertId()]);
} else {
    echo json_encode(['success' => false, 'error' => 'Failed to save']);
}
?>