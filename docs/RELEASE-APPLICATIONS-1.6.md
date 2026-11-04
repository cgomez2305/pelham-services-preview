# Pelham Applications 1.6 — documentos con diseño profesional y reclamación sin bloqueos

Pelham Applications **1.6.0** · Pelham Content **1.21.9** (traducciones) · Tema **1.26.3** sin cambios

## 1. Documentos con diseño nuevo
El cliente veía los PDF «muy de principiante, muy hechos a mano», porque se rellenaban sobre los formularios escaneados antiguos. Ahora los cuatro documentos se generan con un diseño propio:

| Documento | Contenido |
|---|---|
| Customer Application | Contacto, empresa, facturación, nota de pagos, 2 referencias, acuerdo (3 puntos), casillas aceptadas, firma autorizada y recuadro «For Pelham use only» (fecha, límite de crédito, aprobado por) |
| Credit Application | Contacto, empresa, banco, 3 referencias comerciales, condiciones de crédito, firma del propietario o directivo, y garantía personal con la firma del garante en la misma página |
| Terms and Conditions of Contract | Las 25 cláusulas numeradas, las iniciales en el pie de cada página, el reconocimiento final, y las firmas del cargador y del representante de Pelham una al lado de la otra |
| Standard Claim for Loss/Damage | Envío, cargador y destinatario, mercancía y reclamación, y la firma del reclamante junto a la lista de documentos de soporte, en una página |

Elementos comunes a los cuatro:
- **Cabecera:** logo, título, dirección y teléfono, y la referencia con su estado. Si es un borrador, aparece en rojo «DRAFT — NOT SUBMITTED».
- **Datos:** en una cuadrícula con etiquetas pequeñas y fondo verde muy claro.
- **Pie:** documento, número de página e iniciales (en los términos).
- **Registro de firma electrónica:** firmante, fecha y hora UTC, método, huella de los datos firmados y huella del documento original.

**Texto legal:** se copia literalmente de los documentos originales en `documents/designed-text.json`. Solo se corrigió un error de la extracción del PDF («we as k» → «we ask»).

**Formato original:** el equipo conserva el formato en papel. En el panel, cada documento tiene «Export … PDF» (diseño nuevo) y «Original paper layout».

**Privacidad en la vista previa:** en la vista previa del solicitante, el FEIN/SSN y el número de cuenta bancaria se muestran con sus 4 últimos dígitos. El equipo los ve completos.

## 2. La reclamación «no dejaba finalizar»
Al enviar, el servidor devolvía el problema, pero el formulario solo lo mostraba si el campo estaba en el paso visible. En el paso 5 aparecía «Please complete the highlighted items before sending», sin nada resaltado. El caso más probable: el cliente firmó, volvió atrás y añadió una foto o corrigió un dato. Eso borra la firma, porque la firma cubre toda la información y las pruebas, y no se avisaba.

Ahora:
- **Errores visibles:** al enviar, el aviso enumera cada problema con un botón **«Go to step N»**, y el formulario salta al paso del primer error.
- **Aviso de firma borrada:** si una edición o un archivo nuevo borran la firma, se avisa en el momento («sign again in step 4»).
- **Factura obligatoria:** el paso 3 no deja avanzar sin la factura de costo.
- **Campos condicionales:** con «Loss», las piezas perdidas son obligatorias; con «Damage», el alcance del daño. Se marcan con asterisco según el tipo elegido.

## Pruebas (WordPress 6.8.3 local)
- **Reclamación realista:** completa y enviada. Si se firma y después se añade una foto, aparece el aviso y, al enviar, salta al paso 4 con «Sign the Freight claim». Con «Loss» sin piezas perdidas, no deja pasar del paso 3.
- **Solicitud en el navegador** (en español) y **por API** (con crédito, sin crédito y reclamación): enviadas.
- **Vista previa del solicitante:** los tres documentos en el diseño nuevo, marcados DRAFT y con los datos sensibles ocultos.
- **Exportación:** los 35 registros enviados del sitio local generan los PDF en el diseño nuevo y en el formato original.
- **Sitio completo:** las 60 URL sin errores PHP ni JS. Panel de administración sin errores.

## Instalación
Subir `pelham-applications-1.6.0.zip` y `pelham-content-1.21.9.zip`, reemplazando las versiones instaladas, y vaciar la caché de LiteSpeed.
