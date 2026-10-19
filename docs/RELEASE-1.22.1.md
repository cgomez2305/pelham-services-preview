# Pelham 1.22.1 — tema 1.22.1 / contenido 1.17.1

Vuelve el equipo a *About us* y se ajustan tres páginas según una segunda revisión independiente del keyword research. Reemplazar **tema y plugin de contenidos** y vaciar caché. No reimportar contenido ni pulsar ningún botón de actualización.

## 1. Equipo en About us

El bloque *Meet the team* desapareció en contenido 1.9, cuando se reescribió About. Los textos originales seguían en `data/pages-before-1.9.json`.

- Se muestra al final de `/about/` sin tocar la página guardada. Los datos están en `data/team.json`.
- Si un editor coloca el shortcode `[pelham_team]` en otra posición, no se duplica.
- Diseño alineado con el resto del sitio: cuatro columnas con filete verde superior, iniciales en lugar de foto, cargo en verde y biografía corta. En tablet se ve a dos columnas y en móvil a una. Sin tarjetas anidadas.
- Si el cliente envía retratos, se pueden añadir sustituyendo las iniciales.

## 2. Segunda revisión del keyword research

**Fuente:** los 8 CSV de Keyword Planner del 14/10/2026, periodo oct 2025–sep 2026. Tres de ellos están en UTF-16.

**Recuento:** 149 keywords únicas, 82 con volumen. Coincide con el análisis anterior (`docs/keyword-research-2026-10/ANALISIS.md`).

**Avisos:**
- Las pujas están en EUR. Confirmar que la ubicación del plan es Estados Unidos y el idioma inglés.
- Ningún CSV trae serie mensual, así que el +900 % no permite hablar de tendencia.

### Qué tiene demanda medible

| Grupo | Búsquedas/mes | Página destino | Estado |
|---|---:|---|---|
| ltl vs ftl · ftl ltl meaning · ftl and ltl meaning (+14 variantes de 50) | 500 cada una | Artículo LTL vs FTL | Title correcto. El H1 no contenía «meaning» y limitaba la pregunta a California–Florida → **corregido** |
| how much does it cost to ship / send a pallet (+7 variantes de 50; FedEx, UPS e internacional no se persiguen) | 500 cada una | Artículo coste de pallet | H1 coincide exactamente. La meta description no respondía a la pregunta → **corregido** |
| freight shipping from california to florida · freight from california to florida | 50 cada una, puja alta hasta 19 € | Servicio LTL | Era la única búsqueda comercial medida del corredor, pero la página apuntaba a «ltl shipping california to florida», que no tiene datos, y la frase «freight shipping» no aparecía en ella → **corregido** en title, H1 y description |
| pelham services · pelham trucking · pelham shipping | 50 cada una | Home | Correcto: marca + «Trucking» en el title |
| pelham services miami · pelham services los angeles | 50 cada una | Fichas Miami y Gardena / Los Angeles | Correcto |
| pelham services tracking | 50 | Contact (sección de actualizaciones de envío) | Correcto y honesto: no promete seguimiento público |
| ltl freight forwarding | 50 | — | Bien descartada como objetivo: atrae a quien busca envío internacional |

**Ruido descartado (53):**
- Búsquedas médicas de «Pelham», con 5.000 al mes las de mayor volumen.
- «rivian bay area».
- Variantes internacionales, FedEx y UPS.

Este ruido explica por qué la marca debe trabajarse siempre como «Pelham Services» y nunca como «Pelham» a secas.

**Rutas por ciudad sin datos (LA–Miami, Orlando, Tampa, Jacksonville):** el planificador no les da volumen. Se mantiene la decisión de no crear páginas por ciudad; la página LTL las cubre en el texto.

**Públicos objetivo:** proveedores, distribuidores, fábricas y forwarders no aparecen en ningún CSV. La página *Who we serve* responde al objetivo de negocio, no a un volumen medido. Conviene pedir un lote específico, por ejemplo:
- `ltl carrier for distributors`
- `wholesale freight shipping`
- `trucking for freight forwarders los angeles`
- `pallet shipping for manufacturers`

### Cambios aplicados (`keyword-map.json` + `inc/keywords-1.17.1.php`)

| Página | Antes | Ahora |
|---|---|---|
| LTL · title | California to Florida LTL Shipping \| Pelham Services | LTL Freight Shipping: California to Florida \| Pelham |
| LTL · H1 | LTL shipping from California to Florida | LTL freight shipping from California to Florida |
| LTL · description | Ship pallets from Los Angeles to Miami… | LTL freight shipping from Los Angeles to Miami, Orlando and businesses across Florida… |
| LTL vs FTL · H1 | LTL vs. FTL: which fits your California–Florida shipment? | LTL vs. FTL: meaning, differences and which fits your shipment |
| LTL vs FTL · description | Learn the difference between LTL and FTL… | What do LTL and FTL mean? Compare less-than-truckload and full truckload… |
| Coste de pallet · description | Learn what affects the cost to ship… | How much does it cost to ship a pallet? See what changes the price… |

El H1 sale del título del contenido en WordPress. El nuevo H1 se aplica solo mientras ese título siga siendo el original: si alguien lo edita a mano en WordPress, se respeta su versión. Las tarjetas del listado de noticias conservan el título guardado.

### Recomendaciones que no se pueden aplicar sin el cliente

1. **Rango de precio orientativo para un pallet LA → Miami.** Quien busca «how much does it cost to ship a pallet» espera una cifra. El artículo explica los factores, pero no da precio. Con un rango aprobado por ventas (por ejemplo, «la mayoría de presupuestos de un pallet estándar están entre X y Y»), el artículo pasaría a competir de verdad. No se inventa.
2. **Artículo LTL vs FTL:** cambiar en el editor el H2 «The short answer» por «What do LTL and FTL mean?». Recoge las variantes en forma de pregunta (*what is the difference between…*, *what does ftl and ltl mean*).
3. Tras la migración, medir en Search Console las consultas reales. Los volúmenes del planificador son orientativos y redondeados (50 / 500).

## Instalación

1. Reemplazar Pelham Modern con `pelham-modern-1.22.1.zip`.
2. Reemplazar Pelham Content con `pelham-content-1.17.1.zip`.
3. Vaciar caché y revisar `/about/`, `/services/freight-consolidation/` y `/news/ltl-vs-ftl-california-florida-shipping/`.
