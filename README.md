# Pelham Services — Website Rebuild

Static, dependency-free rebuild of pelhamservices.com: semantic HTML5, modern CSS (Grid/Flexbox + custom properties) and vanilla JS. No WordPress, no build step, no framework — open any `.html` file in a browser or serve the folder with any static file server.

## Project structure

```
NUEVO THEME/
├── index.html          Home
├── services.html       Transloading · Dedicated Transport · Freight Consolidation
├── team.html           Story, values, leadership
├── news.html           Latest 3 posts (summarized from current site)
├── quote.html          Get a Quote form + new-customer registration
├── contact.html        Both terminals, embedded maps, contact form
├── portal.html         Branded bridge page to the external client portal
├── design-system.md    Color / type / spacing / component reference
├── README.md           This file
└── assets/
    ├── css/styles.css  All design tokens + component styles
    ├── js/main.js      Mobile nav, scroll reveal, form validation, active-link state
    └── img/            favicon.svg, og-cover.svg (abstract, no stock photography)
```

## Running it

No build step required:

```bash
# from this folder
npx serve .
```

or simply open `index.html` directly in a browser. (A local server is recommended over `file://` for the map `<iframe>`s and to mirror real hosting.)

## Design decisions

- **Stack:** plain static HTML/CSS/JS instead of a framework. This is a low-page-count marketing site with no dynamic data source — static markup gives the best Core Web Vitals and SEO out of the box with zero build tooling, and is trivial to host anywhere (Netlify, S3, GitHub Pages, or the existing host).
- **One accent color:** steel blue (`#3d7cc9`) against near-black/white, per the brief's "sober, high-contrast, one accent used sparingly" direction. See [design-system.md](design-system.md).
- **No stock photography:** all placeholder visuals are abstract inline SVG (route lines, node diagrams, grids). Testimonials and team members without real photos use typographic initials avatars instead of generic headshots.
- **No auto-rotating hero carousel:** replaced with a static hero + one abstract route-diagram visual, per the brief's explicit rejection of the current template's slider.
- **Client portal:** kept as an external link to `pelham.supply-vision.com/Portal/Login`, but given its own designed bridge page (`portal.html`) plus a prominent header button, instead of a plain nav link.
- **Registration page:** the current site's "Register" page is actually a document-upload workflow (Claim Form, Customer Application, Credit Application, Terms & Agreement), not a quote form. `quote.html` preserves both: a proper quote-request form (per the brief) and the existing document-registration flow, so no real functionality is lost in the rebuild.
- **Facebook link duplication fix:** the current site links to the same Facebook profile three times in the footer (and once to LinkedIn). The new footer links to Facebook and LinkedIn exactly once each.
- **Real content preserved:** service descriptions, terminal addresses/phones, team names & titles, testimonial authors, and the three latest news posts were pulled directly from the live site and rewritten in the new voice — not fabricated.

## Data the client still needs to confirm

Marked inline in the HTML as `[DATA PENDING]` / `[... PENDING]` — search the codebase for `PENDING` to find every instance. Summary:

| Item | Where | Notes |
|---|---|---|
| Years in operation | `index.html` trust bar | Not stated anywhere on the current site |
| On-time delivery % | `index.html` trust bar | Do not invent — needs real ops data |
| Insurance / carrier certifications | `index.html` trust bar | e.g. FMCSA MC/DOT number, cargo insurance limits |
| Current fuel surcharge figure | `index.html` transparency panel | Current site shows generic copy with no live number; needs a real value and an update cadence (e.g. weekly, tied to the DOE diesel index) |
| Verified testimonial wording + permission | `index.html` | Quotes are paraphrased from the current site's truncated testimonials — get the exact wording and written OK to publish from Meredith K. and Carlos G. |
| Real team photography | `team.html` | Currently typographic initials (`GR`, `DG`, `VD`, `RU`) for Gabriel Reverón, Dayana Garcia, Victor Delurintu, Regina Umaña |
| Fleet / warehouse photography | sitewide | All hero/section visuals are abstract SVG placeholders by design — swap in real photography if/when available, or keep the abstract system permanently (see design-system.md §7) |
| Onboarding PDF documents | `quote.html` | Claim Form, Customer Application, Credit Application, Terms & Agreement — need the actual files to link |
| Full news articles | `news.html` | Currently a 3-post summary; individual article pages/CMS not built |
| Social links | footer, all pages | Only Facebook + LinkedIn found on the current site — confirm if Instagram/X/YouTube exist and should be added |
| Form backend | `quote.html`, `contact.html` | Client-side validation is fully wired; submission currently simulates success client-side. Needs a real endpoint (e.g. a serverless function, Formspree, or the existing WordPress mail handler) to actually deliver messages |
| Business hours by day | footer, `contact.html` | Source material only gives "08:00–18:00" with no day range specified — confirm days (e.g. Mon–Fri only?) |

## Accessibility & responsiveness

Built mobile-first; verified breakpoints at ~375px (mobile), ~768px (tablet) and ~1280px+ (desktop) via the CSS grid collapse rules in `styles.css`. Keyboard navigation, focus states, semantic landmarks, and form labeling/ARIA follow WCAG AA — see design-system.md §8 for the checklist.
