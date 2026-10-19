# Pelham 1.15 — entradas y estados de interacción

Los títulos se recortaban durante la entrada, las fotos llegaban tarde y quedaban reglas de hover que desplazaban tarjetas. Se han eliminado las máscaras, reducido duraciones/desfases y anticipado la activación al aproximarse al viewport. Las FAQ también entran. Las fotos tienen un acercamiento sutil dentro de su marco.

Los botones comparten diseño y estados normal, hover, foco, pulsado y deshabilitado. El hover cambia color/borde y desplaza únicamente el icono. Las tarjetas permanecen en su sitio y señalan el enlace mediante borde y subrayado. Menú, filtros, navegación y acordeones comparten cadencias. Movimiento reducido y pausa mantienen las indicaciones de estado sin desplazamientos.

## Comprobaciones

- 24 combinaciones de seis páginas y cuatro anchos, con geometría estable, espacios, barra administrativa y sin errores JS ni overflow.
- Entrada efectiva del hero y ruta en escritorio/móvil; fallback con script bloqueado y movimiento reducido.
- Ocho botones de home, servicio, contacto, cotización y registro: cambio real de color, área de clic estable y foco visible.
- Tarjetas sin desplazamiento, FAQ con entrada y teclado, cotización guiada, menú/Escape/foco, filtro de noticias y funcionamiento de formularios sin JS. No se enviaron solicitudes.

La comparación local de scroll, con recursos cargados, midió 159 intervalos por modo: mediana de 16.7 ms y máximo de 16.8 ms tanto con entradas activas como en pausa. Las entradas estuvieron activas en 138 fotogramas. No es una garantía de rendimiento en todos los dispositivos.

El video y las capturas son del WordPress local de revisión. No constituyen una publicación ni una grabación de Hostinger actualizado.

## Instalación

Reemplazar únicamente el tema con pelham-modern-1.15.0.zip. Conservar el plugin Pelham Content 1.8.6 y los contenidos existentes; no reimportar páginas.
