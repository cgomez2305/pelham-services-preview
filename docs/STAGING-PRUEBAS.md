# Sitio de prueba en Hostinger: formularios abiertos al cliente

Objetivo: que el cliente pruebe los formularios reales en inglés y en español (Cotizar, Contacto, Registro digital y Reclamaciones) en un WordPress de prueba, sin tocar la web actual.

Paquetes:
- `pelham-modern-1.25.0.zip`
- `pelham-content-1.21.0.zip`
- `pelham-applications-1.4.1.zip`

## 1. Crear el sitio de prueba (unos 15 minutos)

1. **Subdominio.** En hPanel: *Dominios → Subdominios*, crear por ejemplo `prueba.pelhamservices.com`.
2. **SSL.** En hPanel: *Seguridad → SSL*, activar el certificado para el subdominio (el formulario de registro exige HTTPS).
3. **WordPress.** En hPanel: *Sitios web → Instalador automático → WordPress*, en el subdominio. Idioma del sitio: **inglés**; el español lo crea el plugin.
4. **Tema y plugins.**
   1. *Apariencia → Temas → Añadir → Subir tema*: `pelham-modern-1.25.0.zip` → Activar.
   2. *Plugins → Añadir → Subir plugin*: `pelham-content-1.21.0.zip` → Activar.
   3. *Plugins → Añadir → Subir plugin*: `pelham-applications-1.4.1.zip` → Activar.
5. **Primera visita al administrador.**
   - El plugin crea las páginas y la versión en español (aviso «Spanish site created…»).
   - Fija la estructura de enlaces `/news/%postname%/`; no hay que tocar *Ajustes → Enlaces permanentes*.
   - Si aparecen avisos con botón «Apply … (save backups)», pulsarlos.

El sitio de prueba se marca solo como **noindex**: Google no lo indexa mientras no esté en `pelhamservices.com`. No cambiar *Ajustes → Lectura*.

## 2. Que los correos lleguen

WordPress envía con la función de correo del servidor, que a menudo acaba en spam o no sale. Hay que configurar SMTP:

1. En hPanel: *Correos*, crear un buzón, por ejemplo `web@pelhamservices.com`.
2. Instalar el plugin **WP Mail SMTP** y configurarlo así:
   - **Mailer:** Other SMTP.
   - **Host:** `smtp.hostinger.com`.
   - **Cifrado y puerto:** SSL y 465.
   - **Usuario y contraseña:** los del buzón.
   - **From Email:** el mismo buzón.
3. *WP Mail SMTP → Tools → Email Test*: enviar una prueba y confirmar que llega.

Si hay que comprobar un problema de entrega, ver las cabeceras SPF/DKIM del correo recibido.

## 3. A quién llegan los formularios

- **Cotizar y Contacto:** *Pelham Company → Recipient email*. Para las pruebas, poner el correo de quien deba recibirlas (el tuyo o el del cliente).
- **Registro digital y Reclamaciones:** *Pelham Applications → Settings → Sales notification email*.

## 4. Cron real (recomendado)

Los correos del registro digital salen en segundo plano con WP-Cron, que solo se ejecuta cuando alguien visita la web. En un sitio de prueba con pocas visitas pueden tardar.

En hPanel: *Avanzado → Cron Jobs*, crear una tarea **cada 5 minutos**:

```
wget -q -O - "https://prueba.pelhamservices.com/wp-cron.php?doing_wp_cron" >/dev/null 2>&1
```

## 5. Enlace de prueba de Pelham Applications

Para que el cliente pruebe la solicitud de cuenta y crédito y la reclamación sin verificar un correo:

1. *Pelham Applications → Client testing link → Create link (14 days)*.
2. Enviarle los enlaces que aparecen (inglés y español). En la página verá **«Test mode» / «Modo de prueba»** con los botones para probar cada formulario.
3. Las solicitudes de prueba se marcan como **TEST** y no envían correos.
4. Al terminar: *Delete N test requests* y *Disable link*.

Detalle en `docs/RELEASE-APPLICATIONS-1.3.md`. Para probar también los correos del registro, el cliente usa el formulario normal (sin enlace de prueba) con su correo real.

## 6. Lista de pruebas para el cliente

Enviar al cliente la URL de prueba y estas indicaciones:

- **Datos ficticios:** usar datos inventados. El correo debe ser real y de un dominio público (gmail.com, la empresa…), porque el registro digital rechaza dominios de ejemplo.
- **Límite antispam:** Cotizar y Contacto aceptan **un envío cada 2 minutos por conexión**. Si sale «Please wait a few minutes» o «Espere unos minutos», esperar 2 minutos.

| # | Prueba | Resultado esperado |
|---|---|---|
| 1 | `/quote/` · LTL, nivel Regular: ZIP 90248 → 33166, piezas, peso, pies cúbicos, mercancía y valor | «Thank you. Your message has been sent…». Llega un correo «Quote request» con cada dato. |
| 1b | `/quote/` · FTL | Pide «Total weight» y no pide pies cúbicos. El mensaje es opcional. |
| 2 | `/es/cotizacion/` · «Ayúdenme a elegir» | Mensaje en español. El correo al equipo incluye «Language: Spanish (reply in Spanish)». |
| 3 | `/quote/` · elegir LTL y cambiar entre Expedited, Regular y Economy | Debajo aparece el plazo estimado y la cobertura de cada nivel (Expedited: solo de Homestead a West Palm Beach). |
| 4 | `/contact/` y `/es/contacto/` | Mensaje de confirmación y correo «Contact enquiry». |
| 5 | Enviar sin rellenar campos obligatorios | Errores junto a cada campo y en el idioma de la página. |
| 6 | `/register/` · empezar solicitud con su correo (sin enlace de prueba) | Mensaje «Check your email…». Llega un enlace privado; al abrirlo continúa la solicitud guardada. |
| 7 | `/es/registro/` · completar y firmar | Toda la interfaz en español. Al enviar, acuse de recibo al cliente y aviso al equipo. |
| 8 | `/claims/` · reclamación de prueba | Mismo flujo con enlace privado y acuse de recibo. |
| 9 | Selector **ES / EN** en cualquier página | Lleva a la misma página en el otro idioma. |
| 10 | Artículos del blog: enlaces internos y «Related freight guides» | Cada enlace abre `/news/…` (o `/es/blog/…`) directamente, sin redirección. |
| 11 | Enlace de prueba · «Probar la solicitud de cuenta» y «Probar la reclamación de carga» | Se abre la solicitud con referencia TEST, sin pedir correo. Se puede completar, firmar y enviar. No llega ningún correo. |
| 12 | Menú principal y Our network | **Home** / **Inicio** es el primer elemento. Gardena aparece como «Pelham Services Gardena». |

Las solicitudes de prueba del registro se pueden borrar después en el administrador de Pelham Applications.

## Si algo falla

| Síntoma | Causa probable |
|---|---|
| «Message sent», pero no llega nada | Falta SMTP (paso 2) o el destinatario no es el correcto (paso 3). Mirar también spam. |
| Siempre «Please wait a few minutes», aunque nadie haya enviado nada | La CDN o el proxy de Hostinger hacen que todos los visitantes compartan IP. Desactivar la CDN del subdominio de prueba y avisarme. |
| El correo del registro tarda mucho | Falta el cron real (paso 4). |
| Los artículos abren `/<artículo>/` | Estructura de enlaces cambiada a mano. *Ajustes → Enlaces permanentes → Estructura personalizada* `/news/%postname%/`. |
