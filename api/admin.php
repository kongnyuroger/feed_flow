<?php
session_start();
require_once 'config.php';

// UPDATED ADMIN EMAIL
$admin_email = 'kenneorla1@gmail.com';

if (!isset($_SESSION['user_id']) || $_SESSION['user_email'] !== $admin_email) {
    header('Location: login.php');
    exit();
}

$total_users = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$total_feeds = $pdo->query("SELECT COUNT(*) FROM feeds")->fetchColumn();
$total_feedback = $pdo->query("SELECT COUNT(*) FROM feedback")->fetchColumn();
$today_users = $pdo->query("SELECT COUNT(*) FROM users WHERE DATE(created_at) = CURDATE()")->fetchColumn();

$users = $pdo->query("SELECT id, full_name, email, phone, created_at FROM users ORDER BY created_at DESC")->fetchAll();
$feedback = $pdo->query("SELECT f.*, u.full_name FROM feedback f LEFT JOIN users u ON f.user_id = u.id ORDER BY f.created_at DESC")->fetchAll();
$feeds = $pdo->query("SELECT f.*, u.full_name FROM feeds f LEFT JOIN users u ON f.user_id = u.id ORDER BY f.created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - FeedFlow</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="responsiveness.css">
    <link rel="stylesheet" href="mobile.css">

    <link rel="stylesheet" href="style.css">
    <style>
        .admin-wrapper { display: flex; min-height: 100vh; }
        .admin-sidebar { width: 260px; background: #178582; color: white; position: fixed; height: 100vh; }
        .admin-sidebar-header { padding: 2rem 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .admin-sidebar-nav a { display: flex; align-items: center; gap: 1rem; padding: 1rem 1.5rem; color: white; text-decoration: none; border-left: 3px solid transparent; }
        .admin-sidebar-nav a:hover { background: rgba(255,255,255,0.1); border-left-color: #09d1b6; }
        .admin-main { flex: 1; margin-left: 260px; background: #f5f7fa; }
        .admin-header { background: white; padding: 1.5rem 2rem; display: flex; justify-content: space-between; align-items: center; }
        .admin-content { padding: 2rem; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.5rem; margin-bottom: 2rem; }
        .stat-card { background: white; border-radius: 10px; padding: 1.5rem; display: flex; justify-content: space-between; align-items: center; }
        .stat-number { font-size: 2rem; font-weight: bold; color: #178582; }
        .stat-icon { width: 60px; height: 60px; background: linear-gradient(135deg, #178582, #09d1b6); border-radius: 50%; display: flex; align-items: center; justify-content: center; }
        .stat-icon i { font-size: 1.8rem; color: white; }
        .table-section { background: white; border-radius: 10px; padding: 1.5rem; margin-bottom: 2rem; }
        .admin-table { width: 100%; border-collapse: collapse; }
        .admin-table th { text-align: left; padding: 1rem; background: #f8f9fa; color: #178582; }
        .admin-table td { padding: 1rem; border-bottom: 1px solid #e9ecef; }
        .user-badge { background: #178582; color: white; padding: 0.2rem 0.5rem; border-radius: 3px; font-size: 0.8rem; }
    </style>
</head>
<body>
    <div class="admin-wrapper">
        <div class="admin-sidebar">
            <div class="admin-sidebar-header"><h2><i class="fa-solid fa-rss"></i> FeedFlow Admin</h2></div>
            <nav class="admin-sidebar-nav">
                <a href="#users"><i class="fa-solid fa-users"></i> Users <span class="user-badge" style="margin-left: auto;"><?php echo $total_users; ?></span></a>
                <a href="#feeds"><i class="fa-solid fa-rss"></i> Feeds <span class="user-badge" style="margin-left: auto;"><?php echo $total_feeds; ?></span></a>
                <a href="#feedback"><i class="fa-solid fa-comment"></i> Feedback <span class="user-badge" style="margin-left: auto;"><?php echo $total_feedback; ?></span></a>
                <a href="index.php"><i class="fa-solid fa-arrow-left"></i> Back to Site</a>
            </nav>
        </div>
        <div class="admin-main">
            <div class="admin-header">
                <h1>Dashboard</h1>
                <div>
                    <img src="https://ui-avatars.com/api/?name=Kenne+Orla&background=178582&color=fff&size=40" alt="Admin" style="width:40px;border-radius:50%;">
                    <span style="margin-left:10px;">Kenne Orla</span>
                </div>
            </div>
            <div class="admin-content">
                <div class="stats-grid">
                    <div class="stat-card"><div><h3>Total Users</h3><div class="stat-number"><?php echo $total_users; ?></div><small>+<?php echo $today_users; ?> today</small></div><div class="stat-icon"><i class="fa-solid fa-users"></i></div></div>
                    <div class="stat-card"><div><h3>Total Feeds</h3><div class="stat-number"><?php echo $total_feeds; ?></div></div><div class="stat-icon"><i class="fa-solid fa-rss"></i></div></div>
                    <div class="stat-card"><div><h3>Total Feedback</h3><div class="stat-number"><?php echo $total_feedback; ?></div></div><div class="stat-icon"><i class="fa-solid fa-comment"></i></div></div>
                </div>

                <div class="table-section" id="users"><h3>Registered Users</h3>
                    <table class="admin-table"><thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Joined</th></tr></thead>
                    <tbody><?php foreach ($users as $u): ?><tr><td>#<?php echo $u['id']; ?></td><td><?php echo htmlspecialchars($u['full_name']); ?></td><td><?php echo htmlspecialchars($u['email']); ?></td><td><?php echo $u['phone'] ?: '-'; ?></td><td><?php echo date('M d, Y', strtotime($u['created_at'])); ?></td></tr><?php endforeach; ?></tbody></table>
                </div>

                <div class="table-section" id="feeds"><h3>All Feeds</h3>
                    <table class="admin-table"><thead><tr><th>ID</th><th>User</th><th>Feed Name</th><th>URL</th><th>Added</th></tr></thead>
                    <tbody><?php foreach ($feeds as $f): ?><tr><td>#<?php echo $f['id']; ?></td><td><?php echo $f['full_name'] ?? 'Unknown'; ?></td><td><?php echo htmlspecialchars($f['feed_name']); ?></td><td><a href="<?php echo $f['feed_url']; ?>" target="_blank"><?php echo substr($f['feed_url'],0,30); ?>...</a></td><td><?php echo date('M d, Y', strtotime($f['created_at'])); ?></td></tr><?php endforeach; ?></tbody></table>
                </div>

                <div class="table-section" id="feedback"><h3>User Feedback</h3>
                    <table class="admin-table"><thead><tr><th>ID</th><th>User</th><th>Message</th><th>Date</th></tr></thead>
                    <tbody><?php foreach ($feedback as $fb): ?><tr><td>#<?php echo $fb['id']; ?></td><td><?php echo $fb['full_name'] ?? 'Anonymous'; ?></td><td><?php echo htmlspecialchars(substr($fb['message'],0,100)); ?>...</td><td><?php echo date('M d, Y H:i', strtotime($fb['created_at'])); ?></td></tr><?php endforeach; ?></tbody></table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>