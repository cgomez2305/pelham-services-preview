# Análisis de competencia y aplicación a Pelham

Consulta de fuentes oficiales: 14 de octubre de 2026. Documento interno; no es una comparación publicitaria para publicar en la web.

## Conclusión comercial

Pelham puede posicionarse como especialista en **paletas LTL desde Los Ángeles hacia Florida**, con una decisión clara entre presupuesto y rapidez. Los competidores analizados venden principalmente rapidez, menor manipulación, atención y herramientas operativas. La oportunidad defendible de Pelham es explicar mejor su corredor concreto y sus tres niveles, facilitar una cotización útil y mostrar evidencia propia de ejecución. No hay evidencia para afirmar que Pelham es más rápido, más barato o más confiable que estas empresas.

Oferta confirmada por el cliente: Expedited, 3 días calendario desde recogida, solo Homestead–West Palm Beach; Regular, 7 días; Economy, 10 días, ambos para toda Florida. Son estimaciones, no garantías. Economy es la opción más económica **dentro de Pelham**, no del mercado. FTL, cross-docking y paletización son complementarios. Transporte doméstico por camión; no vender transporte marítimo, ferrocarril, aduanas ni trabajo directo con el puerto.

## Método y límites

Se consultaron 14 URLs públicas mediante HTTP, conservando fecha, URL solicitada y final, estado y texto extraído en [seo-research-2026-10](seo-research-2026-10/). Todas devolvieron HTTP 200; una redirigió. No se enviaron cotizaciones, formularios ni solicitudes de cuenta, ni se ingresó a portales privados. Las herramientas se constatan por enlaces y contenido público, no por pruebas de sus resultados. Los mensajes de rendimiento son afirmaciones de cada empresa, no métricas verificadas independientemente. No se midieron rankings, backlinks, volumen de búsqueda, participación de mercado, tarifas ni desempeño real. No se extrapolan plazos de una ruta a otra.

## Comparación comprobada

| Empresa / identidad | Propuesta pública observada | Conversión y herramientas visibles | Aplicación para Pelham |
|---|---|---|---|
| EDI Express Inc., ediexpressinc.com | Expedited LTL California–Midwest/East Coast; anuncia 3–5 días, equipos de dos conductores y menor manipulación. Páginas por estado e industria. | Tracing, login, formularios BOL/crédito/reclamación, consulta de tránsito por ZIP. | Combinar servicio y corredor con información útil para compradores; no copiar sus promesas de indemnidad ni tiempos. |
| Sterling Transportation, sterlingtransportation.com | Expedited LTL con origen declarado en la ruta LAX–MIA, expansión a Texas; atención personalizada y horario por origen/destino. | Portal de seguimiento/documentos, teléfono comercial, tabla de tránsito, terminales y recargos. | Es una referencia directa para la ruta y el comprador forwarder. Publicar límites de cobertura y condiciones junto al plazo. |
| Daylight Transport, dylt.com | Long-haul LTL; home anuncia coast-to-coast en 2 días y carga directa. Página específica para forwarders y garantías premium. | Cotización, calculador de tránsito/densidad, recogida, tracking PRO, cuenta y reclamaciones. | Crear contenido para forwarders que explique el tramo doméstico. No competir con superlativos de rapidez ni copiar garantías. |
| JTS Express, jtsexpress.com | Direct LTL/TL, niveles de servicio, menor manipulación y contacto único; distribución/almacenaje. | Request Quote/Request Rate, tracking, formularios y mapa. La página Transit Times dice “Coming soon”. | Ofrecer una comparación de niveles visible y completa; un enlace a una herramienta no sustituye información operativa utilizable. |

Identificación de JTS: el sitio se presenta como JTS Express, con LTL/TL, sede 20505 Annalee Avenue, Carson, CA 90746, teléfono 310-537-8400 y correo ops@jtsexpress.com. Es una coincidencia contextual fuerte con la empresa mencionada, no una empresa de paquetería de nombre parecido. Conviene confirmar el enlace con el cliente si se usara para una comparación comercial externa.

## Evidencia por competidor

### EDI Express

- [Home](https://www.ediexpressinc.com): “expedited 3-5 day service” entre California, Midwest y East Coast; “Exclusive 2-person sleeper team service.” Evidencia: `edi-home.json`.
- [Florida](https://www.ediexpressinc.com/areas-schedule/ltl-shipping-services-in-florida): explica rutas inbound/outbound y distingue mapas generales de tiempos por código postal: “transit times listed in this graphic are general in nature”. Evidencia: `edi-florida.json`.
- [Expedited LTL](https://www.ediexpressinc.com/services/expedited-less-than-truckload-services): contenido educativo, beneficios y CTA; anuncia servicios adicionales como liftgate/residential. **No atribuirlos a Pelham sin confirmación.** Evidencia: `edi-ltl.json`.
- Arquitectura observada: páginas de apparel, footwear, electronics y otras industrias. Oportunidad: contenido por comprador con requisitos reales, no páginas clonadas para cada palabra clave. La terminología promocional “guaranteeing” en Florida no prueba una garantía contractual con devolución; no se verificó su tarifa legal.

### Sterling Transportation

- [Expedited LTL](https://www.sterlingtransportation.com/expedited-ltl/): “began with the LAX to MIA route”; presenta rutas West Coast, Florida y Texas, CTA telefónico y atención personalizada. Evidencia: `sterling-expedited.json`.
- [Transit Schedule](https://www.sterlingtransportation.com/transit-schedule/): tabla extensa con origen, destino y horarios/días. No convertirla en un único plazo sin revisar origen, corte y destino exactos. Evidencia: `sterling-schedule.json`.
- [Home](https://www.sterlingtransportation.com): portal para tracking/documentos, terminales y avisos de recargos. Evidencia: `sterling-home.json`.
- `/economy-ltl/`, enlazado desde home, **redirige a `/expedited-ltl/`** en esta consulta. No se encontró aquí una definición independiente de Economy ni se infiere que el servicio no exista. Evidencia: `sterling-economy.json` con URL final.
- Sus servicios CFS, customs bonded y TSA son propios de su oferta; no justifican anunciarlos para Pelham.

### Daylight Transport

- [Home](https://www.dylt.com): “Ship coast to coast in 2 days at standard LTL rates”. Afirmación publicitaria general; no es una cotización específica LA–Miami. Evidencia: `daylight-home.json`.
- [Guaranteed Services](https://www.dylt.com/guaranteed-services/): distingue Urgent Care (día) y Critical Care (día/hora), con prima y promesa de devolución. Evidencia: `daylight-guarantee.json`. No se validaron exclusiones contractuales ni disponibilidad por ZIP.
- [Services for Forwarders](https://www.dylt.com/services-for-forwarders/): propone una red doméstica alimentadora para forwarders, incluyendo carga que continúa desde Miami. Evidencia: `daylight-forwarders.json`.
- Lección: separar quién compra del destino final. Pelham puede hablar a freight forwarders sin anunciar servicio internacional propio. Nunca usar “fastest”, “guaranteed” o “damage-free” sin soporte operativo y contractual.

### JTS Express

- [Direct LTL and TL](https://www.jtsexpress.com/services/direct-ltl-and-tl-service/): “tiered levels of service”; CTA para cotización personalizada. Evidencia: `jts-ltl.json`.
- [About](https://www.jtsexpress.com/about-us/): contacto único, comunicación, distribución y alcance coast-to-coast declarados. Evidencia: `jts-about.json`.
- [Transit Times](https://www.jtsexpress.com/transit-times/): “Coming soon! We’re currently updating our transit time tool.” Evidencia: `jts-transit.json`.
- [Home](https://www.jtsexpress.com): herramientas y datos de identidad. Evidencia: `jts-home.json`.
- No hay base en estas páginas para atribuirle un plazo específico LA–Florida ni una garantía. No afirmar que carece de seguimiento: sí muestra acceso al tracking.

## Qué indica la muestra sobre la compra B2B

Esta muestra sugiere que la decisión combina ruta, plazo, coste, manipulación, comunicación y facilidad operativa. No es una encuesta representativa del mercado. Los compradores necesitan saber si se recoge en su origen, si se entrega a su destinatario, qué información permite cotizar y dónde pedir seguimiento/documentos. Los forwarders valoran poder explicar el tramo doméstico a sus propios clientes. Las fábricas, proveedores y distribuidores necesitan coordinar entrega y recepción. Esto orienta contenidos y formularios; las necesidades concretas deben validarse con ventas.

## Acciones recomendadas, en orden

1. **Página LTL como centro comercial:** ruta Los Ángeles–Florida; tabla 3/7/10; días calendario desde recogida; condición no garantizada; Expedited limitado a Homestead–West Palm Beach. CTA “Request an LTL quote”. No publicar tarifas inventadas.
2. **Cotización útil:** origen/destino, cantidad de paletas, peso/dimensiones, mercancía, fecha disponible, nivel preferido y datos de contacto. Registrar si quien consulta es proveedor, distribuidor, fábrica o forwarder de forma opcional. No bloquear solicitudes por desconocer el freight class; asesoría de ventas.
3. **Contenido para compradores:** incorporar inicialmente bloques útiles en LTL y una guía del tramo doméstico para forwarders. Crear páginas separadas solo cuando haya material específico suficiente; evitar cuatro páginas con el mismo texto cambiando el público.
4. **Prueba de confianza:** fotos auténticas de operaciones, direcciones verificadas, horarios, identidad y contacto, procedimientos claros. Obtener autorización para testimonios/casos. Si se publican estadísticas de puntualidad, documentar periodo, muestra y método.
5. **Guías prácticas:** preparación de una cotización de paletas; elegir Expedited/Regular/Economy; qué entrega Pelham a un forwarder en Miami; cross-docking frente a paletización. Cada guía debe dirigir a una acción relevante.
6. **Herramientas honestas:** enlaces de tracking reales cuando Supply Vision esté confirmado. No simular tracking ni inventar una integración; mantener una alternativa clara de contacto mientras tanto.
7. **Medición:** medir cotización efectivamente recibida, calidad del lead, servicio solicitado y resultado comercial. Diferenciar clic de llamada de llamada atendida. Revisar Search Console al disponer de datos reales.

## Hipótesis de búsqueda para validar (sin volúmenes)

| Grupo | Consultas candidatas | Destino editorial |
|---|---|---|
| Ruta y servicio | `LTL shipping Los Angeles to Florida`, `California to Florida LTL freight` | Página principal LTL existente, conservando URL válida |
| Sur de Florida | `Los Angeles to Miami pallet shipping`, `expedited LTL Los Angeles to South Florida` | Sección específica Expedited/cobertura; página aparte solo si aporta valor adicional |
| Ahorro y decisión | `economy LTL California to Florida`, `LTL vs full truckload shipping` | Comparación de niveles y guía de decisión |
| Comprador | `LTL freight for freight forwarders`, `pallet shipping for distributors` | Bloques de compradores y guía doméstica para forwarders |
| Apoyo | `cross docking Los Angeles`, `palletizing Los Angeles` | Servicio complementario, únicamente con ubicación operativa confirmada |

Son hipótesis de intención basadas en el servicio confirmado y en lenguaje público observado, **no un keyword research cuantitativo**. Validarlas después con Search Console y fuentes de demanda; no prometer posiciones. No crear páginas de ciudades atendidas que aparenten oficinas inexistentes.

## Criterios para evaluar resultados

Durante el primer mes tras publicar: indexación de páginas válidas, ausencia de errores de migración y registro correcto de solicitudes. Después: consultas no de marca relevantes, clics hacia LTL, cotizaciones válidas por nivel y comprador, y porcentaje que ventas acepta. Comparar periodos con cautela por estacionalidad y por empezar sin histórico. El objetivo es negocio cualificado, no aumentar tráfico genérico sobre transporte internacional que Pelham no ofrece.
