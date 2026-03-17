<?php
session_start();

// Database connection
$host = 'localhost';
$dbname = 'rss_reader';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Helper function
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

$currentUserName = isLoggedIn() ? $_SESSION['user_name'] : '';

// Get user's feeds if logged in
$user_feeds = [];
if (isLoggedIn()) {
    $stmt = $pdo->prepare("SELECT * FROM feeds WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$_SESSION['user_id']]);
    $user_feeds = $stmt->fetchAll();
}

// Check if user is admin (UPDATED EMAIL)
$is_admin = (isset($_SESSION['user_email']) && $_SESSION['user_email'] == 'kenneorla1@gmail.com');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FeedFlow - RSS News Aggregator</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-logo">
                <i class="fa-solid fa-rss"></i>
                <span>FeedFlow</span>
            </div>
            <ul class="nav-menu">
                <li><a href="features.php">Features</a></li>
                <li><a href="trending.php">Trending</a></li>
                <li><a href="categories.php">Categories</a></li>
            </ul>
            <div class="nav-buttons" id="navButtons">
                <a href="feedback.php" class="btn btn-outline">Feedback</a>
                
                <?php if ($is_admin): ?>
                    <a href="admin.php" class="btn btn-outline">Admin</a>
                <?php endif; ?>
                
                <?php if (isLoggedIn()): ?>
                    <span class="user-greeting">Hi, <?php echo htmlspecialchars($currentUserName); ?></span>
                    <a href="logout.php" class="btn btn-outline">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-outline">Log In</a>
                    <a href="register.php" class="btn btn-primary">Sign Up</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-container">
            <div class="hero-content">
                <h1>All Your Favorite News, <span class="gradient-text">One Feed</span></h1>
                <p>FeedFlow aggregates RSS feeds from your favorite sources. Read personalized news in a clean, distraction-free interface.</p>
                
                <!-- Updated Buttons -->
                <div class="hero-buttons">
                    <?php if (isLoggedIn()): ?>
                        <a href="#demo" class="btn btn-primary btn-large" onclick="loadFirstFeed()">Start Reading <i class="fa-solid fa-arrow-right"></i></a>
                    <?php else: ?>
                        <a href="#demo" class="btn btn-primary btn-large" onclick="showDemo()">Start Reading <i class="fa-solid fa-arrow-right"></i></a>
                        <a href="register.php" class="btn btn-outline btn-large">Register Now <i class="fa-solid fa-user-plus"></i></a>
                    <?php endif; ?>
                </div>
                
                <div class="hero-stats">
                    <div class="stat-item">
                        <span class="stat-number">10K+</span>
                        <span class="stat-label">Active Readers</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">500+</span>
                        <span class="stat-label">News Sources</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">24/7</span>
                        <span class="stat-label">Updates</span>
                    </div>
                </div>
            </div>
            <div class="hero-image">
                <div class="demo-feed">
                    <div class="feed-card">
                        <div class="feed-source">TechCrunch <span>5 min ago</span></div>
                        <h3>OpenAI Announces GPT-5</h3>
                        <p>The latest model shows significant improvements...</p>
                    </div>
                    <div class="feed-card">
                        <div class="feed-source">The Verge <span>12 min ago</span></div>
                        <h3>Apple's Mixed Reality Headset</h3>
                        <p>Early reviews highlight innovative features...</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Feeds Section -->
    <section class="featured-feeds">
        <div class="container">
            <h2 class="section-title">Today's Top Stories</h2>
            <div class="feed-grid" id="feed-grid"></div>
        </div>
    </section>

    <!-- Main RSS Reader Section -->
    <div class="main-container">
        <div class="main-head">
            <div class="aside-container">
                <aside class="feed-main-container">
                    <div class="aside-heading">
                        <span><h1>FEEDS</h1></span>
                        <?php if (isLoggedIn()): ?>
                            <button id="add-feed" title="Add a Feed"> <i class="fa-solid fa-plus"></i></button>
                        <?php endif; ?>
                    </div>
                    <div class="feed-container">
                        <ul id="feed-list">
                            <?php if (isLoggedIn()): ?>
                                <?php if (empty($user_feeds)): ?>
                                    <li>No feeds yet! Click + to add your first feed.</li>
                                <?php else: ?>
                                    <?php foreach ($user_feeds as $feed): ?>
                                    <li class="feed-li-el" data-id="<?php echo $feed['id']; ?>" data-url="<?php echo $feed['feed_url']; ?>" data-name="<?php echo htmlspecialchars($feed['feed_name']); ?>">
                                        <span class="feed-name-span"><?php echo htmlspecialchars($feed['feed_name']); ?></span>
                                        <button class="delete-feed-item-btn" onclick="deleteFeed(<?php echo $feed['id']; ?>, event)">x</button>
                                    </li>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            <?php else: ?>
                                <li><a href="login.php">Login</a> to add and manage your feeds</li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </aside>
            </div>
            
            <div class="main-body">
                <div class="headings">
                    <div class="feed-heading"><h1>Select a Feed<span>News</span></h1></div>
                </div>
                <div class="news-container" id="news-container">
                    <h1>Select a Feed or add any of your choice</h1>
                </div>
                <div class="loader-text" style="display: none;">
                    <div class="spinner"></div> Loading news...
                </div>
                <div class="error-message" style="display: none;">
                    Something went wrong. Please try again.
                </div>
            </div>
        </div>

        <!-- Add Feed Form -->
        <div class="feed-form1">  
            <div class="contact-form">
                <div class="exit-form">
                    <button id="close-feed"><i class="fa-solid fa-xmark"></i></button>
                </div>
                <div class="form-heading"> 
                    <h3>Add New Feed</h3>
                </div>
                <form id="feed-form">
                    <div class="input">
                        <label for="Feed-Url">Feed URL :</label>
                        <input type="url" id="Feed-Url" required>
                        <label for="Feed-Name">Feed Name :</label>
                        <input type="text" id="Feed-Name" required> 
                    </div>
                    <div class="form-button">
                        <button type="submit" id="save">Save Feed</button>
                        <button type="button" id="Cancel">Cancel</button>
                    </div>
                </form>
            </div> 
        </div> 

        <!-- Footer -->
        <div class="footer">
            <footer>Â© Latest updates 2025. All rights reserved.</footer>
        </div> 
    </div>
    
    <script>
        var isLoggedIn = <?php echo isLoggedIn() ? 'true' : 'false'; ?>;
    </script>
    <script src="script.js"></script>
</body>
</html>