"""data/i18n-es.json = translation memory (tm-es.json) + interface strings (ui-es.json, ui-applications-es.json)."""
import json, os, sys
sys.path.insert(0, os.path.dirname(__file__)); import segment as S
here = os.path.dirname(__file__)
tm = json.load(open(os.path.join(here, 'tm-es.json')))
ui = {}
for name in ('ui-es.json', 'ui-applications-es.json'):
    ui.update({S.norm(k): v for k, v in json.load(open(os.path.join(here, name))).items()})
tm.update(ui)
json.dump(tm, open('wordpress/pelham-content/data/i18n-es.json', 'w'), ensure_ascii=False, indent=0, sort_keys=True)
print(len(tm), 'entries')
