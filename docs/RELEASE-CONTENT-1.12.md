# Pelham Content 1.12.0 — keyword research aplicado

Incluye cuatro actualizaciones editoriales y dos guías nuevas basadas en el research del cliente: LTL California–Florida, comparación LTL/FTL, coste de pallets y entregas domésticas a freight forwarders. Se conservan los servicios confirmados, los límites de Expedited y el carácter estimado de los tiempos.

## Instalar

Reemplazar el plugin con `dist/pelham-content-1.12.0.zip`. Mantener el theme 1.17.1 y el plugin de aplicaciones actual. En **Pelham Company**, completar primero las actualizaciones anteriores pendientes y pulsar **Apply keyword content (save backups)**. Purgar caché.

La acción sustituye texto/título/extracto/SEO de cuatro contenidos, guardando respaldo. Conserva slug, fecha de publicación, autor, estado e imagen destacada. Publica dos artículos nuevos con la fecha de WordPress. Detecta conflictos con contenido ajeno en los slugs nuevos y no lo sustituye. Una segunda ejecución no repite cambios. Los nuevos artículos no se añaden a la actualización antigua de nueve artículos.

Los enlaces internos editoriales respetan la estructura real de permalinks de WordPress. No se cambia esa configuración global. La vista previa estática adapta esos enlaces a sus propias rutas y mantiene formularios desactivados y noindex.

## Verificación

`node tools/test-keywords-1.12.cjs` contra WordPress local, con la fixture de `tools/fixtures/keywords-qa.php` montada exclusivamente para QA:

- Acción administrativa y respaldo comprobados; rechazo de petición anónima y sin nonce.
- Cuatro contenidos mantienen identidad, publicación, estado, autor e imagen; dos nuevos publicados.
- Reejecución sin cambios ni duplicados.
- Seis páginas: H1 único, título y descripción esperados, canonical correspondiente al permalink real, JSON-LD válido y noindex de staging.
- 18 enlaces internos comprobados; ambas guías visibles en Insights.
- 12 capturas a 1440 y 390 px, sin desbordamiento horizontal ni errores JavaScript. Revisadas visualmente la guía de costes en escritorio y la de receptores en móvil. Movimiento reducido para la prueba de composición; no es una auditoría de animaciones.

La fixture de QA no se incluye en el ZIP. No se ha instalado esta versión remotamente en Hostinger. Search Console y analítica siguen pendientes de configuración; no se afirma indexación, posiciones ni conversiones.

Plan: `docs/keyword-research-2026-10/PLAN-EJECUCION.md`.
