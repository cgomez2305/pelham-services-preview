# Auditoría de producción y plan de SEO local — noviembre 2026

Fecha: 11 de noviembre de 2026. Rama `claude/seo-local`. Producción: https://pelhamservices.com (tema 1.26.3, Pelham Content 1.21.13).
Este documento no cambia nada en producción. Todo cambio se aprueba antes, con copia de seguridad reciente.

## 1. Método

- Rastreo de las 66 URL de `wp-sitemap.xml` (6 sub-sitemaps): código HTTP, title, meta description, robots, canonical, hreflang, H1 y tipos JSON-LD.
- Contenido visible y schema de las páginas de ubicación.
- Redirecciones de host, `http` → `https` y URL antiguas.
- Fichas de Google Maps con DataForSEO Business Listings (consulta del 11/11/2026, datos actualizados por DataForSEO a finales de septiembre de 2026).
- Volumen de búsqueda local con Google Ads (EE. UU., inglés), una sola consulta.
- **No medido:** Core Web Vitals. La API pública de PageSpeed Insights devolvió «cuota diaria agotada». Pendiente: PSI con clave propia, Lighthouse de DataForSEO o el informe CWV de Search Console. Search Console y GA4 todavía no están conectados a esta sesión.

## 2. Estado técnico de producción

### Lo que está bien

| Elemento | Estado |
|---|---|
| Host canónico | `http://`, `www` → `https://pelhamservices.com/` con 301. |
| robots.txt | Bloquea solo `/wp-admin/` y declara `wp-sitemap.xml`. |
| Sitemap | `/sitemap.xml` redirige a `wp-sitemap.xml`. Incluye EN y ES, servicios y ubicaciones. |
| Canonical | Autorreferente en las 65 URL con canonical. |
| hreflang | `en-US` / `es-US` / `x-default` recíprocos en páginas principales, servicios, ubicaciones y artículos. |
| Titles y descriptions | Únicos y dentro de longitud en las páginas del sitio nuevo. |
| Schema | Organization, WebSite, WebPage, BreadcrumbList, Service con OfferCatalog en LTL, Article en artículos y LocalBusiness por ubicación. |
| Sucuri | Activo (`server: Sucuri/Cloudproxy`). |

### Problemas encontrados

| # | Problema | Evidencia | Impacto |
|---|---|---|---|
| T1 | **Contenido heredado indexable y duplicado** en el sitemap | `/news/hello-world/`, `/news/technology-transforms-last-mile-delivery-across-the-nation/` y `/news/rising-demand-for-sustainable-freight-solutions-in-the-u-s/` publican títulos y textos antiguos de artículos que ya se reescribieron en otras URL. Además están `/forms/` («Register»), `/contact-us/`, `/team/`, `/blog/` (sin canonical ni description; mezcla artículos EN/ES) y `/news/category/uncategorized/`. | Alto: canibalización y páginas finas que diluyen el sitio y pueden aparecer en Google en lugar de las nuevas. |
| T2 | `/es/contacto/` tiene **dos H1** iguales | Rastreo | Bajo |
| T3 | Organization con `name: "Pelham"`, logo antiguo (`cropped-pelham-black-1.png`), sin `legalName`, sin teléfono | JSON-LD | Medio: la entidad de marca no coincide con «Pelham Services» de las fichas. |
| T4 | El email del schema usa el dominio `pelhamservicesca.com` | JSON-LD | Bajo: confirmar que es el correo correcto. |
| T5 | CWV sin medir | Cuota de PSI | Desconocido |

## 3. Estado local: NAP y Perfiles de Empresa de Google

Hallazgo principal: **las fichas de Google no coinciden con la web.**

| Ficha en Google Maps | Dirección | Teléfono | Web enlazada | Categoría | Reseñas | Situación |
|---|---|---|---|---|---|---|
| **PELHAM SERVICES** (CID 10431901153798144411) | 249 E Redondo Beach Blvd, Gardena, CA 90248 | 323-201-4995 | `https://pelhamservices.com/contact-information/` → **404** | Shipping service | 0 · 1 foto · sin horario | Coincide con la web, salvo el nombre en mayúsculas. **El enlace está roto.** |
| **Pelham Services** (CID 3482934744328394879) | 5628 Bandini Blvd, **Bell**, CA 90201 | 323-201-4995 | www.pelhamservices.com | Logistics service | 4,2 ★ (5) · 18 fotos · L–V 9–17 | **Dirección que no aparece en la web.** Compite con Gardena por el mismo teléfono y la misma marca. |
| Pelham Services Ca Corp (CID 3745237245764500262) | 1022 San Julian St, Los Angeles, CA 90015 | — | — | — | — | «Cerrado permanentemente». Correcto si ya no se usa. |
| **Pelham Services** Miami (CID 1369684482569079900) | **8436 NW 72nd St**, Miami, FL 33166 | **305-805-9082** | www.pelhamservices.com | Logistics service + **Car repair** + **Food** | 3,9 ★ (41) · L–V 9–17 | **Dirección y teléfono distintos de la web** (8005 NW 80th St, Door 16 to 30 Suite #1; 323-201-4995). Categorías secundarias erróneas. |
| Orlando | — | — | — | — | — | **No se encontró ficha.** |

Consecuencias:
- Google ve tres «Pelham Services» en Los Ángeles con datos distintos. La ficha con más señales (Bell: fotos, reseñas y horario) no está en la web; la que coincide con la web (Gardena) está casi vacía y enlaza a un 404.
- En Miami, la ficha con 41 reseñas y la web no coinciden: la confianza en la dirección es baja para Google y para el cliente que llega en camión.
- La web usa el 323 (Los Ángeles) en las tres sedes. Para el SEO local de Miami y Orlando conviene un número local por sede, si existe.

**Preguntas para el cliente (bloquean la parte local):**
1. ¿Bell (5628 Bandini Blvd) sigue operando? Si no, ¿se trasladó a Gardena? → Marcar la ficha de Bell como trasladada a Gardena (o cerrada) y quedarnos con una sola ficha en LA, conservando reseñas y fotos si Google permite moverla.
2. Miami: ¿la dirección real es 8005 NW 80th St o 8436 NW 72nd St? ¿El 305-805-9082 sigue activo? Una de las dos fuentes debe corregirse.
3. Orlando: ¿hay personal y atención en horario en 9777 Satellite Blvd suite 130 (ubicación «Land Stand»)? Sin eso, Google no permite una ficha.
4. Horarios reales de cada sede (las fichas dicen L–V 9–17; la web no publica horario).
5. ¿Quién administra las fichas? Necesitamos acceso de administrador o que el propietario haga los cambios.

## 4. Palabras clave locales

Volumen de Google Ads EE. UU. (consulta del 11/11/2026). Volúmenes pequeños: son búsquedas de alta intención B2B, no tráfico masivo.

| Keyword | Búsquedas/mes | Página destino |
|---|---:|---|
| freight company miami | 70 (140–210 en ago–oct 2026) | `/locations/miami-fl/` + LTL |
| transloading los angeles | 30 | `/services/transloading/` + Gardena |
| ltl freight miami | 20 | LTL + Miami |
| cross docking los angeles | 10 | `/services/transloading/` + Gardena |
| trucking companies in gardena ca | 10 | `/locations/gardena-ca/` |
| pelham services / pelham services los angeles / pelham services miami | 50 cada una (Keyword Planner, oct. 2026) | Home / Gardena / Miami |
| cross docking miami, palletizing services los angeles, trucking company gardena, ltl carrier los angeles | sin dato | ubicaciones / servicios |

Del corredor (`docs/keyword-research-2026-10/`) se mantienen: `freight shipping from california to florida` (50), `ltl shipping los angeles to miami/orlando`, `pallet shipping los angeles to florida`, todas sin volumen informado pero con encaje comercial y destino ya definido (LTL). Gran parte de la demanda local se resuelve en **Google Maps**, no en orgánico: por eso las fichas pesan más que las páginas.

## 5. Plan de SEO local

### 5.1 Perfil de Empresa de Google (fuera de la web; lo hace el cliente o quien tenga acceso)

| Sede | Acción |
|---|---|
| Gardena / LA | 1) Unificar: una sola ficha activa en LA (decidir Bell). 2) Nombre «Pelham Services» (sin mayúsculas ni keywords). 3) Web: `https://pelhamservices.com/locations/gardena-ca/?utm_source=google&utm_medium=organic&utm_campaign=gbp-gardena`. 4) Categoría principal: probar «Trucking company» o «Freight forwarding service» frente a «Shipping service» y «Logistics service»; secundarias reales (Trucking company, Logistics service, Warehouse si aplica). 5) Horario, servicios (LTL, FTL, cross-docking, palletizing), descripción, 10+ fotos reales. |
| Miami | 1) Corregir la dirección o la web, según la respuesta del cliente. 2) Quitar «Car repair» y «Food». 3) Web: `/locations/miami-fl/` con UTM. 4) Responder las 41 reseñas, sobre todo las 7 de 1 ★. |
| Orlando | Crear y verificar la ficha solo si cumple los requisitos de Google (personal en horario). Si es un muelle compartido sin atención, **no** crear ficha: mantener la página de ubicación sin presentarla como oficina abierta. |
| Todas | Publicar una actualización al mes (rutas, salidas, consejos de preparación de pallets) y preguntas frecuentes reales. |

### 5.2 NAP y citas

1. Fuente única de NAP: la tabla corregida del punto 3, igual en la web, las fichas, el schema y los directorios.
2. Citas prioritarias (calidad antes que cantidad): Bing Places, Apple Business Connect, Facebook (ya enlazado), LinkedIn (ya enlazado), Yelp, BBB, FMCSA/SAFER (verificar que el registro USDOT/MC muestre la misma dirección), DAT o Truckstop si las usan, cámaras de comercio de Gardena y Miami (Doral/Medley) y asociaciones del sector (p. ej., FIATA local o asociaciones de agentes de carga de Miami, solo si son socios reales).
3. Auditar y limpiar citas antiguas con la dirección de Bell, San Julian St o el 305.

### 5.3 Reseñas

- Proceso: enlace corto de reseña de cada ficha en la firma de correo, en la factura y en el correo de entrega, sin incentivos ni filtrado.
- Objetivo realista: Gardena de 0 a 10 reseñas en 90 días; Miami, responder todo el historial y subir la media.
- Respuestas sin datos de envíos ni de clientes.

### 5.4 Páginas de ubicación (en la web; requiere cambio de plugin)

Hoy tienen ~55 palabras: dirección, teléfono y mapa. Propuesta, sin cambiar URL, title ni H1:
- Horario y cómo entregar o recoger (cita previa, muelles, documentos), con datos confirmados por operaciones.
- Servicios disponibles en esa sede (LTL, FTL, cross-docking, palletizing) con enlaces a cada servicio.
- Zona atendida: en Gardena, recogidas en el área de LA (South Bay, Vernon, Commerce, City of Industry… solo si el cliente las confirma); en Florida, entrega en Miami-Dade, Broward, Palm Beach y Central Florida, según los niveles LTL ya publicados.
- Tiempo de tránsito LA → esa ciudad (Expedited 3 días solo Homestead–West Palm Beach; Regular 7; Economy 10).
- 3–5 preguntas frecuentes locales y fotos reales de la sede.
- Botón «Ver en Google Maps» que enlace a la ficha verificada (refuerza la relación web–ficha).
- Las mismas mejoras en las páginas `/es/ubicaciones/…`.

### 5.5 Schema por ubicación (cambio en `pelham-modern/inc/seo.php`)

- Mantener `LocalBusiness`. **No** usar `MovingCompany`: en schema.org es mudanzas de hogar, otro negocio. Schema.org no tiene `FreightService`; añadir `additionalType` con la entidad de Wikidata/ProductOntology de «freight transport» y `knowsAbout`.
- Añadir `telephone` en formato E.164, `geo` (lat/long de la ficha), `hasMap` (URL de la ficha por CID), `openingHoursSpecification` (cuando el cliente confirme), `image`, `areaServed`, `makesOffer` → Service de LTL, FTL y cross-docking.
- La página ES debe referirse a la **misma entidad** (`@id` de la URL inglesa) en lugar de crear otra.
- Organization: `name` «Pelham Services», `legalName` «Pelham Services CA Corp», logo actual, `telephone`, `contactPoint` y `sameAs` con las URL de las fichas de Google.

### 5.6 Limpieza técnica relacionada (T1–T3)

| URL | Propuesta |
|---|---|
| `/news/hello-world/`, `/news/technology-transforms-…/`, `/news/rising-demand-…/` | 301 al artículo nuevo equivalente (comprobar antes en GSC si reciben clics o enlaces). |
| `/contact-us/` | 301 → `/contact/` |
| `/contact-information/` (destino de la ficha de Gardena) | 301 → `/locations/gardena-ca/` (además de corregir la ficha). |
| `/team/`, `/forms/` | 301 a `/about/` y al formulario de registro vigente, o `noindex` y fuera del sitemap si se usan internamente. |
| `/blog/` | 301 → `/news/` |
| `/news/category/uncategorized/` | Fuera del sitemap y `noindex`, o recategorizar. |
| `/es/contacto/` | Dejar un solo H1. |

## 6. Resumen priorizado

| Prioridad | Acción | Impacto | Esfuerzo | Quién |
|---|---|---|---|---|
| 1 | Redirigir `/contact-information/` → Gardena y corregir la URL de la ficha | Alto | Muy bajo | Web + cliente |
| 2 | Resolver NAP: Bell frente a Gardena, y dirección y teléfono de Miami | Muy alto | Bajo (decisión del cliente) | Cliente |
| 3 | Optimizar las fichas de Gardena y Miami (categorías, horario, fotos, UTM) | Alto | Bajo | Cliente / quien gestione GBP |
| 4 | Limpiar contenido heredado (301 / noindex, T1) | Alto | Bajo | Web (plugin + WPVibe) |
| 5 | Schema de Organization y LocalBusiness ampliado | Medio | Bajo–medio | Web (tema) |
| 6 | Ampliar las páginas de ubicación EN/ES | Medio–alto | Medio (requiere datos de operaciones) | Web + cliente |
| 7 | Proceso de reseñas | Alto a 3–6 meses | Bajo, pero continuo | Cliente |
| 8 | Citas en directorios y limpieza de NAP antiguo | Medio | Medio | SEO |
| 9 | Ficha de Orlando (si es elegible) | Medio | Medio | Cliente |
| 10 | Medir CWV y conectar GSC/GA4 para la línea base | Necesario para medir | Bajo | Usuario conecta las cuentas |

## 7. Estado

- **Hecho en código (tema 1.27.0 / contenido 1.22.0, sin desplegar):** puntos 1 (redirección `/contact-information/`), 4 (contenido heredado), 5 (schema), T2 (H1 doble) y la parte de 5.4 que no depende del cliente (tránsito por sede). Medición de escritorio de CWV hecha. Detalle en [RELEASE-1.27-SEO-LOCAL.md](RELEASE-1.27-SEO-LOCAL.md).
- **Pendiente del cliente:** puntos 2, 3, 7, 8 y 9, y horarios y servicios por sede para completar 5.4.

## 8. Siguiente paso

No se ha hecho ningún cambio en producción ni en el código. Para cada cambio en producción: aprobación previa y copia de seguridad reciente confirmada; después, vaciar la caché de Sucuri.
