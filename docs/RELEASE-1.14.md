# Pelham 1.14 — revisión UX/UI y glassmorphism

El sitio usaba paneles planos con poca diferenciación de profundidad. La revisión aplica una superficie coherente a descubrimiento, servicios, contacto, noticias y terminales. El desenfoque real se concentra en la cabecera, el panel sobre la fotografía de portada y el menú móvil. Las tarjetas comparten bordes, transparencia y sombras suaves. Se conservan el verde corporativo y DM Sans.

## Mejoras aplicadas

- Contacto: paneles separados, datos de contacto destacados y campos sobre fondo de lectura sólido.
- Servicios: apertura con profundidad, navegación de secciones agrupada, selección visible y FAQ con indicadores circulares.
- Tarjetas: espacios interiores consistentes, bordes de interacción y foco de teclado visibles.
- Navegación: el apartado activo también se marca en páginas de servicios con jerarquía plana de WordPress.
- Móvil: información de terminales superpuesta a la imagen sin tapar el camión, menú de cristal con contraste corregido tras revisar capturas, campos de contacto apilados.
- Fallback sólido para navegadores sin backdrop-filter y preferencias de transparencia reducida.

## Validación

50 casos (25 páginas en escritorio y móvil): HTTP 200, un H1, sin imágenes rotas, desbordamiento horizontal ni errores JavaScript. Doce casos adicionales entre 320 y 1440 px verificaron superficies, apartado activo, checkbox y dimensiones del menú. Pasaron cotización guiada, teclado, menú con Escape y recuperación del foco, filtro de noticias y FAQ de los tres servicios. También pasaron las entradas del hero y la ruta, estabilidad de tarjetas, movimiento reducido y recuperación cuando falla el script. Una muestra de scroll con recursos cargados registró 159 intervalos por modo, mediana de 16.7 ms y máximo de 16.8 ms, tanto con animaciones como en pausa. No sustituye una medición en dispositivos reales. No se enviaron solicitudes reales. Se revisaron visualmente portada, contacto, servicio individual, FAQ y menú; las capturas se incluyen en la galería.

La revisión usa el WordPress local con contenido de referencia. No modifica automáticamente el contenido ni el tema instalado en Hostinger.

## Instalación

Instalar pelham-modern-1.14.0.zip reemplazando el tema. Mantener Pelham Content 1.8.6 y los contenidos existentes; no reimportar páginas.
