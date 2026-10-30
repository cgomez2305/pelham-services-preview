# Pelham Applications 1.4.1 — vuelta a la solicitud de cuenta única

Pelham Applications **1.4.1** · Pelham Content **1.20.8** · Tema **1.24.0** sin cambios

## Qué pasó

- **1.4.0.** Separó los documentos en 4 formularios independientes (Customer, Credit, Terms y Claim) a petición del cliente.
- **Decisión final.** Tras verlo, el cliente prefirió la estructura simplificada anterior:
  - una **solicitud de cuenta** que reúne Customer Application, Credit Application (solo si se pide crédito) y Terms and Agreements;
  - la **reclamación** (Claim Form) aparte.

La 1.4.1 vuelve a esa estructura: la de la 1.3.1, documentada en `RELEASE-APPLICATIONS-1.3.md`.

## Qué incluye la 1.4.1

- **Registro.** `/register/` y `/es/registro/` son de nuevo la solicitud de cuenta en 5 pasos, con «Sus cuatro documentos de Pelham» arriba, que indica en qué paso está cada documento.
- **Reclamación.** `/claims/` sin cambios.
- **Modo de prueba.** Dos botones: «Probar la solicitud de cuenta» y «Probar la reclamación de carga».
- **Panel.** Igual que en la 1.3.1.
- **Mejora conservada de la 1.4.0.** El mensaje final del modo de prueba se muestra traducido al español.
- **Compatibilidad.** Las solicitudes creadas con la 1.4.0 (Customer, Credit o Terms por separado) se siguen viendo y exportando en el panel. No se pueden empezar nuevas solicitudes de ese tipo.

## Instalación

Subir `pelham-applications-1.4.1.zip` y elegir reemplazar la versión instalada. Pelham Content 1.20.8 no cambia. Si la 1.4.0 no llegó a instalarse, se instala igual.

## Pruebas (WordPress 6.8.3 local)

- **Solicitud de cuenta por API:**
  - con crédito «Sí»: 4 firmas (cliente, términos, crédito y garantía) y envío;
  - con crédito «No»: 2 firmas y envío.
- **Reclamación:** con factura adjunta, firma y envío.
- **En el navegador, como un visitante con el enlace de prueba:** solicitud de cuenta con crédito «Sí» y reclamación, en español y en inglés, sin errores JS.
- **Panel:** filtros y fichas de la 1.3.1; las solicitudes 1.4.0 siguen generando su PDF.
- **Sitio completo:** las 60 URL pasan la auditoría sin errores PHP ni JS.

## 1.4.2 · Una sola solicitud, sin PDF a la vista

El cliente vio en `/register/` el bloque «Your four Pelham documents», con cuatro tarjetas y enlaces «View original PDF». Le pareció que los documentos seguían separados y que había que descargar el PDF y llenarlo. Lo que quiere es lo de la primera versión: **una sola solicitud en línea que llena el cliente**, con la solicitud de crédito y los términos y acuerdos incluidos.

- **Se quita el bloque de los cuatro documentos** de `/register/`, `/claims/` y sus versiones en español. La página ya no enlaza ningún PDF.
- **Texto de entrada:** «One online application: your company details, the credit application if you need credit, and the terms and agreements, signed online. No printing or scanning required.» En español: «Una sola solicitud en línea: los datos de su empresa, la solicitud de crédito si necesita crédito, y los términos y acuerdos, firmados en línea…».
- **La solicitud sigue igual,** en 5 pasos:
  1. Empresa.
  2. Facturación y referencias.
  3. Crédito, opcional.
  4. Acuerdos y firmas: los textos se leen completos en la página.
  5. Revisión y envío.
- **Los PDF originales** solo se usan internamente: el equipo recibe el PDF ya llenado y firmado en el panel *Pelham Applications*.

### Instalación
Subir `pelham-applications-1.4.2.zip` y reemplazar la 1.4.1. Vaciar la caché de LiteSpeed.

### Pruebas (WordPress 6.8.3 local)
- `/register/`, `/claims/`, `/es/registro/` y `/es/reclamaciones/`: ningún enlace a PDF ni bloque de documentos.
- **Solicitud por API:**
  - con crédito «Sí»: 4 firmas y envío;
  - con crédito «No»: 2 firmas y envío.
- **Reclamación:** firma y envío.
- **En el navegador, con el enlace de prueba:** en español y en inglés, completada hasta el final sin errores JS.

## 1.4.3 · Botones de prueba visibles en el sitio de prueba

El cliente entraba en `/register/` y no veía cómo probar los formularios. El recuadro «Test mode» solo aparecía a administradores o con el enlace secreto `?pa_test=…`.

- En cualquier dominio que no sea `pelhamservices.com` (por ejemplo, `deeppink-bear-572523.hostingersite.com`), el recuadro **«Test mode» / «Modo de prueba»** aparece a todos los visitantes en registro y reclamaciones, en inglés y en español.
  - Cada botón crea una solicitud TEST que no envía correos.
  - Hay un límite de 20 pruebas cada 15 minutos por conexión.
- El administrador puede ocultarlo con *Hide test buttons from visitors*, en el panel *Client testing link*.
- En `pelhamservices.com` nunca se muestra. Ahí siguen funcionando el enlace secreto y el acceso de administrador.

### Pruebas
- Como visitante anónimo, sin enlace, en `/register/`, `/es/registro/`, `/claims/` y `/es/reclamaciones/`: aparece el recuadro y cada botón abre su solicitud TEST en el idioma de la página, sin errores JS.
- Simulando el dominio `pelhamservices.com`: el recuadro no aparece.
- El enlace secreto y los recorridos completos (crédito «Sí» con 4 firmas y reclamación) siguen funcionando.

