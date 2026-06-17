/* Shared navigation component — injected on every page.
   When window.NSML_NAV is present (set by the WordPress theme's header.php)
   real permalinks and the current-page slug from PHP are used; otherwise
   this falls back to the original static-site .html links unchanged. */
(function () {
  const cfg     = window.NSML_NAV || null;
  const home    = cfg ? cfg.home : '';
  const logoUrl = cfg ? cfg.logo : 'images/logo.png';
  const current = cfg ? cfg.current : (window.location.pathname.replace(/\/$/, '').split('/').pop() || 'index.html');

  const links = cfg ? cfg.links : [
    { href: 'index.html',      label: 'Home',       key: 'index.html' },
    { href: 'about.html',      label: 'About',      key: 'about.html' },
    { href: 'services.html',   label: 'Services',   key: 'services.html' },
    { href: 'properties.html', label: 'Properties', key: 'properties.html' },
    { href: 'news.html',       label: 'News',       key: 'news.html' },
    { href: 'contact.html',    label: 'Contact',     key: 'contact.html' },
  ];

  const contactHref = cfg ? cfg.contact : 'contact.html';

  const navHTML = `
    <nav class="nav" id="mainNav">
      <div class="nav-pill" id="navPill">
        <a href="${home || 'index.html'}" class="nav-logo">
          <img src="${logoUrl}" alt="Nilayo Sports Management Ltd" class="nav-logo-img">
        </a>
        <div class="nav-links">
          ${links.map(l => `<a href="${l.href}" class="${current === l.key ? 'active' : ''}">${l.label}</a>`).join('')}
        </div>
        <a href="${contactHref}" class="nav-cta">
          <span>Partner With Us</span>
          <span class="nav-cta-arrow">↗</span>
        </a>
        <button class="nav-burger" id="burger" aria-label="Toggle navigation">
          <span></span><span></span>
        </button>
      </div>
    </nav>

    <div class="mob-overlay" id="mobOverlay">
      ${links.map(l => `<a href="${l.href}">${l.label}</a>`).join('')}
      <a href="${contactHref}">Partner With Us</a>
    </div>
  `;

  /* Auto-inject main.js modern features on every page, unless the host
     environment (e.g. WordPress via wp_enqueue_script) already loaded it. */
  (function injectMain() {
    if (window.__nsmlMainJsEnqueued) return;
    const s  = document.createElement('script');
    s.src    = 'js/main.js';
    s.defer  = true;
    document.head.appendChild(s);
  })();

  document.addEventListener('DOMContentLoaded', () => {
    const root = document.getElementById('nav-root');
    if (root) root.innerHTML = navHTML;
    const navPill = document.getElementById('navPill');
    const burger  = document.getElementById('burger');
    const overlay = document.getElementById('mobOverlay');

    window.addEventListener('scroll', () => {
      if (navPill) navPill.classList.toggle('scrolled', window.scrollY > 60);
    }, { passive: true });

    document.querySelectorAll('.f-copy').forEach(el => {
      el.textContent = el.textContent.replace(/\d{4}/, new Date().getFullYear());
    });

    if (burger && overlay) {
      burger.addEventListener('click', () => {
        const open = overlay.classList.toggle('open');
        burger.classList.toggle('open', open);
        document.body.style.overflow = open ? 'hidden' : '';
      });

      overlay.querySelectorAll('a').forEach(a => {
        a.addEventListener('click', () => {
          overlay.classList.remove('open');
          burger.classList.remove('open');
          document.body.style.overflow = '';
        });
      });
    }
  });
})();
