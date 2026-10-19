# SEO nativo y migración — Pelham Content 1.10.0 / Modern 1.17.0

## Implementado en el proyecto

- Home con mensaje para proveedores, distribuidores, freight forwarders y fábricas; página LTL ampliada con requisitos de cotización y explicación del tramo doméstico. Enlazado a guías útiles existentes, sin crear páginas geográficas duplicadas.
- Título de marca para home y título específico de servicio para LTL, evitando títulos idénticos. Fallbacks del tema actualizados.
- Datos estructurados de LTL con los tres niveles, estimaciones no garantizadas y cobertura; sin precios, reseñas, horarios ni direcciones inventados. Las fichas sin dirección no generan LocalBusiness vacío.
- Fuera de pelhamservices.com/www.pelhamservices.com: noindex en robots y cabecera HTTP, sitemap nativo desactivado. No se cambia automáticamente la opción de privacidad de WordPress al mover el sitio.
- Registro, reclamaciones y acceso fuera del sitemap nativo y noindex; archivos de autor/fecha, búsquedas y 404 noindex. Proveedor de usuarios excluido del sitemap.
- Redirecciones de IDs antiguos verificados en la web pública. Interruptor explícito, desactivado por defecto; funcionan solo en dominio de producción, GET/HEAD, rutas raíz o index.php. Sin redirecciones masivas de 404 a home, sin interferir con POST ni previews. Paginación antigua no equivalente queda pendiente de evaluación, no se envía a una página ficticia.
- Pantalla Pelham Company → SEO & migration con estado de indexación, interruptor de redirecciones y campo de verificación HTML de Search Console.

## Instalar y aplicar

1. Respaldar archivos y base de datos del WordPress instalado.
2. Reemplazar Pelham Content con `pelham-content-1.10.0.zip` y Pelham Modern con `pelham-modern-1.17.0.zip`. Pelham Applications permanece en 1.1.1.
3. En Pelham Company pulsar **Apply LTL content and SEO update (save backups)**. Actualiza las páginas especificadas en la pantalla y metadatos; guarda copias por página en `pelham_before_offer_1_9` y revisiones. Esta operación sustituye textos editados de esas páginas, no es una migración automática al subir el ZIP. Las copias conservan fecha; el nombre histórico de la clave se mantiene.
4. Vaciar cachés. En Hostinger debe seguir apareciendo noindex. El interruptor de redirecciones puede permanecer apagado hasta el reemplazo de producción.
5. Tras mover al dominio final y verificar SSL, rutas, formularios y respaldos: revisar Ajustes → Lectura, permitir indexación y activar los 301 verificados en SEO & migration.
6. Verificar GET de cada URL del CSV, destinos 200 y una sola redirección de aplicación (la normalización www/HTTPS del hosting puede añadir otra). No activar reglas antiguas contradictorias de servidor/CDN. Revisar enlaces a PDFs y conservar los archivos antiguos usados por clientes: esta versión no redirige ni elimina documentos.
7. Verificar sitemap y canonical del dominio definitivo. Si hay otro plugin SEO, sus metadatos/sitemaps deben revisarse por separado; el tema cede gestión a esos plugins.

No se ha desplegado en Hostinger ni sustituido pelhamservices.com. La prueba de producción es una simulación local de host y visibilidad; no demuestra comportamiento del hosting/CDN.

## Investigación realizada

Se consultó la home y enlaces públicos de Pelham, robots.txt, wp-sitemap.xml y listado público de páginas REST. Robots y sitemap devolvieron 404 durante la consulta: esto no prueba que Google no conozca otras URLs ni que no exista otro sitemap. Los IDs de páginas se verificaron con títulos y enlaces. Inventario en `seo-research-2026-10/pelham-inventory.json`.

Se consultaron 14 páginas oficiales de EDI Express, Sterling Transportation, Daylight Transport y JTS Express. El informe separa afirmaciones comerciales de resultados medidos. No se midieron volúmenes, posiciones, backlinks ni cuotas de mercado. No se publican comparaciones con competidores en la web de Pelham.

## Pendiente de acceso/datos reales

- Search Console: crear/verificar propiedad y enviar sitemap. Para propiedad de dominio se necesita DNS; el token HTML del panel sirve para propiedad de prefijo URL, no reemplaza DNS. No compartir contraseñas.
- Analítica: elegir/crear propiedad y configurar consentimiento. No se instaló GA4 ni otro rastreador sin identificador y configuración. Medición propuesta en `SEO-MEASUREMENT-PLAN.md`.
- Validar direcciones/horarios de instalaciones, especialmente Orlando y la correspondencia Los Ángeles/Gardena; verificar fichas existentes antes de crear Google Business Profile.
- Confirmar conversiones reales y entrega SMTP en el hosting. El formulario existente informa handoff a WordPress, no entrega a bandeja.
- Tras publicar: Search Console cobertura/404, cotizaciones calificadas por nivel, consultas reales y prioridades editoriales. No se promete ranking ni resultados en un plazo fijo.
