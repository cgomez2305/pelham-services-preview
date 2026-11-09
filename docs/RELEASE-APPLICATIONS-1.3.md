# Pelham Applications 1.3 — enlace de prueba y los cuatro documentos

Pelham Applications **1.3.1** · Pelham Content **1.20.7** (traducciones) · Tema **1.24.0** sin cambios

Permite que el cliente pruebe la **solicitud de cuenta y crédito** y la **reclamación de carga** sin ser administrador y sin verificar un correo.

## Los cuatro documentos de la página anterior (1.3.1)

La página de registro anterior ofrecía 4 PDF para descargar: **Claim Form, Customer Application, Credit Application y Terms and Agreements**. El plugin ya digitalizaba los cuatro, pero en **dos formularios**, y la página no lo explicaba. Además, al reemplazar el contenido de `/register/`, ocultaba la lista de documentos.

| Documento original | Dónde se completa |
|---|---|
| Customer Application | Solicitud de cuenta en línea, pasos 1 y 2 (empresa, facturación, referencias) |
| Credit Application | Solicitud de cuenta, paso 3, solo si se pide crédito (referencias, banco, garantía personal) |
| Terms and Agreements | Solicitud de cuenta, paso 4: lectura completa, iniciales y firma |
| Claim Form | Formulario de reclamación en `/claims/` (`/es/reclamaciones/`) |

En 1.3.1, `/register/` y `/claims/` (y sus versiones en español) muestran arriba **«Your four Pelham documents» / «Sus cuatro documentos de Pelham»**. Son cuatro tarjetas, una por documento, y cada una incluye:
- en qué paso o página se completa ese documento;
- la marca «In this application» / «On this page» si es el formulario actual, o un enlace al otro formulario si no lo es;
- **View original PDF / Ver el PDF original**, que abre el PDF original sin modificar (los mismos archivos verificados en `documents/manifest.json`).

En español, cada documento aparece con su nombre en español y el nombre oficial en inglés entre paréntesis, porque los acuerdos legales se firman en inglés. Con el enlace de prueba activo, el salto entre formularios mantiene el modo de prueba.

## Cómo se usa

1. Instalar `pelham-applications-1.3.1.zip` y `pelham-content-1.20.7.zip` (reemplazan a los anteriores).
2. Administrador: *Pelham Applications → Client testing link → Create link (14 days)*.
3. Aparecen 4 enlaces privados para enviar al cliente:
   - inglés: solicitud de cuenta (`/register/?pa_test=…`) y reclamación (`/claims/?pa_test=…`);
   - español: `/es/registro/?pa_test=…` y `/es/reclamaciones/?pa_test=…`.
4. Con el enlace, la página muestra el recuadro **«Test mode» / «Modo de prueba»** con dos botones: **Probar la solicitud de cuenta** y **Probar la reclamación de carga**. Cada botón abre una solicitud nueva en su página y en el mismo idioma.
5. Al terminar las pruebas: **Delete N test requests** borra todas las solicitudes de prueba y **Disable link** desactiva el enlace.

## Qué es una solicitud de prueba

- Se crea sin verificación por correo y se abre directamente: pasos, guardado automático, acuerdos, firmas y revisión, como una real.
- Lleva la referencia **TEST-000…** y la etiqueta «TEST — no emails sent» / «PRUEBA — no se envían correos».
- **No envía ningún correo**, ni al cliente ni al equipo. Para probar los correos reales se usa el formulario normal, sin enlace de prueba, con un correo real.
- En el administrador aparece marcada como TEST RECORD.

## Seguridad

- **Enlace secreto.** Es aleatorio (48 caracteres) y solo se guarda su hash; el administrador lo ve cifrado.
- **Caducidad.** Caduca a los 14 días. *Replace link* lo renueva e invalida el anterior; *Disable link* lo anula.
- **Accesos no válidos.** Sin enlace, o con uno inválido o caducado, el recuadro no aparece y el servidor responde 403.
- **Límite de uso.** Máximo 20 solicitudes de prueba cada 15 minutos por conexión.
- **Privacidad de la página.** Las páginas abiertas con el enlace se sirven con `noindex` y sin caché, para que no queden en la caché de Hostinger ni en Google.
- **Administradores.** Siguen viendo los botones de prueba sin enlace, como antes.

## Otros cambios

- La etiqueta TEST del área de trabajo se traduce al español.
- Se corrigieron los avisos «Deprecated» de PHP en el panel de solicitudes cuando hay una sola página de resultados.

## Pruebas (WordPress 6.8.3 local, visitante sin sesión)

- **Recuadro de prueba.** Visible solo con un enlace válido; con un enlace inválido o caducado no aparece y el envío devuelve 403.
- **Solicitud de cuenta en español y reclamación en inglés.** Se abren con su referencia TEST, guardan de forma automática y no dan errores JS.
- **Botón cruzado.** Desde registro se abre la reclamación, y al revés, en el mismo idioma.
- **Correo.** No se envía ninguno.
- **Panel del administrador.** Crear el enlace (4 URL y su fecha de caducidad), borrar las solicitudes de prueba y desactivar el enlace funcionan.
- **Sitio completo.** Las 60 URL pasan la auditoría sin errores PHP ni JS.
