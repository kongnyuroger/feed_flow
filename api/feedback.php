<?php
session_start();
require_once 'config.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $feedback_text = $_POST['feedback'] ?? '';
    if (empty($feedback_text)) {
        $error = 'Please enter your feedback';
    } else {
        $user_id = $_SESSION['user_id'] ?? null;
        $stmt = $pdo->prepare("INSERT INTO feedback (user_id, message) VALUES (?, ?)");
        if ($stmt->execute([$user_id, $feedback_text])) {
            $message = 'Thank you for your feedback!';
        } else {
            $error = 'Failed to submit feedback';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback - FeedFlow</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="style.css">
     <link rel="stylesheet" href="responsiveness.css">
     <link rel="stylesheet" href="mobile.css">
</head>
<style>
    body{
        background:#178582 ; 
    }
    </style>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-logo"><i class="fa-solid fa-rss"></i><span>FeedFlow</span></div>
            <div class="nav-buttons">
                <a href="index.php" class="btn btn-outline">Home</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="logout.php" class="btn btn-outline">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-outline">Login</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="feedback-container">
        <div class="feedback-box">
            <h2>Send us your feedback</h2>
            <?php if ($message): ?><div class="success-message"><?php echo $message; ?></div><?php endif; ?>
            <?php if ($error): ?><div class="error-message"><?php echo $error; ?></div><?php endif; ?>
            <form method="POST">
                <div class="form-group">
                    <label>Your Feedback:</label>
                    <textarea name="feedback" rows="5" required placeholder="Tell us what you think..."></textarea>
                </div>
                <button type="submit" class="login-btn">Submit Feedback</button>
            </form>
        </div>
    </div>
</body>
</html>