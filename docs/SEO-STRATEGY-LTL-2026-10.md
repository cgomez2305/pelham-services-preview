# Estrategia SEO de Pelham: LTL Los Ángeles → Florida

Fecha: 14 de octubre de 2026. Documento de planificación en español; contenido público propuesto en inglés. Responsable de aprobación comercial: Pelham. La ejecución externa requiere acceso a producción, Search Console y perfiles locales.

## Objetivo y límites de esta revisión

Generar solicitudes de cotización calificadas para transportar paletas desde proveedores de Los Ángeles hacia negocios y transportadoras en Florida. Medir oportunidades aceptadas por ventas y clientes adquiridos, no solo visitas o posiciones.

Esta estrategia se basa en la oferta confirmada por el cliente y una inspección del repositorio. No incluye investigación en vivo de resultados de Google, volúmenes, dificultad, rankings, competencia o enlaces. Las palabras clave son hipótesis comerciales priorizadas, pendientes de validar con Search Console y herramientas de investigación. No se prometen posiciones ni resultados en 90 días.

Este documento sustituye, para la planificación comercial actual, las premisas de `seo-strategy.md` en la raíz: allí todavía se mencionan cinco instalaciones y terminología anterior. No usar ese documento antiguo como prueba de implementación o de cobertura.

## Oferta que debe mantenerse consistente

| Oferta | Tránsito estimado | Condición de destino |
|---|---|---|
| LTL Expedited | 3 días calendario desde recogida | Sur de Florida: Homestead a West Palm Beach |
| LTL Regular | 7 días calendario desde recogida | Florida |
| LTL Economy | 10 días calendario desde recogida | Florida; opción más económica |
| FTL | Se confirma al cotizar | Capacidad exclusiva; no hereda los plazos de LTL |
| Cross-docking, transloading y paletización | Según operación | Servicios complementarios; confirmar instalación y manejo |

Los plazos no son garantizados. Pelham realiza transporte terrestre doméstico y entrega a empresas, incluidas transportadoras que se encargan de la continuación internacional. No anunciar transporte marítimo, despacho aduanero, servicios ferroviarios, recogida portuaria ni envíos internacionales de Pelham. No convertir “economy” en una promesa de ser la empresa más barata del mercado. No anunciar trayecto inverso Florida → California sin confirmación.

Instalaciones operadas confirmadas por el cliente: Los Ángeles, Orlando y Miami. Confirmar dirección de Orlando y correspondencia entre Los Ángeles y la ficha existente de Gardena antes de publicar direcciones, coordenadas, horarios o perfiles locales. Ciudad atendida e instalación operada no son equivalentes.

## Auditoría técnica del repositorio: qué existe y qué verificar

| Evidencia inspeccionada | Estado observado en código | Acción y criterio de aceptación |
|---|---|---|
| `wordpress/pelham-modern/inc/seo.php` | Títulos y descripciones personalizados; Open Graph, Twitter y JSON-LD; se retira si detecta Yoast, Rank Math, AIOSEO o SEOPress | Elegir un único propietario SEO. Si se activa otro plugin, migrar los campos `pelham_seo_*`; no asumir que los importa. Revisar HTML renderizado: un title, una descripción y una canonical coherentes |
| Mismo archivo | Organization, WebSite, WebPage, BreadcrumbList, Service, LocalBusiness y Article | Validar datos visibles contra schema. No añadir ratings, precios o garantías inexistentes. No publicar LocalBusiness con dirección inventada o incompleta |
| Mismo archivo | Canonical de archivos; singulares dependen de WordPress | Comprobar canonical efectiva en producción, paginación y URLs con parámetros. En páginas distintas de paginación no canonicalizar todas hacia la primera |
| Mismo archivo | Algunos textos fallback mantienen oferta anterior | Actualizar los fallback junto al contenido real; revisar metadatos de páginas ya guardadas y overrides manuales |
| `wordpress/pelham-content/data/pages.json` y `inc/offer-1.9.php` | Datos importables y módulo de oferta en desarrollo | Verificar actualización en WordPress instalado; cambiar JSON o ZIP no demuestra que el contenido almacenado haya cambiado |
| `wordpress/pelham-content/pelham-content.php` | Servicios e instalaciones son tipos públicos; rutas existentes preservables | Mantener slugs actuales y validar sitemap generado por WordPress o plugin SEO |
| `robots.txt` y `sitemap.xml` de raíz | Archivos de la versión estática con dominio `pelhamservices.com` y listado fijo | No confundir con robots/sitemap de WordPress. Comprobar el sitemap realmente servido, sin páginas privadas, duplicadas, antiguas o inexistentes |
| `wordpress/pelham-applications/inc/security.php` y `api.php` | Registro/reclamaciones y respuestas API privadas incorporan noindex/no-store | Mantener protección; comprobar que expedientes, enlaces privados, firmas y PDF no entren en buscadores, caché ni analítica. Noindex no sustituye autorización |
| Búsqueda en PHP/JS del theme y plugins | No se localizó instrumentación GA4/dataLayer en esos archivos | Comprobar configuración real: podría existir vía otro plugin/GTM. No instalar duplicados |

Esta tabla es evidencia de código, no certificación del servidor Hostinger. Tras desplegar: rastrear todas las URLs públicas y comprobar HTTP, canonicals, indexabilidad, enlaces, títulos, headings y datos estructurados.

### Producción frente a staging: prioridad 0

Confirmar por escrito el dominio de producción. `pelhamservices.com` es la marca pública conocida y `deeppink-bear-572523.hostingersite.com` se ha usado para pruebas; esta revisión no prueba su configuración actual.

1. Mantener staging fuera del índice, preferiblemente con autenticación de acceso y noindex. `robots.txt Disallow` por sí solo no evita indexación de URLs ni permite leer un noindex si el rastreo está bloqueado.
2. En producción, comprobar que no se arrastre la opción de WordPress de disuadir buscadores, headers noindex o protección del staging.
3. Verificar dominio HTTPS, una versión canónica de host, URLs internas de producción, imágenes y formularios sin enlaces al staging.
4. Inventariar URLs del sitio anterior antes de reemplazarlo. Conservar rutas actuales; si desaparece una URL con equivalente, redirigir 301 al equivalente concreto. No redirigir todo a home. Investigar rutas antiguas `?page_id=` antes de escribir reglas globales.
5. Enviar únicamente el sitemap público correcto a Search Console y revisar muestras de URLs. No declarar terminada una migración por haber subido los ZIP.

## Arquitectura y mapa de palabras clave

Prioridad P1: intención de compra directamente relacionada con el servicio principal. P2: servicios complementarios, confianza y educación. Sin volúmenes inventados. Una URL principal por intención; variantes similares se trabajan dentro de la misma página.

| Prioridad | URL conservada | Intención / términos candidatos en inglés | Contenido y acción |
|---|---|---|---|
| P1 | `/` | Pelham Services; Los Angeles to Florida freight shipping; California to Florida trucking | Oferta general de marca y corredor; LTL protagonista; enlace a comparación y cotización |
| P1 | `/services/freight-consolidation/` | LTL shipping Los Angeles to Florida; pallet shipping California to Florida; LTL Los Angeles to Miami | Página principal LTL: tres niveles, cobertura, paletas, proceso, datos necesarios y CTA |
| P1 | misma página, sección de niveles | expedited LTL Los Angeles to South Florida; economy LTL shipping to Florida | Expedited 3 días solo Homestead–West Palm Beach; Regular 7 y Economy 10 a Florida; aclaración junto a tabla |
| P1 | `/quote/` | LTL freight quote Los Angeles to Florida; pallet shipping quote | Formulario guiado con origen/destino, carga y nivel de servicio; envío exitoso comprobable |
| P2 | `/services/` | Pelham freight services; LTL and FTL trucking services | Comparador de servicios y navegación; no duplicar todo el texto de LTL |
| P2 | `/services/dedicated-transport/` | full truckload Los Angeles to Florida; FTL California to Florida | Capacidad exclusiva, cuándo elegirla y solicitud de propuesta |
| P2 | `/services/transloading/` | cross docking services Los Angeles; palletizing services Los Angeles; transloading Los Angeles | Explicar operaciones reales; diferenciar preparación/transferencia y tramo terrestre. Ubicaciones específicas solo si confirmadas |
| P2 | `/locations/` | Pelham locations; Los Angeles Orlando Miami freight facilities | Tres instalaciones operadas; enlaces y datos verificados, sin inventar fichas |
| P2 | `/locations/gardena-ca/` | Pelham Gardena; trucking company Gardena; Los Angeles freight pickup | Datos de ubicación y recogida; confirmar identidad/dirección y alcance local |
| P2 | `/locations/miami-fl/` | Pelham Miami; Miami freight delivery; California freight to Miami | Operación local y entrega doméstica; evitar oferta inversa o portuaria |
| P2 | `/about/` y `/contact/` | Pelham trucking company; Pelham Services contact | Equipo, fotos reales, trayectoria comprobada, teléfonos y canal comercial |
| P2 | `/news/ltl-vs-ftl-california-florida/` o artículo equivalente existente | LTL vs FTL California Florida | Ayudar a elegir y enlazar ambos servicios; confirmar slug real antes de publicar enlaces |

No crear ahora decenas de páginas de ciudad cambiando únicamente el nombre: sería contenido duplicado y poco útil. Una página específica de Orlando solo cuando haya dirección y contenido local verificable. No separar todavía Expedited, Regular y Economy en tres páginas casi idénticas; usar secciones enlazables y validar demanda antes de ampliar.

### Títulos y snippets propuestos, sujetos a verificar duplicados

- Home: `Los Angeles to Florida Freight Shipping | Pelham Services`.
- LTL: `LTL Shipping from Los Angeles to Florida | Pelham Services`.
- FTL: `Full Truckload: Los Angeles to Florida | Pelham Services`.
- Transferencias: `Cross-Docking & Palletizing | Pelham Services`.
- Cotización: `Request a Los Angeles–Florida Freight Quote | Pelham`.
- Descripción LTL: `Ship pallets from Los Angeles to Florida. Compare 3-day South Florida expedited, 7-day regular and 10-day economy LTL options. Transit times are estimates.`

No sacrificar claridad por un límite rígido de caracteres; comprobar apariencia en escritorio/móvil y no prometer que Google mostrará exactamente el snippet. El H1 debe explicar el servicio, no repetir una lista de keywords. Los términos LTL y less-than-truckload aparecen naturalmente con una explicación para quienes envían por primera vez.

## Contenido y enlaces internos

La home enlaza a LTL con prioridad visual, a sus niveles y a cotización. LTL enlaza a FTL como alternativa de capacidad, a preparación de pallets, ubicaciones y solicitud. Cada ubicación enlaza a los servicios realmente disponibles allí. Artículos enlazan al servicio pertinente y a un CTA contextual; evitar insertar todos los enlaces en todos los párrafos.

Priorizar actualización de artículos existentes sobre creación duplicada:

| Orden | Pieza / pregunta del cliente | Resultado útil | Revisión necesaria |
|---|---|---|---|
| 1 | Cómo elegir Expedited, Regular o Economy | Comparador de plazos y alcance con ejemplos sin fechas garantizadas | Ventas confirma restricciones operativas |
| 2 | Qué incluir en una cotización de paletas LA → Florida | Cantidad, peso, medidas, recogida/destino y necesidades de entrega | Revisar formulario y datos realmente solicitados |
| 3 | LTL frente a FTL: cuándo compartir camión | Criterios de decisión sin umbrales de precio o capacidad inventados | Operaciones |
| 4 | Entrega a una transportadora en Miami | Explicar dónde termina el servicio doméstico de Pelham y coordinación del receptor | Ventas; sin convertirlo en asesoría aduanera |
| 5 | Preparar paletas para transporte terrestre | Checklist revisado por operaciones; explicar paletización disponible | No inventar aceptación de mercancías/equipos |
| 6 | Cross-docking y transloading: qué ocurre con tu mercancía | Proceso real con fotos propias y límites operativos | Confirmar instalación donde se realiza |

Usar fotos reales con permiso y descripciones de lo que muestran. Nombre de archivo descriptivo y alt útil; imágenes decorativas con alt vacío. No rellenar alt con keywords. No publicar testimonios, clientes, métricas de puntualidad o casos de éxito sin autorización y evidencia. Las fechas deben corresponder a publicación/actualización reales.

Inglés como primera versión. Evaluar español según consultas y clientes reales; si se añade, crear traducciones completas con URLs propias, hreflang recíproco y canonicals por idioma. No usar redirección obligatoria por idioma ni anunciar Colombia/Venezuela/Costa Rica como rutas propias.

## SEO local y autoridad

1. Inventariar perfiles de Google Business Profile existentes y evitar duplicados. Cada instalación requiere comprobar elegibilidad, presencia real, personal, horarios y atención; ser una ciudad de cobertura no justifica una ficha.
2. Confirmar nombre comercial, dirección, teléfono y horarios de cada sede. No cambiar el nombre de marca para introducir keywords. Mantener estos datos coherentes en web y perfiles.
3. No crear dirección ficticia de Orlando. Mientras se confirma, mencionar la operación aprobada sin mapa ni PostalAddress inventado.
4. Subir fotos reales de equipo, instalaciones y manejo; documentar aprobación del cliente y fecha. Elegir categorías disponibles que representen la actividad comprobada.
5. Solicitar reseñas a clientes reales después del servicio sin incentivos ni filtrado de usuarios satisfechos. Responder sin divulgar información de envíos.
6. Conseguir menciones útiles de asociaciones, proveedores y socios reales. Revisar elegibilidad y calidad antes de pagar un directorio; evitar paquetes de enlaces. No se han investigado backlinks actuales en este trabajo.

## Conversión, medición y privacidad

Primero establecer una línea base de 28 días (o todo el historial disponible); después comparar periodos equivalentes considerando estacionalidad. Configurar Search Console de producción con propiedad adecuada y responsables del cliente. Validar GA4 u otra herramienta con el responsable de privacidad y la configuración de consentimiento aplicable; no dar por hecho que ya está instalada.

| Evento propuesto | Cuándo ocurre | Parámetros permitidos |
|---|---|---|
| `quote_start` | Primera interacción real con cotización | Servicio, página de origen categorizada |
| `ltl_tier_select` | Selección de nivel | expedited / regular / economy / unsure |
| `quote_submit_success` | Backend confirma recepción | Servicio, nivel; un envío no es una reserva |
| `quote_submit_error` | Error validado | Código de error genérico, nunca contenido del campo |
| `contact_click` | Clic en teléfono o email | Tipo de canal, ubicación categorizada |
| Calificación comercial | Ventas revisa la solicitud | Registro interno de válido / cotizado / ganado; exportación agregada |

No enviar a analítica nombres, emails, teléfonos, direcciones exactas, ZIP de cliente, mensajes, firmas, documentos, tokens o enlaces privados. No grabar sesiones del registro/reclamaciones ni capturar campos del formulario. Excluir URLs privadas y parámetros sensibles antes de instrumentar pageviews. Evitar etiquetas que se disparen en administración. Un clic al teléfono no demuestra una llamada atendida.

KPIs mensuales: impresiones/clics orgánicos por página e intención; consultas no asociadas a marca; solicitudes recibidas y calificadas por servicio; tasa de cotización; tasa de cierre y valor agregado cuando ventas lo pueda registrar; errores/abandono del formulario; cobertura de índice y Core Web Vitals. La atribución incompleta debe declararse. No fijar un objetivo numérico de leads hasta conocer línea base, margen y capacidad de atención.

## Plan de ejecución de 90 días

| Periodo | Prioridad y responsable | Entregable / aceptación |
|---|---|---|
| Días 1–7 | P0 — desarrollo + cliente | Dominio y staging definidos; oferta consistente en contenido, metadata y formularios; direcciones pendientes señaladas internamente; revisión del módulo de actualización en copia de WordPress |
| Días 8–14 | P0 — desarrollo + SEO | Rastreo de producción, sitemap correcto, canonicals/indexación y redirecciones verificadas; pruebas móviles de cotización; Search Console conectado sin publicar credenciales |
| Días 15–30 | P1 — SEO + ventas | Baseline, mapa de consultas validado con datos reales, páginas LTL/FTL/transferencias completas; primera guía actualizada; instrumentación sin datos privados; ventas registra calidad de leads |
| Días 31–45 | P1 — cliente + SEO local | Fichas elegibles y datos consistentes; fotos aprobadas; guía de preparación y comparación de niveles; revisión de búsquedas reales para corregir intención |
| Días 46–60 | P1 — SEO + operaciones | Dos contenidos útiles adicionales, enlaces internos revisados; contacto con socios reales para menciones; primera evaluación de conversiones y preguntas frecuentes de ventas |
| Días 61–90 | P2 — SEO + desarrollo + ventas | Comparación con baseline, ajustes de snippets/conversión, auditoría técnica de seguimiento; decidir si merece página Orlando, contenido en español o páginas de ruta adicionales |

Los 90 días se cuentan desde el inicio real de la ejecución, no desde una supuesta publicación de esta estrategia. Dependencias externas (accesos, verificaciones de perfiles, confirmación de direcciones) no deben detener correcciones internas de contenido.

### QA antes de publicar y al terminar cada fase

- Revisar home, servicios, cada servicio, ubicaciones, artículo, contacto y cotización en móvil/escritorio: texto visible, enlaces funcionales y formulario usable con teclado.
- Medir LCP, INP y CLS con datos de campo si existen (objetivos orientativos en percentil 75: LCP ≤2.5 s, INP ≤200 ms, CLS ≤0.1). Lighthouse es diagnóstico de laboratorio, no prueba de resultados reales.
- Las animaciones no deben esconder contenido si falla JavaScript; respetar reduced-motion y evitar imágenes grandes/efectos que bloqueen la interacción.
- Comprobar una sola canonical válida por página, descripciones coherentes, ausencia de garantías accidentales y disponibilidad de contenido HTML para rastreo.
- Revisar cobertura de Expedited tanto en tabla como en cotización; no atribuir sus 3 días a Orlando ni al resto del estado.
- Probar recepción de cotizaciones en entorno controlado y confirmar entrega de correos por separado; éxito de `wp_mail` no prueba llegada a bandeja.
- Documentar resultados, URL/versión probada y evidencia. La validación local no certifica producción.

## Información que falta para avanzar externamente

1. Dominio final y accesos delegados a Search Console/perfiles locales, sin compartir contraseñas en documentos.
2. Dirección y horarios verificables de Orlando; confirmar Los Ángeles/Gardena y datos actuales de Miami.
3. Zonas exactas de recogida en California y requisitos de entrega que afecten cotización (no asumir recogida en todo el estado).
4. Condiciones operativas adicionales: cortes de recogida, días de salida, restricciones y qué ocurre en fines de semana/festivos, sin alterar los plazos calendario ya confirmados.
5. Responsable comercial y mecanismo interno para distinguir lead válido, cotizado y ganado.

Se puede ejecutar la actualización comercial confirmada mientras se recogen estos datos. Los puntos pendientes no autorizan a inventar información ni a mantener afirmaciones antiguas incompatibles.

## Actualización posterior del cliente

Las tres direcciones ya fueron facilitadas y se incorporan en [la entrega 1.11](RELEASE-CONTENT-1.11.md), incluida la correspondencia Los Ángeles/Gardena. Siguen pendientes los horarios, el teléfono de Orlando y la creación/verificación de fichas de Google. El [calendario editorial](BLOG-EDITORIAL-PLAN.md) concreta las próximas piezas.
