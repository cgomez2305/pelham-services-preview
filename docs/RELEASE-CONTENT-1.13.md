# Pelham Content 1.13.0 — cobertura completa del research relevante

Revisión editorial del 14 de octubre de 2026 (America/Bogota). Fuente: ocho archivos de Keyword Planner del cliente; Estados Unidos e inglés confirmados.

## Resultado

Se revisaron las 96 keywords relevantes, una por una. 93 se trabajan por intención, 2 se responden con alcance limitado a los servicios confirmados y 1 queda pendiente. No equivale a 95 coincidencias literales, páginas nuevas, posiciones ni búsquedas garantizadas.

- **93 implementadas por intención:** rutas LTL, destinos, comparación LTL/FTL, tarifas/costes, FTL, públicos B2B, marca e instalaciones.
- **`pelham services tracking`:** ayuda para solicitar una actualización por contacto, con referencia del envío; no se promete un buscador público ni funciones desconocidas de Supply Vision.
- **`ltl freight forwarding`:** explica que Pelham hace el tramo doméstico hasta el receptor. No se anuncia forwarding internacional.
- **`pelham services reviews`:** pendiente. El cliente confirmó que no hay enlaces de reseñas ni testimonios autorizados disponibles. No se insertaron testimonios, puntuaciones ni marcado de valoraciones.

La matriz CSV contiene una fila por keyword: estado, página, sección, title, meta description, motivo, evidencia textual y si aparece literalmente o como variante semántica. No se trató una mera asignación como cobertura implementada: las secciones existen en el contenido y se verifican contra WordPress.

## Doce destinos actualizados

1. Home: identidad Pelham, actividad y enlace directo a LTL; title/meta de marca y corredor.
2. LTL: ruta California–Florida, Miami/South Florida, Orlando, Tampa y Jacksonville; origen real Los Ángeles, cobertura frente a instalaciones, públicos B2B y enlaces.
3. FTL: camión exclusivo California–Florida/LA–Miami, información para tarifas y CTA específico. Sin atribuirle los tiempos LTL.
4. Cotización: datos necesarios para tarifas LTL y FTL, ruta y enlaces a guías. Se conserva el formulario existente.
5. Contacto: ayuda para envíos existentes y corrección de «Two terminals» a tres instalaciones.
6. About: business-to-business, proveedores, fábricas, distribuidores y mayoristas.
7. Gardena/Los Ángeles: identidad compartida, dirección confirmada y conexión con los servicios.
8. Miami: dirección/puertas/suite y teléfono confirmados; diferencia entre la instalación y otro receptor.
9. Orlando (Land Stand): dirección confirmada, Regular/Economy y exclusión de Expedited. Sin inventar horarios o teléfono.
10. Comparación LTL/FTL: definiciones, diferencias, cuándo elegir y comparación de tarifas.
11. Coste de pallets: una o varias paletas, LA–Miami y factores de precio sin tarifas ficticias.
12. Entrega a forwarders: papel de Pelham como transportista doméstico y coordinación de receptor.

Los doce titles y metadescripciones son distintos y se revisaron junto al contenido. Las variantes se agrupan; no se crean páginas vacías por ciudad, ni se cambia la estructura de permalinks de WordPress.

## Instalar

1. Instalar `dist/pelham-content-1.13.0.zip`, sustituyendo Pelham Content. Mantener el theme 1.17.1 y el plugin de aplicaciones actual.
2. Completar primero las actualizaciones antiguas pendientes de diseño/blog/instalaciones, si las hubiera.
3. En **Pelham Company**, pulsar **Complete keyword coverage (save backups)**. Incluye la cobertura anterior; no hace falta ejecutar aparte el botón 1.12.
4. Purgar caché y revisar los doce destinos.

El botón sustituye el texto, título, extracto y SEO de estas páginas; guarda respaldo en `pelham_before_keywords_1_13`. Conserva URLs, fechas de publicación, estado, autor e imagen destacada. Crea las dos guías de 1.12 si faltan y no hay colisión con otro contenido. Usa la fecha actual de WordPress para nuevas publicaciones. Puede reintentarse tras un fallo sin repetir páginas completadas. No incluye un botón de restauración automática.

No se instaló esta versión en Hostinger desde este entorno. Staging sigue noindex. El despliegue y la indexación son distintos de la implementación local.

## Validación

`python tools/build-keyword-coverage.py` genera la matriz y comprueba las 96 filas y sus secciones de origen.

`node tools/test-keyword-coverage-1.13.cjs` usa un WordPress local y la fixture exclusiva de QA `tools/fixtures/coverage-qa.php` (no se incluye en el ZIP). Prueba actualización administrativa, nonce/permisos, respaldo y preservación de datos, reejecución, contenido renderizado de las 95 keywords, metadatos/canonical/H1/schema/noindex y enlaces internos. Capturas a 1440 y 390 px verifican composición y ausencia de desbordamiento; movimiento reducido, no prueba de animación.

Los resultados y capturas se guardan en `docs/audit/coverage-1.13/`. La cobertura semántica es una decisión editorial documentada; ninguna prueba automática garantiza relevancia o posiciones en Google.

## Seguimiento

Se mantiene el plan a 30/60/90 días de `docs/keyword-research-2026-10/PLAN-EJECUCION.md`: publicar en el dominio definitivo, configurar Search Console, medir consultas/páginas y cruzar con cotizaciones calificadas. Revisar especialmente las búsquedas ambiguas de marca y forwarding. No se inventaron KD orgánica ni métricas de competidores.
