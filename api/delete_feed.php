<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['feed_id'])) {
    echo json_encode(['success' => false, 'error' => 'Feed ID required']);
    exit();
}

// Verify ownership
$check = $pdo->prepare("SELECT id FROM feeds WHERE id = ? AND user_id = ?");
$check->execute([$data['feed_id'], $_SESSION['user_id']]);
if (!$check->fetch()) {
    echo json_encode(['success' => false, 'error' => 'Not authorized']);
    exit();
}

$stmt = $pdo->prepare("DELETE FROM feeds WHERE id = ? AND user_id = ?");
$success = $stmt->execute([$data['feed_id'], $_SESSION['user_id']]);

echo json_encode(['success' => $success]);
?>