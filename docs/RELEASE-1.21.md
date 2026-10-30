# Pelham 1.21 — tema 1.21.0 / contenido 1.16.0

Fotografía profesional en toda la web. Reemplazar **tema y plugin de contenidos** y vaciar caché. No reimportar contenido: las fotos se asignan desde los archivos del plugin.

## Origen y optimización

- Sesión fotográfica profesional en la terminal de Gardena (148 fotos, 6000×4000 px, carpeta de Google Drive del cliente). Se revisaron todas en hojas de contacto y se eligieron 19, una por hueco, sin repetir ni usar tomas casi idénticas.
- Conversión a WebP sin retoque: Lanczos, enfoque suave tras reducir, calidad 78–80, perfil sRGB, **sin metadatos** (EXIF retirado; los originales no tenían GPS). Tamaños 640, 960 y 1600 px, y 2400 px para la panorámica de la home.
- De ~300 MB de originales a **5 MB** en total; una foto típica de 1600 px pesa ~140 KB.
- Se retiran las 60 imágenes anteriores (capturas de la web antigua y versiones mejoradas con IA). Siguen en el historial de Git.

## Dónde aparece cada foto

| Hueco | Foto |
|---|---|
| Home, panorámica | Flota de camiones frente a la terminal de Gardena |
| Servicio FTL (dedicated transport) | Tráiler en los muelles de carga |
| Servicio cross-docking (transloading) | Carretilla cargando palés junto a un tráiler |
| Servicio LTL (freight consolidation) | Carretilla con palés consolidados |
| Contacto | Coordinadora atendiendo una llamada |
| About | Dos miembros del equipo dándose la mano |
| Our network | Muelles de la terminal con tráileres |
| Gardena / Los Angeles | Edificio de la terminal |
| 11 artículos (cabecera y tarjeta) | Una foto propia cada uno: flota, almacén, muelle, interior de tráiler, palés, documentación, salida de entrega… |

Miami y Orlando **no** llevan foto: la sesión es de Gardena y usarla allí sería engañoso.

Claves en `wordpress/pelham-content/data/photography.json` (texto alternativo descriptivo, encuadre `object-position`, origen) y asignación por página en `data/keyword-map.json`. Las claves que usaba el contenido guardado (`pelham-services`, `pelham-hero`, `truck`, `cargo`, `containers`, `warehouse`) existen ahora y se rellenan solas.

## Correcciones incluidas

- Las cabeceras de artículo mostraban un marco vacío: sus claves de foto no existían en el catálogo.
- La clave `warehouse` estaba bloqueada en el código; ya no.
- Las tarjetas de Insights ahora muestran la foto de cada artículo (antes solo si tenía imagen destacada). En la página de Insights el contenedor medía 0 px de ancho; corregido, y el artículo destacado lleva la foto como banner.
- Una copia antigua importada en Media ya no tapa la foto nueva: solo se usa si procede de la misma foto del catálogo. «Importar a Media» vuelve a importar las nuevas.
- `sizes` ajustado a cada hueco (artículos a ancho completo, portadas de servicio, tarjetas) para que el navegador baje la versión justa.
- Se quita el pie «Image from the original Pelham website» de las terminales.

## Comprobaciones

Vista previa: 28 rutas a 1440 y 390 px sin errores, 404, desbordamiento ni entradas bloqueadas; todas las fotos cargan y eligen la resolución adecuada. PHP sin errores de sintaxis. No probado sobre WordPress real: revisar en staging la home, un servicio, Insights y un artículo.
