# Pelham Applications 1.1.0 — documentos y panel de ventas

Actualizar solo Pelham Applications mediante `dist/pelham-applications-1.1.0.zip`, reemplazando la versión instalada. Mantener Pelham Content 1.8.7 y el tema actual. No reimportar contenido ni cambiar la clave de cifrado. La configuración de Cloudmersive de wp-config.php se conserva. No se ha desplegado en Hostinger.

## Panel

Lista con empresa, referencia única, tipo, estado y fecha según la zona horaria de WordPress. Filtros accesibles y ajustes/herramientas de prueba plegados. Los expedientes se organizan por Customer application, Credit application (si se solicitó), Transport terms o Freight claim. Cada documento agrupa sus campos y firmas. Los datos bancarios y fiscales están ocultos inicialmente tras una acción de mostrar.

Los datos de un expediente enviado y su exportación corresponden a la misma revisión enviada. Las correcciones aún sin enviar no sustituyen la revisión firmada. Se mantienen las descargas del expediente completo de cada revisión. Un borrador se puede revisar y exportar con la marca DRAFT — NOT SUBMITTED; no permite registrar una decisión de ventas como si estuviera enviado.

## Exportación sobre los originales

Las primeras páginas son las plantillas originales completadas en sus espacios, con sus logos, textos, tablas y líneas:

- Customer Application: una página original.
- Credit Application: dos páginas originales, incluida garantía personal.
- Terms and Agreements: **cuatro páginas originales**, iniciales y firma del cliente; contrafirma de Pelham correspondiente a esa revisión.
- Claim Form: dos páginas originales.

Se añade un suplemento claramente identificado con valores completos, datos adicionales sin casilla en el original, trazabilidad de firmas y hash del documento aceptado. Si un valor no cabe de forma legible en el campo, se muestra `[Annex]` y el texto completo en el suplemento. No se reduce indefinidamente ni se recorta el contenido. El documento mantiene las páginas originales; el total incluye el suplemento.

El archivo original de condiciones tiene cuatro páginas. Se corrigió la solicitud errónea de cinco iniciales en formularios nuevos. El quinto campo de expedientes antiguos permanece en el suplemento histórico, sin reescribir lo firmado. Fechas históricas impresas en los originales no se alteran: las fechas de firma se rellenan en sus casillas.

Los campos de uso interno reflejan la revisión administrativa al exportar y se identifican como tal en el suplemento; la firma del cliente sigue vinculada a su revisión enviada. Los archivos fuente se verifican contra la huella del documento aceptado: no se usa silenciosamente otra versión de condiciones.

## Adjuntos

La pantalla del cliente distingue archivo en subida, guardado pendiente de revisión y comprobación aprobada. Ventas ve tarjetas por evidencia con nombre, tamaño, estado y descarga solo cuando el análisis lo permite. La aprobación sigue dependiendo de Cloudmersive configurado o del analizador autorizado. Esta versión no confirma el funcionamiento del proveedor en Hostinger ni añade una cola global para su cuota de una llamada por segundo.

## Implementación y pruebas

FPDI 2.6.4 (Setasign, licencia MIT incluida) importa copias normalizadas de los originales; FPDF conserva su licencia. La normalización se realizó durante desarrollo con pypdf. No requiere instalar Python, Ghostscript, Composer ni herramientas de consola en Hostinger. Las nueve páginas normalizadas se compararon visualmente con las fuentes y resultaron idénticas en el render de prueba. Se conservan hashes de origen y plantillas.

Comprobados en WordPress local: exportación de los cuatro documentos, borrador marcado, revisión histórica con contrafirma, suplementos, descargas privadas y bloqueo anónimo, navegación del panel, escritorio/móvil sin desbordamiento y registro firmado con las validaciones existentes. Evidencia en `docs/audit/documents-1.1.0/`; datos y firmas son ficticios. Las pruebas no constituyen validación legal de firmas ni auditoría del hosting.
