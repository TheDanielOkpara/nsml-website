/* Shared navigation component — injected on every page */
(function () {
  let path = window.location.pathname.replace(/\/+$/, '');
  if (path === '') path = '/';

  const links = [
    { href: '/',           label: 'Home' },
    { href: '/about',      label: 'About' },
    { href: '/services',   label: 'Services' },
    { href: '/properties', label: 'Properties' },
    { href: '/news',       label: 'News' },
    { href: '/contact',    label: 'Contact' },
  ];

  const navHTML = `
    <nav class="nav" id="mainNav">
      <div class="nav-pill" id="navPill">
        <a href="/" class="nav-logo">
          <img src="images/logo.png" alt="Nilayo Sports Management Ltd" class="nav-logo-img">
        </a>
        <div class="nav-links">
          ${links.map(l => `<a href="${l.href}" class="${path === l.href ? 'active' : ''}">${l.label}</a>`).join('')}
        </div>
        <a href="/contact" class="nav-cta">
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
      <a href="/contact">Partner With Us</a>
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
