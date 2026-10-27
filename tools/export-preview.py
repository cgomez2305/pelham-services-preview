"""Export the isolated WordPress review site. Static forms never send requests."""
from pathlib import Path
from urllib.request import urlopen
from urllib.parse import urlparse
import json,re,shutil
root=Path(__file__).resolve().parents[1]
base=__import__('os').environ.get('PELHAM_PREVIEW_URL','http://127.0.0.1:9401')
entries=json.loads((root/'wordpress/pelham-content/data/pages.json').read_text())
routes=['']+[x['route'] for x in entries]
# Map real WordPress post permalinks to the static preview routes.
post_aliases={}
for e in entries:
 if e['type']!='post':continue
 records=json.loads(urlopen(base+'/wp-json/wp/v2/posts?slug='+e['route'].split('/')[-1]+'&_fields=link',timeout=30).read())
 if records:post_aliases[urlparse(records[0]['link']).path]='/'+e['route']+'/'
for name,kind in [('pelham-modern','themes'),('pelham-content','plugins')]:
 shutil.copytree(root/'wordpress'/name/'assets',root/'wp-content'/kind/name/'assets',dirs_exist_ok=True)
for route in routes:
 body=urlopen(base+'/'+route+('/' if route else ''),timeout=30).read().decode()
 for url in re.findall(r'(?:href|src)=["\']([^"\']+)',body):
  if not url.startswith(base+'/wp-includes/'):continue
  path=urlparse(url).path;dest=root/path.lstrip('/')
  if not dest.exists():
   dest.parent.mkdir(parents=True,exist_ok=True);dest.write_bytes(urlopen(base+path,timeout=30).read())
 body=body.replace(base,'').replace(base.replace('/',r'\/'),'')
 body=re.sub(r"<link[^>]+(?:dns-prefetch|application/json\+oembed|text/xml\+oembed)[^>]*>",'',body)
 body=re.sub(r'http://127\.0\.0\.1:[0-9]+','',body)
 for actual,preview in post_aliases.items():
  body=body.replace(actual,preview)
 body=re.sub(r'<input[^>]+name="(?:pelham_nonce|_wp_http_referer)"[^>]*>','',body)
 body=re.sub(r'<meta[^>]+name=["\']robots["\'][^>]*>','',body)
 body=body.replace('</head>','<meta name="robots" content="noindex,nofollow"></head>')
 body=re.sub(r'action="[^"]*admin-post.php"','action="#"',body)
 body=body.replace('</body>','<script src="/assets/preview-forms.js"></script></body>')
 dest=root/route/'index.html';dest.parent.mkdir(parents=True,exist_ok=True);dest.write_text(body)
(root/'assets/preview-forms.js').write_text("document.addEventListener('submit',e=>{if(!e.target.matches('.pelham-form'))return;e.preventDefault();e.stopImmediatePropagation();let note=e.target.querySelector('.preview-status');if(!note){note=document.createElement('p');note.className='preview-status';note.setAttribute('role','status');e.target.append(note);}note.textContent='Design preview only. No request has been sent. Use the installed WordPress site to submit.';},true);")
(root/'preview-version.json').write_text(json.dumps({'theme':'1.24.0','plugin':'1.20.8','forms':'disabled','routes':len(routes)},indent=2))
print('Exported',len(routes),'pages; static submissions disabled.')
