const {chromium}=require('/opt/codex/runtimes/codex-primary-runtime/dependencies/node/node_modules/playwright');
const assert=require('node:assert/strict'),fs=require('fs');
(async()=>{
 const base=process.env.PELHAM_PREVIEW_URL||'http://127.0.0.1:9411';
 const browser=await chromium.launch({executablePath:'/usr/bin/chromium',args:['--no-sandbox']});
 try{
 const p=await browser.newPage({reducedMotion:'reduce'}),errors=[];p.on('pageerror',e=>errors.push(e.message));
 const anon=await browser.newContext();assert((await anon.request.post(base+'/wp-admin/admin-post.php',{form:{action:'pelham_keywords_113'}})).status()>=400);await anon.close();
 await p.goto(base+'/wp-login.php',{waitUntil:'domcontentloaded'});await p.locator('#user_login').fill('offer-reviewer');await p.locator('#user_pass').fill('local-offer-test-only-190');await p.locator('#wp-submit').click();await p.waitForURL('**/wp-admin/**');
 const snap=async()=>await(await p.request.post(base+'/wp-admin/admin-ajax.php',{form:{action:'pelham_qa_coverage_snapshot'}})).json();
 const before=await snap();assert.equal(before.legacy_blog_count,9);
 assert.equal((await p.request.post(base+'/wp-admin/admin-post.php',{form:{action:'pelham_keywords_113'}})).status(),403);
 await p.goto(base+'/wp-admin/admin.php?page=pelham-company',{waitUntil:'domcontentloaded'});
 const applied=before.release!=='1.13.0';
 if(applied){await p.getByRole('button',{name:'Complete keyword coverage (save backups)',exact:true}).click();await p.waitForURL('**/admin.php?page=pelham-company');}
 const after=await snap();assert.equal(after.release,'1.13.0');assert.equal(Object.keys(after.rows).length,12);
 for(const [route,a] of Object.entries(after.rows)){
  assert.equal(a.done,'1');const old=before.rows[route];
  if(old){assert(a.backup);for(const k of ['id','slug','date','status','author','thumbnail'])assert.equal(a[k],old[k],route+' '+k);if(applied)assert.equal(a.backup.content,old.content);}
 }
 assert((await(await p.request.post(base+'/wp-admin/admin-ajax.php',{form:{action:'pelham_qa_coverage_repeat'}})).json()).ok);assert.deepEqual(await snap(),after);
 const plan=JSON.parse(fs.readFileSync('wordpress/pelham-content/data/keyword-rollout-1.13.json'));
 const es=JSON.parse(fs.readFileSync('wordpress/pelham-content/data/pages.json'));
 const km=JSON.parse(fs.readFileSync('wordpress/pelham-content/data/keyword-map.json'));
 es.unshift({route:'home',type:'page',seo:km.home});
 const coverage=JSON.parse(fs.readFileSync('docs/keyword-research-2026-10/cobertura-96-keywords.json'));assert.equal(coverage.length,96);
 const links=new Set(),titles=new Set(),descriptions=new Set();let checked=0;
 fs.mkdirSync('docs/audit/coverage-1.13',{recursive:true});
 for(const route of plan.existing){
  const e=es.find(e=>e.route===route),url=after.rows[route].permalink;
  assert.equal((await p.goto(url,{waitUntil:'domcontentloaded'})).status(),200);
  assert.equal(await p.locator('h1').count(),1,route);assert.equal(await p.title(),e.seo.seo_title);
  assert.equal(await p.locator('meta[name="description"]').getAttribute('content'),e.seo.description);
  titles.add(e.seo.seo_title);descriptions.add(e.seo.description);
  assert.equal(await p.locator('link[rel="canonical"]').getAttribute('href'),url);
  assert((await p.locator('meta[name="robots"]').getAttribute('content')).includes('noindex'));
  const schemas=await p.locator('script[type="application/ld+json"]').allTextContents();assert(schemas.length);for(const s of schemas){const data=JSON.parse(s);assert(!s.includes('AggregateRating'));}
  const records=coverage.filter(r=>r.url_objetivo===(route==='home'?'/':'/'+route+'/')&&!r.estado.startsWith('Pendiente'));
  for(const r of records){if(r.seccion){const section=p.locator('[id="'+r.seccion+'"]');assert.equal(await section.count(),1,r.keyword);const text=(await section.innerText()).replace(/\s+/g,' ').trim();assert(text.includes(r.evidencia_textual.slice(0,100)),r.keyword);}checked++;}
  for(const u of await p.locator('main a[href]').evaluateAll(as=>as.map(a=>a.href)))if(u.startsWith(base)&&!u.includes('/wp-admin/')&&!u.includes('#'))links.add(u);
  for(const width of [1440,390]){await p.setViewportSize({width,height:1000});await p.waitForTimeout(120);assert.equal(await p.evaluate(()=>document.documentElement.scrollWidth>innerWidth+1),false,route+' '+width);await p.screenshot({path:'docs/audit/coverage-1.13/'+width+'-'+route.replaceAll('/','-')+'.png',fullPage:true});}
 }
 assert.equal(checked,95);assert.equal(titles.size,12);assert.equal(descriptions.size,12);
 for(const url of links)assert.equal((await p.request.get(url)).status(),200,url);
 assert.deepEqual(errors,[]);
 const result=`PASS 96 rows accounted for: 95 verified against 12 rendered destinations, 1 reviews query explicitly pending. ${applied?'Admin button applied this run':'Previously applied update rechecked'}; authorization and nonce checks; backups; IDs/slugs/dates/status/authors/media preserved; idempotency; 12 distinct titles/descriptions, H1/canonical/schema/noindex checks; ${links.size} internal URLs; 24 desktop/mobile screenshots without overflow; no JS errors.\n`;
 fs.writeFileSync('docs/audit/coverage-1.13/results.txt',result);console.log(result);
 }finally{await browser.close();}
})().catch(e=>{console.error(e);process.exit(1)});
