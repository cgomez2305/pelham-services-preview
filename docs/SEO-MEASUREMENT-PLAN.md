# Medición de posicionamiento y negocio

No hay métricas históricas proporcionadas. No usar estimaciones inventadas como línea base.

## Search Console

Verificar pelhamservices.com, enviar wp-sitemap.xml después de publicar e indexar, revisar consultas y páginas semanalmente. Separar marca/no marca, LTL/FTL/manejo, corredor y dispositivo. Medir impresiones, clics, CTR y posición como indicadores, no como clientes. Revisar migración de las URLs numéricas y sus destinos; no usar la herramienta de cambio de dirección cuando solo cambia el diseño/estructura en el mismo dominio.

## Analítica pendiente de configurar

Se propone GA4 o alternativa elegida por el propietario, con configuración de consentimiento adecuada. No se ha instalado un rastreador ni inventado una propiedad. Eventos a instrumentar después de esa configuración:

| Evento | Cuándo | Parámetros permitidos | Interpretación |
|---|---|---|---|
| quote_start | Primera interacción real con cotización, una vez por sesión de formulario | service, ltl_level de listas permitidas | Interés, no lead |
| quote_level_select | Cambio explícito de nivel | expedited/regular/economy | Preferencia, no venta |
| quote_submit_success | Confirmación de respuesta de backend real para ese envío | service, ltl_level | Handoff a correo; no prueba inbox ni lead calificado |
| contact_click | Clic en llamada/correo | channel phone/email | Microconversión |
| lead_qualified | Ventas confirma encaje | periodo, servicio, nivel; agregado | Resultado comercial |
| customer_won | Ventas confirma contratación | periodo, servicio; agregado | Resultado de negocio |

No disparar quote_submit_success solo por visitar ?pelham_status=sent; esa URL puede reproducirse. No registrar nombre, correo, teléfono, direcciones, ZIP, texto libre, firmas, identificadores privados ni enlaces de reanudación. Excluir de analítica formularios privados de registro/reclamaciones y tráfico interno. Evitar page_location con parámetros sensibles. No añadir tracking dentro de PDFs o documentos privados.

## Primeros 90 días

- Semana 1: comprobar etiquetas y consentimiento, enviar cotización ficticia autorizada, distinguir error de éxito y evitar doble conteo.
- Semanas 2–4: establecer línea base real por canal/página/dispositivo. Reportar cifras pequeñas sin extrapolar porcentajes engañosos.
- Mes 2: cruzar consultas con landing pages y calidad de solicitudes; priorizar objeciones frecuentes de ventas.
- Mes 3: mejorar los bloques que generan interés pero pocos leads calificados. El SEO no garantiza ventas ni posición en 90 días.

Reporte mensual: consultas no marca relevantes, tráfico orgánico a servicios, formularios recibidos, leads calificados, clientes adquiridos y tiempo real de respuesta. Si aún no existe integración CRM, ventas puede llevar el conteo agregado en una hoja privada sin exponer datos personales en analytics.
