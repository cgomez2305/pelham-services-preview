# GEO y visibilidad en buscadores con IA

## Conclusión
La nueva web tiene una base adecuada para que los sistemas interpreten la oferta de Pelham, pero esta revisión no mide indexación, menciones ni citas reales en ChatGPT, Google AI Overviews o similares. No existe garantía de aparición. Las mejoras de esta entrega corresponden al tema 1.19.0; no se han desplegado en Hostinger ni en producción.

## Evidencia pública
- La portada de deeppink-bear-572523.hostingersite.com devuelve `X-Robots-Tag: noindex, nofollow` y meta `noindex`. Su robots.txt bloquea Googlebot, pero permite el resto de agentes. Mantener la protección de staging hasta migrar. Noindex no es control de acceso: si se necesita impedir todo acceso público, proteger staging mediante contraseña en el alojamiento.
- pelhamservices.com sigue mostrando la web anterior. Su robots.txt devolvió 404; esto no equivale a bloquear el rastreo. No se verificó el estado de indexación en Search Console.
- Las respuestas y fuentes consultadas están registradas en `geo-audit/sources.json`. Una petición HTTP normal no demuestra que el WAF permita el rastreador real de un proveedor.

## Mejoras aplicadas
- Organización vinculada con las tres instalaciones publicadas mediante entidades LocalBusiness y direcciones estructuradas, sin inventar teléfono de Orlando, coordenadas u horarios.
- Relación explícita entre cada página y su servicio, instalación o artículo principal.
- Los servicios LTL describen origen Los Angeles y cobertura Florida. Expedited limita su cobertura a Homestead–West Palm Beach; los plazos siguen siendo estimaciones de días calendario desde la recogida, no garantías.
- Editor visible en artículos, enlazado a About, y fechas reales de publicación y modificación coherentes con Article. No se inventan autores, credenciales ni reseñas.
- Corrección del menú móvil sin JavaScript para que no cubra el contenido.
- El robots.txt del repositorio estático bloquea su vista previa. No sustituye el robots.txt que sirve WordPress en producción.

Ya existían respuestas directas al inicio de los artículos, contenido HTML renderizado en servidor, enlaces internos, metadatos por intención, canonicals y protección de formularios privados. Se conserva la cotización individual, sin precios inventados.

## Qué indican las fuentes oficiales
[Google: AI features and your website](https://developers.google.com/search/docs/appearance/ai-features): no exige schema especial ni archivos para IA. Para ser elegible como enlace de apoyo, una página debe estar indexada y ser elegible para mostrar un fragmento. Los principios de SEO habituales siguen aplicando. Los datos de estas funciones se incluyen en el rendimiento de búsqueda Web de Search Console; no prometemos un informe independiente de menciones IA.

[OpenAI: crawlers](https://developers.openai.com/api/docs/bots): OAI-SearchBot sirve para búsqueda; GPTBot tiene una finalidad de entrenamiento distinta. Permitir búsqueda no obliga a permitir entrenamiento. ChatGPT-User corresponde a acciones iniciadas por usuarios y no determina la elegibilidad para Search. No se cambió la política de entrenamiento del sitio.

No añadimos llms.txt ni marcado ficticio como supuesto requisito de posicionamiento.

## Publicación y seguimiento
1. Instalar el tema 1.19.0; mantener el plugin de contenido 1.15.0. Vaciar caché. Estas mejoras no necesitan una nueva migración de textos.
2. Al migrar a pelhamservices.com, comprobar canonicals, enlaces y sitemap `/wp-sitemap.xml`, y retirar solamente las restricciones de indexación de la web pública definitiva. Mantener privadas solicitudes, documentos y registros. No abrir staging a indexación.
3. Verificar Googlebot, Bingbot y OAI-SearchBot en reglas del alojamiento/CDN y registros con los métodos/IP oficiales. No basta simular su User-Agent. Registrar y enviar sitemap en Search Console y Bing Webmaster Tools cuando se disponga de acceso.
4. Crear/verificar las fichas reales de las tres instalaciones con direcciones coherentes. Añadir fotografías propias, horarios confirmados y reseñas auténticas cuando existan. Conseguir referencias de socios y directorios pertinentes, sin compra masiva de enlaces.
5. Revisar mensualmente las preguntas de `geo-audit/monitoring-questions.csv` en los sistemas seleccionados. Registrar fecha, motor, ubicación, si búsqueda está activada, URLs citadas y exactitud. Repetir con condiciones comparables: una respuesta aislada no representa cuota de visibilidad.
6. Medir páginas de entrada, consultas, clics y solicitudes cualificadas. Los referidos de IA no identifican todas las visitas. No se instalaron herramientas de seguimiento ni se recogieron datos personales en esta auditoría.

## Validación
Prueba automatizada en ocho rutas con JavaScript desactivado: respuesta 200, H1 único, noindex de staging, JSON-LD válido y entidades enlazadas, direcciones, cobertura de Expedited, editor y fechas visibles. Capturas de artículo en escritorio y móvil; comprobación de ausencia de desbordamiento horizontal y de superposición del header móvil. Evidencia en `geo-audit/runtime-results.json` y capturas del mismo directorio.
