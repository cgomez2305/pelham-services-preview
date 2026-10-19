# Pelham Content 1.10.1 — revisión de la instalación Hostinger

Se corrige la descripción de Registro: ahora explica el registro digital, acuerdos y firma. El administrador actualiza automáticamente solo la descripción inicial obsoleta y guarda copia; conserva una descripción personalizada, el contenido de registro y los expedientes. También se actualizan las fuentes para nuevas instalaciones.

Pelham Company → SEO & migration incluye comprobación de robots.txt público con autenticación y nonce, tiempo de espera limitado y detección de reglas Disallow raíz para Googlebot y *. Se muestra la URL consultada y fecha. La comprobación no sustituye un validador completo de robots ni garantiza indexación. Advierte si existe archivo robots.txt físico y sobre posibles reglas del hosting/CDN.

Hostinger conserva noindex y sitemap desactivado. No se cambia blog_public ni se activan 301 o indexación. La regla Googlebot observada en el hosting no es un error mientras se usa como staging. Un archivo o regla servido antes de WordPress no se puede cambiar mediante el filtro del plugin; requiere revisar Hostinger/CDN al migrar. No se ha modificado el servidor remoto.

## Instalar

Reemplazar únicamente Pelham Content por `pelham-content-1.10.1.zip`. Mantener Modern 1.17.0 y Applications 1.1.1. Entrar al administrador y vaciar LiteSpeed/hosting/CDN. No hace falta reaplicar los textos de toda la web para esta corrección. En SEO & migration se puede pulsar Check public robots.txt.

Validado localmente: migración de descripción antigua con backup, preservación de texto personalizado, meta description renderizada y noindex de Registro, interpretación de grupos robots Googlebot/* y presencia del control de revisión. La entrega SMTP no está incluida en estas pruebas.
