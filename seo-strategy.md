# Pelham Services — SEO Strategy & Roadmap

This complements [design-system.md](design-system.md) (technical/structural SEO already built into the site) with the client's content strategy and long-term roadmap. Metadata below is already implemented site-wide as of this writing — this file is the durable reference for *why* those choices were made and *what comes next*, most of which happens outside this codebase (Google Business Profile, directories, review outreach, PR).

## 1. Metadata & H1 reference (implemented)

Every page's `<title>`, meta description, and `<h1>` follow this exact table. If either ever drifts from the live HTML, the HTML is correct and this table should be updated to match — but check before assuming, since the two should stay in sync.

| Page | Title | H1 |
|---|---|---|
| `/` | Pelham Services \| Coast-to-Coast Freight Transportation | Coast-to-Coast Freight Transportation, Built on Transparency |
| `/services/` | Freight Services \| Transloading, Dedicated Transport & Consolidation | Freight Solutions Built Around Your Supply Chain |
| `/services/transloading/` | Transloading Services \| Los Angeles, Jacksonville, Tampa, Orlando, Miami | Transloading Services at 5 Strategic Facilities |
| `/services/dedicated-transport/` | Dedicated Freight Transport \| California to Florida | Dedicated Transport for Predictable, Reliable Delivery |
| `/services/freight-consolidation/` | Freight Consolidation Services \| Save on LTL Shipping | Consolidate Freight, Reduce Costs |
| `/locations/` | Our Terminals \| Gardena, CA & Miami, FL | Two Terminals, One Coast-to-Coast Network |
| `/locations/gardena-ca/` | Trucking Company in Gardena, CA \| Pelham Services | Gardena, CA Terminal |
| `/locations/miami-fl/` | Trucking Company in Miami, FL \| Pelham Services | Miami, FL Terminal |
| `/about/` | About Pelham Services \| Our Story & Values | About Pelham Services |
| `/news/` | News & Updates \| Pelham Services | News & Updates |
| `/news/<slug>/` | `[Article Title] \| Pelham Services` per article | Article title |
| `/quote/` | Request a Freight Quote \| Pelham Services | Request Your Freight Quote |
| `/contact/` | Contact Pelham Services \| Gardena, CA & Miami, FL | Contact Us |

Note the deliberate keyword split between `/services/transloading/` and `/locations/miami-fl/`: the service page targets facility breadth ("5 Strategic Facilities" — LA, Jacksonville, Tampa, Orlando, Miami), while the Miami terminal page targets the local "trucking company" intent. Both still cross-link to each other in body copy so neither loses the other's relevance.

## 2. Suggested recurring `/news` topics

- Monthly fuel-surcharge update (ties directly to the homepage Transparency/FAQ section).
- Expansion or partnership announcements, as they happen.
- Educational guides: transloading explained, LTL vs. FTL consolidation, peak-season shipping prep.

## 3. Roadmap

### Months 1–3 — Foundation
- Launch the new site with the structure and metadata in this document already live (done as of this build).
- Claim and optimize a Google Business Profile for **both** terminals — correct category, real photos, hours, first posts.
- Get NAP (Name/Address/Phone) consistent across 5–8 relevant industry directories: FreightWaves, DAT, broker directories, BBB, Yelp, Apple Maps. The site's own NAP is already identical across every page and both `/locations/*` pages — use that as the source of truth when submitting to directories.
- Set up Google Search Console and start tracking the priority keywords (see design-system.md §9 target-phrase list).
- Publish 2–4 seed articles on `/news` — one per core service plus one explaining the fuel surcharge.

### Months 4–6 — Local authority & first conversions
- Active review-request campaign to real clients — target 10–15 new reviews per terminal. (The `/locations/*` pages already have a reviews placeholder block ready to receive these once collected — no fabricated reviews were ever added.)
- 1–2 articles/month on `/news`, alternating educational guides and transparency updates (surcharge changes).
- First outreach to Gardena/LA and Miami-Dade chambers of commerce for local backlinks.
- Review Search Console performance and adjust metadata for underperforming pages.

### Months 7–12 — Scale & defend rankings
- Expand each service cluster (transloading, dedicated transport, consolidation) with more specific subtopics based on actual Search Console query data.
- Real press releases (expansions, partnerships) to earn backlinks from logistics trade media and local outlets.
- Evaluate industry-specific landing pages (e.g. "freight for retail," "freight for perishables") if traffic data justifies it.
- Full technical audit each quarter: Core Web Vitals, broken links, duplicate content.

## 4. Monthly KPIs to report

- Rankings for each terminal's local keywords and each service's commercial keywords.
- Organic traffic, total and per pillar page.
- Leads generated via the quote form — for B2B freight, lead quality matters more than raw traffic volume.
- New reviews per quarter, per terminal.

## 5. Constraint (carries over from the master brief)

Never invent performance figures, reviews, or contact data for reporting or for the site. Anything not independently confirmed is `[DATA PENDING]` until the client supplies it — see the pending-data table in [README.md](README.md).
