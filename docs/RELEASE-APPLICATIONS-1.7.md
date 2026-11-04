# Pelham Applications 1.7 — Terms and Conditions leídos e inicialados antes de continuar

Pelham Applications **1.7.0** · Pelham Content **1.21.10** (traducciones) · Tema **1.26.3** sin cambios

## Qué pidió el cliente
Los Terms and Conditions of Contract son el documento más importante: definen la responsabilidad de Pelham. Aunque el cliente llene toda la solicitud, sus iniciales deben demostrar que leyó y comprobó específicamente esos términos. El sistema no debe dejarle seguir hasta que ponga sus iniciales.

## Cómo funciona ahora (paso 4)
1. **A. Authorized signer:** nombre y cargo del firmante.
2. **B. Terms and Conditions of Contract:**
   - las 25 cláusulas y el reconocimiento final se muestran completos en un recuadro con desplazamiento; también se pueden abrir en PDF;
   - la casilla de iniciales **solo se activa al llegar al final del texto**;
   - las iniciales **deben coincidir con el firmante autorizado**: GR para Gabriel Reverón; GR o GMR para Gabriel Miguel Reverón; ML, MJL o MJLG para María José López Gómez.
3. **C. Agreements y D. Signatures:** quedan **bloqueados**, con un aviso, hasta que las iniciales son válidas.
4. **«Siguiente»** no deja salir del paso 4 sin las iniciales y explica qué falta.

**Validación en el servidor:** el servidor aplica la misma regla. Si las iniciales no coinciden con el firmante, no se puede firmar ni enviar, aunque alguien salte el formulario. Las iniciales aparecen en el pie de cada página del PDF de los términos y en su registro.

## Pruebas (WordPress 6.8.3 local, inglés y español)
- **Bloqueo inicial:** al llegar, iniciales, acuerdos y firmas bloqueados; «Siguiente» bloqueado con su mensaje.
- **Tras leer hasta el final:** se activa la casilla de iniciales.
- **Iniciales incorrectas:** «XX» se rechaza en pantalla y en el servidor (sin firmar ni enviar).
- **Iniciales correctas:** «GR» desbloquea todo; con 3 firmas, la solicitud se envía.
- **API:** con crédito, sin crédito y reclamación, con iniciales correctas.
- **Navegador:** solicitud y reclamación completas.
- **Panel y sitio completo:** las 60 URL sin errores.

## Instalación
Subir `pelham-applications-1.7.0.zip` y `pelham-content-1.21.10.zip`, reemplazando las versiones instaladas, y vaciar la caché de LiteSpeed. Un borrador a medias conserva sus datos; al llegar al paso 4 tendrá que leer los términos y poner sus iniciales.
