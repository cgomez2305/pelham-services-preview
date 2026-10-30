# Revisiones del cliente del 28 de octubre: tema 1.25 / contenido 1.21

Tema **1.26.1** · Pelham Content **1.21.4** · Pelham Applications **1.4.1** (sin cambios)

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

## Logo del pie de página (tema 1.26.1)

El pie de página usa el mismo logo que la cabecera en su versión para fondos oscuros: pájaro verde y texto blanco. Los archivos nuevos se llaman igual que los antiguos, así que el navegador o la caché de Hostinger podían seguir mostrando el logo horizontal anterior. Desde la 1.26.1, la dirección de los dos logos lleva una huella del archivo (`?ver=…`). Cuando se cambia el logo, cambia la dirección y todas las cachés cargan el nuevo.

## Commodity por categorías (contenido 1.21.4)

El cliente explicó que «commodity» es el **tipo de mercancía**, que va clasificado, y que con él el equipo ya sabe cómo es la carga y cómo viene empacada. En LTL y FTL el campo es ahora:

- **Commodity (type of goods)**: una lista obligatoria con 12 categorías.
  - Apparel and textiles · Footwear · Auto parts and accessories · Perfume and cosmetics · Costume jewelry and accessories · Electronics and appliances · Furniture and home goods · Food and beverages (non-perishable) · Packaging, paper and supplies · Building materials and hardware · Machinery and equipment · Other.
  - En español: Ropa y textiles, Calzado, Autopartes, Perfumes y cosméticos, Bisutería, etc.
- **Describe the goods**: opcional, con el ejemplo «sneakers in cartons, 12 pairs per box». Si eligen **Other**, pasa a ser obligatorio.
- Una nota explica para qué sirve: «The commodity is the type of goods you are shipping. It tells our team how the freight looks and how it is packed…».
- Con **Perfume and cosmetics** aparece un aviso: los productos con alcohol pueden estar regulados como materiales peligrosos y el equipo confirmará la documentación y el empaque.
- El correo al equipo incluye `Commodity: Footwear` y `Commodity details: …`.
- El servidor solo acepta categorías de la lista y exige la descripción con «Other».

La lista de categorías está en `pelham_commodities()`, en `pelham-content.php`. Si el cliente usa otra clasificación, se cambia ahí y en `tools/i18n/ui-es.json`.

De paso se corrigió «(optional)», que salía en inglés en algunos campos del español; ahora es «(opcional)».

## Logo real (1.26.0 / 1.21.3)

- El logo nuevo es el real del cliente (pájaro sobre «Pelham Services»), con los colores de la web y sin fondo:
  - pájaro en el verde de la web, `#00a858` (el del botón principal);
  - texto en el tono oscuro del sitio, `#101713`;
  - versión para fondo oscuro, con el texto en blanco, para el pie de página.
- Se quitó el fondo calculando la transparencia de cada píxel, así los bordes quedan suaves y sin halo blanco.
- Web:
  - cabecera a 52 px de alto (46 px en móvil) para que «SERVICES» se lea;
  - pie de página a 150 px de ancho.
- Archivos en alta resolución (928 × 554, PNG transparente): `brand/pelham-logo-color-928.png` y `brand/pelham-logo-white-928.png`.
- Si en *Apariencia → Personalizar → Identidad del sitio* hay un logo subido, WordPress muestra ese en lugar del del tema. Hay que quitarlo o subir `brand/pelham-logo-color-928.png`.

## Segunda ronda del cliente (1.25.2 / 1.21.2)

- **Commodity:** el ejemplo del campo es ahora «e.g. wearing apparel, auto parts» («p. ej., ropa, autopartes» en español). Commodity es el tipo de mercancía.
- **«Dos páginas bajo Services».** No hay dos páginas grabadas: `/services/` es la página general y `/services/freight-consolidation/` es la página de LTL, con su propia foto. Lo comprobé en el sitio de Hostinger: desde About, la tarjeta LTL abre la página de LTL, igual que desde Services. La confusión venía del menú, que resaltaba «Services» igual en las dos páginas. Ahora:
  - en la página general, «Services» sigue resaltado con el fondo verde;
  - en una página de servicio (y de ubicación o artículo), el menú solo subraya la sección, así se ve que es una subpágina;
  - el breadcrumb sigue mostrando Home › Services › LTL.
- **Menú móvil.** Los enlaces a servicios tenían los nombres antiguos (Dedicated transport, Freight consolidation, Transloading). Ahora son Less Than Truckload (LTL), Full Truckload (FTL) y Cross-Docking & Palletizing, en el mismo orden que en Services, y se marca el servicio en el que estás.

## Revisión visual (1.25.1 / 1.21.1)

Revisé las 60 páginas (EN/ES) a 1366, 1024 y 390 px, con capturas de los casos dudosos.

| Dónde | Problema | Corrección |
|---|---|---|
| Portada · Who we ship for | Con la entrada más larga, el título se partía en 4 líneas («Who / we / ship / for.»). | El título conserva sus 2 líneas y el texto se ajusta a su columna. |
| Cotizar · «Getting ready to ship?» y «Request rates…» | Las dos tarjetas estaban pegadas, sin separación. | 20 px entre tarjetas. |
| Services, About, Portada · tarjeta LTL | La etiqueta «1 to 7 pallets» ocupaba una columna: el título caía en 3 líneas y la descripción no se alineaba con FTL y Cross-docking. | La etiqueta va debajo del título y las tres tarjetas comparten columnas. |
| Our network · tarjetas | En móvil había un hueco vacío arriba y el título quedaba pegado a la dirección; había una línea vacía de horario. | El título va arriba, separado de la dirección, y sin líneas vacías. |
| Services · entrada → tarjetas | Poco espacio entre el párrafo y la primera tarjeta. | 32 px. |
| Portada · red | Todavía decía «Los Angeles, Orlando and Miami facilities». | «Gardena, Orlando and Miami», en EN y ES. También en la actualización del administrador. |

Si un sitio ya aplicó la 1.21.0, el aviso aparece de nuevo una sola vez para cambiar la frase de la portada.

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

1. Subir el tema `pelham-modern-1.26.1.zip` y el plugin `pelham-content-1.21.4.zip`; reemplazan a los anteriores.
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
