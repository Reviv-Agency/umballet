# New project setup

Checklist for spinning up a new site on this stack (Bedrock + Elementor + the
`agency-elementor-widgets` (AEW) plugin). AEW itself is **colour-free**: its widgets read
`var(--e-global-color-aew-<role>)` with neutral-grey fallbacks, so branding is layered on
per-site via a tiny seeder mu-plugin.

## Fresh site

1. **Clone / install AEW.** Bring the colour-free plugin in via Composer/git as usual.
   With no palette seeded the site renders in neutral greys — unbranded but not broken.

2. **Create the colour mu-plugin.** Copy the template and fill in this site's palette:

   ```
   web/app/plugins/agency-elementor-widgets/templates/brand-colors.example.php
     →  web/app/mu-plugins/<site>-colors.php
   ```

   Set the `Plugin Name` header, choose a **unique** option key (replace
   `project_colors_seeded`), and put the site's brand hexes in the `$palette` array.

3. **First wp-admin load seeds the globals.** Open wp-admin once. The seeder writes the
   `aew-*` colours into the active Elementor kit (idempotent: adds missing colours, never
   overwrites; guarded by the option flag; regenerates kit CSS).

4. **Design.** Build pages with the widgets. Tweak colours live any time in
   **Elementor → Site Settings → Global Colours** — the mu-plugin only seeds once.

See `web/app/plugins/agency-elementor-widgets/SETUP.md` for the full role-id list.

## Migrating an EXISTING branded site onto AEW

When a site already uses a different global-colour id prefix (e.g. `notched-*` or
`brand-*`) and you want it to ride on the shared `aew-*` roles, follow this proven
sequence. Replace `<oldprefix>` with the site's current colour id prefix and `<site>`
with the project slug.

1. **Back up the database.** Non-negotiable — the search-replace steps rewrite all tables.

2. **Add the colour mu-plugin** at `web/app/mu-plugins/<site>-colors.php` carrying the
   site's **real** existing palette (same hexes, now under `aew-*` ids). This guarantees
   the `aew-*` globals exist after the rename.

3. **Rewrite the global-colour references** in all Elementor data. Two replacements: the
   picker reference form and the emitted CSS-variable form.

   ```bash
   wp search-replace 'colors?id=<oldprefix>-' 'colors?id=aew-' --all-tables
   wp search-replace 'e-global-color-<oldprefix>-' 'e-global-color-aew-' --all-tables
   ```

4. **Remove the old duplicate globals.** Delete the now-orphaned `<oldprefix>-*` colour
   entries from the active kit's `custom_colors` so the kit is clean and only the `aew-*`
   roles remain.

5. **Regenerate Elementor CSS.**

   ```bash
   wp elementor flush-css
   ```

After this, every widget resolves to the `aew-*` globals and the site is on the shared
role system. Colours remain editable in Elementor → Site Settings → Global Colours.
