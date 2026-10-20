"""Static preview: add language links to the English pages and build /es/.

Mirrors what WordPress renders with Pelham Content's i18n module: hreflang
alternates, og:locale:alternate, the header language switch, Spanish URLs,
Spanish metadata (keyword-map-es.json) and the Spanish dictionary.
Run from the repository root: python3 tools/i18n/preview_es.py
"""
import html, json, os, re, sys
sys.path.insert(0, os.path.dirname(__file__)); import segment as S

D = 'wordpress/pelham-content/data/'
DIC = json.load(open(D+'i18n-es.json'))
ROUTES = json.load(open(D+'i18n-routes.json'))
SEO_ES = {v['en_route']: v for v in json.load(open(D+'keyword-map-es.json')).values()}
JS_STRINGS = ['Primary','Menu','Close','article','articles','Site navigation','Reduced motion enabled','Resume animations','Pause animations','Your device is set to reduced motion.','Sending…','Sending your request. Please wait.','Your details have been restored in this tab. Review them and confirm consent before trying again.','Expedited: estimated 3 calendar days from pickup. South Florida only, from Homestead to West Palm Beach. Not guaranteed. Confirm your delivery address with our team.','Regular: estimated 7 calendar days from pickup. All Florida destinations. Not guaranteed.','Economy: estimated 10 calendar days from pickup. All Florida destinations. Our lowest-cost LTL option. Not guaranteed.']

def en_path(route): return '/' if route == 'home' else '/%s/' % route
def es_path(route): return '/%s/' % ROUTES[route]
def t(text): return DIC.get(S.norm(text), text)

def switch(target_lang, href, extra=''):
    label = {'es': ('ES', 'Español', 'Ver esta página en español'), 'en': ('EN', 'English', 'View this page in English')}[target_lang]
    return ('<a class="language-switch%s" href="%s" hreflang="%s" lang="%s" aria-label="%s"><span aria-hidden="true">%s</span><span class="language-switch-name">%s</span></a>'
            % (extra, href, 'es-US' if target_lang == 'es' else 'en-US', target_lang, label[2], label[0], label[1]))

def strip_language(doc):
    doc = re.sub(r'\n?<a class="language-switch[^"]*"[^>]*>.*?</a>', '', doc, flags=re.S)
    doc = re.sub(r'<link rel="alternate" hreflang="[^"]*" href="[^"]*">\n', '', doc)
    doc = re.sub(r'<meta property="og:locale:alternate"[^>]*>\n', '', doc)
    doc = re.sub(r'<script id="pelham-i18n">.*?</script>\n', '', doc, flags=re.S)
    doc = re.sub(r'<input type="hidden" name="pelham_lang" value="[a-z]+">', '', doc)
    return re.sub(r' lang-(en|es)(?=[ "])', '', doc)

def add_language(doc, route, lang):
    other = 'es' if lang == 'en' else 'en'
    href = es_path(route) if other == 'es' else en_path(route)
    end = doc.index('</a>', doc.index('<a class="brand"')) + 4
    doc = doc[:end] + '\n' + switch(other, href, ' language-switch-bar') + doc[end:]
    doc = doc.replace('<div class="account-navigation">', '<div class="account-navigation">' + switch(other, href), 1)
    alt = ''.join('<link rel="alternate" hreflang="%s" href="%s">\n' % (h, u) for h, u in (('en-US', en_path(route)), ('es-US', es_path(route)), ('x-default', en_path(route))))
    doc = doc.replace('<link rel="canonical"', alt + '<link rel="canonical"', 1)
    doc = re.sub(r'(<meta property="og:locale" content="[^"]*">\n)', r'\1<meta property="og:locale:alternate" content="%s">\n' % ('es_US' if lang == 'en' else 'en_US'), doc, count=1)
    doc = doc.replace('name="action" value="pelham_enquiry">', 'name="action" value="pelham_enquiry"><input type="hidden" name="pelham_lang" value="%s">' % lang)
    return re.sub(r'(<body class="[^"]*)"', r'\1 lang-%s"' % lang, doc, count=1)

def es_url(path):
    """Spanish URL for an internal English path (keeps ?query and #hash)."""
    m = re.match(r'^(/[^?#]*)(.*)$', path)
    if not m or m.group(1).startswith(('/wp-', '/es/')): return path
    route = m.group(1).strip('/') or 'home'
    return es_path(route) + m.group(2) if route in ROUTES else path

def es_links(doc):
    return re.sub(r'(href|action)="(/[^"]*)"', lambda m: '%s="%s"' % (m.group(1), es_url(m.group(2))), doc)

def es_jsonld(doc, route):
    meta = SEO_ES[route]
    def walk(value, key=''):
        if isinstance(value, dict): return {k: walk(v, k) for k, v in value.items()}
        if isinstance(value, list): return [walk(v, key) for v in value]
        if not isinstance(value, str): return value
        if key == 'inLanguage': return 'es-US'
        if value.startswith('/'): return es_url(value)
        days = re.match(r'^(\d+) calendar days from pickup in Los Angeles, estimated and not guaranteed\. (.*)$', value)
        if days: return '%s %s %s' % (days.group(1), t('calendar days from pickup in Los Angeles, estimated and not guaranteed.'), t(days.group(2)))
        return t(value)
    def fix(m):
        data = json.loads(m.group(1))
        for node in data.get('@graph', []):
            if node.get('@type') == 'WebPage':
                node['name'] = meta['seo_title']; node['description'] = meta['description']
            if node.get('@type') in ('Article', 'Service') and 'description' in node:
                node['description'] = meta['description']
            if node.get('@type') == 'WebSite':
                node['inLanguage'] = ['en-US', 'es-US']
        data = walk(data)
        for node in data.get('@graph', []):
            if node.get('@type') == 'WebSite': node['inLanguage'] = ['en-US', 'es-US']
        return '<script type="application/ld+json">%s</script>' % json.dumps(data, ensure_ascii=False, separators=(',', ':')).replace('/', '\\/')
    return re.sub(r'<script type="application/ld\+json">(.*?)</script>', fix, doc, flags=re.S)

def en_jsonld(doc):
    def fix(m):
        data = json.loads(m.group(1))
        for node in data.get('@graph', []):
            if node.get('@type') == 'WebSite': node['inLanguage'] = ['en-US', 'es-US']
        return '<script type="application/ld+json">%s</script>' % json.dumps(data, ensure_ascii=False, separators=(',', ':')).replace('/', '\\/')
    return re.sub(r'<script type="application/ld\+json">(.*?)</script>', fix, doc, flags=re.S)

def spanish(doc, route):
    meta = SEO_ES[route]; desc = html.escape(meta['description'], quote=True); title = html.escape(meta['seo_title'], quote=True)
    head, body = doc[:doc.index('<body')], doc[doc.index('<body'):]
    body = es_links(S.apply(body, DIC))
    head = head.replace('<html lang="en-US">', '<html lang="es-US">', 1)
    head = re.sub(r'<title>.*?</title>', '<title>%s</title>' % title, head, count=1, flags=re.S)
    head = re.sub(r'<meta name="description" content="[^"]*">', '<meta name="description" content="%s">' % desc, head, count=1)
    for prop in ('og:title', 'og:description'):
        head = re.sub(r'<meta property="%s" content="[^"]*">' % prop, '<meta property="%s" content="%s">' % (prop, title if prop.endswith('title') else desc), head, count=1)
    for name in ('twitter:title', 'twitter:description'):
        head = re.sub(r'<meta name="%s" content="[^"]*">' % name, '<meta name="%s" content="%s">' % (name, title if name.endswith('title') else desc), head, count=1)
    head = re.sub(r'(<meta (?:property|name)="(?:og|twitter):image:alt" content=")([^"]*)(">)', lambda m: m.group(1) + html.escape(t(html.unescape(m.group(2))), quote=True) + m.group(3), head)
    head = re.sub(r'<meta property="og:locale" content="en_US">', '<meta property="og:locale" content="es_US">', head, count=1)
    head = re.sub(r'(<link rel="canonical" href=")([^"]*)(")', lambda m: m.group(1) + es_url(m.group(2)) + m.group(3), head, count=1)
    head = re.sub(r'(<meta property="og:url" content=")([^"]*)(")', lambda m: m.group(1) + es_url(m.group(2)) + m.group(3), head, count=1)
    head = es_jsonld(head, route)
    script = '<script id="pelham-i18n">window.pelhamI18n=%s;</script>\n' % json.dumps({s: t(s) for s in JS_STRINGS}, ensure_ascii=False).replace('</', '<\\/')
    head = head.replace('</head>', script + '</head>', 1)
    return add_language(head + body, route, 'es')

def main():
    done = 0
    for route in ROUTES:
        src = 'index.html' if route == 'home' else route + '/index.html'
        if not os.path.exists(src): continue   # e.g. /claims/ (Pelham Applications only)
        doc = strip_language(open(src).read())
        es = spanish(doc, route)
        open(src, 'w').write(add_language(en_jsonld(doc), route, 'en'))
        out = ROUTES[route] + '/index.html'
        os.makedirs(os.path.dirname(out), exist_ok=True)
        open(out, 'w').write(es); done += 1
    print(done, 'routes: English pages updated, Spanish pages written under es/')

if __name__ == '__main__':
    main()
