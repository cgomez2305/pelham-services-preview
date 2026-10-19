const {chromium}=require('/opt/codex/runtimes/codex-primary-runtime/dependencies/node/node_modules/playwright');const assert=require('node:assert/strict'),fs=require('fs');
(async()=>{const b=await chromium.launch({executablePath:'/usr/bin/chromium',args:['--no-sandbox']});try{
 const base='http://127.0.0.1:9411',p=await b.newPage({reducedMotion:'reduce'}),errors=[];p.on('pageerror',e=>errors.push(e.message));
 await p.goto(base+'/wp-login.php',{waitUntil:'domcontentloaded'});await p.locator('#user_login').fill('offer-reviewer');await p.locator('#user_pass').fill('local-offer-test-only-190');await p.locator('#wp-submit').click();await p.waitForURL('**/wp-admin/**');
 const snap=async()=>await(await p.request.post(base+'/wp-admin/admin-ajax.php',{form:{action:'pelham_qa_reader_snapshot'}})).json();const before=await snap();
 assert.equal((await p.request.post(base+'/wp-admin/admin-post.php',{form:{action:'pelham_article_intent_115'}})).status(),403);
 await p.goto(base+'/wp-admin/admin.php?page=pelham-company',{waitUntil:'domcontentloaded'});
 if(before.release!=='1.15.0'){await p.getByRole('button',{name:'Apply reader-focused articles (save backups)',exact:true}).click();await p.waitForURL('**/admin.php?page=pelham-company');}
 const after=await snap();assert.equal(after.release,'1.15.0');assert.equal(Object.keys(after.rows).length,11);
 for(const [route,a] of Object.entries(after.rows)){assert(a.backup);for(const k of ['id','slug','date','status','author','thumbnail'])assert.equal(a[k],before.rows[route][k],route+k);}
 assert((await(await p.request.post(base+'/wp-admin/admin-ajax.php',{form:{action:'pelham_qa_reader_repeat'}})).json()).ok);assert.deepEqual(await snap(),after);
 const es=JSON.parse(fs.readFileSync('wordpress/pelham-content/data/pages.json')).filter(e=>e.type==='post');const links=new Set();fs.mkdirSync('docs/audit/article-intent-1.15',{recursive:true});
 for(const e of es){
  assert.equal((await p.goto(after.rows[e.route].permalink,{waitUntil:'domcontentloaded'})).status(),200);assert.equal(await p.locator('h1').count(),1);assert.equal(await p.locator('h1').innerText(),e.title);assert.equal(await p.title(),e.seo.seo_title);assert.equal(await p.locator('meta[name="description"]').getAttribute('content'),e.seo.description);
  assert.equal(await p.locator('#quick-answer').count(),1);assert((await p.locator('#quick-answer p').innerText()).length>100);
  assert.equal(await p.locator('.entry-content').evaluate(e=>e.firstElementChild.id),'quick-answer');
  const ids=await p.locator('.entry-content [id]').evaluateAll(es=>es.map(e=>e.id));assert.equal(ids.length,new Set(ids).size,e.route);
  const headings=await p.locator('.entry-content h2').allTextContents();assert.equal(headings.length,new Set(headings).size);
  for(const href of await p.locator('.reading-toc a').evaluateAll(as=>as.map(a=>a.getAttribute('href'))))assert.equal(await p.locator('[id="'+href.slice(1)+'"]').count(),1);
  assert(await p.locator('.entry-content ul,.entry-content table').count()>0);
  for(const table of await p.locator('.entry-content table').all()){assert.equal(await table.locator('caption').count(),1);assert(await table.locator('th[scope="col"]').count()>0);}
  for(const u of await p.locator('.entry-content a').evaluateAll(as=>as.map(a=>a.href)))if(u.startsWith(base))links.add(u);
  for(const w of [1440,390,320]){await p.setViewportSize({width:w,height:1000});assert.equal(await p.evaluate(()=>document.documentElement.scrollWidth>innerWidth+1),false,e.route+' overflow '+w);if(w!==320)await p.screenshot({path:'docs/audit/article-intent-1.15/'+w+'-'+e.route.split('/').pop()+'.png',fullPage:true});}
 }
 for(const url of links)assert.equal((await p.request.get(url)).status(),200,url);assert.deepEqual(errors,[]);
 const msg='PASS: 11-article update with backups, preserved identities/dates/status/media/authors, nonce rejection and idempotency; direct answer first; unique headings/IDs; working TOC; accessible tables and practical resources; '+links.size+' internal links; 3 widths without overflow; no JS errors.\n';fs.writeFileSync('docs/audit/article-intent-1.15/results.txt',msg);console.log(msg);
}finally{await b.close();}})().catch(e=>{console.error(e);process.exit(1)});
