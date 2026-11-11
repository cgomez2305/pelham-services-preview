# SEO local: tema 1.27.0 / contenido 1.22.0

Tema **1.27.0** · Pelham Content **1.22.0** · Pelham Applications **1.7.2** (sin cambios)

Aplica la parte del [plan de SEO local](SEO-LOCAL-AUDIT-PLAN-2026-11.md) que no depende del cliente. **No cambia ninguna URL activa, title ni H1** de las páginas del sitio nuevo.

## Cambios

### 1. Redirecciones 301 (contenido, `inc/local-seo-1.22.php`)

| URL antigua (todas con 200 en producción el 11/11/2026, salvo la primera) | Destino | Motivo |
|---|---|---|
| `/contact-information/` (404) | `/locations/gardena-ca/` | Es la web que enlaza el Perfil de Empresa de Google de Gardena. Se conservan los parámetros `utm_*`. |
| `/contact-us/` | `/contact/` | Página antigua duplicada |
| `/team/` | `/about/` | Página antigua («Meet Our Team») |
| `/forms/` | `/register/` | Página antigua de descarga de PDF; el registro digital la sustituye |
| `/blog/` | `/news/` | Índice de entradas sin canonical ni description, con artículos EN y ES mezclados |
| `/news/hello-world/` y `/hello-world/` | `/news/how-pelham-elevates-logistics-in-ca-fl/` | Artículo antiguo con el mismo título que el reescrito |
| `/news/technology-transforms-last-mile-delivery-across-the-nation/` (y sin `/news/`) | `/news/navigating-challenges-in-us-logistics/` | Ídem |
| `/news/rising-demand-for-sustainable-freight-solutions-in-the-u-s/` (y sin `/news/`) | `/news/future-of-logistics-trends-pelham-is-embracing/` | Ídem |

- Funcionan aunque la página o entrada antigua siga publicada, y en cualquier dominio. No dependen de la casilla «legacy redirects».
- Solo GET/HEAD; nunca en vistas previas.
- Las rutas en español (`/es/blog/`…) no se tocan.

### 2. Sitemap y noindex (contenido)

- Las entradas y páginas redirigidas salen de `wp-sitemap-posts-*.xml`.
- Se retiran los sitemaps de categorías y etiquetas.
- Los archivos de categoría y etiqueta pasan a `noindex, follow`: eran listados finos de artículos antiguos (por ejemplo, `/news/category/uncategorized/`).

### 3. Sección de tránsito en las páginas de ubicación (contenido)

Las tres sedes, en inglés y en español, tienen ahora una sección después de los datos de la sede:
- **Gardena:** «Shipping from Los Angeles to Florida», con los tres niveles.
- **Miami:** «Freight from Los Angeles to Miami», con Expedited, Regular y Economy.
- **Orlando:** «Freight from Los Angeles to Orlando», solo con Regular y Economy, porque Expedited cubre únicamente Homestead–West Palm Beach.

Cada nivel enlaza a la cotización con el nivel preseleccionado. Además hay enlaces a LTL, FTL y Cross-docking. Los plazos salen de `pelham_ltl_levels()` (los publicados), con la nota «estimados, no garantizados». No se publica ningún dato nuevo de la sede (horario, servicios en la instalación): eso queda pendiente del cliente.

Traducciones nuevas en `tools/i18n/ui-es.json`, compiladas con `build_dict.py`. De paso se añaden «Request Expedited/Regular/Economy», que salían en inglés en la página de LTL en español.

### 4. Schema (tema, `inc/seo.php`)

- **Organization:**
  - `name` «Pelham Services» (antes «Pelham»), con `legalName` «Pelham Services CA Corp» y `alternateName` «Pelham»;
  - logo actual del tema;
  - `telephone` y `contactPoint` (ventas, inglés/español).
- **WebSite:** `name` «Pelham Services». Es el nombre de sitio que Google puede mostrar.
- **LocalBusiness** de cada sede:
  - teléfono en formato E.164, `hasMap`, `image`, `areaServed` y `knowsAbout`;
  - Gardena enlaza su ficha de Google verificada en `hasMap` y `sameAs`. Miami y Orlando usan un enlace de Google Maps por dirección hasta que el cliente confirme su NAP. Se añaden en `pelham_terminal_profiles()`.
  - La página en español describe **la misma entidad** (mismo `@id` que la inglesa) en lugar de crear una segunda.
- No se usa `MovingCompany`: en schema.org es mudanzas de hogar, otro negocio.

### 5. H1 doble en `/es/contacto/` (tema, `functions.php`)

La página española se pintaba con la plantilla genérica, que añade un H1 encima del propio de la página. Ahora usa `page-contact.php`, igual que `/contact/`: un solo H1.

## Pruebas (WordPress Playground local, EN y ES)

- **Redirecciones:** las 11 rutas devuelven 301 al destino correcto. `/contact-information/?utm_source=google&utm_medium=organic&utm_campaign=gbp-gardena&x=1` conserva solo los UTM.
- **Archivos:** `/news/category/…` lleva `noindex`. El sitemap no se pudo comprobar en local porque el plugin lo desactiva fuera de `pelhamservices.com` (comportamiento previsto); hay que revisarlo en producción.
- **H1:** uno solo en `/contact/`, `/es/contacto/`, las seis páginas de sede, la home y About.
- **JSON-LD:** válido en 14 rutas. El `@id` de la sede es el mismo en EN y ES, y Orlando no muestra Expedited.
- **Visual:** sin desbordes a 1366 y 390 px. Sin errores de JavaScript. Capturas revisadas.
- **PHP:** `php -l` sin errores. El zip del plugin sigue sin archivos `.js`.

## Instalación

1. **Copia de seguridad** reciente (cPanel o plugin).
2. *Plugins → Subir*: `dist/pelham-content-1.22.0.zip` (reemplazar). *Apariencia → Temas → Subir*: `dist/pelham-modern-1.27.0.zip` (reemplazar).
3. Vaciar la caché de Sucuri.
4. Comprobar:
   - `https://pelhamservices.com/contact-information/` → 301 a Gardena;
   - `/wp-sitemap.xml` sin taxonomías;
   - `/news/hello-world/` → 301;
   - una sede, con la Prueba de resultados enriquecidos de Google.
5. Después, en el Perfil de Empresa de Gardena, cambiar el sitio web a `https://pelhamservices.com/locations/gardena-ca/?utm_source=google&utm_medium=organic&utm_campaign=gbp-gardena`.

## Medición de velocidad (Lighthouse de Data for SEO, escritorio, 11/11/2026)

| URL | Rendimiento | LCP | CLS | Respuesta del servidor |
|---|---:|---:|---:|---:|
| `/` | 93 | 1,05 s | 0 | 1,45 s |
| `/locations/gardena-ca/` | 95 | 0,93 s | 0,001 | 1,38 s |

Accesibilidad y SEO: 100. El punto débil es el tiempo de respuesta del servidor (~1,4 s; Sucuri devolvía `X-Sucuri-Cache: MISS`). Conviene revisar que Sucuri cachee el HTML de las páginas públicas. Pendiente: medición móvil y datos de campo (CrUX/Search Console).
