(() => {
  'use strict';
  const root = document.documentElement;
  const body = document.body;
  body.classList.add('has-js');
  const toggle = document.querySelector('.menu-toggle');
  const nav = document.querySelector('#site-navigation');
  const scrim = document.querySelector('.menu-scrim');
  const mobile = matchMedia('(max-width: 900px)');
  const reduce = matchMedia('(prefers-reduced-motion: reduce)');
  const controls = [...document.querySelectorAll('.motion-toggle,.scene-motion')];
  const background = [...document.querySelectorAll('main,.site-footer,.brand,.skip-link')];
  const previousInert = new Map();
  let menuOpen = false;
  let savedScroll = 0;
  let manualPause = false;
  try { manualPause = sessionStorage.getItem('pelham-motion') === 'paused'; } catch {}

  // Requested direction: down reveals, up hides. Hysteresis avoids trackpad jitter.
  const header=document.querySelector('.site-header');
  let headerY=Math.max(0,scrollY),headerTravel=0,headerDirection=0,headerFrame=0;
  function showHeader(){header?.classList.remove('is-scroll-hidden');}
  function updateHeader(){
    headerFrame=0;
    const y=Math.max(0,Math.min(scrollY,document.documentElement.scrollHeight-innerHeight));
    const delta=y-headerY;headerY=y;
    if(y<80||menuOpen||reduce.matches||root.classList.contains('motion-paused')||header?.contains(document.activeElement)){
      showHeader();headerTravel=0;headerDirection=0;return;
    }
    if(Math.abs(delta)<1)return;
    const direction=Math.sign(delta);
    if(direction!==headerDirection){headerTravel=0;headerDirection=direction;}
    headerTravel+=Math.abs(delta);
    if(headerTravel>=24){header?.classList.toggle('is-scroll-hidden',direction<0);headerTravel=0;}
  }
  window.addEventListener('scroll',()=>{if(!headerFrame)headerFrame=requestAnimationFrame(updateHeader);},{passive:true});
  document.addEventListener('focusin',e=>{if(header?.contains(e.target))showHeader();});
  document.addEventListener('pelham:menu',()=>{headerY=Math.max(0,scrollY);headerTravel=0;showHeader();});
  document.addEventListener('pelham:motion',updateHeader);
  window.addEventListener('pageshow',()=>{headerY=Math.max(0,scrollY);headerTravel=0;showHeader();});

  function closeMenu(restoreFocus = true) {
    if (!menuOpen) return;
    menuOpen = false;
    nav.classList.remove('is-open');
    nav.removeAttribute('role');
    nav.removeAttribute('aria-modal');
    nav.setAttribute('aria-label', (window.pelhamI18n||{})['Primary'] || 'Primary');
    toggle.setAttribute('aria-expanded', 'false');
    toggle.querySelector('.menu-toggle-label').textContent = (window.pelhamI18n||{})['Menu'] || 'Menu';
    scrim.hidden = true;
    body.classList.remove('menu-is-open');
    body.style.removeProperty('--menu-scroll');
    previousInert.forEach((value, el) => { el.inert = value; });
    previousInert.clear();
    window.scrollTo({ top: savedScroll, behavior: 'instant' });
    if (restoreFocus && mobile.matches) toggle.focus({ preventScroll: true });
    document.dispatchEvent(new CustomEvent('pelham:menu', {detail:{open:false,scroll:savedScroll}}));
  }
  function openMenu() {
    if (!nav || !mobile.matches) return;
    savedScroll = window.scrollY;
    menuOpen = true;
    document.dispatchEvent(new CustomEvent('pelham:menu', {detail:{open:true,scroll:savedScroll}}));
    background.forEach(el => { previousInert.set(el, el.inert); el.inert = true; });
    body.style.setProperty('--menu-scroll', `${-savedScroll}px`);
    body.classList.add('menu-is-open');
    nav.classList.add('is-open');
    nav.setAttribute('role', 'dialog');
    nav.setAttribute('aria-modal', 'true');
    nav.setAttribute('aria-label', (window.pelhamI18n||{})['Site navigation'] || 'Site navigation');
    toggle.setAttribute('aria-expanded', 'true');
    toggle.querySelector('.menu-toggle-label').textContent = (window.pelhamI18n||{})['Close'] || 'Close';
    scrim.hidden = false;
    nav.querySelector('.menu-close').focus({ preventScroll: true });
  }
  toggle?.addEventListener('click', () => menuOpen ? closeMenu() : openMenu());
  nav?.querySelector('.menu-close')?.addEventListener('click', () => closeMenu());
  scrim?.addEventListener('click', () => closeMenu());
  nav?.addEventListener('click', event => { if (event.target.closest('a')) closeMenu(false); });
  document.addEventListener('keydown', event => {
    if (!menuOpen) return;
    if (event.key === 'Escape') { event.preventDefault(); closeMenu(); }
    if (event.key !== 'Tab') return;
    const focusable = [...nav.querySelectorAll('a[href],button:not([disabled])')].filter(el => el.getClientRects().length);
    const first = focusable[0], last = focusable.at(-1);
    if (event.shiftKey && (document.activeElement === first || !nav.contains(document.activeElement))) {
      event.preventDefault(); last?.focus();
    } else if (!event.shiftKey && (document.activeElement === last || !nav.contains(document.activeElement))) {
      event.preventDefault(); first?.focus();
    }
  });
  mobile.addEventListener('change', () => closeMenu(false));

  function setMotion() {
    const paused = manualPause || reduce.matches;
    root.classList.toggle('motion-paused', paused);
    controls.forEach(button => {
      button.disabled = reduce.matches;
      button.setAttribute('aria-pressed', String(paused));
      const i18n = (window.pelhamI18n||{});
      const label = reduce.matches ? i18n['Reduced motion enabled'] || 'Reduced motion enabled' : paused ? i18n['Resume animations'] || 'Resume animations' : i18n['Pause animations'] || 'Pause animations';
      button.setAttribute('aria-label', label);
      button.title = reduce.matches ? i18n['Your device is set to reduced motion.'] || 'Your device is set to reduced motion.' : label;
      if (button.classList.contains('scene-motion')) {
        button.dataset.state = paused ? 'paused' : 'running';
        button.textContent = '';
      } else button.textContent = label;
    });
    document.dispatchEvent(new CustomEvent('pelham:motion', {detail:{paused, reduced:reduce.matches}}));
  }
  controls.forEach(button => button.addEventListener('click', () => {
    manualPause = !manualPause;
    try { sessionStorage.setItem('pelham-motion', manualPause ? 'paused' : 'running'); } catch {}
    setMotion();
  }));
  reduce.addEventListener('change', setMotion);
  // Photos degrade cleanly when a CDN is blocked; never show a broken-image icon.
  document.querySelectorAll('.freight-photo-image').forEach(image => {
    const container = image.closest('.freight-photo,.card-art,.news-art');
    if (!container) return;
    const mark = () => {
      container.classList.toggle('photo-is-loaded', image.naturalWidth > 0);
      container.classList.toggle('photo-is-unavailable', image.naturalWidth === 0);
    };
    image.addEventListener('load', mark);
    image.addEventListener('error', mark);
    if (image.complete) mark();
  });
  setMotion();
  document.querySelector('.form-status')?.focus();
  window.addEventListener('pageshow', () => { if (menuOpen) closeMenu(false); });
})();
