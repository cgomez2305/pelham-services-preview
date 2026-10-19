# Pelham Content 1.9.0 — oferta confirmada

## Qué cambia

LTL es el servicio principal: recogida de paletas en Los Ángeles y entrega doméstica a negocios o transportadoras en Florida. Expedited: 3 días calendario desde recogida, solo Homestead–West Palm Beach. Regular: 7 días, toda Florida. Economy: 10 días, toda Florida y opción más económica. Todos son tiempos estimados, no garantizados. No se aplican estos niveles a FTL.

Home, listado, los tres servicios, About, Locations, Contact, Quote y las fichas existentes de Gardena/Miami reciben contenido o metadatos coherentes. Se conservan URLs, imágenes y diseño. FTL, cross-docking, transloading y paletización son complementarios. No se ofrece tramo internacional, tren ni servicio portuario directo.

Orlando aparece como instalación operada por Pelham, sin inventar dirección, teléfono o ficha estructurada local. Se mantiene la dirección existente de Gardena como dato histórico del sitio; sigue pendiente confirmar la identificación exacta de la instalación que el cliente denomina Los Ángeles. Las fichas de contacto verificadas siguen disponibles.

## Instalación en el WordPress existente

1. Guardar una copia completa del sitio y base de datos con las herramientas del hosting.
2. Instalar `dist/pelham-content-1.9.0.zip` desde Plugins → Añadir plugin → Subir plugin y reemplazar Pelham Content.
3. Abrir **Pelham Company** y pulsar **Apply confirmed LTL offer (save backups)**. La pantalla identifica las páginas afectadas. Esta acción reemplaza sus textos y metadatos SEO por los de esta versión; cada página guarda primero una copia en `pelham_before_offer_1_9`, además de revisión WordPress. No sustituye slugs, imágenes, estado de publicación ni contactos. Si hay un error, se informa y los backups de páginas procesadas permanecen disponibles; la operación no es una transacción global.
4. Vaciar LiteSpeed/hosting/CDN y comprobar las páginas en sesión privada.
5. Verificar una cotización LTL y la entrega del correo en el hosting. La preferencia incluye plazo y cobertura; ventas debe confirmar la dirección concreta. No se implementa geocodificación ni validación automática de límites por ZIP.

**No basta con subir el ZIP:** el contenido está en la base de datos y requiere el paso 3. No reimportar todo el sitio para forzar cambios. Tema 1.16.0 y Pelham Applications 1.1.1 se mantienen. No se ha desplegado en Hostinger.

Las páginas y metadatos siguen editables en WordPress. Los tres niveles y su lógica de formulario están centralizados en el módulo de oferta del plugin; cambiar sus reglas requiere actualizar ese módulo y sus textos de ayuda, no solo un párrafo de la página. Si hay un plugin SEO externo activo, revisar allí títulos y descripciones porque el tema cede la gestión a ese plugin.

## Comprobaciones locales

- Aplicación sobre entradas existentes con backups; contenido actualizado comprobado contra las fuentes.
- Home, tres servicios, red, About y cotización en escritorio y móvil, sin desbordamiento horizontal en tamaños probados.
- Preselección del nivel desde los CTA; ocultación y desactivación del campo al salir de LTL.
- Selección válida incluida en correo interceptado localmente, con plazo no garantizado y cobertura. No se enviaron correos reales.
- Rechazo en servidor de nivel desconocido y combinación FTL con nivel LTL.
- Revisión de capturas tras recorrer las páginas para activar sus entradas.

No valida entrega SMTP ni comportamiento del WordPress remoto. La estrategia SEO separada es un plan de implementación e investigación, no una promesa de posiciones ni un estudio de volúmenes medidos.
