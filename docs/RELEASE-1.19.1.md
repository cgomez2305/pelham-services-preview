# Pelham 1.19.1 — tema 1.19.1 / contenido 1.15.1

Versión de correcciones. No cambia contenido ni diseño: reemplazar el tema y el plugin de contenidos, vaciar caché. No reimportar contenido.

## Tema 1.19.1

- **Caché de estilos y scripts.** Los CSS y JS se publicaban con versiones fijas (`?ver=1.16.0`) que no cambiaban al editar los archivos, así que el navegador o la CDN podían seguir sirviendo estilos antiguos tras una actualización. Ahora la versión es la fecha de modificación de cada archivo; los `preload` usan la misma versión.
- **Imagen para redes sociales.** Las páginas sin foto propia (la mayoría de artículos) compartían el logo de 156×96 px como `og:image`, por debajo del tamaño que aceptan las redes. Ahora usan la foto de la flota de la portada.
- **Datos estructurados `Article`.** Añadidos `author` e `image`, recomendados para resultados enriquecidos.
- **Logo de reserva.** Dimensiones declaradas corregidas a las reales (156×96) para evitar saltos de maquetación.
- **Artículos sin H2.** Ya no se muestra un índice «In this article» vacío ni se duplica el atributo `id` de títulos que ya lo tenían.

## Contenido 1.15.1

- **Foto principal de la portada.** Estaba en `loading="lazy"` aunque está arriba del todo, lo que retrasaba el LCP. Ahora carga con prioridad alta y un `sizes` acorde a su ancho real (1200 px). Funciona con el contenido ya importado. El shortcode `[pelham_photo]` acepta además `eager="1"` para otras fotos visibles al cargar.

## Limpieza del repositorio

Eliminados de la raíz los restos del sitio estático anterior que ya no usa ninguna página: `assets/css/`, `assets/js/`, `assets/img/` y `sitemap.xml` (listaba URLs antiguas; producción usa `/wp-sitemap.xml`).

## Comprobaciones

PHP sin errores de sintaxis (`php -l`). Encolado de estilos y scripts verificado con funciones de WordPress simuladas: mismo orden y dependencias que antes. La vista previa estática (28 rutas, 1440 y 390 px) no muestra errores JavaScript, recursos 404, desbordamiento ni animaciones bloqueadas. No probado sobre una instalación WordPress real: revisar en staging antes de producción.
