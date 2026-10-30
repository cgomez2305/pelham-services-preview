# Pelham 1.22 — tema 1.22.0 / contenido 1.17.0

Pulido de diseño con la auditoría de Impeccable y SEO orientado a los cuatro públicos objetivo: proveedores, distribuidores, fábricas y freight forwarders. Reemplazar **tema y plugin de contenidos** y vaciar caché. No reimportar contenido.

Al entrar al administrador tras instalar, el plugin crea la página **Who we serve** (`/who-we-serve/`), solo si ese slug está libre, y la añade al menú principal después de *Services*. No modifica ninguna página existente.

## 1. Auditoría de diseño (Impeccable)

Herramienta: [Impeccable](https://github.com/pbakaus/impeccable) 4.7.0, detector determinista de 61 reglas, sobre las 28 rutas a 1440 y 390 px (54 pasadas válidas), más una revisión visual con sus criterios de *critique*, *audit* y *polish*. Criterio: **pulir sin rediseñar**; se conserva el mundo visual existente (verde Pelham, Arial/DM Sans, Georgia de énfasis en la home).

| Hallazgo | Alcance | Corrección |
|---|---|---|
| Tarjetas dentro de tarjetas | 44 casos, 34 páginas: respuesta rápida dentro del cuerpo del artículo, niveles LTL dentro de un panel, contacto, notas legales y guía de la cotización | El contenedor exterior pierde borde y fondo; queda una sola superficie elevada |
| Raya lateral de 4 px | 22 artículos (*The short answer*) | Panel tintado sin raya |
| Líneas de 90 a 155 caracteres | Legales, contacto, terminales, nota de plazos | Medida máxima de 72 caracteres en texto largo |
| H1 desproporcionados y sin escala común | 88/76/64/58/56 px según la plantilla; *Privacy policy* a 76 px | Escala única de 38 a 64 px para todas las cabeceras; la home conserva su hero |
| Saltos de encabezado (H1 → H3) | Servicios y Noticias | H2 de sección accesible y no visible |
| Contraste 4,3:1 | Enlace activo de la navegación de servicios | Color más oscuro (AA) |
| Numeral activo del proceso horizontal con contraste 1,1:1 | Home | Verde sólido |
| Palabras sueltas al final de párrafo | 72 casos, 34 páginas | `text-wrap: pretty` en el texto y `balance` en los títulos |
| Superficies del navegador sin tema | Selección de texto, casillas y radios | Selección verde suave y `accent-color` de marca |
| Our network: orden distinto al del footer y tercera tarjeta sola en otra fila | `/locations/` | Orden alfabético como el footer y tres columnas en escritorio |

Resultado: en las 10 pasadas re-escaneadas tras los cambios, los avisos de diseño pasan de 11 a 0 (tarjetas anidadas, raya lateral, H1 desproporcionado y saltos de encabezado).

**Conservado a propósito:** el titular de la home en Georgia cursiva («connected.») y el halo verde de fondo. El detector los marca como recursos frecuentes, pero forman parte de la identidad documentada en DESIGN.md desde 1.11/1.14.

## 2. Análisis SEO: web antigua frente a la nueva

**Web antigua (pelhamservices.com, WordPress):** sin meta description, 6 etiquetas H1 en la home, URLs `?page_id=` y `?p=`, sin `robots.txt` ni sitemap (responden 404). Contenido genérico («coast to coast», «bespoke logistics»), sin mencionar a ningún público concreto. Datos que conviene validar con el cliente antes de reutilizarlos: «flota local de más de 30 camiones», «final mile» y el equipo directivo (Gabriel Reverón, Dayana Garcia, Victor Delurintu, Regina Umaña).

**Web nueva:** buena base técnica (titles y descriptions únicos, canonicals, schema, redirecciones 301 de las URLs antiguas). Pero el público objetivo estaba poco trabajado:

| Público | Antes de 1.22 |
|---|---|
| Freight forwarders | Un artículo (Miami) y menciones en la home y en LTL |
| Proveedores, distribuidores y fábricas | Párrafos casi idénticos en About y LTL; ninguna página propia; FTL y cross-docking no los mencionaban |
| Formulario de cotización | No registraba qué tipo de empresa pedía el precio |

## 3. Cambios SEO en 1.22 / 1.17

- **Página central `/who-we-serve/`** — *Freight for Suppliers, Distributors & Forwarders | Pelham* (title de 57 caracteres, description de 146). Una sección por público con contenido propio, no clonado: qué suele enviar, qué servicio encaja, qué incluir en la cotización, una guía relacionada y un enlace a la cotización con el público preseleccionado. Incluye FAQ (sin envíos internacionales; cobertura en Florida) y foto de la sesión (dos personas revisando documentación de carga). Solo usa datos ya publicados: plazos LTL estimados de 3/7/10 días, cobertura y servicio doméstico.
- **Home:** sección *Who we ship for* con los cuatro públicos, tras las filas de servicios. Se añade al mostrar la página, sin editar el contenido guardado; si la home se personalizó, no aparece.
- **FTL y cross-docking:** bloque «quién usa este servicio» con enlaces a cada público. LTL ya tenía el suyo.
- **Cotización:** campo opcional *Your business* (proveedor/mayorista, distribuidor, fábrica, forwarder u otro) que llega en el correo al equipo y se preselecciona desde los enlaces `?business=`. Valores en lista cerrada, validados en servidor.
- **Schema:** cada `Service` declara `audience` (`BusinessAudience`) con los cuatro públicos.
- **Menú y footer:** «Who we serve» en el menú principal, tras Services. En el menú de WordPress se añade una sola vez y sin reordenar el resto; también en la lista de reserva y en el footer.
- **Migración:** nueva redirección `?cat=1` → `/news/`. La home antigua enlazaba esa categoría y no estaba en el mapa.

## 4. Recomendaciones fuera del código

1. Al migrar a pelhamservices.com: retirar el `noindex` de staging solo en producción, enviar `/wp-sitemap.xml` a Search Console y comprobar las 11 redirecciones del mapa (`docs/seo-research-2026-10/redirect-map.csv`).
2. Medir en Search Console las consultas por público («ltl freight for freight forwarders», «pallet shipping for distributors», «freight for manufacturers california to florida») y ampliar cada sección con preguntas reales de clientes.
3. Confirmar con el cliente si se publican el equipo directivo y el tamaño de la flota (E-E-A-T); no se añadieron sin validar.
4. Revisar en el correo de cotizaciones qué público llega más (campo *Your business*) para priorizar contenido.

## Comprobaciones

PHP sin errores de sintaxis. Fragmentos de los públicos generados con el propio PHP del plugin. Vista previa: 29 rutas (incluida `/who-we-serve/`) a 1440 y 390 px sin errores JS, 404, desbordamiento ni entradas bloqueadas. No probado sobre WordPress real: revisar en staging la creación de la página, el menú y el envío de una cotización con *Your business*.
