# Pelham 1.10.1 — entradas por componentes

Reemplaza la entrada uniforme de párrafos y formularios y los títulos partidos en líneas por una composición coordinada de unidades visuales: hero, imágenes, encabezados de sección, tarjetas, proceso, preparación, FAQ, terminales y footer.

Texto y grupos recorren 16 px (10 en móvil) durante 580 ms; imágenes hacen un fundido de 720 ms sin zoom. Los bloques que coinciden tienen un desfase de hasta 135 ms. En paneles con fondo, la superficie permanece anclada y entra su contenido. Se conserva GSAP; deja de cargarse Lenis y los plugins que este motor ya no necesita. El scroll rueda/táctil y los enlaces internos utilizan comportamiento nativo.

No se oculta contenido fuera de pantalla. Las entradas se ejecutan una vez y terminan en opacity/transform limpios. Los formularios permanecen disponibles; el cuerpo de artículos y documentos legales conserva lectura estática. Pausa, foco, movimiento reducido, menú y cambio de pestaña resuelven las entradas activas.

## Instalación

Reemplazar el tema por pelham-modern-1.10.1.zip, conservar plugin 1.8.6 y limpiar caché. No reimportar contenido. No hay cambios automáticos en Hostinger.

La vista previa utiliza el contenido de referencia de la copia local. Sus formularios están deshabilitados.

## Validación final

12 combinaciones: home, servicio individual, contacto, cotización, noticias y registro a 1440/390 px. Se recorrieron las páginas con saltos rápidos de scroll, se verificó que las entradas terminaran y no se repitieran al volver al inicio, sin overflow ni errores JavaScript. Los formularios no tuvieron opacity/transform de entrada. Movimiento reducido desactiva entradas; pausa resuelve las activas. Evidencia y capturas en `audit/motion-1.10.1/`. No se enviaron solicitudes ni se publicó el tema en Hostinger.
