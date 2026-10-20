"""Build wordpress/pelham-content/data/pages-es.json from the English seed
content and the translation memory (data/i18n-es.json)."""
import json, re, sys, os
sys.path.insert(0, os.path.dirname(__file__)); import segment as S
D = 'wordpress/pelham-content/data/'
tm = json.load(open(D+'i18n-es.json'))
routes = json.load(open(D+'i18n-routes.json'))
seo = json.load(open(D+'keyword-map-es.json'))
by_route = {v['en_route']: (k, v) for k, v in seo.items()}

def es_links(html):
    """Point internal English links to their Spanish route."""
    def fix(m):
        path, rest = m.group(2), m.group(3)
        route = path.strip('/') or 'home'
        if route in routes:
            return '%s="/%s/%s"' % (m.group(1), routes[route], rest)
        return m.group(0)
    return re.sub(r'(href)="(/(?!wp-content|es/)[^"#?]*)([^"]*)"', fix, html)

def translate(html, missing):
    return es_links(S.apply(html, tm, missing))

entries = json.load(open(D+'pages.json'))
# The English homepage H1 gets the California-Florida lane at render time
# (inc/keywords-1.17.php); the Spanish page stores it directly.
home = open(D+'home.html').read().replace('Pelham Services.<br>Your freight, <em>connected.</em></h1>', 'Pelham Services.<br>California to Florida freight, <em>connected.</em></h1>')
entries.insert(0, {'route': 'home', 'type': 'page', 'title': 'Home', 'content': home})
if not any(e['route'] == 'who-we-serve' for e in entries):
    entries.append({'route': 'who-we-serve', 'type': 'page', 'title': 'Who we serve', 'content': open(D+'who-we-serve.html').read()})
out = []; missing = set()
order = {r: i for i, r in enumerate(routes)}
for e in sorted(entries, key=lambda e: order.get(e['route'], 999)):
    r = e['route']
    if r not in routes: continue
    slug, meta = by_route[r]
    item = {'route': routes[r], 'en_route': r, 'type': e['type'],
            'title': 'Inicio' if r == 'home' else meta['heading'],
            'content': translate(e['content'], missing),
            'excerpt': meta['description']}
    if e.get('service_label'):
        item['service_label'] = tm.get(S.norm(e['service_label']), e['service_label'])
    out.append(item)
# Freight claims page: created by Pelham Applications in English; the Spanish
# page is only created when that plugin's shortcode is available.
slug, meta = by_route['claims']
out.append({'route': routes['claims'], 'en_route': 'claims', 'type': 'page', 'title': meta['heading'],
            'content': '[pelham_claim_form]', 'excerpt': meta['description'], 'requires_shortcode': 'pelham_claim_form'})
json.dump(out, open(D+'pages-es.json', 'w'), ensure_ascii=False, indent=1)
real = sorted(k for k in missing if not re.fullmatch(r'\[[^\]]+\]', k))
print(len(out), 'Spanish entries;', len(real), 'untranslated segments')
for k in real: print('  MISSING:', k[:140])
