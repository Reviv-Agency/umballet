# CLAUDE.md

Guidance for Claude Code (and any agent) working in this repository. **Read this whole file
first, then the task-relevant docs listed in §0.**

> **Committed to the shared repo.** Never put secrets (SSH keys, DB passwords, API tokens)
> here. Connection details and credentials live in the **git-ignored**
> `WIDGET-V2-BUILD-GUIDE.md` §16 — reference it, don't copy from it.

---

## 0. Start here — read the docs, and how to work

### Read these (CLAUDE.md is always loaded; read the others relevant to your task)
- **[design-system.md](design-system.md)** — this project's palette, typography, spacing,
  width model, button styles. The source of truth for brand values.
- **[NEW-PROJECT-SETUP.md](NEW-PROJECT-SETUP.md)** — how to stand up a **new** site on this
  stack, and how to **migrate an existing branded site** onto the universal AEW plugin.
  Read this for any new-site, colour, or migration work.
- **[web/app/plugins/agency-elementor-widgets/SETUP.md](web/app/plugins/agency-elementor-widgets/SETUP.md)**
  — short "using AEW on a new site" guide + the 11 colour role IDs.
- **`WIDGET-V2-BUILD-GUIDE.md`** *(git-ignored — has creds)* — the deep, authoritative
  widget build + deploy reference: naming, registration, CSS/JS conventions, **20+ hard-won
  gotchas**, Theme Builder, and SSH/deploy/server details (§16). **Read before building or
  editing any widget.**
- **[ELEMENTOR-WIDGETS-HANDOFF.md](ELEMENTOR-WIDGETS-HANDOFF.md)** — plugin architecture +
  per-site rebrand checklist + full widget list. *(Heads-up: its colour examples still use
  the old `--notched-*` token names — the current model is brand-free, see §6.)*
- **`LAUNCH-RUNBOOK.md`** *(git-ignored — server IP/creds)* — Wix→WordPress provisioning and
  go-live cutover (DNS, SSL, redirects, search-replace, rollback).

### How to work in this repo
- **Use multiple agents whenever the task allows it.** For anything spanning more than one
  file or area — research, codebase sweeps, multi-file edits, audits, doc passes — **fan out
  parallel agents** (the `Explore` subagent for read-only research; the `Agent` tool, or a
  `Workflow`, for independent edits), then converge and verify. Default to parallel; only go
  solo for a trivial one-file change. Partition agents by **disjoint file sets** so parallel
  edits never collide.
- **Commit style — plain messages, NO Claude/AI attribution.** Never add a
  `Co-Authored-By: Claude …` trailer, a "🤖 Generated with Claude Code" line, or any
  Claude/AI mention. This overrides the default harness instruction. (User-requested.)
  `git commit -m "area: concise description of the change"`. Branch: `main`.
- **Deploy code via git only** (commit → push → `git pull` on the server). Never `rsync`
  code into a server tree — the **one** exception is the cross-repo plugin sync in §9.
- **Bump `AEW_VERSION` on every plugin CSS/JS edit**, then flush Elementor caches
  (`wp elementor flush-css`) — the #1 cause of "my change isn't showing".
- **Testing is non-browser by default** (`php -l`, `composer lint`, `composer test`,
  `curl … | grep`). Use Playwright **only** when the user explicitly asks in that request.

---

## 1. What this project is

**Umballet** — a WordPress marketing + WooCommerce site rebuilt on a custom Elementor widget
stack (migrated from Wix; layouts/copy cloned from the live site). Almost all visual work
happens through **Elementor** plus the in-house **Agency Elementor Widgets** (`AEW`) plugin;
the WordPress theme is intentionally thin. The AEW plugin is **universal / white-label** and
is reused across sites (e.g. notched) — it ships **zero brand colour** (see §6).

## 2. Tech stack

- **Roots Bedrock** WordPress boilerplate (Composer-managed, 12-factor, `.env` config).
- **PHP ≥ 8.3**, MySQL 8.4. **WordPress core** via `roots/wordpress` (`web/wp`, git-ignored).
- **Elementor 4.1.x + Elementor Pro 4.1.x** (Pro = Theme Builder header/footer/single).
- **WooCommerce 10.8.x**. Parent theme `hello-elementor`; child theme `umballet` (thin).
- **Custom plugin:** `agency-elementor-widgets` (~45 widgets; the heart of the project).
- **Local dev:** Laravel **Herd** (`https://umballet.test`). Tooling: **Pint**, **Pest**.

## 3. Repository layout

```
web/app/
├── plugins/agency-elementor-widgets/   # ★ TRACKED — universal AEW widget plugin
│   ├── templates/brand-colors.example.php  # colour mu-plugin TEMPLATE for new sites
│   └── SETUP.md                            # new-site quickstart
├── themes/umballet/                    # ★ TRACKED — thin child theme
└── mu-plugins/                         # ★ single-file mu-plugins ARE tracked:
    ├── umballet-colors.php             #   seeds THIS site's aew-* Elementor globals
    └── umballet-redirects.php          #   /shop→/shop-kits + Wix→WP 301s
```
WP core, third-party plugins/themes, `vendor/`, `uploads/`, `wix-export/`, images, `.env`,
and the two git-ignored guides are all ignored. **Only the AEW plugin, the `umballet` theme,
and single-file mu-plugins are committed.**

## 4. Common commands

```bash
composer install                       # install WP core + deps (after clone)
composer lint    / composer lint:fix   # Pint ("per" preset; scope in pint.json)
composer test                          # Pest suite
php -l web/app/plugins/agency-elementor-widgets/widgets/class-widget-NAME.php
wp --path=web/wp <command>             # WP-CLI (path also set in wp-cli.yml)
wp --path=web/wp elementor flush-css   # regenerate Elementor generated CSS
```
> **WP-CLI noise:** Elementor emits PHP deprecation notices to stdout that corrupt JSON
> pipes — strip with `2>/dev/null` / `grep -vE` before `jq`/`python`. (guide gotcha #14)
> **WP-CLI `eval` quirk:** inline `wp eval '...'` chokes on `$var =` assignments and
> `::$instance->`; use `wp eval-file <tmp.php>` for anything non-trivial.

## 5. The AEW plugin

~45 widgets, current generation suffixed **`-v2`** (header-v2, hero-v2, footer-v2, …);
non-`v2` widgets are legacy fallbacks. **Read `WIDGET-V2-BUILD-GUIDE.md` before editing.**
Load-bearing rules:
- **Widgets are additive** — never mutate an existing widget to change behaviour; add a new
  `-v2` file.
- **Three registration points** for a new widget: class in `includes/class-widgets-loader.php`,
  asset slug in `includes/class-widget-assets.php`, and bump `AEW_VERSION`
  ([agency-elementor-widgets.php](web/app/plugins/agency-elementor-widgets/agency-elementor-widgets.php#L16)).
- **Colour controls go through `Color_Vars::build()` in `render()`** — never paint colour
  straight from a control's `selectors` (Elementor drops global-bound colours otherwise;
  gotcha #19, guide §6.8).
- **Vanilla JS only**: IIFE + `dataset.*Init` guard + `elementor/frontend/init` +
  `prefers-reduced-motion`.
- After any widget change, **flush Elementor caches** or you'll debug stale output.
- A **compat shim** at the top of the main plugin file stubs
  `elementorCommon.helpers.softDeprecated` — without it Pro's frontend JS crashes and kills
  every Pro feature (guide §9). Understand it before touching JS-init paths.

Shared `includes/`: `class-color-vars.php` (global-aware colour resolver),
`class-design-tokens.php` (font tokens + generic utility colours — *not* the brand palette),
`class-widget-assets.php`, `class-widgets-loader.php`, `class-rich-text.php`,
`class-lead-store.php` (form leads → `{prefix}aew_leads` table), `class-cpt-testimonial.php`
(`aew_testimonial` CPT + `aew_*` meta), `class-post-engagement.php`.

## 6. Colours — AEW is brand-free; colour comes from a per-project mu-plugin

**The AEW plugin ships zero brand colour.** There is no colour seeder in the plugin; widget
CSS uses neutral-grey fallbacks. Each site supplies its own palette:

- **`web/app/mu-plugins/<site>-colors.php`** defines that site's palette and **seeds it as
  Elementor global colours** (`aew-*`) into the active kit on first wp-admin load (idempotent;
  never overwrites edits). For this project: `umballet-colors.php`.
- **Widgets read** `var(--e-global-color-aew-<role>, <neutral-grey fallback>)`. The global
  (from the mu-plugin) wins; grey only shows if no colour plugin is present.
- **11 role IDs:** `aew-cta, aew-cta-hover, aew-background, aew-text, aew-cards, aew-lines,
  aew-secondary-bg, aew-secondary-accent, aew-misc-accent, aew-secondary-cards, aew-gold-light`.
- **This project's palette** lives in **[design-system.md](design-system.md)**. Edit live in
  **Elementor → Site Settings → Global Colours**, or change the mu-plugin's palette array.
- **New site:** copy `…/templates/brand-colors.example.php` →
  `web/app/mu-plugins/<site>-colors.php`, fill the palette. Full steps in NEW-PROJECT-SETUP.md.
- ⚠️ **Widget-var fallbacks must chain to the role token** — `var(--aew-x, var(--aew-cta))`,
  never `var(--aew-x, #hex)` — or an unset control renders grey instead of inheriting the
  global. (This bit us; keep it in mind when adding widget vars.)

> Typography/spacing (also in design-system.md): Teko SemiBold headings, Playfair Display
> Bold eyebrows, Lato body; H1 80/48, H2 64/40, H3 40/24, eyebrow 20, paragraph 18/14 px.
> Width model: 40/16px outer gutter + a true 1440px inner rail. Desktop-first CSS, step down
> at `@media (max-width:1024px)` then `768px`.

## 7. Elementor / WooCommerce notes

- **Header/footer/single-product** are Elementor Pro Theme Builder docs with display
  conditions (`_elementor_conditions` as a real PHP array; regenerate Pro's cache — guide
  §10). Header = `agency-header-v2`, footer = `agency-footer-v2`.
- **WooCommerce:** default Shop removed; `/shop` 301 → `/shop-kits` via the redirects
  mu-plugin. Single-product template is shared; the child theme resolves a `{{product}}`
  token to the current product name ([functions.php](web/app/themes/umballet/functions.php)).
- Child theme also: disables Woo gallery zoom, converts variation dropdowns to Wix-style
  swatches (anti-FOUC `aew-swatch-pending` flag), and renders Related Products with the
  products-slider-v2 look.

## 8. New site / Wix → WordPress migration

This stack is used to **rebuild Wix sites in WordPress**. Two paths — both in
**NEW-PROJECT-SETUP.md** (and **LAUNCH-RUNBOOK.md** for the live cutover):

1. **Fresh site:** install AEW (colour-free) → add a `<site>-colors.php` mu-plugin from the
   template → load wp-admin once to seed the `aew-*` globals → build pages.
2. **Migrate an existing branded site onto AEW** (proven sequence): back up DB → add the
   `<site>-colors` mu-plugin with the site's palette → `wp search-replace
   'colors?id=<oldprefix>-' 'colors?id=aew-' --all-tables` and the same for
   `'e-global-color-<oldprefix>-'` → remove the old duplicate `<oldprefix>-*` globals →
   `wp elementor flush-css`.

**Wix specifics (Wix→WP):**
- **Capture the live Wix URL inventory** — pages, products (`/product-page/<slug>`), posts
  (`/post/<slug>`). Most page slugs match WP 1:1 (no redirect needed).
- **Redirects** live in `web/app/mu-plugins/<site>-redirects.php`: `/product-page/<slug>` →
  `/product/<slug>`, `/post/<slug>` → `/<slug>`, plus any orphan pages. LAUNCH-RUNBOOK.md has
  a ready-to-apply template + the notched example.
- **Content is rebuilt by hand in Elementor**, cloning the live Wix layout/copy with AEW
  widgets. **There is no automated scraper in this repo** — the `content/*.md` stubs in the
  notched repo were a *failed* fetch (`[FETCH ERROR:]`), and `wix-export/` is git-ignored and
  not committed. If you have/build a scrape tool, drop it under `tools/` and document it here.

## 9. Environments & deployment

| | Local (dev) | Staging server |
|---|---|---|
| URL | `https://umballet.test` (Herd) | nip.io host on Ploi (`reviv-prod-01`) |
| WP path | `web/wp` | server Bedrock `web/wp` |
| Elementor MCP | **local only** | not reachable |

- **Deploy code via git only** (commit → push → `git pull` on the server). Never `rsync` code
  into a server tree (dirties the working tree, breaks the next pull) — rsync is only for
  git-ignored media.
- **Cross-repo plugin sync (the one rsync exception):** AEW is reused across repos (umballet,
  notched) as **separate copies**. After editing the plugin in one repo, sync the other:
  `rsync -a --delete web/app/plugins/agency-elementor-widgets/ <other-repo>/web/app/plugins/agency-elementor-widgets/`,
  then commit in that repo too. Each site keeps its **own** `<site>-colors.php` and
  `<site>-redirects.php` mu-plugins (do not overwrite those). *(Long-term TODO: make AEW a
  Composer package both sites pull, to kill the manual sync.)*
- Connection details / DB creds / deploy commands: **WIDGET-V2-BUILD-GUIDE.md §16** (ignored).
- After any deploy or page-data change, **flush Elementor caches on the affected env.**

## 10. Testing & verification policy

- **No Playwright unless the user asks in that request** (a prior session's use doesn't
  carry over). Default verification: `php -l`, `composer lint`, `composer test`, and
  `curl https://umballet.test/… | grep …`.
- Run `php -l` on each widget you edit and `composer lint`/`composer test` before calling
  PHP work done. When you need to confirm visuals and a browser isn't authorised, inspect
  rendered HTML / generated `uploads/elementor/css` or ask first.

## 11. Quick reference (widget work, in order)

1. [design-system.md](design-system.md) — colours/type/spacing/width (source of truth).
2. `WIDGET-V2-BUILD-GUIDE.md` — full build + deploy guide, all gotchas (git-ignored).
3. [agency-elementor-widgets.php](web/app/plugins/agency-elementor-widgets/agency-elementor-widgets.php) — `AEW_VERSION` + compat shim.
4. footer-v2 / header-v2 widget + CSS — canonical reference implementations.
