(() => {
 const root=document.documentElement;
 try{
  if(!Element.prototype.animate||!('IntersectionObserver' in window)||matchMedia('(prefers-reduced-motion:reduce)').matches||sessionStorage.getItem('pelham-motion')==='paused'||location.hash||performance.getEntriesByType('navigation')[0]?.type==='back_forward')return;
 }catch(_){return;}
 const boot=window.PelhamEntranceBoot={ready:false,expired:false};
 root.classList.add('pelham-entering');
 boot.timer=setTimeout(()=>{if(!boot.ready){boot.expired=true;root.classList.remove('pelham-entering');}},1600);
})();
