# Pelham 1.11 — nuevo motor de entradas y corrección de espacios

## Problemas corregidos

- Una regla específica de la primera fila de servicios anulaba el padding común. Se elimina esa excepción y se mantiene el mismo inset en las tres filas.
- La regla `.news-hub .news-grid` heredada tenía gap vertical cero y vencía al token genérico. Se corrige la fuente, con 24 px en escritorio y 20 px móvil. Las acciones de cada fila quedan alineadas.
- Se retiran animaciones CSS continuas heredadas (rutas, pulsos y elementos de transporte), además de las entradas anteriores.
- `site.js` mantenía una rutina de escenas con actualizaciones de clases/estilos por scroll, ResizeObserver y marcador de ruta, junto al motor de entrada. Se retira esa rutina, además del envío de formulario duplicado que ya gestiona editorial.js.
- Header ajustado a la barra de administración WordPress.

## Nuevo motor

Web Animations API e IntersectionObserver, sin GSAP/Lenis ni bucles propios de renderizado. El contenido entra una vez con 8 px/420 ms; imágenes con fundido 560 ms. Superficies y geometría de tarjetas permanecen fijas. Foco, pausa, pestaña y menú cancelan efectos al estado natural; movimiento reducido desactiva entradas. La llegada tardía del script y la restauración de scroll mantienen el contenido visible. No se ocultan campos ni texto de lectura.

## Validación

24 casos de página/viewport a 1440, 768, 390 y 320 px: geometría de tarjetas antes/después idéntica, inset igual en servicios, separación real de filas de blog, sin overflow ni errores JavaScript. Barra de administración comprobada con una barra de dimensiones equivalentes inyectada en el navegador; no se accedió al admin de Hostinger. Se verifican pausa, movimiento reducido y script retrasado 900 ms sin flash inicial.

Pruebas de interacción: selector de servicios/teclado/valores conservados, menú/Escape/foco, filtros y FAQ de los tres servicios. No se enviaron solicitudes reales.

## Instalar

Reemplazar el tema con `pelham-modern-1.11.0.zip`, conservar plugin 1.8.6 y purgar caché de WordPress/Hostinger y navegador. No reimportar páginas. Hostinger no fue modificado automáticamente.

## Confirmación final

50 vistas de las 25 rutas y 158 capturas de componentes: HTTP 200, un H1, sin imágenes ausentes, overflow ni errores JS. Se inspeccionaron especialmente las filas de servicios y la separación de tarjetas que señalaban las capturas del usuario.

Comparación aislada de scroll en Chromium local: 159 intervalos en cada modo (pausado/activo), mediana 16.7 ms, máximo 16.8 ms y cero intervalos superiores a 50 ms. El modo activo tuvo entradas en 118 fotogramas (hasta 8 grupos concurrentes); no es una prueba vacía. La muestra diagnóstica previa bajo carga queda en stress.json; la medición final es performance-comparison.json. Estos resultados describen este entorno y no certifican todos los dispositivos.

[Revisión visual y vídeo](audit/motion-1.11/review.html). El vídeo es un recorrido controlado en el navegador, no una simulación de la animación.

El menú móvil con barra de administración simulada también se verificó a 390×600 px: permanece dentro del viewport, permite llegar a sus últimos enlaces con scroll y cierra con Escape.
