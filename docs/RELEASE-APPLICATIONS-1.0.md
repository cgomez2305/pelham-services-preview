# Pelham Applications 1.0.3

Plugin adicional para WordPress 6.4+, PHP 8.0+ con OpenSSL y HTTPS en producción. Compatible con Pelham Modern 1.16.0 y Pelham Content 1.8.6. No cambia esos paquetes ni publica cambios en Hostinger.

## Instalación

1. Subir `dist/pelham-applications-1.0.3.zip` en Plugins → Añadir plugin → Subir y activar. Mantener activos el tema y Pelham Content.
2. En Pelham Applications → Settings, configurar el correo real de ventas y la caducidad de borradores (30 días por defecto).
3. Configurar el transporte de correo de WordPress con el proveedor de la empresa. Mantener WP-Cron operativo; para bajo tráfico usar un cron del hosting. Comprobar recepción real del enlace de verificación, aviso a ventas y acuse al cliente. “Accepted” significa aceptado por el transporte, no entrega confirmada en la bandeja.
4. Asignar el rol Pelham Sales únicamente al personal autorizado. Revisar privacidad y retención con la empresa antes de recoger información real.
5. Vaciar caché y excluir `/register/`, `/claims/` y `/wp-json/pelham-applications/` de caché compartida. Probar ambos formularios sin sesión de administrador.

El plugin reemplaza el formulario antiguo al renderizar `/register/`, conserva el contenido guardado de la página y crea `/claims/` si no existe. Desactiva el envío del formulario antiguo mientras está activo. No crea automáticamente usuarios de WordPress ni cuentas de Supply Vision.

## Cobertura documental

Los cuatro originales y sus hashes se conservan en `documents/manifest.json`. Los PDFs generados reconstruyen los datos y textos originales; no replican su maquetación exacta.

| Original | Digitalización |
| --- | --- |
| Customer Application | Empresa, domicilio, entidad, inicio de actividad, facturación, cuentas por pagar, medios de pago, dos referencias, acuerdos y firma. Fecha automática; aprobación y límite de crédito internos. |
| Credit Application | Contacto, identificación fiscal, actividad, ventas, propietario, banco y cuenta, tres referencias, autorización de crédito y firma. Garantía personal con nombre y firma independiente. Aparece al solicitar crédito. |
| Terms and Agreements | Texto de las cinco páginas, iniciales por página, nombre/cargo/firma del cliente. Contrafirma del representante de Pelham desde el expediente. |
| Claim Form | BOL, fecha, porte, remitente/destinatario, mercancía, peso/cantidad, pérdida/daño, importe, factura, reparación y fotografías, declaración y firma. Flujo separado para reclamaciones. |

Firma electrónica mediante trazo o imagen PNG/JPG/WebP (normalizada); no es un certificado de firma digital. Cada documento requiere confirmación propia, aun al reutilizar la imagen. Cambiar información o adjuntos invalida las firmas. Se guardan fecha, método, versiones documentales y huellas del contenido. Las correcciones crean nuevas revisiones sin sustituir las enviadas. Pelham debe validar que este mecanismo satisface sus requisitos contractuales.

## Operación y datos

El cliente verifica su email con enlace de un solo uso de 30 minutos. La sesión privada dura hasta 24 horas. Puede retomar un borrador solicitando otro enlace. Ventas revisa, asigna responsable, registra seguimiento, solicita correcciones y registra decisiones de crédito y alta manual en plataforma.

Datos y adjuntos se cifran con AES-256-GCM en la base de datos; no se publican en la biblioteca multimedia. Los correos de ventas enlazan al expediente privado y no incluyen cuentas bancarias ni identificación fiscal. Archivos de reclamación: hasta 2 MB por categoría; firma: hasta 1 MB. Pueden agruparse fotografías en un PDF. La eliminación automática afecta solo borradores caducados, no expedientes enviados.

**Copias de seguridad:** conservar también las claves de WordPress. Por defecto el cifrado deriva de `wp_salt('auth')`; cambiar las salts impediría leer expedientes existentes. Antes de usarlo se puede definir una clave estable y secreta `PELHAM_APPLICATIONS_KEY` en wp-config.php. No cambiarla después sin migrar los datos. Respaldar base de datos y clave por canales seguros. Desactivar el plugin conserva los expedientes.

## Validación

Pruebas locales reales sobre WordPress con Chromium/Playwright: solicitud con crédito y cuatro firmas, contrafirma y PDF; solicitud sin crédito desde móvil con trazo y subida de imagen; corrección y segunda revisión; reclamación con factura y foto. Se verificaron CSRF/origen, token de un uso, acceso privado, invalidación de firmas, envío idempotente y conservación ante fallo del correo. Evidencia: `docs/audit/applications-1.0/` y `tools/test-applications.cjs`.

El correo se interceptó exclusivamente en el entorno local de pruebas. No se verificó entrega real ni se instaló este paquete en Hostinger. El test necesita un WordPress de QA y su adaptador local de correo/inspección; ese adaptador y sus credenciales no se distribuyen en el plugin. La vista previa estática del repositorio no ejecuta este backend.

Incluye FPDF 1.8.6 de Setasign/FPDF, conservando su licencia en `vendor/fpdf/license.txt`.

## Pruebas sin SMTP (1.0.1)

Actualizar el plugin subiendo `pelham-applications-1.0.3.zip` y elegir reemplazar la versión existente. No es necesario reinstalar el tema ni el plugin de contenido.

Iniciar sesión como administrador y abrir **Pelham Applications → Test account application** o **Test freight claim**. También aparecen estos botones en el registro para administradores. Se crea un expediente independiente con email ficticio y referencia TEST; permite rellenar datos, firmar, enviar y revisar desde WordPress sin correo. Utilizar solo datos ficticios. No se envían notificaciones para estos expedientes, tampoco al revisarlos. Para repetir, crear otra prueba. Los visitantes y el personal sin permisos de administrador no pueden iniciarlas. La verificación de correo de los clientes permanece intacta.

Esto prueba formularios y almacenamiento, no el transporte de correo. Antes de aceptar clientes reales sigue siendo necesario configurar y verificar entrega de email.

Se eliminó el segundo contenedor centrado: título y formulario comparten ahora el contenedor del header. Validado con captura de escritorio y móvil, inicio de prueba sin SMTP y ausencia de botones de prueba para visitantes.

## Validaciones de datos (1.0.2)

Aplicadas al registro digital y reclamaciones. Teléfonos y faxes usan formato NANP de diez dígitos, prefijo +1 opcional y extensión de hasta seis dígitos. Se rechazan prefijos estructuralmente imposibles y repeticiones obvias. NANP también incluye Canadá y otros territorios: el formato no demuestra ubicación en EE. UU., asignación vigente ni titularidad. El cliente puede escribir espacios, guiones y paréntesis.

ZIP: cinco dígitos o ZIP+4, conservando ceros iniciales; 00000 se rechaza. Estados/territorios y códigos militares mediante selección permitida. No se afirma que el ZIP corresponda a la ciudad/estado ni que la calle sea entregable. Las direcciones libres de reclamaciones se conservan según el documento original.

Correo: sintaxis y dominio público, sin dominios reservados de ejemplo/prueba en registros reales. El correo principal mantiene verificación por enlace de un uso; los correos de referencias y facturación no tienen verificación de titularidad. No se consulta DNS/MX ni se determina si un buzón existe. Los expedientes TEST permiten su dirección sintética.

El servidor vuelve a validar antes de firmar y enviar, aunque se omita JavaScript. Guardar un borrador incompleto sigue permitido. Se rechazan arrays/objetos, caracteres de control y tamaños excesivos en campos escalares; se mantienen lista de campos permitidos, saneamiento, escape de salida, CSRF, origen, sesiones privadas y límites de solicitudes. No se promete prevención absoluta de fraude.

Para confirmar direcciones reales se requiere una integración postal (USPS o proveedor equivalente); para titularidad telefónica, OTP por SMS. No se enviaron datos personales a nuevos proveedores ni se configuró una API externa. Estas comprobaciones no están incluidas en esta versión.

Prueba local: `tools/test-applications-validation.cjs` comprueba rechazo del servidor para teléfono, ZIP, estado y email incorrectos, rechazo de arrays, aceptación de +1/extensión/ZIP+4 y envío firmado sin notificaciones en el modo TEST.

## Actualización de seguridad 1.0.3

Leer `SECURITY-HARDENING-1.0.3.md`: actualizar también Pelham Content a 1.8.7. Los adjuntos de evidencia quedan en cuarentena hasta configurar un analizador; no se pueden descargar sin aprobación del analizador. Esto incluye adjuntos antiguos. Los PDFs generados por el sistema siguen disponibles.
