# Pelham Applications 1.5 — firmas simplificadas y PDF antes de firmar

Pelham Applications **1.5.0** · Pelham Content **1.21.8** (traducciones) · Tema **1.26.3** sin cambios

Cambios del documento del cliente `WEBSITE_REVISIONS_11-02` para la solicitud de cuenta en línea.

## 1. Billing & references: listas desplegables
| Campo | Opciones |
|---|---|
| Preferred payment method | ACH · Check · Credit card · Zelle |
| Preferred billing method | Email · Customer portal |
| Reference 1, 2 y 3 — Type of account | Bank reference · Trade reference |

Antes eran campos de texto libre. En español: Cheque, Tarjeta de crédito, Correo electrónico, Portal de clientes, Referencia bancaria y Referencia comercial.

## 2. Paso 4 «Agreements & signatures», más claro
Antes había muchos nombres y firmas: firmante y cargo de la solicitud, nombre y cargo del cargador (*shipper*), propietario o *controller* del crédito, garante, 6 casillas y 4 iniciales. Ahora el paso tiene esta estructura:

**Sus documentos completados.** Cada documento tiene su botón **Open PDF**, que abre ese documento lleno con la información guardada y marcado «DRAFT — NOT SUBMITTED»:
- Completed customer application.
- Completed credit application (solo si se pide crédito).
- Terms and agreements, además con «Read the complete text».

**A. Authorized signer.**
- Nombre completo y cargo de un propietario o directivo («Title / corporate office», p. ej. Owner, VP of Logistics, CFO).
- Se sugieren con la persona de contacto del paso 1 y se pueden cambiar.
- Se indica la razón social de la empresa.
- Si se pide crédito: **Personal guarantor**, con la casilla «The personal guarantor is the same person as the signer», que copia el nombre.

**B. Agreements** (4 casillas):
1. General & transportation terms.
2. Credit & investigation authorization (solo con crédito).
3. Personal guaranty (solo con crédito).
4. Electronic signature consent (ESIGN Act).

Y **una sola casilla de iniciales**, con la nota «By placing your initials here, you acknowledge and agree to all provisions detailed in pages 1–4 of the Transport Conditions.»

**C. Signatures:**
- **Account and transport agreement:** una sola firma que se registra en la solicitud de cuenta y en los términos de transporte.
- **Credit applicant signature** y **Personal guarantor signature:** solo con crédito. El garante puede reutilizar la firma de arriba.

## Los PDF siguen siendo los originales
Los PDF que recibe el equipo siguen siendo los originales llenos, en las mismas posiciones. El plugin rellena automáticamente los campos que pide el papel a partir de los datos nuevos:
- **Nombre y cargo:** los del firmante autorizado, en la solicitud (*Title*), en el crédito (*Owner/Office signature*, *Title*) y en los términos (*Shipper's printed name / title*).
- **Iniciales:** las mismas en las 4 páginas de los términos.
- **Garante:** su nombre en la *Personal Guaranty*.

Las solicitudes enviadas con versiones anteriores conservan sus datos y se siguen exportando igual.

## Por qué antes «no salía el PDF»
El cliente solo podía leer el texto en blanco de los documentos; el PDF lleno solo lo veía el equipo en el administrador. Ahora:
- **Botón «Open PDF»:** abre el PDF del solicitante con su información, antes de firmar y antes de enviar.
- **Privacidad:** solo el propio solicitante, con su sesión verificada, puede abrir su vista previa. Sin sesión, el servidor responde 401, y un documento que no forma parte de la solicitud responde 404. Hay un límite de 60 vistas previas cada 15 minutos.

## Pruebas (WordPress 6.8.3 local)
- **Navegador, en inglés y en español:** solicitud completa con crédito, con 3 firmas (principal, crédito y garante), y reclamación, sin errores JS.
- **Paso 4:**
  - firmante sugerido desde el contacto;
  - «same as signer» copia el nombre del garante;
  - los tres «Open PDF» devuelven `application/pdf` (customer, credit y terms).
- **API:**
  - con crédito: `main`, `credit` y `guarantee`, y envío;
  - sin crédito: solo `main`, y envío;
  - reclamación: firma y envío.
- **PDF final:**
  - firma y cargo en la solicitud;
  - firma, cargo y garante en el crédito;
  - iniciales en las 4 páginas;
  - firma, nombre, cargo y fecha del cargador en los términos.
- **Compatibilidad:** los 20 registros enviados del sitio local, de versiones anteriores y de la nueva, generan su PDF sin errores.
- **Panel de administración y sitio completo:** las 60 URL sin errores PHP ni JS.

## Instalación
Subir `pelham-applications-1.5.0.zip` y `pelham-content-1.21.8.zip`, reemplazando las versiones instaladas, y vaciar la caché de LiteSpeed. El borrador de un cliente que ya estaba a medias conserva sus datos, pero tendrá que completar la nueva sección A y volver a firmar.
