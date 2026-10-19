# Analizador opcional para hosting compartido — 1.0.4

## Propuesta pendiente de aprobación

Cloudmersive ofrece una API HTTPS de análisis avanzado compatible con peticiones salientes de WordPress. El conector está implementado, pero **desactivado por defecto**. No se ha creado una cuenta, verificado una suscripción, activado en Hostinger ni transmitido archivos reales.

Documentación consultada: https://api.cloudmersive.com/docs/virus.asp — `POST /virus/scan/file/advanced`, multipart `inputFile`, autenticación de servidor mediante cabecera `Apikey`.

Antes de activarlo, Pelham debe aprobar el proveedor y comprobar con él el contrato de tratamiento, conservación de documentos, región de procesamiento, subencargados, precios/cuotas y acceso al endpoint avanzado. Los archivos pueden contener datos personales y financieros. No se afirma que el proveedor no los conserve. El conector usa el endpoint global documentado, sin garantía de región específica. No se ha evaluado una oferta contractual para Pelham.

## Qué hace el código

Envía solo el archivo y su tipo, con nombre neutro. No añade email, empresa, expediente ni nombre original; el contenido del propio documento sí puede identificar al cliente. La clave permanece en configuración de servidor, no en JavaScript, páginas ni respuestas de la API del sitio.

TLS verificado, destino HTTPS fijo, sin redirecciones, timeout de ocho segundos y respuesta limitada a 64 KB. Hasta 2 MB por adjunto. Solicita bloqueo de ejecutables, formatos inválidos, scripts, contraseñas, macros, archivos inseguros y acciones no deseadas. Restringe tipos a PDF/JPG/PNG/WebP.

Solo HTTP 200 con `CleanResult` booleano verdadero, lista `FoundViruses` vacía y sin indicadores contradictorios permite aprobación. `CleanResult=false` rechaza el archivo (puede ser por política de contenido, no necesariamente malware). Respuesta incompleta, cuota agotada, error de autenticación, timeout, excepción o indisponibilidad conservan la cuarentena. El hash de los bytes aprobados se vincula al archivo descargable.

Los archivos antiguos no se envían automáticamente al activar. Un administrador solicita su análisis por expediente mediante el botón existente. Con el conector activo, los nuevos adjuntos sí se envían para análisis al subirlos.

## Instalación sin activar el servicio

Actualizar únicamente Pelham Applications a 1.0.4. Mantener Pelham Content 1.8.7 y el tema existente. Instalar el ZIP no autoriza ni activa transmisión externa. Sin configuración, se conserva la cuarentena actual.

## Activación posterior a la aprobación

Un administrador del hosting debe añadir en wp-config.php, antes de cargar WordPress, una clave real obtenida por Pelham y la aprobación explícita:

```php
define('PELHAM_CLOUDMERSIVE_APPROVED', true);
define('PELHAM_CLOUDMERSIVE_API_KEY', 'CLAVE_REAL_DEL_PROVEEDOR');
```

No pegar claves en el chat ni versionarlas. Usar configuración protegida y copias seguras. No añadir estas constantes hasta aprobar proveedor, contrato y coste. Cambiar APPROVED a false detiene nuevos envíos; los expedientes ya aprobados conservan su resultado.

Verificar que el hosting permite HTTPS saliente a api.cloudmersive.com y que el plan contratado soporta análisis avanzado. Probar primero en staging con documentos sintéticos: PDF válido aprobado, contenido de prueba del antivirus rechazado, clave incorrecta y cuota/error mantienen cuarentena. Confirmar descarga privada y ausencia de datos en logs públicos. No ejecutar muestras de malware real.

## Evidencia y límites

Nueve casos probados dentro de WordPress con transporte simulado: limpio, rechazado, booleano falso en formato de texto, respuesta incompleta, indicador contradictorio, 429, redirección, timeout y JSON inválido. Comprobados límites de transporte, preservación de bytes y desactivación por defecto. No prueban eficacia antivirus, entrega real al proveedor, disponibilidad de Hostinger ni funcionamiento de una cuenta de pago.

Reproducción de QA: montar `tools/fixtures/cloudmersive-test.php` como MU-plugin **solo en WordPress local desechable** con el adaptador y usuario de QA existentes; ejecutar `node tools/test-cloudmersive.cjs`. Nunca instalar el fixture de pruebas en un sitio público. No está incluido en el ZIP.
