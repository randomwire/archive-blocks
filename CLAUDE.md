# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Build Commands

```bash
npm run build    # Production build - compiles src/ to build/
npm run start    # Development mode with watch
```

## Architecture

This is a WordPress Gutenberg block plugin providing archive and discovery blocks. Each block lives in its own `src/<block-name>/` directory with `block.json`, `index.js`, `edit.js`, and `render.php`.

### Plugin Entry Point

- **archive-blocks.php** — Registers all blocks in `archive_blocks_init()` and provides shared helper functions with caching (`archive_blocks_get_data()`, `archive_blocks_get_yearly_counts()`, `archive_blocks_get_popular_terms()`, `archive_blocks_get_popular_posts()`).
- **src/index.js** — Imports all block modules for the editor build.

### Blocks

| Block | Slug | Description |
|-------|------|-------------|
| Monthly Archives | `archive-blocks/monthly-archives` | Compact year/month archive display (e.g., "2024: Jan Feb Mar...") |
| Popular Terms | `archive-blocks/popular-terms` | Weighted list of popular categories/tags |
| On This Day | `archive-blocks/on-this-day` | Posts published on today's date in previous years |
| Category Nav Buttons | `archive-blocks/category-nav-buttons` | Navigation buttons for category filtering |
| Popular Posts | `archive-blocks/popular-posts` | Top posts by views from Jetpack Stats (`stats_get_csv()`) |

### Common Patterns

- All blocks use `ServerSideRender` for live editor preview
- Inspector controls use `__nextHasNoMarginBottom` and `__next40pxDefaultSize` props
- Helper functions cache results via `wp_cache_set()` for `HOUR_IN_SECONDS`
- Each `render.php` uses `get_block_wrapper_attributes()` for the outer wrapper
