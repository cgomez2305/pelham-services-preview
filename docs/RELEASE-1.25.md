# Revisiones del cliente del 28 de octubre: tema 1.25 / contenido 1.21

Tema **1.25.0** · Pelham Content **1.21.0** · Pelham Applications **1.4.1** (sin cambios)

Cambios del documento `WEB_CONTENT_REVISIONS_10-28`. Se mantienen las URL, los títulos SEO, los H1 y los H2 con keywords.

## Cambios

| Punto del documento | Qué cambia |
|---|---|
| **Home** · añadir botón Home | **Home** es el primer elemento del menú principal (**Inicio** en español). Solo se marca como página actual en la portada, no en las demás páginas. |
| **Who we ship for** | Nueva entrada: «Connecting California production directly to Florida supply chains, from single pallets to high-volume distribution.». También cambian las cuatro descripciones (Suppliers, Distributors, Manufacturers, Freight forwarders). Se mantienen el título y el enlace «How we work with your business». Todo está traducido al español. |
| **Request a quote** · eliminar llamadas | Se quita la barra «Call Pelham Services Gardena / Los Angeles · Miami · Orlando». El texto pide ahora los **códigos ZIP** de recogida y entrega, no «city/state/ZIP». |
| **Quoting LTL / FTL** | El origen y el destino son **códigos ZIP de 5 dígitos**. Según el servicio elegido aparece **Shipment details**: <br>**LTL:** Pieces, Weight, **Cubic feet**, Commodity y Value. <br>**FTL:** Pieces, **Total weight**, Commodity y Value. <br>Con LTL o FTL el mensaje pasa a ser opcional («Anything else we should know?»). «Help me choose» y Cross-docking siguen pidiendo el mensaje y no muestran estos campos. |
| **Our network** · eliminar Los Angeles | La instalación se llama **«Pelham Services Gardena»** en el título, el H1, las tarjetas de la red, Contacto, el pie de página y los textos de LTL, About y Our network, en inglés y en español. |
| **About** · LTL lleva a Services con otras imágenes | Ver la explicación abajo. Se corrige solo. |

### Lo que no cambia, por SEO

- La URL de Gardena: `/locations/gardena-ca/` y `/es/ubicaciones/gardena-los-angeles/`.
- El título de búsqueda «Pelham Services Gardena | Los Angeles Freight Facility». La gente busca «Los Angeles», así que se queda en el título de Google, pero no en la página.
- Las keywords del corredor («Los Angeles to Florida», «LTL from Los Angeles to Miami»…). Es la ruta del servicio, no el nombre de la instalación.

## El problema de About → LTL

En el sitio del cliente, WordPress usaba los **enlaces permanentes «simples»** (`?page_id=…`). Con esa configuración, las direcciones de los servicios, como `/services/freight-consolidation/`, no existen y WordPress muestra la portada en su lugar. Por eso aparecían otras imágenes según desde dónde se entraba.

Desde 1.20.5 el plugin cambia la estructura a `/news/%postname%/`, pero en ese caso no volvía a registrar las direcciones de servicios y ubicaciones. En 1.21.0 sí lo hace. Al entrar en el administrador después de actualizar, `/services/…`, `/locations/…` y `/es/servicios/…` funcionan sin tocar *Ajustes → Enlaces permanentes*.

## Correo de cotización

El correo al equipo incluye ahora líneas claras: Origin ZIP, Destination ZIP, LTL level (solo LTL), Pieces, Weight (lb) o Total weight (lb), Cubic feet (solo LTL), Commodity y Value (USD). Se mantiene «Language: Spanish (reply in Spanish)» para las cotizaciones en español.

El servidor valida los mismos datos que el navegador, con errores junto a cada campo en el idioma de la página:
- ZIP de 5 dígitos (o ZIP+4);
- piezas: número entero de 1 o más;
- peso y pies cúbicos mayores que 0;
- valor de 0 o más;
- mercancía obligatoria.

## Instalación en un sitio existente

1. Subir el tema `pelham-modern-1.25.0.zip` y el plugin `pelham-content-1.21.0.zip`; reemplazan a los anteriores.
2. Entrar en el administrador. Aparece el aviso **«the client’s web revisions of October 28 are ready»**; pulsar **Apply web revisions (save backups)**. El botón hace esto:
   - cambia solo las frases afectadas de Quote, About, Our network, Gardena y LTL, en inglés y en español;
   - quita la barra de llamadas;
   - renombra Gardena (título y descripción de búsqueda);
   - añade **Home** al principio del menú.
3. Las páginas anteriores se guardan en cada página (`pelham_before_revisions_1_21`) y en sus revisiones.

Si alguien ya editó a mano alguna de esas frases, el botón no la toca: solo sustituye el texto original exacto.

## Pruebas (WordPress local, inglés y español)

- **Actualización en un sitio existente:** 10 páginas actualizadas (5 en inglés y 5 en español), Home añadido al menú y el aviso desaparece.
- **Cotización LTL:** Regular, 4 piezas, 1800 lb, 220 ft³. Se envía y el correo trae cada campo.
- **Cotización FTL:** 26 piezas, 42 000 lb de «Total weight», sin mensaje. Se envía; no se piden pies cúbicos.
- **«Help me choose» y Cross-docking:** sin campos de detalle; el mensaje es obligatorio; se envían.
- **Datos no válidos** (ZIP «Gardena» o «902», campos vacíos): seis errores junto a cada campo, en inglés y en español.
- **Menú** a 1024, 1100, 1180, 1280 y 1440 px: una sola línea, sin desbordes, en los dos idiomas.
- **Auditorías:**
  - WordPress: 60 URL sin errores PHP ni JS.
  - Enlaces: 60 páginas de WordPress y 58 de la vista previa, sin enlaces rotos.
  - Vista previa estática: solo las 4 incidencias conocidas de `/claims/`, que no existe en la vista estática.
