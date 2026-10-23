(() => {
 'use strict';
 const animateMini=window.PelhamMotionEngine?.animateMini;
 if(!animateMini||!('IntersectionObserver' in window)||!Element.prototype.animate)return;
 const root=document.documentElement,reduce=matchMedia('(prefers-reduced-motion: reduce)');
 const played=new WeakSet(),active=new Map(),pending=new Set();let observer,menuOpen=false,firstConnect=true;
 const blocked=()=>reduce.matches||root.classList.contains('motion-paused');
 const selector='.industrial-title,.industrial-lead,.industrial-panorama,.section-heading,.network-copy,.network-art,.process-card,.industrial-invitation,.service-card,.news-card,.service-cover-copy,.service-cover-photo,.service-introduction,.service-planning>h2,.service-step,.service-preparation,.service-questions>h2,.service-faq,.service-network,.contact-intro,.contact-terminals>h2,.contact-directory article,.terminal-card,.value-card,.team-card,.page-masthead,.registration-copy,.footer-top,.footer-grid>div';
 function clearPending(el){el.removeAttribute('data-pelham-pending');pending.delete(el);}
 function finish(){pending.forEach(clearPending);active.forEach(animations=>animations.forEach(a=>a.cancel()));active.clear();counters.forEach(settleCounter);}
 // Transit-day figures count up once on entry (under a second, then the real
 // value stays; reduced motion, pause and focus always show the final figure).
 const counters=[];
 document.querySelectorAll('.pelham-level-days').forEach(holder=>{
  const node=holder.firstChild,value=parseInt(node?.nodeType===3?node.textContent:'',10);if(!value)return;
  const shown=document.createElement('span');
  shown.className='count-up';shown.style.minWidth=String(value).length+'ch';shown.textContent=String(value);
  holder.replaceChild(shown,node);counters.push({el:shown,value,frame:0});
 });
 function settleCounter(counter){cancelAnimationFrame(counter.frame);counter.frame=0;counter.el.textContent=String(counter.value);}
 function count(counter,delay){
  const start=performance.now()+delay,duration=900;
  const step=now=>{const t=Math.min(1,Math.max(0,(now-start)/duration));counter.el.textContent=String(Math.round(counter.value*(1-Math.pow(1-t,3))));counter.frame=t<1?requestAnimationFrame(step):0;};
  counter.el.textContent='0';counter.frame=requestAnimationFrame(step);
 }
 const counterObserver=counters.length?new IntersectionObserver(records=>{
  records.filter(r=>r.isIntersecting).forEach((r,i)=>{const counter=counters.find(c=>c.el===r.target);counterObserver.unobserve(r.target);if(!blocked())count(counter,i*120);});
 },{rootMargin:'0px 0px -12% 0px'}):null;
 counters.forEach(counter=>{
  // Below the fold, start from zero so the figure never visibly jumps back.
  if(!blocked()&&counter.el.getBoundingClientRect().top>=innerHeight)counter.el.textContent='0';
  counterObserver.observe(counter.el);
 });
 function targetsFor(el){
  if(el.matches('.network-art'))return [...el.querySelectorAll('.map-route')];
  if(el.matches('.service-faq'))return [...el.querySelectorAll(':scope>details')];
  return [el];
 }
 function enter(el,batchDelay=0){
  if(played.has(el))return;
  if(blocked()||menuOpen||document.hidden)return;
  played.add(el);observer.unobserve(el);clearPending(el);
  if(el.getBoundingClientRect().bottom<=0||el.contains(document.activeElement))return;
  const targets=targetsFor(el);if(!targets.length)return;
  const animations=targets.map((target,index)=>{
   const original=['opacity','transform','clip-path','stroke-dashoffset'].map(name=>({name,value:target.style.getPropertyValue(name),priority:target.style.getPropertyPriority(name)}));
   const distance=matchMedia('(max-width:760px)').matches ? 36 : 56;
   const route=el.matches('.network-art')&&target.matches('.map-route');
   let frames,duration;
   if(route){const length=target.getTotalLength();target.setAttribute('stroke-dasharray',String(length));frames={strokeDashoffset:[length,0]};duration=1.2;}
   else {frames={opacity:[0,1],transform:[`translate3d(0,${distance}px,0)`,'translate3d(0,0,0)']};duration=1.05;}
   const openingDelay=el.matches('.industrial-lead') ? .16 : el.matches('.industrial-panorama,.service-cover-photo') ? .32 : 0;
   const control=animateMini(target,frames,{duration,delay:openingDelay+batchDelay+Math.min(index*.14,.42),ease:[.25,.65,.3,1]});
   return {finished:control.finished,cancel(){control.cancel();original.forEach(p=>{if(p.value)target.style.setProperty(p.name,p.value,p.priority);else target.style.removeProperty(p.name);});}};
  });
  active.set(el,animations);el.dataset.motionComponent='';
  Promise.all(animations.map(a=>a.finished.catch(()=>{}))).then(()=>{animations.forEach(a=>a.cancel());if(active.get(el)===animations)active.delete(el);});
 }
 function connect(){
  observer?.disconnect();pending.forEach(clearPending);if(blocked()||menuOpen||document.hidden){finish();root.classList.remove('pelham-entering');return;}
  observer=new IntersectionObserver(records=>{records.filter(r=>r.isIntersecting).forEach((r,i)=>enter(r.target,Math.min(i*.16,.48)));},{rootMargin:'0px 0px -48px 0px',threshold:0});
  document.querySelectorAll(selector).forEach(el=>{
   if(played.has(el)||el.closest('.pelham-form,.registration-form,.reading-layout,.legal-page,[hidden]')||el.parentElement?.closest(selector))return;
   if(getComputedStyle(el).display==='contents')return;
   const rect=el.getBoundingClientRect();
   if(firstConnect&&rect.top<innerHeight-12&&rect.bottom>0){
    // Avoid a visible-to-hidden flash when scripts arrive late or scroll is restored.
    const boot=window.PelhamEntranceBoot;
    if(scrollY>0||boot?.expired)played.add(el);else enter(el);
   }else {if(rect.top>=innerHeight){el.setAttribute('data-pelham-pending','');pending.add(el);}observer.observe(el);}
  });
  firstConnect=false;
  root.classList.remove('pelham-entering');
 }
 document.addEventListener('pelham:motion',connect);
 document.addEventListener('pelham:menu',e=>{menuOpen=!!e.detail.open;connect();});
 document.addEventListener('visibilitychange',connect);
 document.addEventListener('focusin',finish);
 document.addEventListener('click',e=>{if(e.target.closest('a[href^="#"]'))finish();});
 reduce.addEventListener('change',connect);
 window.addEventListener('pageshow',e=>{if(e.persisted){finish();connect();}});
 // Section navigation uses the same browser observer model, no scroll loop.
 const links=[...document.querySelectorAll('.service-section-nav a')],visible=new Set();
 if(links.length){
  const setCurrent=index=>links.forEach((a,i)=>{if(i===index)a.setAttribute('aria-current','location');else a.removeAttribute('aria-current');});
  const sections=links.map(a=>document.getElementById(a.hash.slice(1)));
  const spy=new IntersectionObserver(records=>{records.forEach(r=>{const i=sections.indexOf(r.target);if(r.isIntersecting)visible.add(i);else visible.delete(i);});if(visible.size)setCurrent(Math.max(...visible));},{rootMargin:'-80px 0px -55% 0px',threshold:0});
  sections.filter(Boolean).forEach(el=>spy.observe(el));
  const cover=document.querySelector('.service-opening-v3');if(cover)new IntersectionObserver(rs=>{if(rs.some(r=>r.isIntersecting))setCurrent(0);}).observe(cover);
  links.forEach((a,i)=>a.addEventListener('click',()=>setCurrent(i)));setCurrent(0);
 }
 window.PelhamMotion={finish,get activeCount(){return active.size;},get paused(){return blocked();}};
 const boot=window.PelhamEntranceBoot;if(boot){boot.ready=true;clearTimeout(boot.timer);}
 connect();
})();
