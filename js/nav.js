/* Shared navigation component — injected on every page */
(function () {
  const path = window.location.pathname.replace(/\/$/, '').split('/').pop() || 'index.html';

  const links = [
    { href: 'index.html',      label: 'Home' },
    { href: 'about.html',      label: 'About' },
    { href: 'services.html',   label: 'Services' },
    { href: 'properties.html', label: 'Properties' },
    { href: 'news.html',       label: 'News' },
    { href: 'contact.html',    label: 'Contact' },
  ];

  const navHTML = `
    <nav class="nav" id="mainNav">
      <div class="nav-pill" id="navPill">
        <a href="index.html" class="nav-logo">
          <img src="images/logo.png" alt="Nilayo Sports Management Ltd" class="nav-logo-img">
        </a>
        <div class="nav-links">
          ${links.map(l => `<a href="${l.href}" class="${path === l.href ? 'active' : ''}">${l.label}</a>`).join('')}
        </div>
        <a href="contact.html" class="nav-cta">
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
      <a href="contact.html">Partner With Us</a>
    </div>
  `;

  /* Auto-inject main.js modern features on every page */
  (function injectMain() {
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
