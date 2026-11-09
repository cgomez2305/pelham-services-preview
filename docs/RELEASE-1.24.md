# Pelham 1.24 — revisiones del cliente

Tema **1.24.0** · Pelham Content **1.19.0** · Pelham Applications **1.2.0** (sin cambios)

Aplica el documento *PELHAM WEBSITE REVISIONS* y las respuestas del cliente, en inglés y en español.

## Instalación

1. Hacer una copia de seguridad.
2. Reemplazar el tema (`pelham-modern-1.24.0.zip`) y Pelham Content (`pelham-content-1.19.0.zip`).
3. Vaciar la caché.
4. En el administrador aparece el aviso **«Apply client revisions (save backups)»**. Pulsarlo actualiza 16 páginas, 8 en inglés y 8 en español:
   - home, Services, Who we serve, About;
   - la página LTL;
   - los artículos LTL vs FTL y coste de pallet;
   - Orlando.

   Cada página guarda su texto anterior en `pelham_before_revisions_1_19` y en sus revisiones. Las instalaciones nuevas ya traen el texto nuevo y no muestran el aviso.

## Cambios

| Página | Cambio |
|---|---|
| Home | Hero nuevo, con el enlace a LTL dentro del texto. Se eliminan «Explore LTL pallet shipping…» y «Shared trailer space…». «The right move for your freight.» en una línea. Niveles: «Choose your service tier.». Tarjeta LTL con «1 to 7 pallets». Tarjeta de cross-docking con las dos viñetas del cliente. |
| Home · proceso | Se quita el scroll horizontal: los 3 pasos se muestran fijos, uno al lado del otro. |
| Home · blog | El título «Notes from the freight journey» enlaza al blog y «Explore all freight insights» pasa a ser un botón visible. |
| Services | H1 «Freight transportation services» y texto nuevo, con enlaces a FTL y a cross-docking. |
| Who we serve | H1 «Trucking for suppliers, distributors, manufacturers and forwarders». Intro y textos nuevos de los 4 públicos, con listas «What to include in your quote request». Se mantienen las anclas, los enlaces a las guías y la cotización por tipo de empresa. |
| About | Textos nuevos: intro, «Service boundary», público en viñetas y B2B. Los enlaces a las guías se mantienen. |
| Orlando | «Pelham Services Orlando» (sin «Land Stand») y teléfono 323-201-4995: en la ficha, el pie, Locations, Contacto, Cotizar (llamada directa), el schema, el title y la description. |

### Respuestas del cliente aplicadas

- **LTL: de 1 a 7 pallets.** Para 8 pallets o más: «volume load or full truckload», sin fijar el corte entre volumen y camión completo. Se corrigieron las frases que decían que no había un umbral de pallets:
  - la página LTL: texto y nueva pregunta frecuente «How many pallets can ship LTL?»;
  - el artículo LTL vs FTL: respuesta corta, fila «Pallet count at Pelham» en la tabla y lista;
  - Who we serve.
- **Sin NMFC:** se eliminó «freight classification».
- **Facturación por el mayor entre peso real y peso volumétrico** (largo × ancho × alto ÷ 194):
  - sustituye a «only paying for the trailer space you use»;
  - se añade a la pregunta frecuente de precios LTL;
  - el artículo «How much does it cost to ship a pallet» tiene una sección nueva con la fórmula y un ejemplo (48 × 40 × 60 in → 594 lb).
  - **Confirmar con el cliente que las medidas son en pulgadas y el resultado en libras.**

### Correcciones al documento

- «3PLS» → 3PLs, «tear» → tier, «Truck load» → Truckload, «NETWRORKS» → Networks.
- «$L \times W \times H$» → L × W × H.
- Se quitó «custom» de «custom service tiers», porque los niveles son fijos.
- Se quitó «dedicated» del texto de agentes de carga en About.

## SEO

- Sin cambios de URL, titles ni descriptions (salvo Orlando). Las anclas de Who we serve se mantienen.
- El H1 de Services conserva la keyword; el de Who we serve cambia a «Trucking», sin volumen medido en juego.
- El enlace interno home → LTL se mantiene dentro del hero, y los enlaces a las guías en los textos nuevos.
- En español, el título de agentes de carga en Who we serve conserva «Miami» («agente de carga en Miami» tiene búsquedas).
- La fórmula de peso volumétrico añade al artículo de coste de pallet la respuesta concreta que buscaba la consulta de 500 búsquedas al mes.
- **Pendiente del cliente:** comprobar que el nombre, la dirección y el teléfono de Orlando coinciden con su Perfil de Empresa de Google.

## Pruebas

- **WordPress 6.8.3 local con los tres plugins:**
  - Actualización aplicada sobre el contenido anterior: 16 páginas. Una segunda pasada no cambia nada.
  - Las 60 URL pasan sin errores PHP (`WP_DEBUG`) ni JS y sin desbordamiento en móvil.
  - `hreflang`, canonical y enlaces por idioma correctos.
  - Ningún texto en inglés en las páginas en español.
- **Vista previa estática:** las 58 páginas actualizadas y comprobadas.
