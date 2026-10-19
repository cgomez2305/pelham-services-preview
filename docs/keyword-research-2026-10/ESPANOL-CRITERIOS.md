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
