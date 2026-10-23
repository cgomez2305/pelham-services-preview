# Pelham Content 1.20 — revisión de los artículos del blog

Pelham Content **1.20.4** · Tema **1.24.0** y Pelham Applications **1.2.0** sin cambios

Incorpora a las 11 guías del blog, en inglés y en español, los cambios con sentido de los documentos reescritos por el cliente.

**Se conserva la estructura SEO:**
- URLs y slugs;
- titles, descriptions y H1;
- «The short answer» al inicio (fragmento destacado);
- los H2 con keyword y sus anclas;
- los enlaces internos.

## Instalación

1. Hacer una copia de seguridad.
2. Reemplazar Pelham Content por `pelham-content-1.20.4.zip`.
3. Vaciar la caché.
4. En el administrador aparece el aviso **«Apply article revisions (save backups)»**:
   - actualiza 32 contenidos, en inglés y en español: los 11 artículos y las páginas FTL, Cross-docking/transbordo, About, Cotizar y Contacto;
   - cada contenido guarda su texto anterior en `pelham_before_articles_1_20` y en sus revisiones;
   - una segunda pasada no cambia nada;
   - si ya se había aplicado la 1.20.0, el aviso vuelve a aparecer una vez y guarda otra copia antes de actualizar.

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

## Respuestas del cliente aplicadas (1.20.1 a 1.20.3)

| Pregunta | Respuesta | Dónde se aplica |
|---|---|---|
| ¿Seguimiento en vivo? | No hay GPS en vivo. El estado se actualiza por etapas: carga recogida, llegada al almacén de origen, salida hacia Florida, llegada al almacén de destino, entrega y prueba de entrega. | Proceso (paso 5 con las 6 etapas), Tecnología, Retos logísticos, Agentes de carga (prueba de entrega) |
| FTL | Siempre tráiler de 53'. El cliente elige 1 o 2 choferes. Puede ir directo, sin pasar por terminales. | LTL vs. FTL (filas «Route» y «Drivers»), Proceso, Recurrentes, Contenedores y **página de servicio FTL**: texto de 53' y preguntas nuevas «Can my load travel with two drivers?» y «How are FTL rates set?» |
| Tarifas | Dinámicas, cambian con el mercado. Hay tarifas por contrato: la tarifa acordada más un porcentaje de combustible que sube o baja con el petróleo. | Recurrentes (sección nueva «How are recurring shipments priced?»), Recargo por combustible (párrafo «At Pelham»), Coste de pallet, LTL vs. FTL, página FTL. No se publica cuántas cuentas tienen contrato. |
| Liftgate | Sí. | Coste de pallet (fila «Additional services»), Requisitos de cotización, LTL vs. FTL, Proceso, Agentes de carga |
| Entrega residencial | Se hace solo para clientes que ya trabajan con Pelham, como excepción; no es un servicio al público ni una especialidad. | No se menciona como servicio. Única nota, en Requisitos de cotización: «Pelham delivers to businesses; if you are already a customer and an occasional shipment goes to a home address, tell the team.» |

- **Tarifas por contrato:** confirmado por el cliente.
- About sigue diciendo «Business-to-business freight shipping», en línea con el cliente.
- **Equipo de atención al cliente y despacho (1.20.3):** el cliente confirma que existe y quiere nombrarlo para las búsquedas. Se publica «customer service and dispatch team» («equipo de atención al cliente y despacho») en:
  - Retos logísticos;
  - Proceso (revisión y seguimiento);
  - Tecnología.
- **Ruta (1.20.3):** unas 2,700 millas de Los Angeles a Florida, de las que 2,300 a 2,400 van por la I-10 hasta Tallahassee y Jacksonville; después, I-75, I-95 o Florida's Turnpike. Se publica en:
  - Retos logísticos;
  - Coste de pallet (fila de códigos ZIP).
- **Combustible:** el ejemplo del «15 %» del documento del cliente se quitó, porque el porcentaje real depende de cada cuenta.

## Enlazado interno (1.20.4)

El cliente indicó que el enlazado con los artículos no estaba bien. El rastreo de las 60 URL no encontró enlaces rotos, redirecciones ni mezcla de idiomas, pero sí estos fallos de estructura:

| Problema | Corrección |
|---|---|
| Tecnología, Proceso de Pelham y Retos logísticos no recibían enlaces de ninguna otra página. Combustible, Transbordo vs. cross-docking y Contenedores recibían uno. | Cada artículo recibe ahora 3 o más enlaces desde otras páginas, en inglés y en español. |
| La página de servicio Cross-docking/transbordo solo enlazaba a «Request a quote». | Nueva sección «Guides for cross-docking and transloading» que enlaza a las dos guías de su keyword, y enlace en la pregunta frecuente sobre la diferencia entre ambos servicios. |
| Textos de enlace genéricos: «receiving guide», «shipment details», «capacity comparison», «pallet cost guide»… | Textos descriptivos con la keyword del destino, variados para no repetir siempre la misma frase exacta: «freight delivery to forwarders in Miami», «freight quote requirements», «freight fuel surcharges explained»… En español: «diferencia entre LTL y FTL», «cuánto cuesta enviar un pallet», «qué incluir al cotizar un flete», «agentes de carga en Miami», «transbordo de contenedores». |
| Sin enlaces entre artículos del mismo tema. | Bloque «Related freight guides» / «Guías relacionadas» al final de cada artículo, dentro de la última sección (sin H2 nuevo). Hay 3 guías por artículo, agrupadas por tema: precios y capacidad; manipulación de carga; operación. |
| Páginas sin enlace a guías clave. | FTL → envíos recurrentes; About → proceso de Pelham; Cotizar → recargo por combustible; Contacto → retrasos y cambios (y el estado por etapas). |
| La home mostraba un tercer artículo distinto en inglés y en español: seis artículos tienen la misma fecha y el desempate usaba el ID, que es distinto en cada idioma. | El listado desempata por el artículo en inglés: los dos idiomas muestran los mismos artículos en el mismo orden. |

URLs, titles, H1 y H2 con keyword sin cambios.

## Pruebas

- **WordPress 6.8.3 local:**
  - cada versión aplicada sobre la anterior guardó otra copia; la 1.20.4 actualizó 32 contenidos;
  - rastreo de enlaces: 60 URL en WordPress y 58 en la vista previa, sin enlaces rotos ni redirecciones; todos los artículos reciben 3 o más enlaces internos;
  - una segunda pasada no cambia nada;
  - el aviso se muestra antes de aplicar y desaparece después.
  - Las 60 URL pasan sin errores PHP ni JS ni desbordamiento en móvil, con `hreflang` y canonical correctos, sin texto en inglés en las páginas en español y con los mismos titles y H1.
- **Español:** 262 segmentos nuevos traducidos (memoria `tools/i18n/tm-es.json`); `build_es.py` no informa ningún segmento sin traducir.
- **Vista previa estática:** solo cambia el bloque de contenido de los artículos y de la página FTL.
