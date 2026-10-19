# Propuesta: solicitud digital de cuenta Pelham

Estado: propuesta, sin cambios en el registro publicado ni en su procesamiento.

## Base revisada

Se revisaron los cuatro PDFs incluidos en `wordpress/pelham-content/assets/documents/` y el procesamiento de `inc/customer-access.php`. El registro actual recoge contacto y PDFs opcionales, usa el destinatario configurado en `pelham_company.recipient` (o el correo administrativo como respaldo), envía con `wp_mail` y borra los archivos temporales tras intentar el envío. No crea un expediente persistente ni acceso en Supply Vision. La respuesta positiva de `wp_mail` no acredita entrega al buzón.

## Recorrido propuesto

1. **Contacto y empresa.** Nombre y cargo del solicitante, correo verificado, teléfono para la llamada, razón social, tipo de empresa, inicio de actividad y domicilio. Guardado de borrador en servidor y enlace temporal de recuperación; no obliga a crear una cuenta WordPress ni implica acceso a Supply Vision.
2. **Facturación.** Domicilio con opción «igual al de la empresa», contacto de cuentas por pagar, correo/teléfono de facturación y preferencias de facturación/pago. Dos referencias comerciales, conforme al Customer Application. Los campos repetidos se reutilizan y pueden corregirse antes del envío.
3. **Crédito, cuando corresponda.** Proponer la pregunta «¿Deseas solicitar crédito?». Si responde sí: años de actividad, ventas anuales, propietario/controlador, identificación fiscal, referencia bancaria y una tercera referencia comercial, reutilizando las dos existentes. Esta condición debe ajustarse a las reglas que confirme Pelham; no se asume que pedir crédito sea obligatorio.
4. **Acuerdos y firma.** Documentos íntegros legibles en la web, aceptación separada de condiciones, autorización de consultas y garantía personal cuando corresponda. Identificar firmante, cargo, fecha y versión exacta de cada documento. La garantía personal debe tener su propia aceptación/firma; no se absorbe en una casilla genérica. El PDF de condiciones contempla iniciales por página y firma del representante Pelham: definir con la empresa su equivalente digital y contrafirma. Si se requiere un proveedor de firma, integrarlo para completar el proceso desde el navegador. No asumir equivalencia jurídica de un nombre escrito o una firma dibujada.
5. **Revisión y envío.** Resumen con enlaces para corregir cada sección. Persistir el expediente y la evidencia antes de generar las notificaciones; asignar número de solicitud, confirmar recepción y explicar que ventas revisará los datos. Generar automáticamente los PDFs de archivo desde la información enviada y la versión de las condiciones aceptadas.

No pedir al solicitante que descargue/rellene/vuelva a subir estas plantillas. Mantener adjuntos únicamente para justificantes externos que sean necesarios. El Claim Form debe convertirse, por separado, en un formulario de reclamaciones asociado al BOL y con facturas/fotografías justificativas; no forma parte de la solicitud de cuenta.

## Experiencia

- Barra de pasos, indicadores de avance y botones de volver/continuar.
- Campos condicionales, autocomplete, formatos adecuados para teléfono/correo/códigos postales y errores junto al campo y en un resumen accesible.
- No perder respuestas al retroceder o corregir errores; mostrar cuándo se guardó el borrador.
- Recuperación mediante correo verificado y enlace temporal; no almacenar números bancarios o identificación personal en localStorage.
- Documentos largos legibles en móvil, sin obligar a completar todo en una sesión ni a dibujar una firma con el dedo.
- Confirmación con referencia y siguientes pasos. No prometer plazos de respuesta que Pelham no haya definido.
- Actualizar los textos del registro, onboarding en cotización y aviso de privacidad para describir el nuevo almacenamiento.

## Plugin y expediente

Recomendación: plugin independiente **Pelham Applications**, compatible con el tema y Pelham Content. Separar esta función operativa del tema para conservar solicitudes cuando se actualice el diseño. Sustituir el punto de entrada del registro existente mediante una migración controlada, sin dos formularios o procesadores activos simultáneamente.

Guardar registros privados y estructurados en WordPress: identificador, empresa/contactos, facturación, referencias, rama de crédito, versiones/documentos/firmas, responsable, estado y fechas. Congelar una revisión al enviar; las correcciones posteriores generan una nueva revisión conservando el historial. Guardar PDFs y adjuntos fuera de la biblioteca pública y servirlos únicamente tras comprobar autorización.

Panel «Solicitudes de clientes» para usuarios de ventas autorizados: búsqueda y filtros, ficha completa, notas internas, responsable, próxima llamada, solicitud de correcciones y exportación de expediente/PDF. Separar el estado comercial de la decisión de crédito y del estado de acceso a plataforma.

Flujo comercial sugerido: borrador → recibida → en revisión → pendiente de información → contactado → aprobada o rechazada. «Pendiente de información» devuelve al cliente únicamente las secciones a completar, mediante acceso seguro. El panel debe mostrar si falta firma o contrafirma.

## Correo y fiabilidad

Destinatario de ventas configurable, sin asumir que la dirección histórica del PDF siga siendo la correcta. Aviso con número de solicitud, empresa, contacto, teléfono, solicitud de crédito y enlace al expediente privado. El agente revisa allí todos los datos y documentos. Mantener números de cuenta/SSN fuera del cuerpo y adjuntos de correos ordinarios; confirmar qué datos fiscales son realmente necesarios antes de trasladar literalmente el campo «FEIN or SS#» del PDF.

Enviar confirmación al solicitante y avisos de corrección o decisión según las acciones del equipo. Cola de notificaciones con registro y reintentos, prevención de envíos duplicados y transporte SMTP/API autenticado. Distinguir «guardada», «notificación pendiente/enviada» y entrega confirmada cuando el proveedor la informe. Un fallo de correo no debe borrar la solicitud ni pedir al cliente que vuelva a completar el registro.

## Supply Vision

El registro digital puede funcionar sin API. Tras aprobar la solicitud, el equipo crea o habilita al cliente en Supply Vision y registra en el expediente su identificador y el estado de acceso. Una integración automática es una fase posterior, dependiente de documentación, permisos y capacidades confirmadas de Supply Vision; no se presupone un endpoint disponible.

## Entrega y comprobaciones

1. Acordar matriz de campos obligatorios/condicionales, destinatario de ventas, versiones definitivas de los acuerdos y mecanismo de firma/contrafirma. Preparar prototipo del recorrido y del expediente.
2. Implementar formulario, borradores seguros, almacenamiento privado, panel de ventas, PDF generado y notificaciones con reintentos. Migrar el registro actual sin alterar solicitudes ni textos ajenos.
3. Validar el recorrido completo con/sin crédito, móvil/teclado, corrección/reanudación, acceso no autorizado, firmas, generación de PDF, doble envío y caída del correo. Probar entrega real únicamente a buzones de prueba designados.
4. Evaluar integración con Supply Vision cuando exista documentación confirmada.

Decisiones de negocio necesarias para cerrar el alcance: correo y responsables de ventas; si el crédito es opcional y quién lo aprueba; qué firmantes deben intervenir y qué solución de firma acepta Pelham; campos fiscales/bancarios realmente requeridos y política de conservación de expedientes.
