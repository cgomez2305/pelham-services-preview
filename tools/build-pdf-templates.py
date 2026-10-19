"""Build FPDI-compatible copies without changing source PDFs. Requires pypdf.
Run from repository root. Review pdf-map.json and visual diffs for any new source version.
"""
from pathlib import Path
from pypdf import PdfReader, PdfWriter
import hashlib
import json
root = Path('wordpress/pelham-applications/documents')
out = root / 'templates'
out.mkdir(exist_ok=True)
manifest = {}
for source in sorted(root.glob('*.pdf')):
    reader = PdfReader(source)
    writer = PdfWriter()
    for page in reader.pages:
        writer.add_page(page)
    target = out / source.name
    with target.open('wb') as stream:
        writer.write(stream)
    manifest[source.name] = dict(source_sha256=hashlib.sha256(source.read_bytes()).hexdigest(), template_sha256=hashlib.sha256(target.read_bytes()).hexdigest(), pages=len(reader.pages))
(out / 'manifest.json').write_text(json.dumps(manifest, indent=2))
