# Pelham 1.20 — tema 1.20.0 / contenido 1.15.1

Reemplazar el tema y vaciar caché. El plugin de contenidos no cambia respecto a 1.15.1. No reimportar contenido.

## Animaciones de scroll

Detalle técnico en [DESIGN.md › Scroll 1.20](../DESIGN.md).

- **Proceso horizontal en la home.** En escritorio, la sección «A clear plan. A familiar voice.» queda fijada y sus tres pasos avanzan horizontalmente al hacer scroll, con un carril CA → FL que se llena, marca cada parada y numera «01 / 03». En móvil, tablet vertical, pantallas bajas, movimiento reducido o pausa se muestran los pasos verticales de siempre.
- **Días de tránsito animados.** Expedited, Regular y Economy cuentan hasta 3, 7 y 10 días al aparecer.
- **Fotos con profundidad.** Desplazamiento suave dentro del marco al hacer scroll (CSS nativo, sin JavaScript; los navegadores sin soporte muestran la foto fija).
- **Progreso de lectura** en la cabecera de los artículos.

Se mantiene la decisión de 1.15 de no usar máscaras en títulos ni fotos.

## Rendimiento y mantenimiento

- **Una sola hoja de estilos.** Las nueve capas CSS (`site`, `experience`, `stories`, `refined`, `industrial`, `complete`, `restructure`, `usability`, `design-system`) pasan a `assets/theme.css`, concatenadas en su orden original: de 9 peticiones bloqueantes a 1. Comprobado píxel a píxel: 28 rutas × escritorio y móvil idénticas antes de añadir las novedades. El editor de bloques usa la misma hoja.
- **Librerías sin uso eliminadas:** GSAP, ScrollTrigger, SplitText, DrawSVG, MotionPath y Lenis (~170 KB que viajaban en el ZIP sin cargarse).
- `tools/audit-design-system.cjs` comprueba ahora `theme.css`. Las pruebas históricas `test-motion-*` de 1.10–1.13 describen el sistema con GSAP de entonces y no aplican.

## Comprobaciones

Vista previa estática, 28 rutas a 1440 y 390 px: sin errores JavaScript, recursos 404, desbordamiento horizontal ni entradas bloqueadas. Proceso horizontal: activación a ≥ 901 px, desactivación a 800 px, móvil y movimiento reducido; pausa y reanudación; el carril llega al borde exacto sin barra horizontal (incluido el `scrollbar-gutter`). Contadores: 0 → valor final; valor final inmediato con movimiento reducido. No probado sobre WordPress real: revisar en staging, sobre todo la home en escritorio.
