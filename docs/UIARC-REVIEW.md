# UIArc y Pelham — evaluación, 10 de octubre de 2026

## Compatibilidad verificada

https://uiarc.dev/docs/installation requiere React 19, TypeScript y alias @/*. Utiliza CSS modules y variables; no requiere Tailwind. La instalación distribuye código mediante shadcn, con dependencias por componente (habitualmente Motion/Lucide, Radix en acordeones). No es un plugin de WordPress. Integración directa posible mediante componentes React compilados e integrados en el tema, pero no necesaria para los patrones identificados.

Recomendación: adaptar patrones con HTML semántico, CSS y JavaScript del tema, conservando contenido editable, backend y validación. No añadir Motion sobre GSAP para resolver las mismas interacciones. Revisar licencia antes de incorporar código distribuido; esta evaluación no copia fuentes ni componentes Pro.

## Prioridades concretas

1. Cotización: opciones de servicio visibles con radio nativo, icono, nombre y una descripción breve. Tres servicios y «Help me choose». Dos columnas amplias o una lista móvil. Indicador de selección y borde, además de color. Conservar nombre/valor enviado, selección por URL, texto introducido, recuperación y guía contextual. Referencia: https://uiarc.dev/components/radio-cards.
2. FAQ: filas amplias, icono uniforme, respuesta claramente separada y apertura/cierre breve e interrumpible. Mantener details/summary y lectura sin JS; no imponer la limitación de una sola respuesta de Arc si dificulta comparar. Referencia: https://uiarc.dev/components/accordion.
3. Formularios: reforzar foco, ayuda y errores asociados, y mantener estados de envío inequívocos. No usar placeholders como sustituto de etiquetas. La guía existente ya aporta instrucciones específicas por servicio.
4. Menú móvil: panel con jerarquía entre navegación, cuenta y cotización; cierre accesible, Escape, control de foco y devolución al disparador. Evaluar visualmente la versión actual antes de reemplazarla.
5. Movimiento: priorizar respuesta a selección, expansión, envío y navegación. Referencia: https://uiarc.dev/docs/motion usa 160 ms para cambios pequeños, 240 ms para paneles y 480 ms para entradas excepcionales; son referencias, no tiempos ya aplicados. Respetar pausa y movimiento reducido, evitar efectos por cada elemento.

## Evidencia y límites

La respuesta HTML pública de /quote/ en https://deeppink-bear-572523.hostingersite.com/ contiene la cotización guiada, los cuatro estados de ayuda, campos opcionales y datos de terminales. Esto confirma su presencia, no una prueba visual ni de entrega de correo. Su footer aún imprime el nombre del dominio como nombre del sitio: corregir el título de WordPress a Pelham Services si sigue así en administración.

El checkout disponible tiene README de reconstrucción estática y commit 02f8467. No contiene wordpress/, dist/, tema 1.9.4 ni plugin 1.8.5. Los directorios correspondientes del runtime están vacíos y no hay artefactos recuperables adjuntos. No se modifica esta versión antigua ni se presenta como actualización del tema. Para implementar sobre la versión correcta hacen falta los ZIP vigentes o el código actualizado en este repositorio.

No se ha instalado UIArc ni publicado cambios.
