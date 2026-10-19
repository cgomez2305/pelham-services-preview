"""Build an auditable intent-to-content matrix; does not estimate rankings or KD."""
from pathlib import Path
import csv,json,re,collections
from lxml import html
root=Path(__file__).resolve().parents[1];data=root/'wordpress/pelham-content/data';out=root/'docs/keyword-research-2026-10'
entries=json.loads((data/'pages.json').read_text());by={e['route']:e for e in entries}
km=json.loads((data/'keyword-map.json').read_text());by['home']={'content':(data/'home.html').read_text(),'seo':km['home']}
rows=[r for r in csv.DictReader((out/'keywords-priorizadas.csv').open(encoding='utf-8-sig')) if r['prioridad']!='Excluir']
ltl='services/freight-consolidation';ftl='services/dedicated-transport';compare='news/ltl-vs-ftl-california-florida-shipping';cost='news/pallet-shipping-cost-california-to-florida';fw='news/freight-delivery-to-forwarders-in-miami'
def mapping(r):
 k=r['keyword'];g=r['grupo']
 if k=='pelham services reviews':return 'home','', 'Pendiente: reseñas verificables','Cliente confirmó que todavía no hay enlace de reseñas ni testimonios autorizados. No se inventaron valoraciones ni schema de estrellas.'
 if g=='Marca':
  if 'tracking' in k:return 'contact','shipment-updates','Implementada con alcance limitado','Responde con ayuda de seguimiento por contacto y referencia; no promete tracking público ni funciones del portal no confirmadas.'
  if 'quote' in k:return 'quote','freight-rates','Implementada por intención','Solicitud de presupuesto de Pelham con datos concretos del envío.'
  if 'ltl' in k:return ltl,'california-florida-ltl','Implementada por intención','Servicio LTL de Pelham y ruta real.'
  if 'gardena' in k or 'los angeles' in k:return 'locations/gardena-ca','facility-shipping','Implementada por intención','Gardena identifica la instalación de Los Ángeles; no se crean dos páginas para el mismo lugar.'
  if 'miami' in k:return 'locations/miami-fl','facility-shipping','Implementada por intención','Dirección y contacto confirmados de la instalación de Miami.'
  if 'orlando' in k:return 'locations/orlando-fl','facility-shipping','Implementada por intención','Instalación Orlando (Land Stand), dirección y cobertura sin teléfono inventado.'
  if 'trucking' in k:return 'about','business-freight','Implementada por intención','Identidad y actividad doméstica de Pelham; desambiguación de marcas homónimas.'
  return 'home','','Implementada por intención','Identidad Pelham Services y especialidad; variantes de marca requieren vigilar homónimos en SERP.'
 if g=='Comparación LTL / FTL':return compare,'ltl-ftl-decision','Implementada por intención','Unifica variantes de significado, diferencias, capacidad, cuándo elegir FTL y comparación de tarifas; no son 19 artículos.'
 if g=='Coste de pallets' or (g=='Cotización' and 'cost' in k):return cost,'one-pallet-miami','Implementada por intención','Responde al coste de una o varias paletas y LA–Miami; factores y solicitud sin precios ficticios.'
 if g=='Cotización':return 'quote','freight-rates','Implementada por intención','Formulario y requisitos de tarifas LTL/FTL por ruta; no simula cotizador instantáneo.'
 if g=='FTL':return ftl,'ftl-california-florida','Implementada por intención','Camión completo/exclusivo para LA–Florida y Miami; horarios FTL cotizados aparte.'
 if g=='Clientes B2B':
  if 'forward' in k:return fw,'domestic-forwarder-trucking','Implementada con alcance limitado' if k=='ltl freight forwarding' else 'Implementada por intención','Solo tramo doméstico a la transportadora; distingue trucking de forwarding internacional.'
  if 'business to business' in k:return 'about','business-freight','Implementada por intención','Actividad B2B explícita y enlace al servicio principal.'
  return ltl,'business-shippers','Implementada por intención','Explica proveedores, fabricantes, distribuidores, mayoristas y recepción comercial, con preparación y recurrencia.'
 if g=='LTL California → Florida':
  regional=any(t in k for t in ['miami','orlando','jacksonville','tampa','south florida'])
  return ltl,'florida-destinations' if regional else 'california-florida-ltl','Implementada por intención','Ruta y cobertura; distingue ciudades de entrega de instalaciones propias y limita Expedited.'
 raise ValueError(k)
result=[]
for r in rows:
 route,anchor,status,reason=mapping(r);e=by[route];doc=html.fromstring('<div>'+e['content']+'</div>')
 nodes=doc.xpath('//*[@id=$id]',id=anchor) if anchor else [doc]
 assert nodes,(r['keyword'],route,anchor)
 evidence=re.sub(r'\s+',' ',' '.join(nodes[0].itertext())).strip()
 result.append({'keyword':r['keyword'],'grupo':r['grupo'],'busquedas_mensuales_exportadas':r['busquedas_mensuales_exportadas'],'estado':status,'url_objetivo':'/' if route=='home' else '/'+route+'/','seccion':anchor,'title':e['seo']['seo_title'],'meta_description':e['seo']['description'],'razon':reason,'evidencia_textual':evidence if anchor else evidence[:700],'coincidencia_literal_en_contenido':'no evaluada; pendiente' if status.startswith('Pendiente') else 'sí' if r['keyword'].lower() in doc.text_content().lower() else 'no; variante semántica','KD_organica':'no proporcionada'})
assert len(result)==96 and len({r['keyword'] for r in result})==96
assert sum(r['estado'].startswith('Pendiente') for r in result)==1
with (out/'cobertura-96-keywords.csv').open('w',encoding='utf-8-sig',newline='') as f:
 w=csv.DictWriter(f,fieldnames=result[0].keys());w.writeheader();w.writerows(result)
(out/'cobertura-96-keywords.json').write_text(json.dumps(result,ensure_ascii=False,indent=2)+'\n')
print(json.dumps({'keywords':len(result),'estados':dict(collections.Counter(r['estado'] for r in result)),'destinos':len({r['url_objetivo'] for r in result})},ensure_ascii=False))
