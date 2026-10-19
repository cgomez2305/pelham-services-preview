# Auditoría SEO técnica, intención y alineación — entrega 1.14

## Alcance y límites

Revisión del código, contenido y WordPress local; 28 rutas de la web, incluidas las páginas legales y de registro. No es una comprobación de la versión instalada en Hostinger ni un informe de posiciones. Sin Search Console no se puede afirmar que exista o no canibalización observada: se revisan y corrigen riesgos estructurales y editoriales.

## Hallazgos corregidos

1. **El H1 real de los servicios no era el título SEO/editorial.** El theme imprimía `pelham_service_label`, de modo que aparecía solo «Less Than Truckload (LTL)». Ahora imprime el título editable del servicio. LTL y FTL identifican California–Florida; el contenido precisa Los Ángeles como origen y no promete recogida uniforme en todo California. Las etiquetas cortas se mantienen en las tarjetas.
2. **La home no nombraba Pelham en el H1.** Se centra ahora en la marca: «Pelham Services. Your freight, connected.» El servicio LTL conserva la intención comercial específica del corredor. No se añade una segunda landing para la misma búsqueda.
3. **La guía de recurrencia tenía como keyword principal «dedicated trucking California Florida».** Se corrige a planificación de envíos recurrentes, acorde con su contenido. El artículo no presenta toda recurrencia como FTL.
4. **El artículo corporativo repetía la oferta comercial del servicio.** Se reescribe como proceso de consulta, revisión de presupuesto, coordinación y entrega. Enlaza a LTL para los niveles en lugar de volver a desarrollar otra landing comercial.
5. **Cotización y checklist tenían encabezados demasiado próximos.** `/quote/` solicita un presupuesto; el artículo explica requisitos y preparación. El coste de pallets sigue en la guía de factores de precio, sin cotizador ni tarifas ficticias.
6. **Contacto tenía un H1 genérico y tracking como keyword primaria.** Ahora el H1 es «Contact Pelham Services» y la intención principal es contacto de marca. Las actualizaciones de envío permanecen como intención secundaria dentro de su sección, sin prometer tracking público.
7. **La página de Gardena no identificaba Gardena en su H1.** Ahora contiene Gardena y Los Ángeles, evitando crear dos destinos SEO para la misma instalación.
8. **Servicios, directorio de instalaciones y noticias usaban encabezados poco descriptivos.** Se distingue el catálogo, el directorio y el índice editorial de sus páginas hijas.
9. **El bloque de tiempos de home usaba un ancho independiente de 1280 px.** El shortcode se envuelve en el mismo `.shell` que el header solo cuando está en home. Dentro de servicios conserva el ancho de su contenedor, sin añadir márgenes dobles. Los nuevos H1 de servicios tienen tipografía adaptable. También se elimina la excepción que extendía las cabeceras de servicios a todo el ancho en móvil: sus bordes pasan a coincidir con el header.

## Mapa de intención principal

| Destino | Intención principal | Evitar que compita por |
|---|---|---|
| Home | Marca Pelham Services, entrada general | Desarrollar otra página completa LTL |
| About | Empresa y actividad doméstica | Guía del proceso o solicitud comercial LTL |
| Services | Catálogo LTL, FTL y manipulación | Explicación exhaustiva de cada servicio |
| Servicio LTL | Contratar LTL California–Florida | Coste informativo o comparativa independiente |
| Servicio FTL | Contratar capacidad exclusiva California–Florida | Planificación genérica de recurrencia |
| Transloading | Consultar cross-docking/paletización | Guía de diferencias entre métodos |
| Quote | Solicitar precio para un envío | Checklist editorial o guía de costes |
| Contact | Contacto y asistencia | Portal público de tracking inexistente |
| Directorio de instalaciones | Encontrar las tres instalaciones | Direcciones completas como tres landings mezcladas |
| Gardena / Miami / Orlando | Marca local y datos de cada instalación | Crear otras páginas para sinónimos del mismo lugar |
| News | Índice de guías y noticias | Repetir artículos enteros |
| Artículo LTL vs FTL | Diferencias, elección y comparación | Contratar exclusivamente uno de los servicios |
| Guía de coste | Factores que afectan el precio | Tarifa instantánea o presupuesto contractual |
| Checklist | Qué datos preparar para cotizar | Capturar otra solicitud comercial |
| Recurrencia | Preparar una pauta de envíos | Presentar un segundo servicio FTL |
| Proceso Pelham | Cómo se coordina una solicitud/envío | Duplicar la oferta LTL o la historia corporativa |
| Guía forwarders | Coordinar el tramo doméstico al receptor | Anunciar forwarding internacional |
| Resto de guías | Tendencias, retrasos, combustible o preparación de transferencia | Mezclar estas preguntas con la landing comercial |
| Registro y legales | Tarea de usuario/documento | Forzar keywords de captación en formularios privados |

Compartir vocabulario no implica canibalización. El criterio es que cada página resuelva una tarea distinta, con enlaces que conducen al servicio o formulario apropiado. No se usan canonicals cruzados para ocultar páginas distintas ni se eliminan URLs útiles por compartir palabras.

## Semántica y encabezados

El H1 contiene la marca o el concepto principal de la página de forma natural. No se introduce cada variante de la matriz en un H1, ni se fuerza la misma frase literal en todas sus variantes. Title, H1, cuerpo y destino de enlaces se revisan juntos. La comprobación de H1 compara el HTML renderizado con el título editorial esperado; no se limita a verificar que exista una etiqueta H1.

La matriz de 96 keywords se regenera con los metadatos revisados. Continúan 95 variantes trabajadas por intención y una pendiente de reseñas verificables. Tracking y forwarding mantienen límites explícitos. No se añaden testimonios, estrellas, precios ni garantías.

## Validación y seguimiento

La prueba `tools/test-seo-intent-1.14.cjs` comprueba la actualización, respaldos, preservación de URLs/fechas/estado/media, reejecución, H1, titles y metas únicos, canonical, JSON-LD y noindex de staging. Compara bordes de header y tiempos en 1440, 1024, 768, 390 y 320 px, con tolerancia inferior a 1 px. Las capturas usan movimiento reducido para verificar composición, no animaciones.

Tras publicar en producción, exportar Search Console por consulta y página en ventanas de 28 días. Investigar si una consulta relevante alterna entre páginas de intención equivalente y si hay pérdida de clics/posición; varias páginas con impresiones para una misma consulta no son por sí solas prueba de canibalización. Mantener staging sin indexar.

## Aplicación

Instalar **Pelham Modern 1.18.0** y **Pelham Content 1.14.0**. Completar primero cualquier actualización de contenido anterior pendiente. En Pelham Company, ejecutar **Apply SEO intent and headings (save backups)** y limpiar caché. Se sustituyen los textos/SEO de 13 destinos con respaldo `pelham_before_seo_audit_1_14`. No hace falta cambiar permalinks. No se ha realizado instalación remota.
