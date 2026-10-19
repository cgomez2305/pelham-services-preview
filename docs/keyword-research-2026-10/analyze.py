"""Run from repository root; preserves input metrics and source provenance."""
import csv, glob, json, collections, pathlib
out=pathlib.Path('docs/keyword-research-2026-10')
groups=collections.defaultdict(list)
for p in sorted(glob.glob('/workspace/attachments/*/Keyword Stats*.csv')):
    lines=pathlib.Path(p).read_text(encoding='utf-16').splitlines()
    for r in csv.DictReader(lines[2:],delimiter='\t'):
        groups[r['Keyword'].strip().lower()].append((pathlib.Path(p).name,r))

def classify(k):
    if any(x in k for x in ['medical','clinic','care','medicine','health','imaging','hospital','gibbs','orthopedic','physiotherapy','rivian','national freight forwarding','internationally','fedex',' ups']):
        return 'Excluir','Fuera de alcance','','Intención médica, otra marca o transporte internacional; no corresponde a la oferta confirmada.'
    if k.startswith('pelham '):
        target='/locations/miami-fl/' if 'miami' in k else '/locations/orlando-fl/' if 'orlando' in k else '/locations/gardena-ca/' if ('gardena' in k or 'los angeles' in k) else '/quote/' if 'quote' in k else '/'
        return 'P2','Marca',target,'Validar identidad en SERP; tracking requiere confirmar portal operativo, no prometer seguimiento público.' if 'tracking' in k else 'Navegación de marca; variantes pueden compartir demanda.'
    if ('ltl' in k and 'ftl' in k) or k=='when to use full truckload shipping':
        return 'P1','Comparación LTL / FTL','/news/ltl-vs-ftl-california-florida-shipping/','Optimizar el artículo existente; una página para las variantes de esta intención.'
    if k.startswith('how much'):
        return 'P1','Coste de pallets','/news/pallet-shipping-cost-california-to-florida/ (propuesta)','Crear guía de factores de coste, sin tarifas inventadas; conectar con cotización.'
    if any(x in k for x in ['quote','rates','cost']):
        return 'P1','Cotización','/quote/','Resolver solicitud comercial; enlazar guía de costes, sin multiplicar páginas de cotización.'
    if any(x in k for x in ['full truckload','dedicated truck','truckload shipping','ftl shipping']):
        return 'P2','FTL','/services/dedicated-transport/','Servicio confirmado; no trasladar los plazos del LTL al FTL.'
    if any(x in k for x in ['forward','suppliers','manufacturers','distributors','business to business','commercial freight','wholesale']):
        return 'P2','Clientes B2B','/services/freight-consolidation/','Sección de clientes; transporte doméstico hasta el receptor, sin ofrecer forwarding internacional.'
    return 'P1','LTL California → Florida','/services/freight-consolidation/','Prioridad por encaje comercial; sin volumen no significa sin demanda. No crear páginas por cada variante o ciudad.'

records=[]; conflicts=[]
for k,items in sorted(groups.items()):
    r=items[0][1]
    differing=[field for field in r if len({x[1].get(field,'') for x in items})>1]
    if differing: conflicts.append({'keyword':k,'fields':differing,'rows':[x[1] for x in items]})
    priority,cluster,url,reason=classify(k)
    records.append({'keyword':k,'prioridad':priority,'grupo':cluster,'url_destino':url,'motivo':reason,'busquedas_mensuales_exportadas':r['Avg. monthly searches'],'estado_volumen':'sin dato' if r['Avg. monthly searches']=='' else 'cero explícito' if r['Avg. monthly searches']=='0' else 'informado','competencia_GOOGLE_ADS':r['Competition'],'indice_competencia_GOOGLE_ADS':r['Competition (indexed value)'],'puja_parte_superior_min':r['Top of page bid (low range)'],'puja_parte_superior_max':r['Top of page bid (high range)'],'moneda':r['Currency'],'cambio_3_meses':r['Cambio en los últimos tres meses'],'cambio_interanual':r['Cambio interanual'],'KD_organica':'no proporcionada','pais':'Estados Unidos (confirmado por cliente)','idioma':'inglés (confirmado por cliente)','red':'no confirmada','periodo':'2025-10-01 / 2026-09-30','fuentes':' | '.join(x[0] for x in items),'filas_origen':len(items),'conflictos':' | '.join(differing)})
with (out/'keywords-priorizadas.csv').open('w',encoding='utf-8-sig',newline='') as f:
    w=csv.DictWriter(f,fieldnames=records[0].keys());w.writeheader();w.writerows(records)
(out/'fuentes-normalizadas.json').write_text(json.dumps({k:[{'source':s,'data':r} for s,r in v] for k,v in groups.items()},ensure_ascii=False,indent=2))
stats={'filas':sum(map(len,groups.values())),'unicas':len(groups),'duplicadas':sum(map(len,groups.values()))-len(groups),'prioridades':dict(collections.Counter(r['prioridad'] for r in records)),'volumen':dict(collections.Counter(r['estado_volumen'] for r in records)),'conflictos':conflicts,'series_mensuales_con_datos':sum(any(v for f,v in r.items() if f.startswith('Searches:')) for items in groups.values() for _,r in items)}
(out/'resumen.json').write_text(json.dumps(stats,ensure_ascii=False,indent=2));print(json.dumps(stats,ensure_ascii=False,indent=2))
