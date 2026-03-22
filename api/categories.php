<?php
session_start();
require_once 'config.php';

$categories = [
    ['name' => 'Technology', 'icon' => 'fa-microchip', 'color' => '#2563eb', 'count' => '2.8k'],
    ['name' => 'World News', 'icon' => 'fa-globe', 'color' => '#dc2626', 'count' => '3.5k'],
    ['name' => 'Business', 'icon' => 'fa-chart-line', 'color' => '#059669', 'count' => '1.9k'],
    ['name' => 'Science', 'icon' => 'fa-flask', 'color' => '#7c3aed', 'count' => '1.2k'],
    ['name' => 'Health', 'icon' => 'fa-heart-pulse', 'color' => '#0891b2', 'count' => '1.8k'],
    ['name' => 'Sports', 'icon' => 'fa-futbol', 'color' => '#ea580c', 'count' => '2.1k']
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categories - FeedFlow</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="style.css">
     <link rel="stylesheet" href="responsiveness.css">
     <link rel="stylesheet" href="mobile.css">
    <style>
        .categories-page { padding: 4rem 2rem; background: var(--bg); min-height: 60vh; }
        .categories-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 2rem; max-width: 1280px; margin: 0 auto; }
        .category-card { background: white; border-radius: 1rem; padding: 2rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); transition: all 0.3s; text-align: center; border-top: 4px solid transparent; cursor: pointer; }
        .category-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.15); }
        .category-icon { width: 70px; height: 70px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; }
        .category-icon i { font-size: 2rem; color: white; }
        .category-card h3 { font-size: 1.3rem; color: var(--text); margin-bottom: 0.5rem; }
        .category-count { color: var(--primary); font-weight: 600; margin-bottom: 1rem; }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-logo"><i class="fa-solid fa-rss"></i><span>FeedFlow</span></div>
            <ul class="nav-menu">
                <li><a href="index.php">Home</a></li>
                <li><a href="features.php">Features</a></li>
                <li><a href="trending.php">Trending</a></li>
                <li><a href="categories.php" class="active">Categories</a></li>
            </ul>
            <div class="nav-buttons">
                <a href="feedback.php" class="btn btn-outline">Feedback</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <span class="user-greeting">Hi, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                    <a href="logout.php" class="btn btn-outline">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-outline">Log In</a>
                    <a href="register.php" class="btn btn-primary">Sign Up</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <section class="page-header">
        <div class="container"><h1>Browse Categories</h1><p>Explore news by topic</p></div>
    </section>

    <section class="categories-page">
        <div class="container">
            <div class="categories-grid">
                <?php foreach ($categories as $cat): ?>
                <div class="category-card" onclick="window.location.href='index.php?category=<?php echo strtolower($cat['name']); ?>'" style="border-top-color: <?php echo $cat['color']; ?>">
                    <div class="category-icon" style="background-color: <?php echo $cat['color']; ?>">
                        <i class="fa-solid <?php echo $cat['icon']; ?>"></i>
                    </div>
                    <h3><?php echo $cat['name']; ?></h3>
                    <div class="category-count"><?php echo $cat['count']; ?> articles</div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <div class="footer"><footer>Â© Latest updates 2025. All rights reserved.</footer></div>
</body>
</html>