<?php
session_start();
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trending - FeedFlow</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="style.css">
     <link rel="stylesheet" href="responsiveness.css">
     <link rel="stylesheet" href="mobile.css">
    <style>
        .trending-page { padding: 4rem 2rem; background: var(--bg); min-height: 60vh; }
        .trending-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem; max-width: 1280px; margin: 0 auto; }
        .trending-card { background: white; border-radius: 1rem; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1); transition: all 0.3s; height: 420px; display: flex; flex-direction: column; }
        .trending-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.15); }
        .trending-image { height: 180px; background: #f0f0f0; position: relative; }
        .trending-image img { width: 100%; height: 100%; object-fit: cover; }
        .trending-image:empty::after { content: '\f15c'; font-family: 'Font Awesome 6 Free'; font-weight: 900; font-size: 3rem; color: #aaa; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); }
        .trending-content { padding: 1.5rem; flex: 1; display: flex; flex-direction: column; }
        .trending-content h3 { font-size: 1.2rem; margin-bottom: 0.5rem; height: 2.8rem; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; }
        .trending-content p { color: var(--text-light); margin-bottom: 1rem; flex: 1; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; }
        .trending-meta { display: flex; justify-content: space-between; margin-bottom: 1rem; color: #999; font-size: 0.85rem; height: 1.5rem; }
        .read-more { display: inline-block; padding: 0.5rem 1rem; background: var(--primary); color: white; text-decoration: none; border-radius: 5px; text-align: center; margin-top: auto; }
        
       
        .trending-content h3 { font-size: 1rem !important; height: 2.4rem; }
        .trending-content p { font-size: 0.8rem !important; -webkit-line-clamp: 2; }
        .trending-meta { font-size: 0.7rem !important; }
        .read-more { font-size: 0.8rem !important; padding: 0.3rem 0.8rem !important; }
        .trending-card { height: 380px !important; }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-logo"><i class="fa-solid fa-rss"></i><span>FeedFlow</span></div>
            <ul class="nav-menu">
                <li><a href="index.php">Home</a></li>
                <li><a href="features.php">Features</a></li>
                <li><a href="trending.php" class="active">Trending</a></li>
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
        <div class="container"><h1>Trending Now</h1><p>Most popular stories right now</p></div>
    </section>

    <section class="trending-page">
        <div class="container">
            <div class="trending-grid" id="trending-grid">
                <div class="loader-text"><div class="spinner"></div> Loading trending stories...</div>
            </div>
        </div>
    </section>

    <div class="footer"><footer>Â© Latest updates 2025. All rights reserved.</footer></div>

    <script>
    const defaultFeeds = [
        { name: 'BBC News', url: 'https://feeds.bbci.co.uk/news/rss.xml' },
        { name: 'CNN', url: 'http://rss.cnn.com/rss/cnn_topstories.rss' },
        { name: 'TechCrunch', url: 'https://techcrunch.com/feed/' }
    ];

    document.addEventListener('DOMContentLoaded', loadTrendingFeeds);

    async function loadTrendingFeeds() {
        const grid = document.getElementById('trending-grid');
        try {
            let articles = [];
            for (const feed of defaultFeeds) {
                           const response = await fetch(`https://api.rss2json.com/v1/api.json?rss_url=${encodeURIComponent(feed.url)}`);
            const data = await response.json();
            if (data.status === 'ok' && data.items) {
                articles = [...articles, ...data.items.slice(0, 2).map(item => ({
                    title: item.title.length > 10 ? item.title.substring(0, 10) + '...' : item.title,

                        title: item.title,
                        description: item.description.replace(/<[^>]*>/g, '').substring(0, 30) + '...',
                        link: item.link,
                        pubDate: item.pubDate,
                        imageUrl: item.thumbnail || ''
                    }))];
                }
            }
            articles.sort((a, b) => new Date(b.pubDate) - new Date(a.pubDate));
            grid.innerHTML = articles.map(a => `
                <div class="trending-card">
                    <div class="trending-image">${a.imageUrl ? `<img src="${a.imageUrl}" alt="">` : ''}</div>
                    <div class="trending-content">
                        <h3>${a.title}</h3>
                        <p>${a.description}</p>
                        <div class="trending-meta"><span>${new Date(a.pubDate).toLocaleDateString()}</span></div>
                        <a href="${a.link}" target="_blank" class="read-more">Read More</a>
                    </div>
                </div>
            `).join('');
        } catch (e) {
            grid.innerHTML = '<p class="error-message">Failed to load trending stories</p>';
        }
    }
    </script>
</body>
</html>