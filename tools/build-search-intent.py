"""Editorial intent inference from query wording and confirmed offer; not a SERP study."""
from pathlib import Path
import csv,json,collections
r=Path(__file__).resolve().parents[1];out=r/'docs/keyword-research-2026-10'
source=list(csv.DictReader((out/'keywords-priorizadas.csv').open(encoding='utf-8-sig')))
coverage={x['keyword']:x for x in json.loads((out/'cobertura-96-keywords.json').read_text())}
rows=[]
for x in source:
 k=x['keyword'];g=x['grupo'];c=coverage.get(k,{})
 intent='';question='';must='';resource='';status='Resuelta editorialmente por intención';limit='';cta=''
 if x['prioridad']=='Excluir':
  intent='Fuera de la oferta / entidad distinta';question='Encontrar otra empresa, atención médica o un servicio que Pelham no ofrece';must='No intentar captar esta búsqueda con contenido irrelevante';resource='No crear contenido';status='Excluida';limit=x['motivo']
 elif k=='pelham services reviews':
  intent='Investigación comercial / reputación';question='¿Qué opinan clientes reales de Pelham?';must='Reseñas verificables y su procedencia';resource='Reseñas auténticas';status='Pendiente: reseñas verificables';limit='Cliente confirmó que no hay fuentes disponibles; no se fabrican testimonios ni puntuaciones.'
 elif k=='pelham services tracking':
  intent='Navegacional / soporte';question='¿Dónde está mi envío y cómo consigo una actualización?';must='Canal de contacto y referencia necesaria; identificar que no es un buscador público';resource='Sección de ayuda de seguimiento';status='Parcial: ayuda por contacto';limit='No está confirmada la funcionalidad de tracking del portal';cta='Contactar con referencia del envío'
 elif g=='Comparación LTL / FTL':
  intent='Informacional / comparación comercial';question='¿Qué significan LTL y FTL, en qué se diferencian y cuál me conviene?';must='Definiciones, capacidad, criterios de elección y comparación de costes/tiempos';resource='Respuesta inicial + tabla + escenarios de elección';cta='Elegir LTL/FTL o pedir ayuda para cotizar'
 elif g=='Coste de pallets' or (g=='Cotización' and any(w in k for w in ['cost','rates'])):
  intent='Investigación de precio / comercial';question='¿Cuánto costará mi envío y qué incluye ese importe?';must='Un importe o rango respaldado para la carga/ruta, o una explicación clara de por qué exige cotización';resource='Factores y ejemplo de comparación sin precios ficticios; acceso a cotización';status='Parcial: importe requiere cotización';limit='No hay tarifa ni rango aprobado; explicar factores no sustituye el importe esperado';cta='Solicitar precio con datos reales de la carga'
 elif g=='Cotización' or k=='pelham services quote':
  intent='Transaccional / solicitud';question='¿Cómo consigo un presupuesto de este envío?';must='Formulario accesible, datos necesarios, servicio/ruta y siguiente paso';resource='Formulario guiado + checklist';cta='Enviar solicitud de presupuesto'
 elif g=='FTL':
  intent='Comercial / contratar servicio';question='¿Ofrecen camión completo para mi ruta y cómo lo contrato?';must='Capacidad exclusiva, ruta, datos para cotizar y tiempos confirmados aparte';resource='Página FTL y solicitud específica';cta='Solicitar FTL'
 elif g=='LTL California → Florida' or k=='pelham services ltl':
  intent='Comercial / contratar servicio';question='¿Puede Pelham llevar mis pallets por esta ruta y en qué plazo?';must='Origen Los Ángeles, destino, cobertura por nivel, plazos estimados y solicitud';resource='Servicio LTL, destinos y comparador de niveles';cta='Cotizar LTL y nivel apropiado'
 elif g=='Clientes B2B':
  intent='Comercial / adecuación y coordinación';question='¿Sirve este transporte para mi tipo de empresa y cómo coordino la entrega?';must='Necesidad del proveedor/distribuidor/fábrica o forwarder, recepción y responsabilidades';resource='Secciones B2B o guía de entrega doméstica';cta='Preparar la carga y solicitar el tramo doméstico'
  if k=='ltl freight forwarding':status='Parcial: solo tramo doméstico';limit='La consulta puede pedir forwarding internacional; Pelham solo ofrece trucking doméstico.'
 elif g=='Marca':
  intent='Navegacional / entidad';question='¿Cuál es la web o instalación correcta de Pelham?';must='Nombre de empresa, actividad y, si corresponde, dirección/contacto de la instalación';resource='Home, About o ficha de instalación';cta='Acceder al servicio, dirección o contacto correcto';limit='Las variantes cortas de marca pueden mezclar homónimos en Google.'
 else:raise ValueError(k)
 rows.append({'keyword':k,'busquedas_mensuales_exportadas':x['busquedas_mensuales_exportadas'],'intencion_principal':intent,'pregunta_del_usuario':question,'respuesta_necesaria':must,'destino':c.get('url_objetivo',''),'recurso_aplicado':resource,'siguiente_paso':cta,'estado_resolucion':status,'limitacion':limit,'base_del_analisis':'Inferencia editorial por consulta, contenido y oferta confirmada; no verificada en SERP','fuentes_csv':x['fuentes']})
assert len(rows)==149 and len({x['keyword'] for x in rows})==149
with (out/'intenciones-149-keywords.csv').open('w',encoding='utf-8-sig',newline='') as f:
 w=csv.DictWriter(f,fieldnames=rows[0]);w.writeheader();w.writerows(rows)
(out/'intenciones-149-keywords.json').write_text(json.dumps(rows,ensure_ascii=False,indent=2)+'\n')
print(json.dumps(collections.Counter(x['estado_resolucion'] for x in rows),ensure_ascii=False))
