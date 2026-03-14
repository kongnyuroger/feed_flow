

const defaultfeeds = [
    { id: 'bbc', name: 'BBC (Top Stories)', url: 'https://feeds.bbci.co.uk/news/rss.xml' },
    { id: 'cnn', name: 'CNN (Top Stories)', url: 'http://rss.cnn.com/rss/cnn_topstories.rss' },
    { id: 'nyt-world', name: 'NYT (World)', url: 'https://www.nytimes.com/services/xml/rss/nyt/World.xml' },
    { id: 'al-jazeera', name: 'Al Jazeera (English)', url: 'https://www.aljazeera.com/xml/rss/all.xml' },
    { id: 'npr', name: 'NPR (Top Stories)', url: 'https://www.npr.org/rss/rss.php?id=1001' },
    { id: 'pbs', name: 'PBS (World)', url: 'https://www.pbs.org/newshour/feeds/rss/podcasts/world' },
    { id: 'new-york', name: 'New York Times (World)', url: 'https://rss.nytimes.com/services/xml/rss/nyt/World.xml' },
    { id: 'the guardian', name: 'The Guardian (World)', url: 'https://www.theguardian.com/world/rss' },
    { id: 'crtv', name: 'CRTV', url: 'https://WWW.crtv.cm/feed/' },
    { id: 'equinox', name: 'Equinox', url: 'https://feeds.feedburner.com/EquinoxeRadio' },
];

// DOM Elements
const feedlistelement = document.getElementById('feed-list');
const newsContainer = document.querySelector('.news-container');
const addfeedbutton = document.getElementById('add-feed');
const closefeedformbutton = document.getElementById('close-feed');
const mainheadings = document.querySelector('.feed-heading');
const loaderText = document.querySelector('.loader-text');
const errorMessage = document.querySelector('.error-message');
const feedformcontainer = document.querySelector('.feed-form1');
const addfeedform = document.getElementById('feed-form');
const addnewfeedurl = document.getElementById('Feed-Url');
const addnewfeedname = document.getElementById('Feed-Name');
const savefeedbutton = document.getElementById('save');
const cancelbutton = document.getElementById('Cancel');

let currentfeeds = JSON.parse(localStorage.getItem('rssFeeds')) || defaultfeeds;
let selectedfeedUrl = '';
let currentlyActiveItem = null;

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    renderfeedlist();
    
    // Check if user is logged in (from PHP variable)
    if (typeof isLoggedIn !== 'undefined' && isLoggedIn) {
        document.querySelector('.main-container').style.display = 'block';
        if (currentfeeds.length > 0) {
            const initialFeed = currentfeeds.find(feed => feed.url === selectedfeedUrl) || currentfeeds[0];
            if (initialFeed) {
                selectedfeed(initialFeed.url, initialFeed.name);
            }
        }
    } else {
        document.querySelector('.main-container').style.display = 'none';
    }

   
    loadFeaturedFeeds();
});


function showLoading() {
    if (newsContainer) newsContainer.innerHTML = '';
    if (loaderText) loaderText.style.display = 'flex';
    if (errorMessage) errorMessage.style.display = 'none';
}

function hideLoading() {
    if (loaderText) loaderText.style.display = 'none';
}

function showErrorMessage(message) {
    if (errorMessage) {
        errorMessage.textContent = message;
        errorMessage.style.display = 'block';
    }
    hideLoading();
}

function hideErrorMessage() {
    if (errorMessage) errorMessage.style.display = 'none';
}


function renderfeedlist() {
    if (!feedlistelement) return;
    
    feedlistelement.innerHTML = '';
    currentfeeds.forEach(feed => {
        const listitem = document.createElement('li');
        listitem.className = 'feed-li-el';
        listitem.dataset.url = feed.url;
        listitem.dataset.name = feed.name;

        const feedNameSpan = document.createElement('span');
        feedNameSpan.textContent = feed.name;
        feedNameSpan.className = 'feed-name-span';
        feedNameSpan.setAttribute('title', feed.name);

        const deleteButton = document.createElement('button');
        deleteButton.textContent = 'x';
        deleteButton.className = 'delete-feed-item-btn';
        deleteButton.setAttribute('aria-label', `Delete ${feed.name}`);
        deleteButton.setAttribute('title', `Delete ${feed.name}`);

        feedNameSpan.addEventListener('click', (event) => {
            event.stopPropagation();
            if (currentlyActiveItem) {
                currentlyActiveItem.classList.remove('active-li');
            }
            listitem.classList.add('active-li');
            currentlyActiveItem = listitem;
            selectedfeed(feed.url, feed.name);
        });

        deleteButton.addEventListener('click', (event) => {
            event.stopPropagation();
            if (confirm(`Are you sure you want to delete "${feed.name}"?`)) {
                currentfeeds = currentfeeds.filter(f => f.url !== feed.url);
                localStorage.setItem('rssFeeds', JSON.stringify(currentfeeds));
                renderfeedlist();

                if (selectedfeedUrl === feed.url) {
                    if (newsContainer) newsContainer.innerHTML = '<h2>Select a Feed or Add a New One.</h2>';
                    if (mainheadings) {
                        mainheadings.innerHTML = '<h1>Welcome! <span>Select a Feed</span></h1>';
                    }
                    selectedfeedUrl = '';
                    localStorage.removeItem('selectedfeedurl');
                    hideErrorMessage();
                }
            }
        });

        listitem.appendChild(feedNameSpan);
        listitem.appendChild(deleteButton);

        if (feed.url === selectedfeedUrl) {
            listitem.classList.add('active-li');
            currentlyActiveItem = listitem;
        }

        feedlistelement.appendChild(listitem);
    });
}


function displayNews(articles) {
    if (!newsContainer) return;

    newsContainer.innerHTML = '';

    if (articles && Array.isArray(articles) && articles.length > 0) {
        articles.forEach(article => {
            const newsEl = document.createElement('div');
            newsEl.className = 'news-card';

            // Always create image div, even if no image
            const imageHtml = article.imageUrl 
                ? `<div class="article-image"><img src="${article.imageUrl}" alt="${article.title || 'News Image'}" /></div>`
                : `<div class="article-image"></div>`; 

            newsEl.innerHTML = `
                ${imageHtml}
                <div class="article-content">
                    <h5>${article.title.toUpperCase()}</h5>
                    <p>${article.description}</p>
                    <small>Posted At ${new Date(article.pubDate).toLocaleDateString()}</small>
                </div>
                <a href='${article.link}' target='_blank'>Read More</a>
            `;
            newsContainer.appendChild(newsEl);
        });
    } else {
        newsContainer.innerHTML = '<p class="empty-message">No articles found.</p>';
    }
}


async function fetchAndParseRssFeed(rssUrlToFetch) {
    const proxyUrl = `https://api.rss2json.com/v1/api.json?rss_url=${encodeURIComponent(rssUrlToFetch)}`;

    try {
        const response = await fetch(proxyUrl);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        if (data.status !== 'ok') {
            throw new Error(`RSS2JSON API Error: ${data.message || 'Unknown API error'}`);
        }

        const items = data.items || [];
        
        const articles = items.map(item => {
            const title = item.title || "No Title";
            const link = item.link || "#";
            const pubDate = item.pubDate || new Date().toISOString();
            const rawDescription = item.description || "No Description";

            const titleWords = title.split(/\s+/);
            const shortTitle = titleWords.slice(0, 6).join(' ') + (titleWords.length > 6 ? '...' : '');

            const cleanDescription = new DOMParser().parseFromString(rawDescription, 'text/html').body.textContent || rawDescription;
            const words = cleanDescription.split(/\s+/);
            const shortDescription = words.slice(0, 20).join(' ') + (words.length > 20 ? '...' : '');

            let imageUrl = "";
            if (item.enclosure && item.enclosure.link) {
                imageUrl = item.enclosure.link;
            } else if (item.thumbnail) {
                imageUrl = item.thumbnail;
            } else {
                const tempDiv = document.createElement("div");
                tempDiv.innerHTML = rawDescription;
                const img = tempDiv.querySelector("img");
                if (img?.src) {
                    imageUrl = img.src;
                }
            }

            return {
                title: shortTitle,
                link,
                pubDate,
                description: shortDescription,
                imageUrl
            };
        });

        return articles;
    } catch (error) {
        console.error("Error in fetchAndParseRssFeed:", error);
        throw error;
    }
}


async function getFeedTitle(rssUrlToFetch) {
    const proxyUrl = `https://api.rss2json.com/v1/api.json?rss_url=${encodeURIComponent(rssUrlToFetch)}`;
    const response = await fetch(proxyUrl);

    if (!response.ok) {
        throw new Error(`Network error (${response.status}).`);
    }

    const data = await response.json();
    if (data.status !== 'ok' || !data.feed || !data.feed.title) {
        throw new Error(`Invalid RSS feed or failed to read title.`);
    }

    return data.feed.title;
}


async function selectedfeed(url, name) {
    selectedfeedUrl = url;
    localStorage.setItem('selectedfeedurl', url);

    if (mainheadings) {
        mainheadings.innerHTML = `<h1><span>${name}</span> News</h1>`;
    }

    showLoading();
    hideErrorMessage();
    
    try {
        const articles = await fetchAndParseRssFeed(url);
        displayNews(articles);
    } catch (error) {
        console.error('Error fetching and displaying feed:', error);
        showErrorMessage(`Failed to load feed: ${error.message || 'Unknown error'}`);
        displayNews([]);
    } finally {
        hideLoading();
    }
}


if (addfeedbutton) {
    addfeedbutton.addEventListener('click', () => {
        if (feedformcontainer) {
            feedformcontainer.style.display = 'flex';
        }
        hideErrorMessage();
    });
}

if (closefeedformbutton) {
    closefeedformbutton.addEventListener('click', () => {
        if (feedformcontainer) feedformcontainer.style.display = 'none';
        if (addfeedform) addfeedform.reset();
        hideErrorMessage();
    });
}

if (cancelbutton) {
    cancelbutton.addEventListener('click', () => {
        if (feedformcontainer) feedformcontainer.style.display = 'none';
        if (addfeedform) addfeedform.reset();
        hideErrorMessage();
    });
}


if (savefeedbutton && addfeedform) {
    addfeedform.addEventListener('submit', async (event) => {
        event.preventDefault();

        const url = addnewfeedurl.value.trim();
        const nameInput = addnewfeedname.value.trim();

        if (!url || !nameInput) {
            showErrorMessage('Please fill in both Feed Name and URL.');
            return;
        }

        savefeedbutton.disabled = true;
        savefeedbutton.textContent = 'Verifying...';
        showLoading();

        try {
            const urlObject = new URL(url);

            if (urlObject.protocol !== 'http:' && urlObject.protocol !== 'https:') {
                throw new Error('URL must start with "http://" or "https://".');
            }

            const isDuplicate = currentfeeds.some(feed => feed.url === url);
            if (isDuplicate) {
                throw new Error('This feed URL already exists in your list.');
            }

            let officialName;
            try {
                officialName = await getFeedTitle(url);
            } catch {
                officialName = nameInput;
            }

            const finalName = officialName || nameInput;

            const newFeed = {
                id: 'custom-' + Date.now(),
                name: finalName,
                url: url
            };

            currentfeeds.push(newFeed);
            localStorage.setItem('rssFeeds', JSON.stringify(currentfeeds));

            renderfeedlist();
            selectedfeed(newFeed.url, newFeed.name);

            hideErrorMessage();
            addfeedform.reset();
            if (feedformcontainer) {
                feedformcontainer.style.display = 'none';
            }

        } catch (e) {
            console.error('Save Feed Error:', e.message);
            showErrorMessage(`Error adding feed: ${e.message}`);
        } finally {
            savefeedbutton.disabled = false;
            savefeedbutton.textContent = 'Save';
            hideLoading();
        }
    });
}


async function loadFeaturedFeeds() {
    const feedGrid = document.getElementById('feed-grid');
    if (!feedGrid) return;
    
    feedGrid.innerHTML = '<div class="loader-text"><div class="spinner"></div> Loading featured feeds...</div>';
    
    try {
        let allArticles = [];
        
        for (const feed of defaultfeeds.slice(0, 3)) {
            const articles = await fetchAndParseRssFeed(feed.url);
            allArticles = [...allArticles, ...articles.slice(0, 2)];
        }
        
        allArticles.sort((a, b) => new Date(b.pubDate) - new Date(a.pubDate));
        
        feedGrid.innerHTML = allArticles.map(article => {
            // Always create image div, even if no image
            const imageHtml = article.imageUrl 
                ? `<div class="article-image"><img src="${article.imageUrl}" alt="${article.title}"></div>`
                : `<div class="article-image"></div>`; // Empty div for placeholder
                
            return `
                <div class="feed-item">
                    ${imageHtml}
                    <h3>${article.title}</h3>
                    <p>${article.description}</p>
                    <a href="${article.link}" target="_blank" class="read-more">Read More</a>
                </div>
            `;
        }).join('');
        
    } catch (error) {
        console.error('Error loading featured feeds:', error);
        feedGrid.innerHTML = '<p class="error-message">No internet connection. Check your internet connection and try again.</p>';
    }
}

// Show demo function
window.showDemo = function() {
    if (currentfeeds.length > 0) {
        selectedfeed(currentfeeds[0].url, currentfeeds[0].name);
        document.querySelector('.main-container').scrollIntoView({ behavior: 'smooth' });
    }
};

// Load first feed
window.loadFirstFeed = function() {
    const firstFeed = document.querySelector('.feed-li-el');
    if (firstFeed) {
        firstFeed.click();
    }
    document.querySelector('.main-container').scrollIntoView({ behavior: 'smooth' });
};

// Delete feed function
window.deleteFeed = async function(feedId, event) {
    event.stopPropagation();
    
    if (!confirm('Are you sure you want to delete this feed?')) {
        return;
    }
    
    try {
        const response = await fetch('delete-feed.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ feed_id: feedId })
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Remove from currentfeeds array
            currentfeeds = currentfeeds.filter(f => f.id !== feedId);
            localStorage.setItem('rssFeeds', JSON.stringify(currentfeeds));
            
            // Remove from DOM
            event.target.closest('.feed-li-el').remove();
            
            // If no feeds left, show message
            if (document.querySelectorAll('.feed-li-el').length === 0) {
                feedlistelement.innerHTML = '<li>No feeds yet! Click + to add your first feed.</li>';
                newsContainer.innerHTML = '<h1>Select a Feed or add any of your choice</h1>';
                if (mainheadings) {
                    mainheadings.innerHTML = '<h1>Welcome! <span>Select a Feed</span></h1>';
                }
            }
        } else {
            alert('Failed to delete feed');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred');
    }
};