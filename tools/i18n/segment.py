"""Split HTML into translatable segments without reserializing it.

A segment is a maximal run of text and inline tags inside a block element.
Tags become numbered placeholders ({1}, {2}…) so a translation can reorder
them, and each document keeps its own tag attributes (links, classes).
Translatable attributes (alt, aria-label, title, placeholder, data-*) are
separate plain-text segments.
"""
import html, re

INLINE = {'a','abbr','b','br','code','em','i','small','span','strong','sub','sup','time','u','mark','q','s','cite','data','kbd','var','wbr','img'}
SKIP = {'script','style','svg','textarea','noscript','title','head'}
VOID = {'area','base','br','col','embed','hr','img','input','link','meta','source','track','wbr'}
ATTRS = ('alt','aria-label','title','placeholder','data-description','data-question')
TOKEN = re.compile(r'<!--.*?-->|<![^>]*>|<[^>]+>|[^<]+', re.S)

def norm(text):
    t = html.unescape(text)
    for a, b in (('’',"'"),('‘',"'"),('“','"'),('”','"'),('…','...'),(' ',' '),('&#8217;',"'")):
        t = t.replace(a, b)
    return re.sub(r'\s+', ' ', t).strip()

def tag_name(tok):
    m = re.match(r'</?\s*([a-zA-Z][\w-]*)', tok)
    return m.group(1).lower() if m else None

def has_words(s):
    return re.search(r'[A-Za-z]{2,}', html.unescape(re.sub(r'<[^>]+>', '', s))) is not None

def wraps(r):
    depth = 0
    for k, (s, e, t) in enumerate(r):
        if t.startswith('<') and tag_name(t) not in VOID and not t.endswith('/>'):
            depth += -1 if t.startswith('</') else 1
            if depth == 0 and k < len(r) - 1: return False
    return True

def segments(doc):
    """Yield (start, end, key, tags) for each text run; key uses {n} placeholders."""
    toks = [(m.start(), m.end(), m.group(0)) for m in TOKEN.finditer(doc)]
    # Mark inline elements that contain a block descendant: they act as blocks.
    blockish = set(); stack = []
    for i, (s, e, t) in enumerate(toks):
        if not t.startswith('<') or t.startswith('<!'):
            continue
        name = tag_name(t)
        if not name: continue
        if t.startswith('</'):
            while stack:
                j, n = stack.pop()
                if n == name: break
        elif name not in VOID and not t.endswith('/>'):
            if name not in INLINE or name in SKIP:
                for j, n in stack:
                    if n in INLINE: blockish.add(j)
            stack.append((i, name))
    # Pair closing tags of blockish inline elements.
    closing = set(); stack = []
    for i, (s, e, t) in enumerate(toks):
        if t.startswith('<') and not t.startswith('<!'):
            name = tag_name(t)
            if t.startswith('</'):
                for k in range(len(stack)-1, -1, -1):
                    if stack[k][1] == name:
                        if stack[k][0] in blockish: closing.add(i)
                        del stack[k:]; break
            elif name and name not in VOID and not t.endswith('/>'):
                stack.append((i, name))
    skip_depth = 0; run = []
    def flush():
        nonlocal run
        r = run; run = []
        # Trim whitespace-only text and dangling edge tags.
        while r and (r[0][2].strip() == '' or r[0][2].startswith('</')): r.pop(0)
        while r and (r[-1][2].strip() == '' or (r[-1][2].startswith('<') and not r[-1][2].startswith('</') and tag_name(r[-1][2]) not in VOID)): r.pop()
        # Drop a tag pair that wraps the whole run (<a>Services</a> -> Services).
        while len(r) > 2 and r[0][2].startswith('<') and r[-1][2].startswith('</') and tag_name(r[0][2]) == tag_name(r[-1][2]) and wraps(r):
            r = r[1:-1]
            while r and r[0][2].strip() == '': r.pop(0)
            while r and r[-1][2].strip() == '': r.pop()
        if not r: return None
        src = doc[r[0][0]:r[-1][1]]
        if not has_words(src): return None
        tags = []; key = ''
        for s, e, t in r:
            if t.startswith('<'):
                tags.append(t); key += '{%d}' % len(tags)
            else:
                key += t
        return (r[0][0], r[-1][1], norm(key), tags)
    out = []
    depth = 0
    for i, (s, e, t) in enumerate(toks):
        is_tag = t.startswith('<')
        name = tag_name(t) if is_tag and not t.startswith('<!') else None
        if name in SKIP:
            if t.startswith('</'): skip_depth = max(0, skip_depth-1)
            elif not t.endswith('/>'): skip_depth += 1
            seg = flush();  seg and out.append(seg); continue
        if skip_depth: continue
        inline = (not is_tag) or (name in INLINE and i not in blockish and i not in closing)
        if t.startswith('<!'): inline = False
        if inline:
            # Sibling inline elements with only whitespace between them (menu
            # links) become separate segments.
            if is_tag and not t.startswith('</') and name not in VOID and run and depth == 0 and any(x[2].startswith('</') for x in run[-2:]) and all(x[2].startswith('<') or x[2].strip() == '' for x in run[-1:]):
                seg = flush(); seg and out.append(seg)
            run.append((s, e, t))
            if is_tag and name not in VOID and not t.endswith('/>'):
                depth += -1 if t.startswith('</') else 1
                depth = max(depth, 0)
        else:
            depth = 0
            seg = flush(); seg and out.append(seg)
    seg = flush(); seg and out.append(seg)
    return out

def attr_segments(doc):
    """Yield (start, end, key) for translatable attribute values outside skipped elements."""
    for m in re.finditer(r'<(?!/|!|script|style)[a-zA-Z][^>]*>', doc):
        for a in ATTRS:
            for am in re.finditer(r'\s%s="([^"]*)"' % re.escape(a), m.group(0)):
                v = am.group(1)
                if has_words(v):
                    yield (m.start()+am.start(1), m.start()+am.end(1), norm(v))

def apply(doc, tm, missing=None):
    """Replace every segment found in tm (key -> translated key with {n})."""
    edits = []
    for s, e, key, tags in segments(doc):
        if key in tm:
            parts = re.split(r'(\{\d+\})', tm[key])
            out = ''.join(tags[int(x[1:-1])-1] if re.fullmatch(r'\{\d+\}', x) and int(x[1:-1]) <= len(tags) else html.escape(x, quote=False) for x in parts)
            src = doc[s:e]
            lead = src[:len(src) - len(src.lstrip())]; trail = src[len(src.rstrip()):]
            edits.append((s, e, lead + out.strip() + trail))
        elif missing is not None: missing.add(key)
    for s, e, key in attr_segments(doc):
        if key in tm: edits.append((s, e, html.escape(tm[key], quote=True)))
        elif missing is not None: missing.add(key)
    edits.sort()
    res = []; pos = 0
    for s, e, out in edits:
        if s < pos: continue
        res.append(doc[pos:s]); res.append(out); pos = e
    res.append(doc[pos:])
    return ''.join(res)
