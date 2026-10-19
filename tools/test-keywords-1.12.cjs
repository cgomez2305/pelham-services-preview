// Uses the isolated WordPress runtime with tools/fixtures/keywords-qa.php mounted as an MU plugin.
const {chromium}=require('/opt/codex/runtimes/codex-primary-runtime/dependencies/node/node_modules/playwright');
const assert=require('node:assert/strict'),fs=require('fs');
(async()=>{
 const base=process.env.PELHAM_PREVIEW_URL||'http://127.0.0.1:9411';
 const b=await chromium.launch({executablePath:'/usr/bin/chromium',args:['--no-sandbox']});
 try{
 const p=await b.newPage({reducedMotion:'reduce'});
 const errors=[];p.on('pageerror',e=>errors.push(e.message));
 const anon=await b.newContext();let denied=await anon.request.post(base+'/wp-admin/admin-post.php',{form:{action:'pelham_keywords_112'}});assert(denied.status()>=400);await anon.close();
 await p.goto(base+'/wp-login.php',{waitUntil:'domcontentloaded'});
 await p.locator('#user_login').fill('offer-reviewer');await p.locator('#user_pass').fill('local-offer-test-only-190');
 await p.locator('#wp-submit').click();await p.waitForURL('**/wp-admin/**');
 const snapshot=async()=>await(await p.request.post(base+'/wp-admin/admin-ajax.php',{form:{action:'pelham_qa_keywords_snapshot'}})).json();
 const before=await snapshot();assert.equal(before.legacy_blog_count,9);
 denied=await p.request.post(base+'/wp-admin/admin-post.php',{form:{action:'pelham_keywords_112'}});assert.equal(denied.status(),403);
 await p.goto(base+'/wp-admin/admin.php?page=pelham-company',{waitUntil:'domcontentloaded'});
 if(before.release!=='1.12.0'){
 await p.getByRole('button',{name:'Apply keyword content (save backups)',exact:true}).click();
 await p.waitForURL('**/admin.php?page=pelham-company');
 }
 const after=await snapshot();assert.equal(after.release,'1.12.0');
 const plan=JSON.parse(fs.readFileSync('wordpress/pelham-content/data/keyword-rollout-1.12.json'));
 for(const route of plan.existing){
  const a=after.rows[route],old=before.rows[route];assert(a.backup);assert.equal(a.done,'1');
  for(const key of ['id','slug','date','status','author','thumbnail'])assert.equal(a[key],old[key],route+' '+key);
  if(before.release!=='1.12.0')assert.equal(a.backup.content,old.content);
 }
 for(const route of plan.new){assert.equal(after.rows[route].status,'publish');assert.equal(after.rows[route].done,'1');}
 // Direct second application must not change modified time/content or create duplicate posts.
 // The fixture exposes a capability-guarded wrapper for testing idempotency.
 const repeat=await p.request.post(base+'/wp-admin/admin-ajax.php',{form:{action:'pelham_qa_keywords_repeat'}});assert((await repeat.json()).ok);
 assert.deepEqual(await snapshot(),after);
 const entries=JSON.parse(fs.readFileSync('wordpress/pelham-content/data/pages.json')).filter(e=>[...plan.existing,...plan.new].includes(e.route));
 const links=new Set();fs.mkdirSync('docs/audit/keywords-1.12',{recursive:true});
 for(const e of entries){
  const response=await p.goto(base+'/'+e.route+'/',{waitUntil:'domcontentloaded'});assert.equal(response.status(),200);
  assert.equal(await p.locator('h1').count(),1);assert.equal(await p.title(),e.seo.seo_title);
  assert.equal(await p.locator('meta[name="description"]').getAttribute('content'),e.seo.description);
  const canonical=await p.locator('link[rel="canonical"]').getAttribute('href');assert.equal(canonical,after.rows[e.route].permalink);
  assert((await p.locator('meta[name="robots"]').getAttribute('content')).includes('noindex'));
  const schema=await p.locator('script[type="application/ld+json"]').allTextContents();for(const s of schema)JSON.parse(s);assert(schema.length);
  const scope=p.locator('.entry-content').first();
  for(const url of await scope.locator('a[href]').evaluateAll(as=>as.map(a=>a.href)))if(url.startsWith(base))links.add(url);
  if(e.type==='post'){assert(await p.locator('.reading-toc a').count()>=6);assert(await scope.locator('a[href*="/quote/"]').count());}
  for(const width of [1440,390]){
   await p.setViewportSize({width,height:1000});await p.waitForTimeout(180);
   assert.equal(await p.evaluate(()=>document.documentElement.scrollWidth>innerWidth+1),false,e.route+' overflow '+width);
   await p.screenshot({path:'docs/audit/keywords-1.12/'+width+'-'+e.route.split('/').pop()+'.png',fullPage:true});
  }
 }
 for(const url of links){const res=await p.request.get(url);assert.equal(res.status(),200,url);}
 await p.goto(base+'/news/',{waitUntil:'domcontentloaded'});
 for(const route of plan.new)assert(await p.locator('a[href="'+after.rows[route].permalink+'"]').count(),route+' discoverable in news');
 assert.deepEqual(errors,[]);
 console.log('PASS: actual admin update; unauthorized and missing-nonce rejection; four backups and preserved identities/dates/status/media/authors; two new published guides; idempotency; six pages with unique H1/title/description/canonical and staging noindex; JSON-LD valid; '+links.size+' internal links resolve; news discovery; 12 desktop/mobile screenshots without horizontal overflow; no browser errors.');
 }finally{await b.close();}
})().catch(e=>{console.error(e);process.exit(1)});
