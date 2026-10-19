# Pelham 1.12 — Motion JavaScript integrado

Se revisaron https://motion.dev/docs/quick-start y https://motion.dev/docs/animate. Se usa `motion/mini` 14.1.0 (MIT), compilado con esbuild y alojado dentro del tema: 8,886 bytes sin comprimir. El hosting no necesita Node, React ni un CDN. La versión híbrida y las funciones de pago no se incorporan.

## Entradas

Titulares: 560 ms con recorrido de 10 px. Apoyo: fundido de 380 ms. Contenido de tarjetas: 460 ms y 6 px. Fotografías: fundido de 620 ms. Desfase interno máximo 120 ms, curva [.22,1,.36,1]. Bordes, superficies y formularios permanecen fijos; no se reactiva el motor anterior ni sus animaciones continuas.

El motor conserva ejecución única, contenido visible si el script llega tarde, scroll nativo, pausa, movimiento reducido y cancelación por foco/menú/pestaña. Restaura estilos originales al finalizar. Los espacios corregidos en 1.11 se conservan.

## Compilación reproducible

Desde tools/motion-build: `npm ci --ignore-scripts`, después `npm run build`. Dependencias fijadas y lockfile incluidos. Licencias de Motion y dependencias se distribuyen en assets/vendor/motion-LICENSE.txt. El ZIP ya incluye el archivo compilado.

## Instalar

Reemplazar el tema por pelham-modern-1.12.0.zip, conservar plugin 1.8.6 y purgar caché. No importar páginas de nuevo. No hay cambios automáticos en Hostinger.

## Validación

Integración inicial: 24 casos en 1440, 768, 390 y 320 px. Confirmación de cadencias finales: 12 casos de home, noticias, servicio, contacto, cotización y registro en escritorio/móvil, con geometría fija y separación de tarjetas conservada; pausa y movimiento reducido comprobados. Sin errores JavaScript ni overflow detectados.

La comparación con recursos ya cargados registró 159 intervalos por modo, mediana 16.7 ms y ningún intervalo mayor de 50 ms. Las entradas estuvieron activas en 120 fotogramas. La muestra de carga inicial y las capturas se conservan por separado en docs/audit/motion-1.12-confirm; los resultados no certifican todos los dispositivos. Se verificó también el script retrasado sin flash y la finalización por foco. No se enviaron solicitudes reales.
