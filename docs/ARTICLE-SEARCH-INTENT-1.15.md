# Intención de búsqueda y resolución de tareas — Pelham Content 1.15

## Qué se analizó

Las 149 keywords únicas del research: 96 relevantes y 53 fuera de la oferta/entidad. La matriz `docs/keyword-research-2026-10/intenciones-149-keywords.csv` identifica para cada una la intención, pregunta del lector, respuesta necesaria, destino, recurso aplicado, siguiente paso y limitación.

La clasificación se infiere del lenguaje de la consulta, el contenido y los servicios confirmados. No es un estudio de SERP en vivo: los CSV de Keyword Planner no contienen los resultados orgánicos, ni métricas de retención. Las intenciones ambiguas deben contrastarse con Google y Search Console tras la publicación. No se presenta esta inferencia como observación de posiciones.

### Resultado honesto de la cobertura

- 81 consultas tienen una respuesta editorial acorde con la tarea inferida.
- 12 consultas de precio/rates tienen resolución parcial: el contenido explica los factores y facilita una cotización, pero no ofrece el importe o rango que algunos lectores buscan. Sin tarifa aprobada, no se inventa una cifra. El cliente confirmó que por ahora mantendremos la cotización individual.
- Tracking: ayuda por contacto; no hay buscador público confirmado.
- Freight forwarding: se responde al tramo doméstico; no al internacional que también puede sugerir la consulta.
- Reviews: sigue pendiente de reseñas verificables.
- 53 consultas excluidas: no se crea contenido para homónimos médicos, otras marcas ni servicios ajenos.

Esto refina la matriz anterior de 95 keywords trabajadas: tener contenido asignado e implementado no significa satisfacer completamente todas las expectativas posibles de una consulta. La nueva matriz distingue ambas cosas.

## Hallazgos editoriales

La comparación LTL/FTL repetía definiciones al final; coste y forwarders añadían un bloque de keywords después de su CTA. Otros artículos abrían con una descripción del tema sin responder y casi todos terminaban con el mismo cierre comercial. La guía de combustible dedicaba espacio a niveles LTL en vez de explicar el cargo. La de retrasos llevaba al lector a cotizar, aunque podía necesitar ayuda para un envío existente. La página de tendencias no sustentaba una panorámica actual del mercado.

## Cambios aplicados a los once artículos

| Artículo / tarea | Respuesta y recurso | Siguiente paso |
|---|---|---|
| LTL vs FTL: entender y elegir | Definición inicial, tabla de diferencias, criterios y dos escenarios; sin definiciones duplicadas | Servicio adecuado o ayuda para elegir |
| Coste de pallets: conseguir un precio | Limitación de tarifa explícita al inicio, tabla de factores, ejemplo de cargas distintas y comparación de presupuestos | Cotización con dimensiones/peso/ruta |
| Requisitos de cotización: preparar información | Checklist, plantilla visible para copiar y qué hacer con datos desconocidos | Enviar datos al formulario |
| Entrega a forwarders: coordinar recepción | Responsabilidades por parte, preguntas al receptor y ejemplo de entrega a Miami | Solicitar tramo doméstico |
| Transloading vs cross-docking: entender el manejo | Diferencia al inicio, tabla con ejemplos y cómo describir la tarea | Checklist de transferencia o consulta de manejo |
| Recurrencia: preparar un patrón de envíos | Datos habituales y excepciones, brief reutilizable y tabla de cambios | Revisión del patrón con ventas |
| Fuel surcharge: entender un cargo | Definición, fórmula condicional sin tarifa de Pelham, preguntas y prevención del doble conteo | Aclarar una cotización existente |
| Preparación de contenedor: organizar la transferencia | Pack de información por categoría, responsabilidades y checklist previo al despacho | Consulta de transferencia |
| Retrasos/cambios: decidir qué hacer ahora | Pasos según el envío esté en tránsito, no esté listo o cambie la recepción | Contacto de soporte, no otro presupuesto por defecto |
| Proceso Pelham: conocer los pasos | Enquiry → elección → confirmación → coordinación → seguimiento | Acción según etapa del envío |
| Tecnología: evaluar lo que ofrece un transportista | Preguntas sobre actualizaciones, documentos y coordinación; worksheet | Aclarar capacidades con Pelham |

La antigua URL de tendencias se conserva, pero el título y la promesa pasan a «Freight technology: questions shippers should ask». No se anuncian tendencias actuales sin fuentes ni capacidades no confirmadas. No se creó una segunda URL para competir con ella.

Todos los artículos comienzan con una respuesta breve visible, seguida por secciones orientadas a resolver la tarea. Se conservaron slugs, fechas originales, estado, autor e imágenes. Los recursos usan bloques editables de WordPress, tablas HTML accesibles y listas; no añaden formularios que recojan datos, dependencias, popups ni rastreadores.

## UX y retención

Se facilita una respuesta rápida y la continuación cuando sea necesaria: índice funcional, comparativas legibles, plantillas y enlaces contextuales. El objetivo no es alargar la visita artificialmente. Resolver una pregunta en pocos segundos puede ser un buen resultado; un tiempo largo también puede indicar dificultad.

No se afirma que la retención haya aumentado. Una vez configurada la analítica y el consentimiento, evaluar por artículo:

- Paso a servicio/cotización cuando la intención es comercial.
- Paso a contacto para dudas o problemas con un envío existente.
- Uso del índice y profundidad de lectura como señales de interacción, no de satisfacción garantizada.
- Cotizaciones recibidas y calificadas como resultado comercial, no solo clics.
- Consultas reales y páginas en Search Console para ajustar la intención inferida.

Comparar periodos suficientes y tráfico comparable; no inventar una mejora porcentual ni considerar el tiempo de permanencia una garantía de ranking. No instalar analítica de terceros sin la configuración acordada ni enviar PII.

## Instalación

Instalar `dist/pelham-content-1.15.0.zip` reemplazando el plugin. Mantener Pelham Modern 1.18.0. Completar antes las actualizaciones anteriores pendientes. En Pelham Company, pulsar **Apply reader-focused articles (save backups)** y limpiar caché.

Reemplaza el texto y SEO de los once artículos con copia en `pelham_before_article_intent_1_15`; las personalizaciones dentro de ellos se sustituyen. No cambia URLs, fecha de publicación, estado, autor ni imagen. La operación es idempotente. Esta entrega no incluye un botón de restauración automática ni se instala remotamente en Hostinger.

## Validación

`tools/test-article-intent-1.15.cjs`: actualización administrativa, rechazo sin nonce, respaldos/preservación y reejecución; once artículos con respuesta inicial, H1/title/meta, índice, IDs/encabezados sin duplicados, recursos prácticos, tablas con caption y cabeceras, enlaces y ausencia de desbordamiento en 1440/390/320 px. Capturas con movimiento reducido; no son pruebas de animación ni de retención real.

`tools/build-search-intent.py`: genera las 149 filas con su procedencia y distingue los límites de resolución. No inventa datos de SERP ni KD.
