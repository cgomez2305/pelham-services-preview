# Pelham Services — Website Rebuild

Static, dependency-free rebuild of pelhamservices.com: semantic HTML5, modern CSS (Grid/Flexbox + custom properties) and vanilla JS. No WordPress, no build step, no framework. Every page is its own `index.html` in its own folder, so hosts serve clean, permanent URLs (`/services/transloading/`, not `/?page_id=30`).

## Project structure

```
NUEVO THEME/
├── index.html                          Home — Organization + FAQPage JSON-LD
├── services/
│   ├── index.html                      Services pillar
│   ├── transloading/index.html         + Service & FAQPage JSON-LD
│   ├── dedicated-transport/index.html  + Service & FAQPage JSON-LD
│   └── freight-consolidation/index.html + Service & FAQPage JSON-LD
├── locations/
│   ├── index.html                      Locations pillar
│   ├── gardena-ca/index.html           + LocalBusiness JSON-LD
│   └── miami-fl/index.html             + LocalBusiness JSON-LD
├── about/index.html                    Story, values, leadership
├── news/
│   ├── index.html                      Listing
│   ├── future-of-logistics-trends-pelham-is-embracing/index.html    + Article JSON-LD
│   ├── navigating-challenges-in-us-logistics/index.html              + Article JSON-LD
│   └── how-pelham-elevates-logistics-in-ca-fl/index.html             + Article JSON-LD
├── quote/index.html                    Quote form + new-customer registration
├── contact/index.html                  Both terminals, embedded maps, contact form
├── design-system.md                    Color / type / spacing / component / IA / schema reference
├── seo-strategy.md                     Metadata/H1 reference table + 3–6–12 month content roadmap & KPIs
├── README.md                           This file
├── sitemap.xml / robots.txt
└── assets/
    ├── css/styles.css                  All design tokens + component styles
    ├── js/main.js                      Mobile nav, scroll reveal, form validation, active-link state
    └── img/
        ├── brand/                      Real logo (pelham-logo.png, pelham-mark.png) + favicons
        └── og-cover.svg                Abstract OG placeholder (no stock photography)
```

There is no `/login` page — the header/footer "Login" button links straight to the existing external portal (`pelham.supply-vision.com/Portal/Login`) in a new tab, per the client's spec ("mantener el enlace externo, no recrear el portal"). `/contact/` and `/quote/` are intentionally not in the top nav (only Home, Services, Locations, About, News are) — they're reached via the "Request a Quote" button, footer links, and in-page CTAs.

## Running it

No build step required — from this folder:

```bash
python -m http.server 8420
```

or any other static file server (Node's `npx serve .`, etc.). A local server is needed (rather than opening `index.html` via `file://`) so the clean directory URLs and map `<iframe>`s resolve correctly, matching real hosting (GitHub Pages, Netlify, S3, etc. all serve `/folder/` → `folder/index.html` natively).

## Brand — real logo & colors, not invented

The client asked for the actual brand identity to be used, not a designer's substitute:

- **Logo:** pulled directly from the live site (`cropped-pelham-black-1.png`) — not redrawn. `assets/img/brand/pelham-logo.png` is the full lockup (used for schema `logo` fields and anywhere a light background needs it); `assets/img/brand/pelham-mark.png` is the green bird icon isolated from that same file via pixel bounding-box (used in the dark header/footer, paired with "Pelham Services" set in real white text since the wordmark itself is black and unreadable on dark). Favicons at 32/180/192/512px are resized copies of the same crop.
- **Color:** the single accent is the real Pelham green, measured directly from that logo file (`rgb(0,168,88)`/`#00A858` dominant logo pixel; `rgb(55,169,92)`/`#37A95C` the site's own already-configured UI green) — not a designer's substitute palette. Shades were only adjusted for WCAG AA contrast, never re-hued. Full token table and rationale in [design-system.md §2](design-system.md).

## SEO architecture (this rebuild's main purpose)

- **Clean URLs sitewide**, replacing `?page_id=` query strings.
- **New pillar pages** `/services/` and `/locations/` didn't exist before — `/locations/*` in particular is the site's strongest untapped local-SEO asset (two real physical terminals) and now has one dedicated, schema-rich page per terminal.
- **JSON-LD on every relevant page**: `Organization` (home), `LocalBusiness` (each terminal, own exact NAP), `Service` (each service subpage), `FAQPage` (home's fuel-surcharge FAQ + each service subpage's FAQ), `Article` (each news post). See [design-system.md §11](design-system.md).
- **Individual news article pages** — the old site only had a 3-post teaser list with no article URLs at all; each post now has a full page with its own metadata and `Article` schema.
- **Target keyword placement** (title/H1/copy, one primary phrase per page, nothing forced): "California to Florida freight shipping" (Services pillar), "transloading services Miami" (Transloading + Miami terminal), "dedicated freight carrier California" (Dedicated Transport), "LTL vs FTL consolidation California Florida" (Freight Consolidation), "trucking company Gardena CA" (Gardena terminal).
- **NAP consistency**: the exact same address/phone/hours strings are used verbatim across home, footer (every page), `/contact/`, and the two `/locations/*` pages — required for local pack ranking and for matching Google Business Profile / directory listings once those are set up (outside this codebase).
- **Exact title/H1 copy per page** is specified by the client and implemented verbatim — see the reference table in [seo-strategy.md](seo-strategy.md), which also holds the 3–6–12 month content/link-building roadmap and the KPIs to report monthly (none of that roadmap work is code — it's tracked there for whoever runs the campaign next).

## Other design decisions carried over from the previous pass

- **Stack:** plain static HTML/CSS/JS — no framework needed for a page count this size, gives the best Core Web Vitals/SEO baseline with zero build tooling.
- **No stock photography:** placeholder visuals are abstract inline SVG (route lines, node diagrams, grids). Testimonials and team members without real photos use typographic initials avatars (`.avatar-mono`) instead of generic headshots.
- **No auto-rotating hero carousel** — static hero + one abstract route-diagram visual.
- **Registration flow preserved:** the old "Register" page was actually a document-upload workflow (Claim Form, Customer Application, Credit Application, Terms & Agreement), not a quote form. `/quote/` keeps both: a real quote-request form and that document flow, so no functionality was dropped.
- **Facebook link duplication fixed:** the old footer linked the same Facebook profile three times; the new footer links Facebook and LinkedIn once each.

## Data the client still needs to confirm

Search the codebase for `PENDING` to find every inline marker. Summary:

| Item | Where | Notes |
|---|---|---|
| Years in operation | Home trust bar | Not stated anywhere on the current site |
| On-time delivery % | Home trust bar | Do not invent — needs real ops data |
| Insurance / carrier certifications, FMCSA/MC/DOT number | Home trust bar; `/news/how-pelham-elevates-logistics-in-ca-fl/` | The old site's own blog copy implied an FMCSA "active" safety status, but that was never independently verified here, so it's deliberately left pending rather than asserted as fact |
| Current fuel surcharge figure + basis | Home transparency panel + FAQ | Needs a real value, the calculation basis (e.g. DOE weekly diesel index), and an update cadence |
| Verified testimonial wording + permission | Home | Quotes are paraphrased from the old site's truncated testimonials — get exact wording and written OK to publish from Meredith K. and Carlos G. |
| Real team photography | `/about/` | Currently typographic initials (GR, DG, VD, RU) for Gabriel Reverón, Dayana Garcia, Victor Delurintu, Regina Umaña |
| Fleet / warehouse / featured-article photography | sitewide | All visuals are abstract SVG placeholders by design — swap in real photography if available, or keep the abstract system permanently |
| Onboarding PDF documents | `/quote/` | Claim Form, Customer Application, Credit Application, Terms & Agreement — need the actual files to link |
| Confirmed open days | `/contact/`, both `/locations/*` pages | Source only gave "08:00–18:00" with no day range; schema currently assumes Mon–Fri as a placeholder — flagged on-page as `[CONFIRM OPEN DAYS]` |
| Terminal-specific customer reviews | Both `/locations/*` pages | Placeholder block ready, no reviews fabricated |
| Social links | Footer, all pages | Only Facebook + LinkedIn confirmed on the current site — confirm if others should be added |
| Form backend | `/quote/`, `/contact/` | Client-side validation is fully wired; submission currently simulates success client-side. Needs a real endpoint to actually deliver messages |
| Production domain in schema/canonical | All `<head>` blocks | Currently hardcoded to `https://pelhamservices.com/...` (the intended final domain) even though the working preview may be hosted elsewhere — update if the final domain changes |

## Accessibility & responsiveness

Mobile-first; CSS grid collapses at ~900px and ~620px. Keyboard navigation, focus states, semantic landmarks, and form labeling/ARIA follow WCAG AA — see [design-system.md §8](design-system.md) for the checklist.
