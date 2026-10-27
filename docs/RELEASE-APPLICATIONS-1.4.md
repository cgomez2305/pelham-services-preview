# Pelham Applications 1.4 — cuatro formularios independientes

Pelham Applications **1.4.0** · Pelham Content **1.20.8** (traducciones) · Tema **1.24.0** sin cambios

El cliente pidió que los 4 documentos de su antigua página de registro fueran **formularios independientes que no se mezclen**. Hasta la 1.3, Customer Application, Credit Application y Terms and Agreements eran una sola solicitud con un único envío.

## Cómo funciona ahora

`/register/` (`/es/registro/`) muestra **«Your four Pelham documents» / «Sus cuatro documentos de Pelham»**. Cada tarjeta abre su propio formulario:

| Documento | Dirección | Pasos | Firmas |
|---|---|---|---|
| Customer Application | `/register/?form=customer` | Empresa → Facturación → Referencias → Acuerdo y firma → Revisar y enviar | Solicitud de cliente |
| Credit Application | `/register/?form=credit` | Empresa → Negocio y banco → 3 referencias → Acuerdos y firmas → Revisar y enviar | Solicitud de crédito y garantía personal (la garantía puede reutilizar la firma de crédito) |
| Terms and Agreements | `/register/?form=terms` | Empresa → Leer los términos → Iniciales por página → Acuerdo y firma → Revisar y enviar | Términos y condiciones de transporte (+ contrafirma de Pelham en el panel) |
| Claim Form | `/claims/` | Sin cambios | Reclamación |

Las direcciones en español son las mismas con `/es/registro/?form=…`.

**Cada formulario es independiente:**
- tiene su propio borrador (el mismo correo puede tener uno de cada tipo);
- tiene su referencia (PEL-…), su firma y su envío;
- envía su propio acuse de recibo al cliente y su propio aviso al equipo, con el nombre del formulario;
- genera solo su PDF original relleno y firmado.

El enlace del correo de verificación abre directamente el formulario correspondiente.

## Panel del equipo

- **Filtro por formulario.** Customer Application, Credit Application, Terms and Agreements, Claim Form y «Combined application (before 1.4)». La columna Tipo muestra el nombre del formulario.
- **Cada ficha muestra solo su documento:**
  - **Credit Application:** decisión de crédito y límite aprobado.
  - **Customer Application:** alta en Supply Vision.
  - **Terms and Agreements:** contrafirma del representante de Pelham.
- **Solicitudes anteriores a la 1.4.** Las que combinaban los tres documentos se siguen viendo, revisando y exportando igual que antes. Un borrador antiguo se puede retomar con su enlace; para empezar una nueva solicitud se usan solo los 4 formularios.

## Modo de prueba

El enlace de prueba (*Client testing link*) muestra un botón por formulario: **Test Customer Application, Test Credit Application, Test Terms and Agreements y Test Claim Form**. En español: «Probar la solicitud de cliente», etc.

## Pruebas (WordPress 6.8.3 local)

- **Por API:** los 4 formularios creados con el enlace de prueba, rellenados, firmados (Credit con 2 firmas) y enviados, cada uno con su referencia. La reclamación incluye la factura adjunta.
- **En el navegador, como un visitante con el enlace de prueba:** los 4 formularios en español y en inglés, rellenando cada paso, dibujando las firmas (la garantía reutiliza la firma de crédito) y enviando. 8 de 8 completados, sin errores JS.
- **PDF:** cada envío genera solo su documento original, relleno y firmado:
  - Customer: 3 páginas;
  - Credit: 5 páginas, con firma de crédito y de garantía;
  - Terms: 5 páginas, con iniciales y firma;
  - Claim: 4 páginas.
- **Flujo real:** una Credit Application empezada con correo envía «Pelham — continúe su solicitud», con enlace a `/es/registro/?form=credit`. La API rechaza crear solicitudes combinadas nuevas.
- **Panel:** el filtro, la columna Tipo y las secciones de cada ficha son correctos, y la solicitud antigua combinada sigue completa.
- **Diseño:** página de inicio y formularios a 1440 px y 390 px, sin desbordamiento. No queda texto en inglés indebido en las páginas en español.
- **Sitio completo:** las 60 URL pasan la auditoría sin errores PHP ni JS.
- **Límite de seguridad:** al superar 20 solicitudes de prueba en 15 minutos, el servidor responde 429.
