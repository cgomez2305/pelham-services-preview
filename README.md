# Pelham Services · WordPress · tema 1.26.1 / contenido 1.21.4 / Applications 1.4.2

Sistema de diseño común para todas las páginas. Tema PHP/CSS/JavaScript y plugin de contenidos editables en `wordpress/`. No requiere React ni compilación en el hosting.

- [Registro digital: Pelham Applications 1.4.2](dist/pelham-applications-1.4.2.zip) · **[Solicitud de cuenta única y enlace de prueba](docs/RELEASE-APPLICATIONS-1.3.md)** · [Vuelta desde 4 formularios separados (1.4.2)](docs/RELEASE-APPLICATIONS-1.4.md) · [Instalación y cobertura de los cuatro PDF](docs/RELEASE-APPLICATIONS-1.0.md)
- [Refuerzo de seguridad y requisitos pendientes](docs/SECURITY-HARDENING-1.0.3.md)
- [Tema 1.26.1](dist/pelham-modern-1.26.1.zip)
- [Plugin de contenidos 1.21.4](dist/pelham-content-1.21.4.zip)
- **[Sitio de prueba en Hostinger y formularios para el cliente](docs/STAGING-PRUEBAS.md)**
- **[Revisiones del cliente del 28/10: Home, cotización por ZIP, Gardena 1.25 / 1.21](docs/RELEASE-1.25.md)**
- **[Artículos del blog revisados y enlazado interno 1.20](docs/RELEASE-CONTENT-1.20.md)** · [Revisiones del cliente 1.24 / 1.19](docs/RELEASE-1.24.md) · [Sitio bilingüe inglés / español 1.23 / 1.18 / Applications 1.2](docs/RELEASE-1.23.md) · [Equipo en About y revisión de keywords, H1 y breadcrumbs 1.22.2 / 1.17.2](docs/RELEASE-1.22.1.md) · [Diseño y SEO por público 1.22 / 1.17](docs/RELEASE-1.22.md) · [Fotografía profesional 1.21 / 1.16](docs/RELEASE-1.21.md) · [Scroll horizontal y animaciones 1.20](docs/RELEASE-1.20.md) · [Correcciones 1.19.1 / 1.15.1](docs/RELEASE-1.19.1.md) · [Cambios, pruebas e instalación 1.16](docs/RELEASE-1.16.md)
- Logo real sin fondo: [color](brand/pelham-logo-color-928.png) · [blanco](brand/pelham-logo-white-928.png)
- [Sistema visual](DESIGN.md)

Reemplazar el tema y los dos plugins, vaciar caché y entrar al administrador: la primera visita crea la versión en español (`/es/`). No reimportar contenido. No se publica automáticamente en Hostinger.

La raíz contiene la vista previa exportada desde una copia de desarrollo con contenidos de referencia de los ZIP. Los formularios estáticos no envían solicitudes. `tools/export-preview.py` usa el WordPress local de revisión configurable con `PELHAM_PREVIEW_URL` (por defecto, revisar el script).

QA: `tools/audit-design-system.cjs` recorre las 25 rutas en escritorio y móvil; `tools/test-design-system.cjs` verifica interacciones sin enviar formularios. Requieren Chromium y Playwright en el entorno de desarrollo.

Conector opcional de análisis HTTPS, desactivado por defecto: [propuesta y activación](docs/SCANNER-CLOUDMERSIVE-1.0.4.md).

[Panel de ventas y exportación sobre PDF originales · 1.1.0](docs/RELEASE-APPLICATIONS-1.1.0.md).

[Oferta LTL confirmada e instalación 1.9.0](docs/RELEASE-CONTENT-1.9.md) · [Estrategia SEO de 90 días](docs/SEO-STRATEGY-LTL-2026-10.md).

[SEO y migración: instalación](docs/RELEASE-SEO-1.10.md) · [Análisis de competidores con fuentes](docs/COMPETITOR-ANALYSIS-2026-10.md).

[Blog y direcciones: instalación](docs/RELEASE-CONTENT-1.11.md) · [Calendario editorial](docs/BLOG-EDITORIAL-PLAN.md).

## Última entrega

- [Auditoría GEO y visibilidad en IA](docs/GEO-AI-VISIBILITY-AUDIT.md).
- [Intención de búsqueda y artículos](docs/ARTICLE-SEARCH-INTENT-1.15.md).
- [Auditoría SEO y alineación](docs/SEO-INTENT-AUDIT-1.14.md).

Los ZIP actuales están versionados en `dist/`; el código editable está en `wordpress/`. La raíz contiene 28 páginas de vista previa con formularios desactivados y protección contra indexación. Publicar en GitHub no instala los paquetes en Hostinger. Las capturas históricas de QA, paquetes antiguos y el runtime local no se incluyen en Git. Los scripts de QA con credenciales ficticias son únicamente para instalaciones locales aisladas.
