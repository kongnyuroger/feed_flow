<?php
session_start();
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Features - FeedFlow</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .features-page { padding: 4rem 2rem; background: var(--bg); min-height: 60vh; }
        .features-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem; max-width: 1280px; margin: 0 auto; }
        .feature-card { background: white; border-radius: 1rem; padding: 2rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); transition: all 0.3s; text-align: center; }
        .feature-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.15); }
        .feature-icon { width: 80px; height: 80px; background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; }
        .feature-icon i { font-size: 2.5rem; color: white; }
        .feature-card h3 { font-size: 1.5rem; color: var(--text); margin-bottom: 1rem; }
        .feature-card p { color: var(--text-light); margin-bottom: 1.5rem; line-height: 1.6; }
        .feature-list { list-style: none; margin-bottom: 2rem; text-align: left; }
        .feature-list li { display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem; color: var(--text-light); }
        .feature-list li i { color: var(--primary); }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-logo"><i class="fa-solid fa-rss"></i><span>FeedFlow</span></div>
            <ul class="nav-menu">
                <li><a href="index.php">Home</a></li>
                <li><a href="features.php" class="active">Features</a></li>
                <li><a href="trending.php">Trending</a></li>
                <li><a href="categories.php">Categories</a></li>
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
        <div class="container"><h1>Amazing Features</h1><p>Everything you need to stay updated</p></div>
    </section>

    <section class="features-page">
        <div class="container">
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon"><i class="fa-solid fa-rss"></i></div>
                    <h3>Unlimited RSS Feeds</h3>
                    <p>Add as many RSS feeds as you want from any source.</p>
                    <ul class="feature-list">
                        <li><i class="fa-solid fa-check-circle"></i> Support for all RSS formats</li>
                        <li><i class="fa-solid fa-check-circle"></i> Automatic feed discovery</li>
                    </ul>
                    <a href="<?php echo isset($_SESSION['user_id']) ? 'index.php' : 'register.php'; ?>" class="btn btn-primary">Get Started</a>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="fa-solid fa-bolt"></i></div>
                    <h3>Real-time Updates</h3>
                    <p>Get notified instantly when new articles are published.</p>
                    <ul class="feature-list">
                        <li><i class="fa-solid fa-check-circle"></i> Automatic refresh</li>
                        <li><i class="fa-solid fa-check-circle"></i> Push notifications</li>
                    </ul>
                    <a href="<?php echo isset($_SESSION['user_id']) ? 'index.php' : 'register.php'; ?>" class="btn btn-primary">Get Started</a>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="fa-solid fa-mobile-screen"></i></div>
                    <h3>Responsive Design</h3>
                    <p>Read on any device - desktop, tablet, or mobile.</p>
                    <ul class="feature-list">
                        <li><i class="fa-solid fa-check-circle"></i> Mobile-optimized</li>
                        <li><i class="fa-solid fa-check-circle"></i> Touch-friendly</li>
                    </ul>
                    <a href="<?php echo isset($_SESSION['user_id']) ? 'index.php' : 'register.php'; ?>" class="btn btn-primary">Get Started</a>
                </div>
            </div>
        </div>
    </section>

    <div class="footer"><footer>Â© Latest updates 2025. All rights reserved.</footer></div>
</body>
</html>