<?php
require_once __DIR__ . '/cms/includes/db.php';
$activeProps = db()->query('SELECT * FROM properties WHERE is_upcoming = 0 ORDER BY sort_order ASC, id ASC')->fetchAll();
$upcomingProps = db()->query('SELECT * FROM properties WHERE is_upcoming = 1 ORDER BY sort_order ASC, id ASC')->fetchAll();

function prop_stats(array $p): array {
    $stats = [];
    foreach ([1, 2, 3] as $n) {
        if (!empty($p["stat{$n}_val"])) {
            $stats[] = ['val' => $p["stat{$n}_val"], 'lbl' => $p["stat{$n}_lbl"]];
        }
    }
    return $stats;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Properties — Nilayo Sports Management</title>
  <meta name="description" content="NSML's portfolio of world-class sporting events across Africa — Lagos City Marathon, Abuja City Marathon, Copa Lagos Beach Soccer, and more.">
  <!-- Open Graph / social sharing -->
  <meta property="og:type" content="website">
  <meta property="og:url" content="https://nilayosports.com/properties">
  <meta property="og:title" content="Properties — Nilayo Sports Management">
  <meta property="og:description" content="NSML's portfolio of world-class sporting events across Africa — Lagos City Marathon, Abuja City Marathon, Copa Lagos Beach Soccer, and more.">
  <meta property="og:image" content="https://nilayosports.com/images/og-image.jpg">
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="Properties — Nilayo Sports Management">
  <meta name="twitter:description" content="NSML's portfolio of world-class sporting events across Africa — Lagos City Marathon, Abuja City Marathon, Copa Lagos Beach Soccer, and more.">
  <meta name="twitter:image" content="https://nilayosports.com/images/og-image.jpg">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=Outfit:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/styles.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>
  <script src="js/nav.js"></script>
  <style>
    /* ── PROPERTIES GRID ──────────────────── */
    .props-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 1rem;
    }

    .pcard {
      padding: 0.25rem;
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: var(--r-xl);
      position: relative;
      opacity: 0;
      transform: translateY(2rem);
      transition: border-color 0.4s var(--ease);
    }

    .pcard:hover { border-color: var(--border-hi); }

    .pcard-inner {
      border-radius: calc(var(--r-xl) - 0.25rem);
      overflow: hidden;
      background: var(--surface-2);
    }

    .pcard-img-wrap {
      overflow: hidden;
      height: 220px;
      position: relative;  /* enables logo overlay */
    }

    /* Event logo overlay — bottom-left of card image */
    .pcard-event-logo {
      position: absolute;
      bottom: 0.875rem;
      left: 0.875rem;
      z-index: 3;
      background: rgba(255,255,255,0.95);
      border-radius: 0.625rem;
      padding: 0.375rem 0.625rem;
      display: flex;
      align-items: center;
      box-shadow: 0 2px 8px rgba(13,31,60,0.15);
    }

    .pcard-event-logo img {
      height: 36px;
      width: auto;
      max-width: 110px;
      object-fit: contain;
      display: block;
    }

    .pcard.featured .pcard-event-logo img {
      height: 44px;
      max-width: 140px;
    }

    .pcard-img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      filter: grayscale(0.4) contrast(1.08);
      transition: transform 0.75s var(--ease), filter 0.75s var(--ease);
      display: block;
    }

    .pcard:hover .pcard-img { transform: scale(1.05); filter: grayscale(0.1) contrast(1.1); }

    .pcard-body { padding: 1.75rem; }

    .pcard-tag {
      display: inline-block;
      background: var(--accent-glow);
      color: var(--accent);
      border: 1px solid var(--accent-ring);
      border-radius: 9999px;
      font-size: 0.625rem;
      font-weight: 700;
      letter-spacing: 0.11em;
      text-transform: uppercase;
      padding: 0.25rem 0.875rem;
      margin-bottom: 0.875rem;
    }

    .pcard-title {
      font-family: var(--font-d);
      font-size: 1.0625rem;
      font-weight: 700;
      letter-spacing: -0.02em;
      line-height: 1.25;
      margin-bottom: 0.625rem;
      color: var(--navy);
    }

    .pcard-desc {
      font-size: 0.8125rem;
      color: var(--text-sub);
      line-height: 1.7;
      margin-bottom: 1.25rem;
    }

    .pcard-stats {
      display: flex;
      gap: 1.5rem;
      flex-wrap: wrap;
      border-top: 1px solid var(--border);
      padding-top: 1rem;
    }

    .pcard-stat-val {
      font-family: var(--font-d);
      font-size: 1.125rem;
      font-weight: 700;
      letter-spacing: -0.025em;
      color: var(--text);
    }

    .pcard-stat-lbl { font-size: 0.6875rem; color: var(--text-muted); margin-top: 0.1rem; }

    .pcard-badge {
      position: absolute;
      top: 1.25rem;
      right: 1.25rem;
      background: var(--accent);
      color: #0c0e0d;
      border-radius: 9999px;
      font-size: 0.5625rem;
      font-weight: 700;
      letter-spacing: 0.09em;
      text-transform: uppercase;
      padding: 0.3rem 0.875rem;
      z-index: 2;
    }

    /* ── FEATURED (WIDE CARD) ─────────────── */
    .pcard.featured {
      grid-column: span 3;
    }

    .pcard.featured .pcard-inner {
      display: grid;
      grid-template-columns: 1.1fr 1fr;
    }

    .pcard.featured .pcard-img-wrap { height: 100%; min-height: 300px; }

    .pcard.featured .pcard-body {
      padding: 2.5rem;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }

    .pcard.featured .pcard-title { font-size: 1.375rem; }

    /* ── UPCOMING BAND ────────────────────── */
    .upcoming-band {
      background: var(--surface);
      border-top: 1px solid var(--border);
      border-bottom: 1px solid var(--border);
      padding: 6rem 1.5rem;
    }

    .upcoming-inner {
      max-width: 72rem;
      margin: 0 auto;
    }

    .upcoming-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 1rem;
      margin-top: 3rem;
    }

    .uc {
      padding: 0.25rem;
      background: var(--bg);
      border: 1px solid var(--border);
      border-radius: var(--r-xl);
      opacity: 0;
      transform: translateY(2rem);
      transition: border-color 0.4s var(--ease);
    }

    .uc:hover { border-color: var(--border-hi); }

    .uc-inner {
      background: var(--surface-2);
      border-radius: calc(var(--r-xl) - 0.25rem);
      overflow: hidden;
    }

    .uc-img-wrap {
      position: relative;
      height: 220px;
      overflow: hidden;
    }

    .uc-img-wrap img.uc-hero {
      width: 100%;
      height: 100%;
      object-fit: cover;
      object-position: center;
      display: block;
      transition: transform 0.5s var(--ease);
    }

    .uc:hover .uc-img-wrap img.uc-hero { transform: scale(1.04); }

    .uc-img-overlay {
      position: absolute;
      inset: 0;
      background: linear-gradient(to bottom, rgba(0,0,0,0.15) 0%, rgba(0,0,0,0.5) 100%);
    }

    .uc-badge {
      position: absolute;
      top: 1rem;
      left: 1rem;
      font-family: var(--font-d);
      font-size: 0.7rem;
      font-weight: 700;
      letter-spacing: 0.1em;
      text-transform: uppercase;
      color: #fff;
      background: var(--green);
      padding: 0.3em 0.75em;
      border-radius: 2rem;
    }

    .uc-logo-wrap {
      position: absolute;
      bottom: 1rem;
      left: 1rem;
    }

    .uc-logo-wrap img {
      height: 48px;
      width: auto;
      object-fit: contain;
      filter: brightness(0) invert(1);
      drop-shadow: 0 1px 4px rgba(0,0,0,0.4);
    }

    .uc-body {
      padding: 1.5rem;
    }

    .uc-date {
      font-family: var(--font-d);
      font-size: 0.75rem;
      font-weight: 700;
      letter-spacing: 0.09em;
      text-transform: uppercase;
      color: var(--accent);
      margin-bottom: 0.4rem;
    }

    .uc-title {
      font-family: var(--font-d);
      font-size: 1.125rem;
      font-weight: 700;
      letter-spacing: -0.02em;
      line-height: 1.2;
      margin-bottom: 0.5rem;
    }

    .uc-desc { font-size: 0.875rem; color: var(--text-sub); line-height: 1.7; }

    /* ── CONSULTATIONS GRID ───────────────── */
    .consult-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 1rem;
      margin-top: 3rem;
    }

    .consult-card {
      padding: 0.25rem;
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: var(--r-xl);
      opacity: 0;
      transform: translateY(2rem);
      transition: border-color 0.4s var(--ease);
    }

    .consult-card:hover { border-color: var(--border-hi); }

    .consult-inner {
      background: var(--surface-2);
      border-radius: calc(var(--r-xl) - 0.25rem);
      padding: 1.75rem;
      box-shadow: inset 0 1px 1px rgba(255,255,255,0.03);
    }

    .consult-type {
      font-size: 0.625rem;
      font-weight: 700;
      letter-spacing: 0.12em;
      text-transform: uppercase;
      color: var(--text-muted);
      margin-bottom: 0.75rem;
    }

    .consult-title {
      font-family: var(--font-d);
      font-size: 1rem;
      font-weight: 700;
      letter-spacing: -0.02em;
      margin-bottom: 0.5rem;
    }

    .consult-desc { font-size: 0.8125rem; color: var(--text-sub); line-height: 1.65; }

    @media (max-width: 1024px) {
      .props-grid { grid-template-columns: repeat(2, 1fr); }
      .pcard.featured { grid-column: span 2; }
      .consult-grid { grid-template-columns: repeat(2, 1fr); }
    }

    @media (max-width: 768px) {
      .props-grid { grid-template-columns: 1fr; }
      .pcard.featured { grid-column: span 1; }
      .pcard.featured .pcard-inner { grid-template-columns: 1fr; }
      .pcard.featured .pcard-img-wrap { min-height: 200px; }
      .upcoming-grid { grid-template-columns: 1fr; }
      .consult-grid { grid-template-columns: 1fr; }
    }

    .pcard-title-link {
      text-decoration: none;
    }

    .pcard-title-link .pcard-title:hover {
      color: var(--green-dark);
    }

    .pcard-view-btn {
      display: inline-flex;
      align-items: center;
      gap: 0.375rem;
      font-size: 0.875rem;
      font-weight: 700;
      color: var(--green-dark);
      text-decoration: none;
      margin-top: 1.25rem;
      transition: gap 0.3s var(--ease), color 0.3s var(--ease);
    }

    .pcard-view-btn:hover {
      gap: 0.625rem;
      color: var(--navy);
    }

  </style>
</head>
<body>
  <div class="noise" aria-hidden="true"></div>
  <div id="nav-root"></div>

  <!-- PAGE HERO -->
  <div class="page-hero">
    <div class="page-hero-bg" style="background-image:url('https://images.unsplash.com/photo-1571019614242-c5c5dee9f50b?w=1920&q=80&auto=format&fit=crop')"></div>
    <div class="page-hero-overlay"></div>
    <div class="page-hero-inner">
      <div class="page-hero-label">Our Portfolio</div>
      <h1 class="page-hero-h1">World-Class Events<br>Across <span class="hi">Africa</span></h1>
      <p class="page-hero-p">From the continent's strongest marathon brand to beach football and heritage races — NSML owns, manages, and grows Africa's most impactful sporting properties.</p>
    </div>
  </div>

  <!-- MAIN PROPERTIES -->
  <div class="section-wrap">
    <div class="section-tag">Active Properties</div>
    <h2 class="sec-h2">Our <span class="hi">Events</span></h2>
    <div class="props-grid" style="margin-top:3rem">

      <?php foreach ($activeProps as $p): ?>
      <div class="pcard<?= $p['is_featured'] ? ' featured' : '' ?>" data-prop>
        <?php if ($p['badge']): ?><span class="pcard-badge"><?= htmlspecialchars($p['badge']) ?></span><?php endif; ?>
        <div class="pcard-inner">
          <div class="pcard-img-wrap">
            <img class="pcard-img" src="<?= htmlspecialchars($p['hero_image'] ?: 'images/events/placeholder.jpg') ?>" alt="<?= htmlspecialchars($p['title']) ?>" loading="lazy">
            <?php if ($p['logo_image']): ?>
            <div class="pcard-event-logo">
              <img src="<?= htmlspecialchars($p['logo_image']) ?>" alt="<?= htmlspecialchars($p['title']) ?> logo">
            </div>
            <?php endif; ?>
          </div>
          <div class="pcard-body">
            <?php if ($p['tag']): ?><div class="pcard-tag"><?= htmlspecialchars($p['tag']) ?></div><?php endif; ?>
            <a href="<?= htmlspecialchars($p['detail_url'] ?: '#') ?>" class="pcard-title-link"><div class="pcard-title"><?= htmlspecialchars($p['title']) ?></div></a>
            <div class="pcard-desc"><?= htmlspecialchars($p['description'] ?? '') ?></div>
            <?php $stats = prop_stats($p); if ($stats): ?>
            <div class="pcard-stats">
              <?php foreach ($stats as $s): ?>
              <div><div class="pcard-stat-val"><?= htmlspecialchars($s['val']) ?></div><div class="pcard-stat-lbl"><?= htmlspecialchars($s['lbl']) ?></div></div>
              <?php endforeach; ?>
            </div>
            <?php endif; ?>
            <a href="<?= htmlspecialchars($p['detail_url'] ?: '#') ?>" class="pcard-view-btn">View Property ↗</a>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
      <?php if (!$activeProps): ?>
        <p style="grid-column:1/-1;text-align:center;color:var(--text-muted);padding:3rem 0;">No properties yet.</p>
      <?php endif; ?>

    </div>
  </div>

  <!-- UPCOMING PROJECTS -->
  <div class="upcoming-band">
    <div class="upcoming-inner">
      <div class="section-tag">Upcoming</div>
      <h2 class="sec-h2">Coming <span class="hi">Soon</span></h2>
      <div class="upcoming-grid">
        <?php foreach ($upcomingProps as $p): ?>
        <div class="uc" data-reveal>
          <div class="uc-inner">
            <div class="uc-img-wrap">
              <img class="uc-hero" src="<?= htmlspecialchars($p['hero_image'] ?: 'images/events/placeholder.jpg') ?>" alt="<?= htmlspecialchars($p['title']) ?>">
              <div class="uc-img-overlay"></div>
              <?php if ($p['badge']): ?><div class="uc-badge"><?= htmlspecialchars($p['badge']) ?></div><?php endif; ?>
              <?php if ($p['logo_image']): ?>
              <div class="uc-logo-wrap">
                <img src="<?= htmlspecialchars($p['logo_image']) ?>" alt="<?= htmlspecialchars($p['title']) ?>">
              </div>
              <?php endif; ?>
            </div>
            <div class="uc-body">
              <a href="<?= htmlspecialchars($p['detail_url'] ?: '#') ?>" style="text-decoration:none;color:inherit;">
                <div class="uc-title"><?= htmlspecialchars($p['title']) ?></div>
              </a>
              <div class="uc-desc"><?= htmlspecialchars($p['description'] ?? '') ?></div>
              <a href="<?= htmlspecialchars($p['detail_url'] ?: '#') ?>" class="pcard-view-btn">View Property ↗</a>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

  <!-- CONSULTATIONS -->
  <div class="section-wrap">
    <div class="section-tag">Consultations</div>
    <h2 class="sec-h2">Projects We've <span class="hi">Shaped</span></h2>
    <p class="sec-p">Beyond our owned properties, NSML has served as technical consultant and brand management partner on some of Africa's largest sporting events.</p>
    <div class="consult-grid">
      <div class="consult-card" data-reveal>
        <div class="consult-inner">
          <div class="consult-type">Technical Consultant</div>
          <div class="consult-title">National Sports Festival Delta 2022</div>
          <div class="consult-desc">Partnered to help the state win the pitch and execute a top-level sporting festival. 15,000+ athletes across all states of the federation. 8 days in Asaba, Delta State.</div>
        </div>
      </div>
      <div class="consult-card" data-reveal>
        <div class="consult-inner">
          <div class="consult-type">Technical Consultant</div>
          <div class="consult-title">Niger Delta Sports Festival</div>
          <div class="consult-desc">Helped the organising committee execute a top-level sporting festival involving all Niger Delta states. 3,000+ competing athletes over 8 days in Uyo, Akwa Ibom State.</div>
        </div>
      </div>
      <div class="consult-card" data-reveal>
        <div class="consult-inner">
          <div class="consult-type">Sponsorship Consultant</div>
          <div class="consult-title">Asaba 2018 African Senior Athletics Championship</div>
          <div class="consult-desc">5,000+ athletes from 54 African countries. Secured commitments from Zenith Bank, GAC Motors, Rite Foods, Ericsson, Gree Electric, and Lontor.</div>
        </div>
      </div>
      <div class="consult-card" data-reveal>
        <div class="consult-inner">
          <div class="consult-type">Project &amp; Sponsorship Consultant</div>
          <div class="consult-title">F5WC Football Five World Championship</div>
          <div class="consult-desc">Nigeria representative for the world's first 5-A-Side amateur tournament. 1M+ players across 48 countries. Raised over ₦20M from the private sector locally.</div>
        </div>
      </div>
    </div>
  </div>

  <!-- PARTNERS -->
  <?php require __DIR__ . '/cms/partials/partners-marquee.php'; ?>

  <!-- FOOTER -->
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
    const io = new IntersectionObserver((entries) => {
      entries.forEach((entry, i) => {
        if (!entry.isIntersecting) return;
        const el = entry.target;
        setTimeout(() => {
          el.style.transition = 'opacity 0.8s cubic-bezier(0.32,0.72,0,1), transform 0.8s cubic-bezier(0.32,0.72,0,1)';
          el.style.opacity = '1'; el.style.transform = 'none';
        }, i * 80);
        io.unobserve(el);
      });
    }, { threshold: 0.08 });

    document.querySelectorAll('[data-prop], [data-reveal], .consult-card').forEach(el => io.observe(el));
  </script>
</body>
</html>
