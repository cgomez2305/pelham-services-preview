# Refuerzo de seguridad — Pelham Applications 1.0.3 y Pelham Content 1.8.7

## Instalación

Actualizar ambos plugins mediante sus ZIP, reemplazando las versiones existentes. Mantener el tema 1.16.0. No importar contenido otra vez. Vaciar la caché del hosting y excluir `/register/`, `/claims/` y `/wp-json/pelham-applications/` de cualquier caché compartida. Los cambios están preparados y probados localmente; no se han desplegado en Hostinger.

## Cambios

- Cabeceras: nosniff, SAMEORIGIN y CSP con `frame-ancestors 'self'`, `base-uri 'self'`, `object-src 'none'`, `form-action 'self'`. Política de referente y desactivación de cámara/micrófono/geolocalización. El tema no necesita esos permisos.
- `script-src 'self'` se añade **solo en Report-Only** para detectar scripts inline o externos sin romper plugins. No es todavía una protección de bloqueo contra scripts inyectados. No se configura envío de informes a terceros; se inspeccionan en navegador.
- Páginas de solicitudes: no-store, noindex, no-referrer. El hosting/CDN debe respetar estas cabeceras.
- Contacto y cotización: control de tipos escalares, tamaños, caracteres de control, tipo de formulario y tamaño de petición. Límite por IP mediante lock atómico antes del procesamiento. Se mantienen nonce, honeypot, saneamiento, escape y redirecciones del mismo sitio.
- Registro: rechazo temprano de tipos inesperados en la API. Incremento del limitador protegido con lock y límite adicional de escrituras por sesión. No sustituye a un WAF ni a protección contra ataques distribuidos.
- Se elimina el procesamiento antiguo de PDFs enviados por correo. Con Applications activo el endpoint antiguo redirige al registro; sin él devuelve 410. Mantener Applications activo para el registro digital.
- Adjuntos de reclamaciones: cuarentena por defecto, incluyendo archivos antiguos sin resultado de análisis. Solo se permite descargar archivos con resultado limpio ligado al SHA-256 de sus bytes. Las imágenes tienen límite adicional de 24 megapíxeles. Los archivos permanecen cifrados y fuera de Media.
- Los PDFs que genera el sistema con los formularios firmados siguen disponibles para personal autorizado. No incluyen como archivos embebidos las evidencias subidas.

## Analizador de archivos: requisito pendiente

**Esta versión no incluye un antivirus ni afirma que los archivos estén limpios.** Sin analizador configurado, las reclamaciones se guardan y se envían para revisión, pero ventas no puede descargar la evidencia. Hay aviso en el panel. No existe botón de omitir la cuarentena.

Punto de integración PHP de servidor: filtro `pelham_scan_attachment($result, $bytes, $mime)`. Un adaptador confiable debe analizar exactamente esos bytes y devolver `['status'=>'clean']` solo con resultado afirmativo; `infected` para rechazo; cualquier otro resultado/error controlado debe devolver `quarantined`. No aceptar esta decisión desde el navegador. El núcleo calcula el hash; nunca confía en un hash aportado por el cliente. El adaptador debe establecer timeout y límites y fallar a cuarentena. No instalar un filtro que marque todo como limpio.

Puede conectarse a un antivirus privado del hosting o un proveedor autorizado por Pelham. No se han compartido expedientes con servicios externos. Después de configurar el analizador, un administrador puede ejecutar “Run configured attachment scanner” en cada expediente, con permiso y nonce. Se conserva la evidencia original y la huella de las revisiones.

## Acciones que requieren Hostinger/administración

- Confirmar actualizaciones de WordPress, PHP y todos los plugins; eliminar plugins sin uso.
- Activar 2FA para administradores, privilegios mínimos y protección de login/WAF.
- Confirmar HTTPS, cookies Secure en producción y HSTS solo tras comprobar todos los subdominios afectados.
- Desactivar editor de archivos desde wp-config (`DISALLOW_FILE_EDIT`) y errores visibles en producción; verificar permisos de archivos y restricciones de ejecución en uploads.
- Verificar copias cifradas y una restauración real que incluya la clave de los expedientes; revisar política de retención.
- Conectar y probar el analizador antes de operar reclamaciones con evidencia real.
- Evaluar la CSP de diagnóstico en todas las integraciones antes de convertirla en bloqueo de scripts.

No se han cambiado estas opciones del hosting ni auditado otros plugins instalados allí. No es una certificación ni una prueba de penetración completa.

## Evidencia

`tools/test-security-hardening.cjs`: cabeceras y render público, rechazo de arrays y redirección externa en contacto, tipos incorrectos de API, acceso anónimo y nonce ausente, cuarentena antes/después de solicitar análisis sin proveedor y bloqueo de descarga autorizada/anónima. `tools/test-applications-validation.cjs`: regresión de registro firmado, controles de campos y modo de prueba sin SMTP. Resultados en `docs/audit/security-1.0.3/`.

Se probaron también resultados clean/infected y excepción mediante un adaptador simulado exclusivamente local, no incluido en los ZIP. Esto verifica el control de acceso, no la eficacia de un antivirus real.
