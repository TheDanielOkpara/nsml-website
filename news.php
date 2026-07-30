<?php
require_once __DIR__ . '/cms/includes/db.php';
$posts = db()->query("SELECT * FROM blog_posts WHERE is_published = 1 ORDER BY published_at DESC, id DESC")->fetchAll();

function news_excerpt(array $p): string {
    if (!empty($p['excerpt'])) return $p['excerpt'];
    return mb_substr(trim(strip_tags($p['body'] ?? '')), 0, 220) . '…';
}
function news_date(array $p): string {
    return $p['published_at'] ? date('F Y', strtotime($p['published_at'])) : '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>News — Nilayo Sports Management</title>
  <meta name="description" content="The latest news, event updates, and insights from Nilayo Sports Management — Africa's leading sports marketing agency.">
  <!-- Open Graph / social sharing -->
  <meta property="og:type" content="website">
  <meta property="og:url" content="https://nilayosports.com/news">
  <meta property="og:title" content="News — Nilayo Sports Management">
  <meta property="og:description" content="The latest news, event updates, and insights from Nilayo Sports Management — Africa's leading sports marketing agency.">
  <meta property="og:image" content="https://nilayosports.com/images/og-image.jpg">
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="News — Nilayo Sports Management">
  <meta name="twitter:description" content="The latest news, event updates, and insights from Nilayo Sports Management — Africa's leading sports marketing agency.">
  <meta name="twitter:image" content="https://nilayosports.com/images/og-image.jpg">

  <link rel="icon" type="image/x-icon" href="/images/favicon.ico">
  <link rel="icon" type="image/png" sizes="32x32" href="/images/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="/images/favicon-16x16.png">
  <link rel="apple-touch-icon" sizes="180x180" href="/images/apple-touch-icon.png">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=Outfit:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/styles.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>
  <script src="js/nav.js"></script>
  <style>
    /* ── NEWS ARTICLE CARDS ───────────────── */
    .news-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 1.5rem;
    }

    /* Featured post — spans full row */
    .article-featured {
      grid-column: 1 / -1;
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 0;
      background: #ffffff;
      border: 1.5px solid var(--border);
      border-radius: var(--r-xl);
      overflow: hidden;
      transition: border-color 0.4s var(--ease), box-shadow 0.4s var(--ease);
    }

    .article-featured:hover {
      border-color: var(--green);
      box-shadow: 0 8px 40px rgba(26,184,60,0.1);
    }

    .article-featured .article-img-wrap {
      height: 100%;
      min-height: 340px;
    }

    .article-featured .article-body {
      padding: 3rem;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }

    .article-featured .article-title {
      font-size: clamp(1.5rem, 2.5vw, 2rem);
      display: -webkit-box;
      -webkit-line-clamp: 3;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }

    .article-featured .article-excerpt {
      display: -webkit-box;
      -webkit-line-clamp: 3;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }

    .article-featured .article-img-wrap {
      max-height: 460px;
    }

    /* Standard article card */
    .article-card {
      background: #ffffff;
      border: 1.5px solid var(--border);
      border-radius: var(--r-xl);
      overflow: hidden;
      display: flex;
      flex-direction: column;
      transition: border-color 0.4s var(--ease), box-shadow 0.4s var(--ease), transform 0.4s var(--ease);
    }

    .article-card:hover {
      border-color: var(--green);
      box-shadow: 0 6px 28px rgba(26,184,60,0.1);
      transform: translateY(-3px);
    }

    /* Shared image wrapper */
    .article-img-wrap {
      overflow: hidden;
      height: 220px;
      flex-shrink: 0;
    }

    .article-img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      display: block;
      filter: contrast(1.06) brightness(0.97);
      transition: transform 0.75s var(--ease), filter 0.75s var(--ease);
    }

    .article-card:hover .article-img,
    .article-featured:hover .article-img {
      transform: scale(1.05);
      filter: saturate(1.1) contrast(1.08);
    }

    /* Shared body */
    .article-body {
      padding: 1.75rem;
      display: flex;
      flex-direction: column;
      flex: 1;
    }

    .article-meta {
      display: flex;
      align-items: center;
      gap: 0.75rem;
      margin-bottom: 1rem;
      flex-wrap: wrap;
    }

    .article-cat {
      display: inline-block;
      background: var(--accent-glow);
      color: var(--green);
      border: 1px solid var(--accent-ring);
      border-radius: 9999px;
      font-size: 0.6rem;
      font-weight: 700;
      letter-spacing: 0.12em;
      text-transform: uppercase;
      padding: 0.25rem 0.875rem;
    }

    .article-date {
      font-size: 0.8125rem;
      color: var(--text-muted);
    }

    .article-title {
      font-family: var(--font-d);
      font-size: 1.1875rem;
      font-weight: 700;
      letter-spacing: -0.025em;
      line-height: 1.25;
      color: var(--navy);
      margin-bottom: 0.75rem;
      text-decoration: none;
      display: block;
      transition: color 0.3s var(--ease);
    }

    .article-title:hover { color: var(--green-dark); }

    .article-excerpt {
      font-size: 0.9375rem;
      color: var(--text-sub);
      line-height: 1.7;
      flex: 1;
      margin-bottom: 1.5rem;
    }

    .article-read-more {
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      font-size: 0.875rem;
      font-weight: 600;
      color: var(--green-dark);
      text-decoration: none;
      transition: gap 0.3s var(--ease), color 0.3s var(--ease);
      margin-top: auto;
    }

    .article-read-more:hover {
      gap: 0.875rem;
      color: var(--navy);
    }

    .article-read-more-arrow {
      font-size: 1rem;
      transition: transform 0.3s var(--ease);
    }

    .article-read-more:hover .article-read-more-arrow {
      transform: translate(3px, -2px);
    }

    /* ── CATEGORIES FILTER ────────────────── */
    .filter-bar {
      display: flex;
      align-items: center;
      gap: 0.625rem;
      flex-wrap: wrap;
      margin-bottom: 3rem;
    }

    .filter-btn {
      background: transparent;
      border: 1.5px solid var(--border);
      border-radius: 9999px;
      padding: 0.5rem 1.25rem;
      font-family: var(--font-b);
      font-size: 0.8125rem;
      font-weight: 600;
      color: var(--text-sub);
      cursor: pointer;
      transition: all 0.3s var(--ease);
    }

    .filter-btn:hover,
    .filter-btn.active {
      background: var(--navy);
      border-color: var(--navy);
      color: #ffffff;
    }

    .filter-btn.active-green {
      background: var(--green);
      border-color: var(--green);
      color: #ffffff;
    }

    /* ── NEWSLETTER BAND ──────────────────── */
    .newsletter-band {
      background: var(--surface);
      border-top: 1px solid var(--border);
      border-bottom: 1px solid var(--border);
      padding: 5rem 1.5rem;
    }

    .newsletter-inner {
      max-width: 48rem;
      margin: 0 auto;
      text-align: center;
    }

    .newsletter-inner .section-tag {
      justify-content: center;
      margin-bottom: 1.5rem;
    }

    .newsletter-h2 {
      font-family: var(--font-d);
      font-size: clamp(1.875rem, 3.5vw, 2.75rem);
      font-weight: 800;
      letter-spacing: -0.03em;
      line-height: 1.1;
      color: var(--navy);
      margin-bottom: 1rem;
    }

    .newsletter-h2 .hi { color: var(--green); }

    .newsletter-p {
      font-size: 1.0625rem;
      color: var(--text-sub);
      line-height: 1.7;
      margin-bottom: 2.5rem;
    }

    .newsletter-form {
      display: flex;
      gap: 0.75rem;
      max-width: 36rem;
      margin: 0 auto;
    }

    .newsletter-input {
      flex: 1;
      background: #ffffff;
      border: 1.5px solid var(--border);
      border-radius: 9999px;
      padding: 0.875rem 1.5rem;
      font-family: var(--font-b);
      font-size: 0.9375rem;
      color: var(--navy);
      outline: none;
      transition: border-color 0.3s var(--ease);
    }

    .newsletter-input::placeholder { color: var(--text-muted); }

    .newsletter-input:focus {
      border-color: var(--green);
      box-shadow: 0 0 0 3px rgba(26,184,60,0.12);
    }

    .newsletter-disclaimer {
      font-size: 0.8125rem;
      color: var(--text-muted);
      margin-top: 1rem;
    }

    /* ── RESPONSIVE ───────────────────────── */
    @media (max-width: 1024px) {
      .news-grid { grid-template-columns: repeat(2, 1fr); }
      .article-featured { grid-template-columns: 1fr; }
      .article-featured .article-img-wrap { min-height: 240px; }
    }

    @media (max-width: 768px) {
      .news-grid { grid-template-columns: 1fr; }
      .article-featured { grid-column: 1; }
      .article-featured .article-body { padding: 1.5rem; }
      .filter-bar { gap: 0.5rem; }
      .filter-btn { padding: 0.4375rem 1rem; font-size: 0.75rem; }
      .newsletter-form { flex-direction: column; gap: 0.75rem; }
      .newsletter-input { border-radius: var(--r-lg); }
      .newsletter-band { padding: 3.5rem 1.25rem; }
    }

    @media (max-width: 480px) {
      .article-img-wrap { height: 180px; }
    }
  </style>
</head>
<body>
  <div class="noise" aria-hidden="true"></div>
  <div id="nav-root"></div>

  <!-- PAGE HERO -->
  <div class="page-hero">
    <div class="page-hero-bg" style="background-image:url('https://images.unsplash.com/photo-1552674605-db5fecabfe68?w=1920&q=80&auto=format&fit=crop')"></div>
    <div class="page-hero-overlay"></div>
    <div class="page-hero-inner">
      <div class="page-hero-label">Latest Updates</div>
      <h1 class="page-hero-h1">News &amp; <span class="hi">Insights</span></h1>
      <p class="page-hero-p">Event announcements, partnership news, athlete stories, and insights from the frontlines of African sport.</p>
    </div>
  </div>

  <!-- NEWS CONTENT -->
  <div class="section-wrap">

    <!-- FILTER BAR -->
    <div class="filter-bar">
      <button class="filter-btn active-green">All</button>
      <button class="filter-btn">Events</button>
      <button class="filter-btn">Partnerships</button>
      <button class="filter-btn">Athletes</button>
      <button class="filter-btn">Industry</button>
      <button class="filter-btn">Club News</button>
    </div>

    <!-- ARTICLES GRID -->
    <div class="news-grid">
      <?php foreach ($posts as $i => $p): ?>
      <article class="<?= $i === 0 ? 'article-featured' : ($i >= 7 ? 'article-card extra-article' : 'article-card') ?>" data-reveal<?= $i >= 7 ? ' style="display:none;opacity:0;transform:translateY(2rem);"' : '' ?>>
        <div class="article-img-wrap">
          <img class="article-img"
            src="<?= htmlspecialchars($p['cover_image'] ?: 'images/news-placeholder.jpg') ?>"
            alt="<?= htmlspecialchars($p['title']) ?>"
            loading="lazy">
        </div>
        <div class="article-body">
          <div class="article-meta">
            <span class="article-cat">News</span>
            <span class="article-date"><?= htmlspecialchars(news_date($p)) ?></span>
          </div>
          <a href="/<?= urlencode($p['slug']) ?>" class="article-title">
            <?= htmlspecialchars($p['title']) ?>
          </a>
          <p class="article-excerpt">
            <?= htmlspecialchars(news_excerpt($p)) ?>
          </p>
          <a href="/<?= urlencode($p['slug']) ?>" class="article-read-more">
            Read Full Story <span class="article-read-more-arrow">↗</span>
          </a>
        </div>
      </article>
      <?php endforeach; ?>
      <?php if (!$posts): ?>
        <p style="grid-column:1/-1;text-align:center;color:var(--text-muted);padding:3rem 0;">No articles yet.</p>
      <?php endif; ?>

    </div><!-- /news-grid -->

    <!-- LOAD MORE -->
    <div style="text-align:center;margin-top:3.5rem;<?= count($posts) <= 7 ? 'display:none;' : '' ?>" id="loadMoreWrap">
      <button id="loadMoreBtn" style="display:inline-flex;align-items:center;gap:0.75rem;cursor:pointer;font-size:0.9375rem;font-weight:600;padding:0.9375rem 2rem;border-radius:9999px;border:1.5px solid var(--border);background:transparent;color:var(--navy);transition:all 0.4s cubic-bezier(0.32,0.72,0,1);font-family:var(--font-b);">
        <span id="loadMoreLabel">Load More Articles</span>
        <span id="loadMoreSpinner" style="display:none;width:1.125rem;height:1.125rem;border:2px solid var(--border);border-top-color:var(--green);border-radius:50%;animation:spin 0.7s linear infinite;"></span>
      </button>
    </div>

  </div><!-- /section-wrap -->

  <!-- NEWSLETTER SIGN-UP -->
  <div class="newsletter-band">
    <div class="newsletter-inner" data-reveal>
      <div class="section-tag" style="justify-content:center;">Stay Informed</div>
      <h2 class="newsletter-h2">Get <span class="hi">NSML News</span><br>in Your Inbox</h2>
      <p class="newsletter-p">Event dates, partnership announcements, and sports industry insights — delivered directly to you. No spam, unsubscribe anytime.</p>
      <form class="newsletter-form" id="newsNewsletterForm" novalidate>
        <input
          type="email"
          id="newsEmail"
          class="newsletter-input"
          placeholder="Enter your email address"
          aria-label="Email address"
          autocomplete="email">
        <button type="submit" class="btn btn-fill" style="white-space:nowrap;flex-shrink:0;">
          <span id="newsSubLabel">Subscribe</span>
          <span class="btn-icon">↗</span>
        </button>
      </form>
      <p class="newsletter-disclaimer" id="newsDisclaimer">By subscribing you agree to receive email communications from NSML. Unsubscribe at any time.</p>
      <p id="newsEmailError" style="font-size:0.8125rem;color:#e53935;font-weight:500;margin-top:0.5rem;display:none;"></p>
      <div id="newsSuccess" style="display:none;align-items:center;gap:0.75rem;justify-content:center;padding:1rem;background:rgba(26,184,60,0.08);border:1px solid var(--accent-ring);border-radius:var(--r-lg);margin-top:1rem;">
        <span style="color:var(--green-dark);font-size:1.125rem;">✓</span>
        <span style="font-size:0.9375rem;color:var(--navy);font-weight:600;">You're subscribed — thanks!</span>
      </div>
    </div>
  </div>

  <!-- FOOTER -->
  
  <?php require __DIR__ . '/cms/partials/partners-marquee.php'; ?>

<footer>
    <div class="footer-inner">
      <div class="footer-top">
        <div>
          <a href="/" class="f-logo"><img src="images/logo.png" alt="Nilayo Sports Management Ltd" class="f-logo-img"></a>
          <div class="f-tagline">Africa's leading sports marketing, brand management and procurement agency. Home of the Access Bank Lagos City Marathon.</div>
        </div>
        <div>
          <div class="f-col-title">Navigate</div>
          <ul class="f-links">
            <li><a href="/">Home</a></li>
            <li><a href="about">About</a></li>
            <li><a href="services">Services</a></li>
            <li><a href="properties">Properties</a></li>
            <li><a href="news">News</a></li>
            <li><a href="contact">Contact</a></li>
          </ul>
        </div>
        <div>
          <div class="f-col-title">Properties</div>
          <ul class="f-links">
            <li><a href="lagos-marathon">Lagos City Marathon</a></li>
            <li><a href="abuja-marathon">Abuja City Half Marathon</a></li>
            <li><a href="abeokuta-race">Abeokuta 10KM Race</a></li>
            <li><a href="ijebu-marathon">Ijebu Heritage Half Marathon</a></li>
            <li><a href="copa-lagos">Copa Lagos Beach Soccer</a></li>
            <li><a href="enugu-marathon">Enugu City Marathon</a></li>
            <li><a href="stormers-club">Stormers Sports Club</a></li>
          </ul>
        </div>
        <div class="footer-social-col">
          <div class="f-col-title">Follow Us</div>
          <div class="footer-social-list">
            <a href="https://www.instagram.com/nilayosports" target="_blank" rel="noopener" aria-label="Instagram" class="social-link">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
            </a>
            <a href="https://www.facebook.com/share/1DSXqskp56/" target="_blank" rel="noopener" aria-label="Facebook" class="social-link">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
            </a>
            <a href="https://x.com/nilayosports" target="_blank" rel="noopener" aria-label="X (Twitter)" class="social-link">
              <svg width="17" height="17" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
            </a>
            <a href="https://www.linkedin.com/company/nilayosports/" target="_blank" rel="noopener" aria-label="LinkedIn" class="social-link">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6zM2 9h4v12H2z"/><circle cx="4" cy="4" r="2"/></svg>
            </a>
            <a href="https://youtube.com/@nilayosports" target="_blank" rel="noopener" aria-label="YouTube" class="social-link">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M22.54 6.42a2.78 2.78 0 0 0-1.95-1.96C18.88 4 12 4 12 4s-6.88 0-8.59.46A2.78 2.78 0 0 0 1.46 6.42 29 29 0 0 0 1 12a29 29 0 0 0 .46 5.58 2.78 2.78 0 0 0 1.95 1.96C5.12 20 12 20 12 20s6.88 0 8.59-.46a2.78 2.78 0 0 0 1.96-1.96A29 29 0 0 0 23 12a29 29 0 0 0-.46-5.58z"/><polygon points="9.75 15.02 15.5 12 9.75 8.98 9.75 15.02" fill="white"/></svg>
            </a>
          </div>
        </div>
      </div>
      
            <div class="footer-bottom">
        <div class="f-copy">&copy; 2025 Nilayo Sports Management Ltd. All rights reserved.</div>
        <a href="http://designthngs.com/" target="_blank" rel="noopener" class="f-credit">Built by Design Things Studio</a>

      </div>
    </div>
  </footer>

  <script>
    /* ── SPINNER ANIMATION ─────────────────── */
    const styleEl = document.createElement('style');
    styleEl.textContent = '@keyframes spin { to { transform: rotate(360deg); } }';
    document.head.appendChild(styleEl);

    /* ── SCROLL REVEALS ─────────────────────── */
    const io = new IntersectionObserver((entries) => {
      entries.forEach((entry, i) => {
        if (!entry.isIntersecting) return;
        const el = entry.target;
        setTimeout(() => {
          el.style.transition = `opacity 0.75s cubic-bezier(0.32,0.72,0,1) ${i * 70}ms,
                                  transform 0.75s cubic-bezier(0.32,0.72,0,1) ${i * 70}ms`;
          el.style.opacity = '1';
          el.style.transform = 'none';
        }, 0);
        io.unobserve(el);
      });
    }, { threshold: 0.06 });

    document.querySelectorAll('[data-reveal]:not(.extra-article)').forEach(el => io.observe(el));

    /* ── LOAD MORE ──────────────────────────── */
    const loadMoreBtn  = document.getElementById('loadMoreBtn');
    const loadMoreWrap = document.getElementById('loadMoreWrap');
    const extras       = document.querySelectorAll('.extra-article');

    if (loadMoreBtn) {
      loadMoreBtn.addEventListener('click', () => {
        const label   = document.getElementById('loadMoreLabel');
        const spinner = document.getElementById('loadMoreSpinner');

        // Show loading state
        label.textContent = 'Loading…';
        spinner.style.display = 'block';
        loadMoreBtn.disabled = true;

        setTimeout(() => {
          extras.forEach((el, i) => {
            el.style.display = '';
            // Small stagger then trigger reveal
            setTimeout(() => {
              el.style.transition = 'opacity 0.75s cubic-bezier(0.32,0.72,0,1), transform 0.75s cubic-bezier(0.32,0.72,0,1)';
              el.style.opacity    = '1';
              el.style.transform  = 'none';
            }, i * 120);
          });

          // Hide the load more button after revealing
          setTimeout(() => {
            loadMoreWrap.style.display = 'none';
          }, extras.length * 120 + 400);
        }, 800);
      });
    }

    /* ── CATEGORY FILTER ────────────────────── */
    const filterBtns = document.querySelectorAll('.filter-btn');

    filterBtns.forEach(btn => {
      btn.addEventListener('click', () => {
        filterBtns.forEach(b => {
          b.classList.remove('active-green', 'active');
          b.style.background = '';
          b.style.borderColor = '';
          b.style.color = '';
        });
        btn.classList.add('active-green');
      });
    });

    /* ── NEWSLETTER VALIDATION ──────────────── */
    const newsForm    = document.getElementById('newsNewsletterForm');
    const newsEmailEl = document.getElementById('newsEmail');
    const newsErrEl   = document.getElementById('newsEmailError');
    const newsSuccess = document.getElementById('newsSuccess');
    const newsDiscl   = document.getElementById('newsDisclaimer');

    function validateNewsEmail(val) {
      if (!val.trim()) return 'Please enter your email address.';
      if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val.trim())) return 'Please enter a valid email address.';
      return '';
    }

    if (newsForm) {
      newsEmailEl.addEventListener('input', () => {
        const err = validateNewsEmail(newsEmailEl.value);
        newsErrEl.textContent    = err;
        newsErrEl.style.display  = err ? 'block' : 'none';
        newsEmailEl.style.borderColor = err ? '#e53935' : (newsEmailEl.value ? 'var(--green)' : '');
      });

      newsForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const err = validateNewsEmail(newsEmailEl.value);
        if (err) {
          newsErrEl.textContent    = err;
          newsErrEl.style.display  = 'block';
          newsEmailEl.style.borderColor = '#e53935';
          newsEmailEl.focus();
          return;
        }
        // Real subscription to the PHP backend
        const fd = new FormData();
        fd.append('email', newsEmailEl.value.trim());
        fetch('subscribe.php', { method: 'POST', body: fd })
          .then(res => res.json())
          .then(data => {
            if (data.ok) {
              newsForm.style.display    = 'none';
              newsDiscl.style.display   = 'none';
              newsErrEl.style.display   = 'none';
              newsSuccess.style.display = 'flex';
            } else {
              newsErrEl.textContent   = data.error || 'Something went wrong. Please try again.';
              newsErrEl.style.display = 'block';
            }
          })
          .catch(() => {
            newsErrEl.textContent   = 'Network error. Please try again.';
            newsErrEl.style.display = 'block';
          });
      });
    }

    /* ── NAV SCROLL STATE ───────────────────── */
    const navPill = document.getElementById('navPill');
    if (navPill) {
      window.addEventListener('scroll', () => {
        navPill.classList.toggle('scrolled', window.scrollY > 60);
      }, { passive: true });
    }
  </script>
</body>
</html>
