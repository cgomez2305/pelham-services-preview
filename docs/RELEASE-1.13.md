# Pelham 1.13 — entradas visibles y activación inicial corregida

La revisión de los archivos de la web pública confirmó que el tema 1.12 y Motion Mini estaban cargando. El problema estaba en nuestra implementación: la protección para scripts tardíos omitía la entrada del hero y los desplazamientos restantes eran demasiado pequeños para el efecto solicitado.

## Cambios

Bootstrap crítico antes del primer pintado, precarga de los tres scripts y fallback de 1.6 segundos si falla el controlador. Apertura coordinada del hero; titulares con máscara y recorrido de 24 px, fotografías con revelado horizontal, contenido de tarjetas con recorrido de 18 px y desfase, y trazado único de la ruta entre terminales. Se mantienen Motion Mini 14.1.0, scroll nativo, superficies fijas, cancelación por foco/menú y preferencias de movimiento reducido/pausa.

## Evidencia

Las pruebas de escritorio y móvil verificaron entradas efectivas del hero y la ruta, geometría estable, ausencia de desbordamiento y errores JavaScript, movimiento reducido y contenido visible si el script falla. Además pasaron 24 combinaciones de seis páginas en cuatro anchos (1440, 768, 390 y 320 px), la separación de tarjetas y el header con barra administrativa. Pasaron cotización guiada, selección por teclado, menú móvil con Escape y recuperación del foco, filtros de noticias y FAQ de los tres servicios. No se enviaron formularios reales. Evidencia en docs/audit/motion-1.13. El video corresponde al WordPress local de revisión con contenido de referencia; no es una grabación de Hostinger actualizado.

Una comparación local con recursos decodificados midió 159 intervalos por modo. La mediana fue 16.7 ms en ambos. Con movimiento habilitado hubo un intervalo mayor de 50 ms (máximo 100.1 ms), frente a ninguno con movimiento pausado (máximo 50 ms). Hubo entradas activas en 154 fotogramas. Esta muestra no demuestra ausencia de tirones en todos los dispositivos.

## Instalación

Reemplazar únicamente el tema por dist/pelham-modern-1.13.0.zip. Mantener el plugin 1.8.6 y los contenidos existentes, sin reimportar páginas. La web pública continúa en 1.12 hasta instalar el nuevo ZIP; este trabajo no publica automáticamente en Hostinger.
