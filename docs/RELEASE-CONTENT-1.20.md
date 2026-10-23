# Pelham Content 1.20 — revisión de los artículos del blog

Pelham Content **1.20.0** · Tema **1.24.0** y Pelham Applications **1.2.0** sin cambios

Incorpora a las 11 guías del blog, en inglés y en español, los cambios con sentido de los documentos reescritos por el cliente.

**Se conserva la estructura SEO:**
- URLs y slugs;
- titles, descriptions y H1;
- «The short answer» al inicio (fragmento destacado);
- los H2 con keyword y sus anclas;
- los enlaces internos.

## Instalación

1. Hacer una copia de seguridad.
2. Reemplazar Pelham Content por `pelham-content-1.20.0.zip`.
3. Vaciar la caché.
4. En el administrador aparece el aviso **«Apply article revisions (save backups)»**:
   - actualiza 22 artículos (11 en inglés y 11 en español);
   - cada artículo guarda su texto anterior en `pelham_before_articles_1_20` y en sus revisiones;
   - una segunda pasada no cambia nada.

   Las instalaciones nuevas ya traen el texto nuevo.

Si la versión 1.19 aún no se aplicó, se pueden pulsar los dos botones en cualquier orden.

## Criterio

| Del cliente | Decisión |
|---|---|
| Ejemplos concretos, pasos numerados, consejos y listas de comprobación | **Adoptado.** |
| Títulos nuevos (p. ej. «Florida International Forwarders», «The Ultimate Cheat Sheet») | **No adoptado:** los titles y H1 actuales llevan las keywords medidas («freight delivery to forwarders in miami», «freight quote requirements»…). |
| Introducciones largas antes de la respuesta | Se pasaron a la primera sección: «The short answer» sigue arriba. |
| «1 to 6 pallets» (LTL) y «10+ pallets» (FTL) | **Corregido** a lo que confirmó el cliente: LTL de 1 a 7 pallets, y desde 8 una carga de volumen o un camión completo. |
| Precio LTL «por clase» | **Corregido:** sin NMFC; se factura el mayor entre el peso real y el volumétrico. |
| «Regular… door-to-door across all major Florida hubs» | Corregido a «all Florida destinations», que es la cobertura publicada. |
| Emojis en los títulos y fórmulas en LaTeX (`$L \times W \times H$`) | Eliminados; se escribe «length × width × height». |

## Cambios por artículo

| Artículo | Cambios principales |
|---|---|
| LTL vs. FTL | Tabla con la fila «Best for» y consejo «no asuma que LTL es más barato». Listas «Choose LTL if» / «Choose FTL if». Ejemplos: 2 pallets a Miami y 5 pallets de electrónica de alto valor. Niveles en lista. |
| Coste de un pallet | «Un pallet no es una unidad estándar». La tabla añade nivel de servicio y requisitos de recepción. Ejemplo de 3 pies frente a 7 pies. Comprobación de cotizaciones en 5 puntos. Lista de 6 datos. Enlace a la guía de agentes de carga. Se mantienen la fórmula 194 y el ejemplo de 594 lb. |
| Agentes de carga en Miami | Tabla de responsabilidades ampliada. 5 preguntas al agente de carga (referencias en el BOL, fecha límite del buque o vuelo). Ejemplo Los Angeles → Cartagena (Colombia). Niveles frente a fechas límite de exportación. Enlaces al coste por pallet y a LTL vs. FTL. |
| Retos logísticos | Sección nueva «Why California–Florida freight plans change». Plan de 4 pasos con la regla «una estimación no es una cita garantizada». Receptores en Miami, Orlando y Jacksonville. Lista de prevención. |
| Requisitos de cotización | Por qué importa cada dato. Medidas tras cargar y envolver, incluido lo que sobresale. Pesos de báscula. 3 errores con ejemplos (48 × 40 frente a 96 × 48; destino en Sudamérica). Revisión antes de reservar. |
| Envíos recurrentes | 5 datos del resumen. LTL o FTL recurrente según la carga (1–7 / 8+). Consejo de modelo mixto en temporada alta. Sección nueva de niveles LTL. |
| Recargo por combustible | Definición (FSC). Método de porcentaje y método fijo o por milla. 4 preguntas. Ejemplo de error al contarlo dos veces. Por qué un porcentaje antiguo no sirve. |
| Transbordo de contenedores | La respuesta breve recoge los 4 factores del cliente. El paquete de información incluye número de contenedor, naviera y pallets de 48 × 40. Quién hace qué (acarreo, transbordo, Pelham). LTL o FTL de salida. 5 comprobaciones. |
| Proceso de Pelham | Los 5 pasos en la respuesta breve. Datos de la consulta. LTL 1–7 / FTL 8+ con niveles. Confirmación con referencias PO/BOL. Preparación de proveedores y receptores. |
| Tecnología | Evaluar la tecnología por la comunicación, no por las palabras de moda. Hitos de seguimiento. Coherencia entre orden de compra, lista de empaque y BOL. Tabla de evaluación actualizada. |
| Transbordo vs. cross-docking | «No siempre hay que elegir uno». Ejemplos más concretos en la tabla. Escenario en 3 pasos. 4 datos a describir. Comprobaciones con pallet de 48 × 40. |

## Afirmaciones suavizadas: pendientes de confirmar con el cliente

No se publicaron como promesas porque no constan en la información confirmada:

- **«Pelham Reference ID» y «live operational updates»:** se publica «shipment reference» y «latest update».
- **«Dispatch team»:** se publica «the team».
- **FTL «53' trailer», «team transit», «zero intermediate dock touches», «dedicated driver», «flat rate per truckload»:** se publica capacidad exclusiva, sin consolidar con otros envíos, cotizada por separado.
- **Recurrente con «contracted rate stability» y «priority capacity»:** se publica que el equipo confirma capacidad y precio.
- **«Residential delivery»:** se omite, porque Pelham es B2B.
- **«Liftgate»:** se menciona solo como requisito que el cliente debe indicar, no como servicio ofrecido.
- **«15 %», «DOE», «2,400 miles / I-10»:**
  - el 15 % se quitó;
  - el índice del Departamento de Energía se menciona como ejemplo del sector;
  - la distancia se publica como «más de 2,000 millas», sin ruta.

Si el cliente confirma alguna de estas prestaciones, se puede añadir en una revisión menor.

## Pruebas

- **WordPress 6.8.3 local:**
  - actualización aplicada a 22 artículos;
  - una segunda pasada no cambia nada;
  - el aviso se muestra antes de aplicar y desaparece después.
  - Las 60 URL pasan sin errores PHP ni JS ni desbordamiento en móvil, con `hreflang` y canonical correctos, sin texto en inglés en las páginas en español y con los mismos titles y H1.
- **Español:** 219 segmentos nuevos traducidos (memoria `tools/i18n/tm-es.json`); `build_es.py` no informa ningún segmento sin traducir.
- **Vista previa estática:** solo cambia el bloque del artículo en las 22 páginas.
