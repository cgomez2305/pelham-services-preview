> Revisión inicial histórica. Las aclaraciones posteriores del cliente y la implementación se documentan en [la versión 1.9](../RELEASE-CONTENT-1.9.md).

# Revisión editorial de servicios — Pelham Services

Fecha: 14 de octubre de 2026. Estado: propuesta lista para revisión, no publicada ni importada en WordPress.

## Fuentes y alcance

Se revisaron los tres servicios enviados en el chat y el archivo «The Ideal Solution for Your Coast.docx», junto con home.html, pages.json, keyword-map.json, el formulario de cotización y la plantilla individual de servicios del proyecto. Solo hay un Word adjunto en este mensaje. Los documentos del cliente son fuentes de contenido comercial; sus afirmaciones operativas no se han verificado independientemente.

## Conclusión

La estructura principal debe ser Full Truckload (FTL), Less Than Truckload (LTL) y Transloading. El propio Word vincula Dedicated Transport con FTL y Freight Consolidation con LTL. Por ello no se deben crear cinco servicios ni páginas duplicadas. Sin embargo, FTL no exige contratar una ruta recurrente: la página actual insiste demasiado en recurrencia y puede excluir consultas de un solo envío. La consolidación debe explicarse como parte del servicio LTL, no como sinónimo técnico universal de LTL.

El contenido público sigue en inglés. Esta revisión y las consultas al cliente están en español. Los textos listos para maquetar figuran en COPY-EN.md.

## Distribución en la web

| Área | Cambio propuesto | Motivo |
|---|---|---|
| Home | Mantener el titular corto California–Florida; nombrar FTL, LTL y Transloading en la introducción | Concretar qué vende la empresa sin saturar el hero |
| Tarjetas y listado | Full Truckload (FTL), Less Than Truckload (LTL), Transloading; resumen diferente para cada uno | Facilitar comparación y reconocimiento |
| Servicio FTL | Capacidad exclusiva, envíos únicos o recurrentes, datos para cotizar | Eliminar la impresión de que solo atienden contratos recurrentes |
| Servicio LTL | Espacio compartido, compatibilidad, dimensiones y peso | Explicar el servicio sin prometer un ahorro fijo |
| Transloading | Equipo entrante, transferencia y salida; requisitos por confirmar | Separar transferencia de carga de servicios complementarios |
| Cotización | Mostrar los mismos nombres de los servicios; conservar identificadores internos | Coherencia entre lo que se lee y lo que se solicita |
| About | Usar la explicación de soluciones adaptadas del cliente | Sustituir historia de fundación no acreditada por esta fuente |
| Red y terminales | Mantener pendiente la ampliación a cinco ciudades | No confundir una ciudad atendida con una instalación propia |
| SEO | Actualizar título, descripción, H1/etiqueta, extracto y enlaces internos en conjunto | Evitar que buscadores y usuarios vean nombres contradictorios |

## Lo que no debe publicarse literalmente todavía

- «Without delay», «uninterrupted transit», «without sacrificing transit time», «on schedule and in pristine condition»: reemplazar promesas absolutas por explicación del servicio y confirmación de condiciones al cotizar.
- «A fraction of the cost»: explicar que se comparte capacidad; no cuantificar ahorro sin datos verificables.
- «High-value cargo»: no implica cobertura ilimitada ni aceptación de cualquier mercancía. Si se destaca, confirmar requisitos de declaración, exclusiones y cobertura con operaciones.
- «Expedited»: el Word lo anuncia, pero falta saber a qué servicios, rutas y condiciones aplica. No inventar días de tránsito ni frecuencia de salidas.
- «Ocean, rail, and over-the-road»: distinguir transferencia de mercancía de la contratación/operación del tramo marítimo o ferroviario. No presentar a Pelham como operador de todos los modos sin confirmación.
- «Transloading & Cross-Docking», paletización y redistribución: están en el Word pero no en la lista breve de servicios. Confirmar alcance y ubicación antes de agregarlos al catálogo público.
- Jacksonville, Tampa y Orlando: no crear direcciones, fichas LocalBusiness ni marcadores como terminales propias hasta confirmar su naturaleza.

## Contenido anterior que también requiere corrección

La página About del contenido inicial incluye una historia sobre cómo nació Pelham, «integrated fleet-and-warehouse operation», cinco instalaciones y respuesta habitual en un día hábil. Estas afirmaciones no quedan acreditadas por el material nuevo. Se propone el texto alternativo del archivo COPY-EN.md. Los nombres y cargos del equipo requieren validación aparte; este Word no los confirma ni desmiente.

La web y sus valores predeterminados también mencionan «Team drivers». No aparece confirmado en los materiales nuevos. Evitar que ese texto antiguo reaparezca como resumen de FTL.

## Aplicación técnica sin perder contenido

1. Exportar una copia de las páginas y metadatos actuales del WordPress instalado antes del cambio.
2. Mantener las URLs /services/dedicated-transport/, /services/freight-consolidation/ y /services/transloading/. Cambiar títulos visibles y metadatos; no hace falta cambiar los slugs por SEO. Si el cliente requiere nuevos slugs, preparar redirecciones 301 y actualizar canonical, sitemap y enlaces.
3. Modificar las entradas existentes de Services: post_title, pelham_service_label, post_excerpt, post_content, pelham_seo_title y pelham_seo_description. Conservar imágenes y estructura de bloques; reemplazar el texto, no el diseño completo.
4. Actualizar home, listado y About, manteniendo shortcodes y enlaces internos. Conservar anclas overview, process, prepare y questions en los servicios.
5. En cotización mantener los valores dedicated-transport, freight-consolidation y transloading para no romper preselección, guías ni solicitudes históricas. Actualizar etiquetas y ayudas editables en Pelham Company.
6. Actualizar las fuentes del repositorio y el mapa SEO para futuras instalaciones, además de los registros existentes de WordPress. Cambiar solo el ZIP del tema o del plugin NO actualiza esos registros: el importador actual crea contenido faltante y preserva páginas existentes. No recomendar reimportaciones como solución a este cambio editorial.
7. Revisar escritorio y móvil, nombres largos en tarjetas, selector de servicio, preselección desde cada CTA, enlaces, metadatos y coherencia de las FAQ visibles con cualquier dato estructurado. Mantener el sistema de animación y diseño actual.

## Preguntas para enviar al cliente

1. ¿Gardena, Miami, Jacksonville, Tampa y Orlando son terminales operadas por Pelham, instalaciones de socios o ciudades de cobertura? ¿Qué servicios se realizan en cada una? Facilitar dirección y datos públicos aprobados donde corresponda.
2. ¿Ofrecen servicio expedito tanto para FTL como para LTL? ¿En ambos sentidos California–Florida? ¿Hay tiempos estimados, frecuencias y condiciones que autoricen publicar?
3. ¿Pelham realiza cross-docking, paletización y redistribución? ¿En qué ubicaciones? ¿La referencia a ferrocarril implica una transferencia disponible o solo una descripción general de logística intermodal?
4. ¿Qué equipos y mercancías aceptan: carga seca, refrigerada, peligrosa, sobredimensionada o de alto valor? ¿Hay límites de peso/dimensiones y condiciones de seguro o declaración de valor que debamos comunicar?
5. ¿Confirman el uso de los nombres comerciales Full Truckload (FTL), Less Than Truckload (LTL) y Transloading? ¿Desean conservar Dedicated Transport y Freight Consolidation como subtítulos explicativos?

Estas preguntas delimitan afirmaciones nuevas. No hace falta detener la preparación del contenido confirmado mientras se resuelven. No se han modificado contratos, PDF firmados, solicitudes existentes ni condiciones de transporte.
