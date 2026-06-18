/* ─────────────────────────────────────────────────
   NSML — Modern Feature Layer
   Loaded on every page via nav.js injection
   ───────────────────────────────────────────────── */
(function () {
  'use strict';

  const ease = 'cubic-bezier(0.32, 0.72, 0, 1)';

  /* ── 1. SCROLL PROGRESS BAR ─────────────────────
     Thin green line at the very top of the viewport
     tracking how far the user has scrolled the page
  ───────────────────────────────────────────────── */
  const progressBar = document.createElement('div');
  progressBar.id    = 'nsml-progress';
  document.body.appendChild(progressBar);

  function updateProgress() {
    const scrolled = window.scrollY;
    const total    = document.documentElement.scrollHeight - window.innerHeight;
    const pct      = total > 0 ? Math.min((scrolled / total) * 100, 100) : 0;
    progressBar.style.width = pct + '%';
  }

  window.addEventListener('scroll', updateProgress, { passive: true });
  updateProgress();


  /* ── 2. PAGE TRANSITION OVERLAY ─────────────────
     Navy crossfade when navigating between pages.
     On load: overlay fades from 1 → 0 (entrance).
     On click: overlay fades 0 → 1, then navigates.
  ───────────────────────────────────────────────── */
  const overlay    = document.createElement('div');
  overlay.id       = 'nsml-transition';
  overlay.setAttribute('aria-hidden', 'true');
  document.body.appendChild(overlay);

  // CSS animation handles the page entrance fade (more reliable than rAF in deferred scripts)
  // JS only handles the EXIT: intercept clicks → fade to 1 → navigate

  document.addEventListener('click', (e) => {
    const link = e.target.closest('a[href]');
    if (!link) return;

    const href = link.getAttribute('href');
    if (
      !href ||
      href.startsWith('#') ||
      href.startsWith('mailto:') ||
      href.startsWith('tel:') ||
      href.startsWith('http://') ||
      href.startsWith('https://') ||
      link.getAttribute('target') === '_blank' ||
      e.metaKey || e.ctrlKey || e.shiftKey
    ) return;

    e.preventDefault();
    overlay.style.opacity = '1';
    overlay.style.pointerEvents = 'all';

    setTimeout(() => {
      window.location.href = href;
    }, 260);
  });


  /* ── 3. SCROLL-TO-TOP BUTTON ────────────────────
     Appears when user scrolls >500px.
     Green on hover, smooth scroll back to top.
  ───────────────────────────────────────────────── */
  const toTop = document.createElement('button');
  toTop.id    = 'nsml-totop';
  toTop.setAttribute('aria-label', 'Back to top');
  toTop.innerHTML = '<svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M8 12V4M4 8l4-4 4 4" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"/></svg>';
  document.body.appendChild(toTop);

  window.addEventListener('scroll', () => {
    toTop.classList.toggle('visible', window.scrollY > 500);
  }, { passive: true });

  toTop.addEventListener('click', () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });


  /* ── 4. MAGNETIC CTA BUTTONS ────────────────────
     Desktop only. Primary CTAs follow the cursor
     slightly — creates a sense of physical mass.
     15% pull factor, springs back on mouse-leave.
  ───────────────────────────────────────────────── */
  const isPointerFine = window.matchMedia('(hover: hover) and (pointer: fine)').matches;

  if (isPointerFine) {
    function initMagnetic(selector) {
      document.querySelectorAll(selector).forEach(btn => {
        // Don't override the nav-cta transform chain
        btn.addEventListener('mousemove', (e) => {
          const rect = btn.getBoundingClientRect();
          const cx   = rect.left + rect.width  / 2;
          const cy   = rect.top  + rect.height / 2;
          const dx   = (e.clientX - cx) * 0.15;
          const dy   = (e.clientY - cy) * 0.15;
          btn.style.transition = `transform 0.2s ${ease}`;
          btn.style.transform  = `translate(${dx}px, ${dy}px) scale(1.02)`;
        });

        btn.addEventListener('mouseleave', () => {
          btn.style.transition = `transform 0.5s ${ease}`;
          btn.style.transform  = '';
        });

        btn.addEventListener('mousedown', () => {
          btn.style.transform = 'scale(0.96)';
        });

        btn.addEventListener('mouseup', () => {
          btn.style.transition = `transform 0.3s ${ease}`;
          btn.style.transform  = '';
        });
      });
    }

    // Apply after nav injection settles
    setTimeout(() => {
      initMagnetic('.btn-fill:not(.nav-cta)');
      initMagnetic('.btn-white');
      initMagnetic('.btn-cta-white');
    }, 100);
  }


  /* ── 5. NAV ACTIVE LINK INDICATOR ───────────────
     A small green dot that slides underneath the
     currently-active nav link on page load.
  ───────────────────────────────────────────────── */
  function initNavIndicator() {
    const navLinks = document.querySelectorAll('.nav-links a');
    if (!navLinks.length) return;

    // Create the indicator pill
    const indicator    = document.createElement('span');
    indicator.id       = 'nsml-nav-indicator';
    const navLinksWrap = document.querySelector('.nav-links');
    if (!navLinksWrap) return;
    navLinksWrap.style.position = 'relative';
    navLinksWrap.appendChild(indicator);

    function positionIndicator(target) {
      const parentRect = navLinksWrap.getBoundingClientRect();
      const targetRect = target.getBoundingClientRect();
      indicator.style.width  = targetRect.width + 'px';
      indicator.style.left   = (targetRect.left - parentRect.left) + 'px';
      indicator.style.opacity = '1';
    }

    // Set initial position on active link
    const activeLink = navLinksWrap.querySelector('a.active');
    if (activeLink) {
      // Skip transition on first paint
      indicator.style.transition = 'none';
      positionIndicator(activeLink);
      requestAnimationFrame(() => {
        indicator.style.transition = '';
      });
    }

    // Slide on hover
    navLinks.forEach(link => {
      link.addEventListener('mouseenter', () => positionIndicator(link));
    });

    navLinksWrap.addEventListener('mouseleave', () => {
      if (activeLink) {
        positionIndicator(activeLink);
      } else {
        indicator.style.opacity = '0';
      }
    });
  }

  // Run after nav.js has injected the nav HTML
  document.addEventListener('DOMContentLoaded', initNavIndicator);


  /* ── 6. IMAGE BLUR-UP PROGRESSIVE LOADING ───────
     Images start at blur(12px) scale(1.04) and
     transition to sharp once they've loaded.
  ───────────────────────────────────────────────── */
  function initBlurUp() {
    const imgs = document.querySelectorAll('img[loading="lazy"]');
    imgs.forEach(img => {
      if (img.complete && img.naturalWidth > 0) return; // already loaded

      img.style.filter    = 'blur(12px)';
      img.style.transform = 'scale(1.04)';
      img.style.transition = `filter 0.65s ${ease}, transform 0.65s ${ease}`;

      img.addEventListener('load', () => {
        img.style.filter    = '';
        img.style.transform = '';
      }, { once: true });
    });
  }

  document.addEventListener('DOMContentLoaded', initBlurUp);


  /* ── 7. KEYBOARD FOCUS RING NORMALISER ──────────
     Remove focus outlines for mouse users;
     restore them for keyboard users.
  ───────────────────────────────────────────────── */
  let usingKeyboard = false;

  document.addEventListener('mousedown', () => { usingKeyboard = false; });
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Tab') usingKeyboard = true;
  });

  const focusStyle = document.createElement('style');
  focusStyle.textContent = `
    :focus:not(:focus-visible) { outline: none; }
    :focus-visible { outline: 2px solid var(--green, #1ab83c); outline-offset: 3px; border-radius: 3px; }
  `;
  document.head.appendChild(focusStyle);


  /* ── 8. LINK HOVER UNDERLINE ANIMATION ──────────
     Footer links and article read-more links get a
     sliding underline reveal on hover via CSS class.
     Injected rather than requiring HTML changes.
  ───────────────────────────────────────────────── */
  const underlineStyle = document.createElement('style');
  underlineStyle.textContent = `
    .f-links a,
    .article-read-more {
      position: relative;
    }
    .f-links a::after {
      content: '';
      position: absolute;
      bottom: -2px;
      left: 0;
      width: 0;
      height: 1px;
      background: #ffffff;
      transition: width 0.35s cubic-bezier(0.32,0.72,0,1);
    }
    .f-links a:hover::after { width: 100%; }
  `;
  document.head.appendChild(underlineStyle);


  /* ── 9. SCROLL REVEALS (ported from the static site) ───
     Every [data-reveal] element starts at opacity:0 (see
     styles.css) and is faded/slid into place the first time
     it enters the viewport. Some page templates (home.php,
     page-about.php, page-contact.php, page-services.php)
     already wire this up inline with template-specific
     stagger timing; this is the shared fallback for every
     other template (front-page.php, single.php,
     single-nsml_property.php, archive-nsml_property.php).
     Re-running it on a page that already revealed its own
     elements is harmless -- it just sets the same opacity/
     transform again.
  ───────────────────────────────────────────────── */
  const revealIo = new IntersectionObserver((entries) => {
    entries.forEach((entry, i) => {
      if (!entry.isIntersecting) return;
      const el = entry.target;
      const delay = i * 90;
      setTimeout(() => {
        el.style.transition = `opacity 0.75s ${ease} ${delay}ms, transform 0.75s ${ease} ${delay}ms`;
        el.style.opacity = '1';
        el.style.transform = 'none';
      }, 0);
      revealIo.unobserve(el);
    });
  }, { threshold: 0.08 });

  document.querySelectorAll('[data-reveal]').forEach(el => revealIo.observe(el));


  /* ── 10. STATS COUNT-UP ──────────────────────────
     .stat[data-count] elements (the "11+ Yrs", "70+
     Partners" band on the homepage) count up from 0 to
     their target the first time they scroll into view.
  ───────────────────────────────────────────────── */
  const statIo = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (!entry.isIntersecting) return;
      const el     = entry.target;
      const target = parseInt(el.dataset.count, 10);
      const numEl  = el.querySelector('.count-num');
      const dur    = 1600;
      const t0     = performance.now();

      el.style.transition = `opacity 0.7s ${ease}, transform 0.7s ${ease}`;
      el.style.opacity    = '1';
      el.style.transform  = 'none';

      if (numEl && !isNaN(target)) {
        (function tick(now) {
          const p     = Math.min((now - t0) / dur, 1);
          const eased = 1 - Math.pow(1 - p, 4);
          const val   = Math.floor(eased * target);
          numEl.textContent = (target >= 1000) ? val.toLocaleString() : val;
          if (p < 1) requestAnimationFrame(tick);
        })(t0);
      }

      statIo.unobserve(el);
    });
  }, { threshold: 0.25 });

  document.querySelectorAll('.stat[data-count]').forEach(el => statIo.observe(el));


  /* ── 11. HOMEPAGE HERO ENTRANCE + PARALLAX ──────
     Only present on front-page.php (#heroBg / #heroEyebrow
     etc. don't exist elsewhere, so this no-ops on every
     other page). Ported from index.html's inline GSAP
     timeline -- without it, the hero text never leaves its
     opacity:0 starting state and the video background
     never parallaxes on scroll.
  ───────────────────────────────────────────────── */
  if (typeof gsap !== 'undefined' && document.getElementById('heroBg')) {
    gsap.registerPlugin(ScrollTrigger);

    gsap.timeline({ defaults: { ease: 'power3.out' } })
      .to('#heroEyebrow', { opacity: 1, y: 0, duration: 0.7,  delay: 0.2 })
      .to('#hl1',         { opacity: 1, y: 0, duration: 0.85 }, '-=0.3')
      .to('#hl2',         { opacity: 1, y: 0, duration: 0.85 }, '-=0.65')
      .to('#heroP',       { opacity: 1, y: 0, duration: 0.75 }, '-=0.45')
      .to('#heroBtns',    { opacity: 1, y: 0, duration: 0.75 }, '-=0.45')
      .to('#heroTrust',   { opacity: 1,       duration: 0.6  }, '-=0.3');

    gsap.to('#heroBg', {
      yPercent: 15,
      ease: 'none',
      scrollTrigger: { trigger: '.hero', start: 'top top', end: 'bottom top', scrub: true }
    });
  }


  /* ── 12. PROPERTY PAGE HERO PARALLAX ────────────
     Only present on single-nsml_property.php (#propHeroBg
     doesn't exist elsewhere). Ported from lagos-marathon.html
     etc. -- a lightweight scroll-linked transform, not GSAP.
  ───────────────────────────────────────────────── */
  const propHeroBg = document.getElementById('propHeroBg');
  if (propHeroBg) {
    window.addEventListener('scroll', () => {
      propHeroBg.style.transform = `translateY(${window.pageYOffset * 0.25}px)`;
    }, { passive: true });
  }

})();
