# ¿Traducir la web al español? Cómo validarlo

Lista para validar: [keywords-es-lista.txt](keywords-es-lista.txt) (40 keywords, se pega directamente en Keyword Planner). La versión con grupo, intención y página destino está en [keywords-es-para-validar.csv](keywords-es-para-validar.csv).

## Cómo sacar los datos

1. En Keyword Planner, abrir *Obtener volumen de búsqueda y previsiones* y pegar la lista.
2. Hacer dos exportaciones con la ubicación **Estados Unidos**:
   - una con el idioma **español**;
   - otra con **todos los idiomas**.

   Si es posible, añadir una tercera limitada a **Florida + California**.
3. Exportar con la moneda en **USD** para que las pujas sean comparables.

## Cómo decidir

| Resultado (suma de keywords comerciales con volumen, sin contar las de «Control») | Decisión recomendada |
|---|---|
| Menos de ~300/mes | No traducir. Como mucho, añadir una frase «Hablamos español» en contacto y cotización: el equipo es bilingüe y eso convierte sin coste SEO |
| ~300–1.000/mes | **Una landing en español** (`/es/`) con el servicio LTL, el coste de pallet y la cotización. No traducir las 29 páginas |
| Más de ~1.000/mes, con pujas similares a las del inglés | Versión completa en español con hreflang (Polylang o WPML), empezando por las páginas que tengan demanda |

**Además del volumen:**
- **Intención.** «Agencia de carga en Miami» tendrá volumen alto, pero lo busca gente que quiere enviar a Latinoamérica, y Pelham no hace envío internacional. No cuenta.
- **Capacidad comercial.** Si llegan cotizaciones en español, alguien tiene que responderlas en español.
- **Mantenimiento.** Cada página traducida duplica las actualizaciones. Con hreflang, una versión mal mantenida perjudica a las dos.
- **Contexto B2B.** Los proveedores y forwarders hispanohablantes de EE. UU. a menudo buscan en inglés los términos técnicos («LTL», «freight forwarder»). Por eso la exportación con todos los idiomas es importante: muestra si existe una demanda en español distinta.

## Resultado (19/10/2026, capturas del cliente, Estados Unidos)

Keyword Planner sin campaña activa solo muestra rangos.

| Rango | Keywords |
|---|---|
| 0–10 | Todas las de ruta y empresa (*transporte de carga de california a florida*, *flete de los angeles a miami*, *compañía de transporte de carga en miami*…), todas las de coste de pallet/paleta/tarima, envío de pallets, carga parcial, camión completo y las tres de públicos |
| 10–100 | carga consolidada, qué significa ltl, diferencia entre ftl y ltl, mercancía paletizada, cross docking que es, transbordo de contenedores, servicio de paletizado, cotizar flete, cotización de transporte de carga, agente de carga en miami, agencia de carga en miami |
| 10–100 (términos en inglés) | cross docking los angeles, cross dock los angeles ca, cross docking miami fl |

**Decisión: no traducir la web ni crear `/es/`.**
- Ninguna búsqueda comercial de la ruta en español supera 0–10.
- Lo que tiene algo de volumen es informativo (qué significa LTL, cross docking qué es) o de ámbito latinoamericano/internacional (carga consolidada, agencia de carga). No encaja con el servicio doméstico de Pelham.
- Coincide con la hipótesis: el comprador B2B hispanohablante de EE. UU. busca en inglés.

**Hallazgos útiles:**
1. **«Hablamos español» en contacto, cotización y footer.** Captura a ese cliente sin coste de mantenimiento. Pendiente de que el cliente confirme que el equipo atiende en español.
2. **Demanda medible de cross-docking por ciudad en inglés:** *cross docking los angeles*, *cross dock los angeles ca* y *cross docking miami fl*. La página de cross-docking no menciona ninguna ciudad. Antes de añadir «Los Angeles» o «Miami» al title y al H1, el cliente debe confirmar en qué instalaciones se hace cross-docking.
