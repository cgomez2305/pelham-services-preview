# Pelham — sistema visual 1.11

Identidad conservada: verde Pelham, títulos Arial/Helvetica, énfasis Georgia reservado a home y DM Sans para lectura/controles. Fotografías existentes; no se incorporan imágenes repetidas ni una nueva identidad.

## Fuente de implementación

`wordpress/pelham-modern/assets/design-system.css` se carga después de las capas heredadas y es la autoridad visual compartida. Los tokens `--p-*` definen fondo, superficies, texto, bordes, radios y tiempos. Se mantiene compatibilidad con bloques editables y páginas importadas; no se sustituye contenido al activar el tema.

- Fondo #f5f7f4, superficie blanca, tinta #101f17, secundario #4d6054.
- Verde de acción #00a858 con texto oscuro; enlaces #007d42.
- Paneles destacados #edf5ef; superficies oscuras #143b2a.
- Radio 10 px en componentes, 16 px en paneles principales, 8 px en campos.
- Acciones mínimo 48 px; campos 48 px; foco visible de 2 px.
- Interacción breve 160 ms; expansión 240 ms; sin nueva biblioteca de animación.

## Composición por tarea

Home: apertura editorial y foto panorámica, comparación de servicios en filas, red y proceso, cierre y noticias. Servicios: apertura oscura con fotografía única, navegación por secciones, proceso, preparación, FAQ y alternativas. Descubrimiento: comparadores abiertos sobre superficies claras. Contacto/cotización/registro: misma familia de formularios, opcionales, estados y botones. Blog: artículo destacado, archivo, columna de lectura con índice. Empresa, terminales y documentos legales comparten la misma jerarquía, espaciado y superficies. Header y footer son comunes a todas las rutas.

La coherencia no exige el mismo número de columnas ni la misma plantilla para tareas distintas. Los formularios no se ocultan detrás de animaciones. FAQ nativas details/summary; selector con radios nativos y fallback select sin JS. Movimiento reducido y pausa se respetan. Ninguna sincronización con Supply Vision se añade en esta versión.

## Edición y entrega

Los contenidos permanecen en WordPress. Tema 1.10.0 compatible con plugin 1.8.6; no requiere reimportar páginas. El HTML raíz es una exportación de la copia local con contenidos de referencia de los ZIP, no una réplica de todas las ediciones que pueda tener Hostinger. Los formularios de esa vista previa están deshabilitados. Los formularios del tema instalado mantienen su procesamiento WordPress.

## Sistema de entrada 1.11

`motion.js` es la única autoridad de entradas. Usa Web Animations API con un observador compartido y no carga GSAP, Lenis ni plugins de animación. `site.js` conserva navegación, pausa y recuperación de imágenes; se eliminan medición de escenas, actualizaciones de estilos en cada scroll y su antiguo ResizeObserver. La navegación por secciones usa observadores separados, sin bucle de scroll.

Las entradas se ejecutan una vez: contenido de tarjetas o paneles a 8 px durante 420 ms, con desfase interno de hasta 105 ms; imágenes mediante opacidad durante 560 ms. Superficies, bordes y filas permanecen fijos. No se animan formularios ni el cuerpo de artículos/legales. En FAQ se mantiene el encabezado de sección y la interacción nativa; no se traslada el acordeón completo.

Contenido visible por defecto. No se aplica entrada inicial si el script llega después de 650 ms o la página recupera una posición de scroll. Foco, pausa, cambio de pestaña y menú cancelan las entradas activas y muestran el estado natural. Los efectos no escriben estilos inline ni usan fill-forwards persistente. Movimiento reducido deja el contenido estático.

Espaciado: filas de servicios sin tratamiento especial de first-child; margen común de la capa de diseño. News tiene gap 24 px escritorio/20 px móvil, sin overrides a cero. Acciones de noticias alineadas al fondo de cada fila. Header respeta los offsets de barra de administración (32/46 px).

Se eliminan de site.css las animaciones ambientales infinitas heredadas. El único movimiento de entrada activo lo gestiona motion.js; las interacciones CSS de botones, menú y FAQ siguen siendo breves y respetan movimiento reducido.

## Motion 1.12

La interpolación pasa a `animate` de `motion/mini`, versión fijada 14.1.0, compilada y alojada en el tema (8,886 bytes sin comprimir). No carga React ni el motor híbrido. Conserva observadores y seguridad de foco/pausa/restauración. Cada control restaura los valores originales de opacity/transform al finalizar o cancelar, evitando propiedades residuales.

Cadencias diferenciadas: titulares 560 ms/10 px, apoyo 380 ms mediante opacidad, contenido de tarjetas 460 ms/6 px y fotografía 620 ms mediante opacidad. Curva [.22,1,.36,1], desfase interno máximo 120 ms. Las superficies y el borde de encabezados permanecen fijos.

## Entradas 1.13

La comprobación de los recursos públicos confirmó que 1.12 estaba instalado, pero su activación inicial omitía el hero y los movimientos de las tarjetas resultaban demasiado sutiles. Un bootstrap crítico en wp_head prepara la apertura antes del primer pintado, con salida de seguridad a los 1.6 segundos y exclusión por movimiento reducido, pausa, anclas o restauración de historial. Se precargan los scripts manteniendo las dependencias de WordPress.

El hero coordina revelado de titulares, texto y fotografía. Los titulares recorren 24 px con una máscara, las fotografías se revelan horizontalmente y las tarjetas entran con 18 px y desfase de grupo. La ruta entre terminales se dibuja una vez en 1.15 segundos con Motion Mini. Los controles restauran opacity, transform, clip-path y stroke-dashoffset al terminar o cancelar. No se desplazan cajas ni se cambia el scroll nativo.

## Materiales y usabilidad 1.14

Glassmorphism verde con fondo de lectura estable: desenfoque real limitado a cabecera, panel de terminales sobre fotografía y menú móvil. Las tarjetas usan superficies translúcidas, bordes claros y luz interior sin añadir filtros a cada componente. Los campos, artículos y páginas legales conservan una base opaca. Fallback sólido, soporte de movimiento/transparencia reducidos y colores forzados.

Contacto usa dos paneles separados; los datos directos tienen una superficie propia. Tarjetas de servicio con relleno consistente en móvil, acordeones con indicador circular y selección de sección persistente incluso cuando WordPress utiliza páginas planas. El menú móvil desactiva el filtro del contenedor mientras está abierto para evitar filtros anidados y texto legible detrás.

## Entradas y respuesta 1.15

Se retiran las máscaras de títulos y fotografías: los títulos entran completos en 640 ms, con recorrido de 20 px (12 px móvil); el texto de apoyo en 500 ms y el contenido de tarjetas en 580 ms. Las imágenes usan un acercamiento leve de 1.025 a 1 durante 800 ms dentro de su marco. El observador empieza 48 px antes del borde inferior; el desfase entre unidades no supera 150 ms. Las FAQ se incorporan a la entrada. Se conserva el motor Motion Mini y el bootstrap con fallback seguro.

Los botones comparten contraste, borde, radio de 10 px, altura mínima de 48 px y respuesta cromática de 180 ms. El icono se desplaza 2 px en hover con ratón. No se desplaza el área de clic. Las tarjetas responden mediante borde/sombra y enlace; se eliminan los desplazamientos heredados. Teclado conserva anillo de foco, dispositivos táctiles evitan hovers persistentes y pausa/movimiento reducido desactivan movimiento decorativo.

## Subida progresiva 1.16

La entrada se aplica al bloque completo, incluidas sus superficies, con 56 px de subida en escritorio y 36 px en móvil durante 1.05 segundos. Los elementos que coinciden al entrar se escalonan 160 ms, hasta 480 ms; las FAQ usan 140 ms. El observador espera a que alcancen 48 px dentro del viewport, y los bloques todavía fuera de pantalla se preparan sin provocar saltos de layout. Foco, pausa y movimiento reducido revelan inmediatamente el contenido pendiente.

La cabecera sigue la dirección solicitada: bajar la muestra, subir la oculta. Umbral de 24 px para filtrar pequeños cambios de dirección; visible cerca del inicio, con foco o menú abierto. Movimiento reducido y pausa la mantienen visible. Un listener pasivo solicita como máximo una actualización por frame y modifica la clase solo tras superar el umbral; no se altera el scroll nativo.
