# Umballet Design System

Source: Figma (captured 2026-05-28). **Use these values for every page, widget, and component. Never use Elementor defaults.**

---

## Colors

| Role | Hex | Elementor Global ID |
|------|-----|---------------------|
| Cards | `#FFFFFF` | `notched-cards` |
| Background | `#F1EADF` | `notched-background` |
| Lines & Accents | `#C2BAAB` | `notched-lines` |
| Misc Accent | `#3B413F` | `notched-misc-accent` |
| Secondary Cards | `#7D958D` | `notched-secondary-cards` |
| Secondary BG / H1–H2 | `#876137` | `notched-secondary-bg` |
| Secondary Accent | `#3E382F` | `notched-secondary-accent` |
| Header H3+ / Paragraph | `#1B150E` | `notched-text` |
| Main CTA | `#632B3A` | `notched-cta` |
| CTA Hover | `#89505F` | `notched-cta-hover` |

> **Palette refresh (2026-06-11, from Figma):** brand moved to a warm brown/maroon
> system. The **CTA** is now maroon `#632B3A` (hover `#89505F`); the gold `#876137`
> is now the **H1–H2 header** colour (`notched-secondary-bg`); body text is `#1B150E`.
> Token *names* (`--notched-*`) are unchanged. White text on the `#632B3A` CTA and on
> `#876137` headers both exceed WCAG AA 4.5:1.

---

## Typography

### Font Families
- **Headings H1–H3+:** Teko SemiBold (600)
- **Subhead / Eyebrow:** Playfair Display Bold (700)
- **Body / Paragraph:** Lato Regular (400)

### Type Scale

| Element | Size Desktop | Size Mobile | Line Height |
|---------|-------------|-------------|-------------|
| H1 | 5rem / 80px | 3rem / 48px | 85% |
| H2 | 4rem / 64px | 2.5rem / 40px | 85% |
| H3 | 2.5rem / 40px | 1.5rem / 24px | 85% |
| Subhead (eyebrow) | 1.25rem / 20px | 1.25rem / 20px | 100% |
| Paragraph | 1.125rem / 18px | 0.875rem / 14px | 140% |

### H1–H2 Color: `#876137`
### H3+ / Body Color: `#1B150E`

---

## Spacing

### Desktop
| Token | Value |
|-------|-------|
| Section Y Padding | 64px / 4rem |
| Section X Padding | 40px / 2.5rem |
| Max Width | 1360px |
| Card Padding | 20px / 1.25rem |
| Text Padding | 20px / 1.25rem |
| Stacking Gap | 16px / 1rem |
| Card Border Radius | 24px / 1.5rem |
| Card Image Border Radius | 16px / 1rem |

### Mobile
| Token | Value |
|-------|-------|
| Section Y Padding | 32px / 2rem |
| Section X Padding | 16px / 1rem |
| Max Width | 100% fill |
| Card Padding | 20px / 1.25rem |
| Text Padding | 20px / 1.25rem |
| Stacking Gap | 16px / 1rem |
| Card Border Radius | 20px / 1.25rem |
| Card Image Border Radius | 12px / 0.75rem |

---

## Buttons

| Property | Desktop | Mobile |
|----------|---------|--------|
| Font | Teko SemiBold | Teko SemiBold |
| Font Size | 1.125rem / 20px | 1rem / 16px |
| Line Height | 85% | 85% |
| Padding Top | 20px | 18px |
| Padding Bottom | 16px | 16px |
| Padding X | 24px | 24px |
| Border Radius | 8px | 8px |
| BG Default | `#632B3A` | `#632B3A` |
| BG Hover | `#89505F` | `#89505F` |
| Text Color | `#FFFFFF` | `#FFFFFF` |
