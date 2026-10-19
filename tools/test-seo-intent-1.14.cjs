const {chromium}=require('/opt/codex/runtimes/codex-primary-runtime/dependencies/node/node_modules/playwright');
const fs=require('fs'),assert=require('node:assert/strict');
(async()=>{const b=await chromium.launch({executablePath:'/usr/bin/chromium',args:['--no-sandbox']});try{
 const base='http://127.0.0.1:9411',p=await b.newPage({reducedMotion:'reduce'}),errors=[];p.on('pageerror',e=>errors.push(e.message));
 await p.goto(base+'/wp-login.php',{waitUntil:'domcontentloaded'});await p.locator('#user_login').fill('offer-reviewer');await p.locator('#user_pass').fill('local-offer-test-only-190');await p.locator('#wp-submit').click();await p.waitForURL('**/wp-admin/**');
 const snap=async()=>await(await p.request.post(base+'/wp-admin/admin-ajax.php',{form:{action:'pelham_qa_audit_snapshot'}})).json();const before=await snap();
 assert.equal((await p.request.post(base+'/wp-admin/admin-post.php',{form:{action:'pelham_seo_audit_114'}})).status(),403);
 await p.goto(base+'/wp-admin/admin.php?page=pelham-company',{waitUntil:'domcontentloaded'});
 if(before.release!=='1.14.0'){await p.getByRole('button',{name:'Apply SEO intent and headings (save backups)',exact:true}).click();await p.waitForURL('**/admin.php?page=pelham-company');}
 const after=await snap();assert.equal(after.release,'1.14.0');
 for(const [route,a] of Object.entries(after.rows)){assert(a.backup,route);for(const k of ['id','slug','date','status','author','thumbnail'])assert.equal(a[k],before.rows[route][k],route+k);}
 assert((await(await p.request.post(base+'/wp-admin/admin-ajax.php',{form:{action:'pelham_qa_audit_repeat'}})).json()).ok);assert.deepEqual(await snap(),after);
 const es=JSON.parse(fs.readFileSync('wordpress/pelham-content/data/pages.json'));const km=JSON.parse(fs.readFileSync('wordpress/pelham-content/data/keyword-map.json'));es.unshift({route:'home',title:km.home.heading,seo:km.home});
 fs.mkdirSync('docs/audit/seo-intent-1.14',{recursive:true});const rows=[],titles=new Set(),descriptions=new Set();
 for(const e of es){
  const r=await p.goto(base+(e.route==='home'?'/':'/'+e.route+'/'),{waitUntil:'domcontentloaded'});assert.equal(r.status(),200,e.route);
  assert.equal(await p.locator('h1').count(),1,e.route);const h1=(await p.locator('h1').innerText()).replace(/\s+/g,' ').trim();assert.equal(h1,e.title,e.route+' H1');
  const title=await p.title(),desc=await p.locator('meta[name="description"]').getAttribute('content');assert(title&&desc);assert(!titles.has(title),e.route+' duplicate title');assert(!descriptions.has(desc),e.route+' duplicate meta');titles.add(title);descriptions.add(desc);
  assert.equal(await p.locator('link[rel="canonical"]').count(),1);const canonical=await p.locator('link[rel="canonical"]').getAttribute('href');assert.equal(canonical,p.url().split('?')[0]);
  assert((await p.locator('meta[name="robots"]').getAttribute('content')).includes('noindex'));
  for(const s of await p.locator('script[type="application/ld+json"]').allTextContents())JSON.parse(s);
  rows.push({route:e.route,h1,title,description:desc,primary_keyword:e.seo.keyword,canonical});
 }
 const measures=[];
 for(const width of [1440,1024,768,390,320]){
  await p.setViewportSize({width,height:1000});await p.goto(base+'/',{waitUntil:'domcontentloaded'});await p.waitForTimeout(100);
  const rect=sel=>p.locator(sel).first().evaluate(e=>{const r=e.getBoundingClientRect();return {x:r.x,right:r.right,width:r.width};});
  const header=await rect('.header-inner'),levels=await rect('.pelham-ltl-levels'),hero=await rect('.industrial-opening');
  assert(Math.abs(header.x-levels.x)<1,'left alignment '+width);assert(Math.abs(header.right-levels.right)<1,'right alignment '+width);assert(Math.abs(header.x-hero.x)<1);
  assert.equal(await p.evaluate(()=>document.documentElement.scrollWidth>innerWidth+1),false);measures.push({width,header,levels});
  await p.locator('.pelham-ltl-levels').scrollIntoViewIfNeeded();await p.screenshot({path:'docs/audit/seo-intent-1.14/home-levels-'+width+'.png'});
  if([1440,390,320].includes(width))for(const slug of ['freight-consolidation','dedicated-transport']){
   await p.goto(base+'/services/'+slug+'/',{waitUntil:'domcontentloaded'});assert.equal(await p.evaluate(()=>document.documentElement.scrollWidth>innerWidth+1),false);
   const sh=await rect('.header-inner'),sp=await rect('.service-opening-v3');assert(Math.abs(sh.x-sp.x)<1 && Math.abs(sh.right-sp.right)<1,'service/header alignment');
   assert(await p.locator('h1').evaluate(e=>e.scrollWidth<=e.clientWidth+1));await p.screenshot({path:'docs/audit/seo-intent-1.14/'+slug+'-'+width+'.png'});
  }
 }
 assert.deepEqual(errors,[]);fs.writeFileSync('docs/audit/seo-intent-1.14/pages.json',JSON.stringify(rows,null,2));fs.writeFileSync('docs/audit/seo-intent-1.14/alignment.json',JSON.stringify(measures,null,2));console.log('PASS: 13-page backed-up update, nonce/idempotency/preservation; 28 routes with expected unique H1, unique titles/descriptions, canonical and schema; staging noindex; header/levels aligned within 1px at 5 widths; service headings fit desktop/mobile.');
}finally{await b.close();}})().catch(e=>{console.error(e);process.exit(1)});
