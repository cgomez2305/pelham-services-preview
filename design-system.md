# Pelham Services — Design System

This is the single source of truth for visual and interaction consistency across the site. All tokens live in [`assets/css/styles.css`](assets/css/styles.css) as CSS custom properties on `:root`.

## 1. Design direction

Premium, architectural, quietly confident — the visual register of a modern logistics/fintech platform, not a WordPress trucking template. No stock "truck at sunset" photography, no auto-rotating carousels, no cluttered icon grids. Every visual is either real content (team, testimonials, terminals) or an abstract geometric composition (route lines, grids, nodes) standing in for photography that doesn't exist yet.

## 2. Color

The accent is **the real Pelham brand green**, not an invented palette. It was extracted directly from the client's existing logo file (`wp-content/uploads/2025/09/cropped-pelham-black-1.png` on the live site): the bird mark's dominant pixel measures `rgb(0,168,88)` / `#00A858`, and the site's own already-configured UI green (nav active states, buttons) measures `rgb(55,169,92)` / `#37A95C`. Every green token below stays within that exact hue — shades were only adjusted for WCAG contrast, never re-colored.

| Token | Value | Usage |
|---|---|---|
| `--ink-900` | `#0a0e13` | Primary dark background (header, footer, dark sections, hero) |
| `--ink-800` | `#12161d` | Secondary dark surface (cards on dark, trust bar) |
| `--ink-700` / `--ink-600` | `#1b212a` / `#2a323d` | Dark borders, hover states |
| `--paper-000` | `#ffffff` | Primary light background |
| `--paper-050` | `#f6f7f8` | Alternating light section background |
| `--line-light` | `#dfe2e6` | Light-mode borders/dividers |
| `--text-primary` | `#0e1217` | Body text on light backgrounds |
| `--text-muted` | `#5b6472` | Secondary text on light backgrounds |
| `--text-on-dark` / `--text-on-dark-muted` | `#f4f6f7` / `#97a1ad` | Text on dark backgrounds |
| `--accent-500` | `#14a44d` | Brand green, brighter — decorative use only (icons, thin lines, large text on dark). Fails AA as body text/button-fill on white, so it's restricted to non-text or dark-background contexts. |
| `--accent-600` | `#0b7a3e` | **Primary accent for text and button fills on light backgrounds** — links, `.btn--primary`, badges. 5.4:1 contrast on white with white button text, AA-safe as body text too. |
| `--accent-700` | `#08592c` | Hover/pressed state for `--accent-600`. |
| `--accent-on-dark` | `#4ed98a` | Eyebrows/links/small accents **on dark sections only** — ~11:1 contrast against `--ink-900`. Never use on light backgrounds (fails AA there). |
| `--accent-050` | `#e7f5ec` | Light tint background for badges on white/`--paper-050` sections. |
| `--success` | `#2f8f5b` | Form success state only |
| `--danger` | `#c94b3d` | Form error state only |

**Rule:** one accent hue (green), used sparingly and consistently — never introduce a second "brand" color alongside it. `--accent-500` and `--accent-on-dark` are brightness variants of the *same* green for contrast purposes, not a second color. Success/danger are functional, not decorative, and only appear in form feedback.

## 2b. Logo

Two real logo files, both supplied by the client (not redrawn):

- `assets/img/brand/pelham-logo.png` — black wordmark + green bird mark, transparent background. Only works on light backgrounds. Used for the `Organization.logo` JSON-LD value and anywhere a light-background lockup is needed.
- `assets/img/brand/pelham-logo-white.png` — the same lockup with a white wordmark, for dark surfaces. This is the one used in the sticky header and footer (`.brand__logo`, `height: 30px` header / `34px` footer, `width: auto`) — it replaced an earlier workaround (icon-only crop + CSS text) once the client supplied the proper white variant. `alt="Pelham Services"` carries the accessible name since the wordmark is now baked into the image.

`assets/img/brand/pelham-mark.png` (the green bird only, isolated via pixel bounding-box from the black-wordmark file) still exists and is the source for `assets/img/brand/favicon-{32,180,192,512}.png` — the favicon needs a square-ish crop, not the full wide lockup.

## 3. Typography

- **Headings:** `Space Grotesk` (500/600/700) — geometric, high-weight, tight tracking (`letter-spacing: -0.01em` to `-0.02em`). Loaded via Google Fonts.
- **Body:** `Inter` (400/500/600) — neutral, highly legible at small sizes.
- Type scale is fluid via `clamp()` on `h1`/`h2`/`h3` so headlines scale smoothly between mobile and desktop instead of jumping between fixed breakpoints.
- Body copy defaults to `--text-muted` for a softer, secondary feel; only headings and emphasis use full-contrast `--text-primary`.

## 4. Spacing

8px-based scale, exposed as `--sp-1` (0.5rem) through `--sp-9` (8rem). Sections use `--sp-8` vertical padding by default (`--sp-6` for `.section--tight`). Never hardcode pixel margins in page markup — use the scale or `.section`/`.grid` utilities.

## 5. Layout

- Max content width: `1240px` (`.container`), `820px` for text-heavy/narrow layouts (`.container--narrow`).
- Grid system: `.grid` + `.grid-2/3/4`, collapsing to 2 columns at 900px and 1 column at 620px.
- Radii: `4px` (buttons/inputs), `8px` (cards/media), `14px` (large panels: hero visuals, CTA bands, surcharge panel).

## 6. Motion

- All transitions run on `--dur` (240ms) with `--ease` (`cubic-bezier(0.4,0,0.2,1)`) — used for hovers, nav states, focus rings.
- Scroll reveals (`[data-reveal]`) fade + translate up 16px on entering the viewport via `IntersectionObserver`, once per element. Respects `prefers-reduced-motion`.
- No auto-advancing carousels or looping animation anywhere on the site — this was an explicit rejection of the old template's slider hero.

## 7. Components

Documented inline in `styles.css` by section comment blocks: buttons (`.btn` + modifiers), header/nav, hero, trust bar, cards (light + `.card--dark`), process steps (`.steps`), surcharge panel, testimonials (typographic `.avatar-mono` initials in place of stock photography), coverage/terminal cards, news cards, CTA band, forms, footer.

**Avatars:** where a real photo doesn't exist (testimonials, team), use `.avatar-mono` — a circular initials badge in the accent-tinted palette. This is a deliberate stand-in system, not a placeholder to be "fixed" — it reads as intentional and can stay even after real photos are supplied, as a fallback for future team members.

**Icons:** every icon on the site is a hand-authored inline SVG in a single consistent line-icon style (`stroke="currentColor"`, `fill="none"`, 1.5–1.6 stroke-width, no fill) — value-card icons (shield/clock/eye), the service-card arrow, social icons, the FAQ accordion marker, the nav hamburger. No emoji, no icon-font glyphs, no raster icon images anywhere in the codebase. Keep new icons in this same stroke style rather than introducing filled/duotone/emoji icons.

## 8. Accessibility (WCAG AA baseline)

- Body text on both dark and light backgrounds meets 4.5:1 contrast; verify any new color usage against `--ink-900` / `--paper-000` before shipping.
- All interactive elements have visible `:focus-visible` states (2px accent outline).
- Skip-to-content link on every page.
- Mobile nav is keyboard-operable (`Escape` closes it, focus returns to the toggle button) and exposes `aria-expanded`.
- Forms use associated `<label>`s, `aria-describedby` error/hint text, and `role="alert"`/`role="status"` on submission feedback.
- Decorative SVGs carry `aria-hidden="true"`; meaningful diagrams carry `role="img"` + `aria-label`.
- `html, body { overflow-x: hidden; max-width: 100%; }` is set globally. The off-canvas mobile nav panel (`position: fixed`, translated off-screen when closed) otherwise makes some engines report the document as wider than the viewport, which can surface as a rubber-band horizontal scroll on mobile even though the panel is visually off-screen. This rule is required, not decorative — don't remove it when touching header/nav CSS.

## 9. Content rules

- No invented performance metrics. Anything not confirmed by the client is rendered as `[DATA PENDING]` (see the trust bar and fuel-surcharge panel on the homepage) rather than a plausible-sounding placeholder number.
- No stock photography. Placeholder visuals are abstract SVG (route lines, grids, node diagrams) — swap for real fleet/warehouse photography by replacing the relevant `<svg>` block, not by adding an `<img>` over it.

## 10. Site architecture & URLs

Every page lives in its own folder as `index.html` so hosts serve clean, extensionless URLs (`/services/transloading/`, not `/services/transloading.html` or `/?page_id=30`). Depth is fixed and every page at the same depth shares byte-identical header/footer markup:

- **Depth 0** (`/index.html`): asset/link prefix is bare (`assets/...`, `services/`).
- **Depth 1** (`/services/`, `/locations/`, `/about/`, `/news/`, `/quote/`, `/contact/`): prefix `../`.
- **Depth 2** (`/services/transloading/`, `/locations/gardena-ca/`, `/news/<slug>/`, etc.): prefix `../../`, always resolved root-then-down rather than sibling-relative, so the same header/footer HTML works unmodified regardless of which cluster the page belongs to.

Main nav is deliberately short: Home, Services, Locations, About, News, plus two buttons — "Request a Quote" (internal) and "Login" (external, direct to `pelham.supply-vision.com`, no in-app portal page). Contact and Quote are reachable via CTAs/footer, not the top nav, per the client's spec.

## 11. Structured data (JSON-LD)

- `Organization` + one `FAQPage` (fuel surcharge) live on the home page only.
- Each `/locations/*` subpage carries its own `LocalBusiness` entity with that terminal's exact NAP — never share one `LocalBusiness` block across both terminals.
- Each `/services/*` subpage carries a `Service` entity plus a page-specific `FAQPage` (reusing the `.faq-item`/`.faq-list` accordion, which must stay in sync with its JSON-LD).
- Each `/news/*` article carries an `Article` entity with `datePublished`/`author`/`publisher`.
- All schema `logo`/`image` URLs point to the absolute production URL of `assets/img/brand/pelham-logo.png`, and all schema `url` fields use the final `https://pelhamservices.com/...` path even while the working preview is hosted elsewhere.
