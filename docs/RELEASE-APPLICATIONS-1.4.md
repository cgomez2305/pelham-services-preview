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
