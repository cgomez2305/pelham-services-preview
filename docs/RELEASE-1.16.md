# Pelham 1.16 — bloques que suben con el scroll

Los bloques completos aparecen desde abajo, con un recorrido visible de 56 px en escritorio y 36 px en móvil, duración de 1.05 segundos y separación de 160 ms entre entradas coincidentes. Se elimina el acercamiento de las fotografías para que sigan el mismo movimiento vertical. Las FAQ se escalonan por pregunta. Se ejecuta una vez al entrar en pantalla; no es una animación que dependa de cada píxel de scroll.

La cabecera aparece al bajar y se oculta al subir, tal como se solicitó. Vuelve a estar visible al inicio, al recibir foco de teclado y al abrir el menú. Cambios menores de 24 px no invierten su estado. Con movimiento reducido o pausa permanece visible.

## Comprobaciones

Pruebas específicas de escritorio y móvil verifican desplazamiento del bloque completo durante la entrada, finalización sin transformaciones residuales, geometría estable, las dos direcciones del header, recuperación con teclado y menú, y eliminación de estados pendientes al reducir movimiento. También pasaron 24 combinaciones de seis páginas y cuatro anchos, incluida la barra de administración de WordPress, sin errores JavaScript ni desbordamientos detectados. Se conservan el fallback cuando falla el script y el scroll nativo.

La muestra local de scroll midió 159 intervalos por modo, mediana de 16.7 ms en ambos. Con entradas activas el máximo fue 50 ms y no hubo intervalos superiores a 50 ms; en pausa hubo uno de 133.3 ms. Es una medición de este entorno, no una garantía en todos los dispositivos.

Las capturas y el video son del WordPress local de revisión, no de Hostinger actualizado.

## Instalación

Reemplazar únicamente el tema por pelham-modern-1.16.0.zip. Mantener el plugin 1.8.6 y los contenidos existentes, sin reimportar páginas.
