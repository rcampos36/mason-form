# Design system

Tailwind CSS 4.3.4 theme for this Sage project. Tokens live in `resources/css/theme.css` (`@theme`). There is no `tailwind.config.js`.

Bricolage Grotesque is loaded from the SIL Open Font License package `@fontsource/bricolage-grotesque`, weights 200–800, with `font-display: swap`. The family stops at 800, so display type uses 800 rather than 900.

## Brand colors

| Token | Value |
| --- | --- |
| `--color-stone-100` | `#EDEBE6` |
| `--color-stone-300` | `#D6D3CC` |
| `--color-concrete-600` | `#67645E` |
| `--color-concrete-400` | `#9C988F` |
| `--color-ink-900` | `#141414` |
| `--color-ink-700` | `#33322F` |
| `--color-blue-500` | `#4E69C9` |
| `--color-blue-700` | `#273C8B` |
| `--color-blue-300` | `#8FA2E6` |
| `--color-white` | `#FFFFFF` |

## Semantic colors

Default: background Stone 100, text Ink 900, muted Concrete 600, border Stone 300, accent Blue 500, accent hover Blue 700, text on accent White.

Inverse: background Ink 900, text Stone 100, muted Concrete 400, border Ink 700, accent Blue 300. The `inverse` tone in `App\Design` uses these utilities.

Site Settings → Accent color writes a sanitized hex to `--color-accent` on the document. The color picker default is `#4E69C9`.

## Type and space

Desktop sizes, with `clamp()` down to smaller screens: display 9rem, h1 5.5rem / 800, h2 4rem / 700, h3 3rem / 600, h4 2.25rem / 500, h5 1.75rem / 500, h6 1.375rem / 500, body large 1.25rem, paragraph 1.125rem, small 1rem, caption 0.875rem.

`<x-heading>` and `<x-type>` render those sizes. `<x-container>` sets the 42rem text width, the page width, or the 100rem site width. Section padding reaches 9rem. The desktop gutter is 2.5rem (`px-gutter`), stepping down on smaller screens. Radius is 0. Borders are 1px.

Spacing steps: 0.25rem, 0.5rem, 0.75rem, 1rem, 1.5rem, 2.5rem, 4rem, 6rem, 9rem.
