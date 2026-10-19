# Ejecución SEO basada en keywords — Pelham

**Actualización 1.13:** completada la revisión de las 96 relevantes: 93 implementadas por intención, 2 con alcance limitado (tracking y forwarding), 1 pendiente de reseñas verificables confirmado por el cliente. La matriz vigente es [cobertura-96-keywords.csv](cobertura-96-keywords.csv). Instalación actual: Pelham Content 1.13.0 → **Complete keyword coverage (save backups)**. Consulte [la entrega 1.13](../RELEASE-CONTENT-1.13.md). El detalle 1.12 de abajo conserva la primera fase como historial.

Plan iniciado el 14 de octubre de 2026 (America/Bogota). Mercado confirmado: Estados Unidos, inglés. Los volúmenes provienen del research entregado; competencia de Ads no es KD orgánica.

## Implementado en Pelham Content 1.12.0

| Grupo / intención | Destino | Acción concreta |
|---|---|---|
| Freight shipping California to Florida; LTL LA–Miami | `/services/freight-consolidation/` | Añadida explicación de ruta, destinos y diferencia entre cobertura e instalaciones; enlaces a instalaciones, costes y receptores B2B. Mantiene niveles y límites reales. |
| LTL vs FTL; FTL and LTL meaning; comparación de costes | `/news/ltl-vs-ftl-california-florida-shipping/` | Definiciones explícitas, comparación de costes sin ahorros inventados, título/meta y CTA equilibrado a LTL y FTL. Conserva URL. |
| How much does it cost to ship a pallet | `/news/pallet-shipping-cost-california-to-florida/` | Guía nueva: medidas, peso, ubicaciones, niveles, comparación de presupuestos y solicitud. No inventa tarifas. |
| Freight delivery to forwarders in Miami | `/news/freight-delivery-to-forwarders-in-miami/` | Guía nueva: dirección receptora, referencias, coordinación y responsabilidad doméstica. Sin atribuir puerto, aduanas ni transporte internacional a Pelham. |
| Freight quote requirements | `/news/what-to-include-in-a-freight-quote/` | Enlaza las nuevas guías para preparar el presupuesto y la entrega. Conserva su función de checklist. |
| Full truckload California to Florida | `/services/dedicated-transport/` | Añadida ayuda para elegir FTL frente a LTL, con enlaces a comparación y servicio. |

Los nuevos artículos se incorporan al listado Insights mediante su categoría existente y enlazan entre sí y a las páginas comerciales. Los metadatos y contenido son editables desde WordPress. Se conserva el diseño editorial del theme y no se reutilizan fotografías de otras páginas para llenar los nuevos artículos.

No se crean páginas para cada variante ni para cada ciudad. La página LTL concentra las búsquedas comerciales del corredor. La guía de coste explica factores; `/quote/` recoge solicitudes. Las keywords de marca se apoyan en las páginas existentes de empresa e instalaciones; las de cross-docking requieren un lote de research específico.

## Instalación y alcance

1. Instalar `dist/pelham-content-1.12.0.zip` reemplazando la versión anterior del plugin. Compatible con el theme entregado 1.17.1; no requiere otro plugin SEO.
2. En Pelham Company, aplicar primero las actualizaciones antiguas de contenido/ubicaciones si siguen pendientes.
3. Pulsar **Apply keyword content (save backups)**. Reemplaza el cuerpo de cuatro contenidos existentes, con respaldo; publica dos guías nuevas con fecha actual. Revisar personalizaciones antes de aplicar.
4. Purgar caché y revisar los seis destinos. Los respaldos están en el meta privado `pelham_before_keywords_1_12`, además de las revisiones que soporte cada post type; no hay botón de restauración automática en este paquete.
5. Staging continúa sin indexación. El ZIP no implica despliegue remoto ni indexación.

La migración rechaza una colisión con un artículo ajeno que use uno de los nuevos slugs. Puede reintentarse tras corregir un fallo: los contenidos ya completados no se reescriben. No modifica los formularios privados ni sus datos.

## Siguientes fases y criterios de decisión

**Antes del lanzamiento:** verificar las páginas instaladas, dominio canónico, redirecciones antiguas y sitemap en producción. Configurar Search Console con acceso del propietario. No solicitar indexación de Hostinger staging.

**Primeros 30 días con datos:** enviar sitemap de producción, revisar indexabilidad de los nuevos artículos y separar consultas de marca, LTL, FTL y guías. Recoger preguntas reales de ventas. No fijar un objetivo numérico sin línea base.

**Días 30–60:** exportar consultas y páginas de Search Console. Si una misma intención alterna entre varias páginas, revisar títulos/enlaces y consolidar contenido cuando proceda. Si una página tiene impresiones relevantes pero poco CTR, revisar su snippet frente a los resultados reales; no atribuirlo automáticamente a mala redacción sin considerar posición.

**Días 60–90:** evaluar cotizaciones recibidas y calificadas por ventas, además de clics. Actualizar guías con objeciones verificadas. Ampliar cross-docking/paletización después de medir sus consultas y confirmar requisitos operativos. Investigar las SERP de EDI Express, Sterling, Daylight y JTS; no copiar promesas competitivas ni inferir rankings a partir de sus webs.

## Medición pendiente de acceso/configuración

Search Console: impresiones, clics, CTR y posición por página/consulta/dispositivo. Analítica: visitas a cotización y confirmación real del backend, sin PII; ver `docs/SEO-MEASUREMENT-PLAN.md`. Ventas: solicitudes calificadas y clientes, no equiparar clics con ventas. Estos sistemas no se han activado ni configurado mediante esta actualización.
