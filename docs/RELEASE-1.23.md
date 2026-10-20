# Pelham 1.23 — sitio bilingüe inglés / español

Tema **1.23.0** · Pelham Content **1.18.0** · Pelham Applications **1.2.0**

Todo el sitio existe ahora también en español bajo `/es/`: las 29 páginas, artículos, servicios e instalaciones, el formulario de cotización, el de contacto, la solicitud digital de cliente y crédito, la reclamación de carga, los correos al cliente, los mensajes de error y los textos legales. Se hace sin plugins de terceros: es parte de Pelham Content.

## Instalación

1. Hacer una copia de seguridad del sitio.
2. Reemplazar los tres paquetes y vaciar la caché:
   - `pelham-modern-1.23.0.zip`
   - `pelham-content-1.18.0.zip`
   - `pelham-applications-1.2.0.zip`
3. Entrar al administrador con un usuario administrador. La primera visita crea la versión en español (30 elementos) y muestra el aviso «Spanish site created…». No modifica ningún contenido en inglés.
4. Ajustes → Enlaces permanentes → Guardar (refresca las URL; el plugin también lo repara solo si hiciera falta).
5. Revisar `/es/`, el selector **ES / EN** de la cabecera y una cotización de prueba.

Si algo se borra o falta, **Pelham Company → Create missing Spanish pages** crea solo lo que falte y nunca sobrescribe.

## Cómo funciona

- **Contenido editable:** cada página en español es una página normal de WordPress, marcada como español y vinculada a su versión en inglés. En los listados del administrador aparece la columna *Language*, con un enlace a la otra versión.
- **URLs propias, pensadas para las búsquedas en español:**

| Inglés | Español |
|---|---|
| `/` | `/es/` |
| `/services/freight-consolidation/` | `/es/servicios/carga-consolidada-ltl/` |
| `/services/dedicated-transport/` | `/es/servicios/camion-completo-ftl/` |
| `/services/transloading/` | `/es/servicios/cross-docking-paletizado/` |
| `/who-we-serve/` | `/es/a-quien-servimos/` |
| `/locations/…` | `/es/ubicaciones/gardena-los-angeles/`, `/miami/`, `/orlando/` |
| `/about/` | `/es/nosotros/` |
| `/news/` y artículos | `/es/blog/` (por ejemplo, `/es/blog/diferencia-entre-ltl-y-ftl/`) |
| `/quote/` · `/contact/` · `/register/` · `/claims/` | `/es/cotizacion/` · `/es/contacto/` · `/es/registro/` · `/es/reclamaciones/` |
| Legales | `/es/aviso-de-privacidad/`, `/es/politica-de-cookies/`, `/es/terminos-y-condiciones/` |

  La lista completa está en `wordpress/pelham-content/data/i18n-routes.json`.
- **Listados:** noticias, servicios, instalaciones y el pie muestran solo el idioma de la página.
- **Menú:** en español se usa el menú asignado a *Primary navigation (Spanish)*. Si no hay ninguno, se muestra el menú por defecto ya traducido. Lo mismo con el pie (*Footer navigation (Spanish)*).
- **Selector de idioma:** «ES»/«EN» lleva a la misma página en el otro idioma. En móvil aparece junto al botón Menú.

## SEO

- `hreflang` en-US / es-US / x-default recíprocos en las 30 parejas de páginas.
- `og:locale` y `og:locale:alternate`; `<html lang="es-US">`; `inLanguage` en el schema.
- Canonical propio por idioma.
- Una sola URL por idioma: un artículo, servicio o instalación pedido con la base del otro idioma redirige con 301 a su dirección.
- Sitemap con las páginas de ambos idiomas; registro y reclamaciones siguen con `noindex`.
- Titles (≤ 60 caracteres), descriptions (≤ 158) y H1 en español escritos para las búsquedas medidas en el Keyword Planner:

| Búsqueda en español | Página |
|---|---|
| carga consolidada | `/es/servicios/carga-consolidada-ltl/` |
| diferencia entre ftl y ltl · qué significa ltl | `/es/blog/diferencia-entre-ltl-y-ftl/` |
| cross docking qué es · transbordo de contenedores | `/es/blog/cross-docking-transbordo-y-paletizado/`, `/es/blog/requisitos-para-el-transbordo-de-contenedores/` |
| servicio de paletizado · mercancía paletizada | `/es/servicios/cross-docking-paletizado/` |
| cotizar flete | `/es/cotizacion/`, `/es/blog/que-incluir-al-cotizar-un-flete/` |
| agente de carga en miami | `/es/blog/entrega-de-carga-a-agentes-de-carga-en-miami/` |

  Los metadatos están en `data/keyword-map-es.json`.

## Formularios y correos

- **Cotización y contacto:** etiquetas, ayudas y mensajes en español. Vuelven a la página en español. El correo al equipo sigue en inglés e incluye `Language: Spanish (reply in Spanish)`.
- **Solicitud de cliente y crédito, y reclamación:** toda la interfaz está en español, incluidos pasos, firmas, revisión, errores y estados. El enlace privado, el acuse de recibo y los avisos de estado o de corrección llegan al cliente en español. El equipo recibe sus avisos en inglés, con el idioma del cliente indicado.
- **Valores internos sin cambios:** las opciones de los desplegables envían los valores en inglés, así que la validación, el panel interno y los PDF no cambian.
- **Documentos legales:** los acuerdos (Customer Application, Credit Application, Terms and Agreements) son documentos legales en inglés. La interfaz lo indica y la versión firmada es la inglesa.

## Traducción

- Unas 9.000 palabras de contenido y más de 1.100 textos de interfaz. Se usó español neutro de EE. UU., trato de *usted* y un glosario fijo: carga consolidada (LTL), camión completo (FTL), cross-docking, transbordo, paletizado, agente de carga…
- Los nombres propios (Pelham Services, Land Stand, Supply Vision), las direcciones y los niveles Expedited / Regular / Economy se mantienen.
- **Recomendación:** que un abogado revise la versión en español de la política de privacidad, la de cookies y los términos antes de publicarla.

## Mantenimiento de la traducción

- **Cambios de texto:** la versión en español se edita directamente en WordPress, como cualquier página.
- **Herramientas** en `tools/i18n/`:
  - `segment.py` divide el HTML en frases sin alterar el marcado.
  - `tm-es.json` es la memoria de traducción del contenido.
  - `ui-es.json` y `ui-applications-es.json` contienen los textos de interfaz.
  - `build_dict.py` genera `data/i18n-es.json`, que usa el sitio.
  - `build_es.py` genera `data/pages-es.json`.
  - `seo_es.py` genera los metadatos SEO.
  - `preview_es.py` genera la vista previa `/es/`.
- **Textos de interfaz nuevos:** si se añade un texto en inglés, se añade su traducción a `ui-es.json` y se ejecuta `python3 tools/i18n/build_dict.py`.

## Pruebas realizadas

- **WordPress 6.8.3 real (SQLite) con los tres plugins:** 60 URL a 390 px, con estos resultados:
  - Sin errores de PHP con `WP_DEBUG` ni errores JS.
  - Sin desbordamiento horizontal.
  - `hreflang` recíproco y canonical correcto.
  - Ningún enlace al otro idioma y ningún texto en inglés en las páginas en español.
- **Flujos completos:**
  - Cotización en español con LTL Regular: llega el correo y el mensaje de agradecimiento sale en español.
  - Cotización en inglés: sin cambios.
  - Solicitud digital en español: correo de verificación en español, apertura del borrador, guardado automático y firmas.
- **Robustez:**
  - Desactivar y reactivar el plugin mantiene las rutas.
  - Repetir la creación no duplica nada y repone una página borrada.
  - El tema sin el plugin sigue funcionando en inglés.
  - Una URL del otro idioma redirige con 301.
- **Vista previa estática:** las 58 páginas (29 + 29) pasan las mismas comprobaciones.
- **Pendiente:** revisar en staging con el contenido real de producción y el correo SMTP del hosting.
