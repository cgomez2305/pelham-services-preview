# Blog y direcciones confirmadas — Content 1.11.0 / Modern 1.17.1

## Entrega

Nueve artículos existentes reescritos: tendencias, empresa, desafíos operativos, cross-docking/transloading/paletización, LTL frente a FTL, cotización, recurrencia, combustible y preparación de transferencias. Se eliminan referencias no acreditadas a cinco instalaciones, flota propia, tecnología o beneficios garantizados. Cross-docking y paletización pasan a describirse como servicios confirmados. Se añaden requisitos prácticos, enlaces relacionados y CTA específicos. No se crean artículos duplicados ni se inventan casos de clientes.

El calendario `BLOG-EDITORIAL-PLAN.md` propone dos piezas por mes durante cuatro meses, combina estas mejoras con futuras guías y marca lo que requiere validación operativa. No programa publicaciones automáticamente. Los temas nuevos aún no son artículos publicados.

## Instalaciones

Datos entregados directamente por el cliente:

- **Pelham Services Miami:** 8005 NW 80th St, Door 16 to 30 Suite #1, Miami, FL 33166, United States. Tel. 323-201-4995.
- **Pelham Services Los Angeles:** 249 East Redondo Beach Blvd, Gardena, CA 90248. Se conserva el teléfono ya guardado en WordPress; no se asigna uno nuevo.
- **Pelham Services Orlando (Land Stand):** 9777 Satellite Blvd suite 130, Orlando, FL 32837, United States. Sin teléfono ni horario inventado.

Se actualizan fichas, directorio, enlaces de mapas por dirección y SEO local. Orlando obtiene `/locations/orlando-fl/`. El footer del tema recorre las tres instalaciones; los enlaces telefónicos se omiten cuando falta el número. No se crean fichas de Google ni se asignan coordenadas. Los PDFs originales y las revisiones firmadas no se cambian.

## Instalar

1. Respaldar el WordPress instalado.
2. Reemplazar **Pelham Content** con `pelham-content-1.11.0.zip` y **Pelham Modern** con `pelham-modern-1.17.1.zip`. Applications permanece en 1.1.1.
3. En **Pelham Company**, pulsar **Update 9 blog articles (save backups)** y **Update confirmed locations (save backups)**. No hace falta reaplicar el resto de los textos de home/servicios si ya se instaló la oferta anterior.
4. Vaciar las cachés y revisar blog, contacto, directorio, Orlando y footer en móvil.

Las acciones reemplazan el contenido de las entradas enumeradas, incluidos cambios manuales, guardando copia por entrada antes de escribir. Blog preserva URLs, fechas originales, autores, estado, comentarios e imágenes destacadas; WordPress actualiza la fecha real de modificación. No hay transacción global: ante error se informa y se conservan las copias de lo ya procesado. No se vuelve a aplicar al repetir el POST después del éxito. Las páginas de ubicación guardan sus datos anteriores. El plugin no publica cambios remotos al subir archivos al repositorio.

La protección noindex de Hostinger se conserva. No se activa indexación ni se migró pelhamservices.com.

## Verificación local

Pruebas completadas: nueve artículos con copia previa; conservación de slug, fecha, estado, autor e imagen destacada; un H1, descripciones, CTA y tabla de contenidos. Revisión de escritorio/móvil sin desbordamiento en la guía de cotización. Direcciones confirmadas, teléfono de Miami, ausencia de teléfono inventado en Orlando y su enlace en footer comprobados. Evidencia en `docs/audit/blog-1.11/`. No se ha desplegado esta versión en Hostinger.
