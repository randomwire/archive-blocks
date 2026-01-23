# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Build Commands

```bash
npm run build    # Production build - compiles src/ to build/
npm run start    # Development mode with watch
```

## Architecture

This is a WordPress Gutenberg block plugin that displays monthly post archives in a compact format (e.g., "2024: Jan Feb Mar...").

### Key Files

- **archive-blocks.php** - Main plugin file. Registers the block and provides `archive_blocks_get_data()` which queries posts and caches archive data (year → months mapping) for 1 hour.
- **src/block.json** - Block metadata defining `archive-blocks/compact-archive` with two attributes: `style` (abbreviation/initial/numeric) and `divider` (separator character).
- **src/edit.js** - Editor component using `ServerSideRender` for live preview. Provides inspector controls for style and divider settings.
- **src/render.php** - Server-side rendering. Iterates through years/months, linking months with posts and showing empty months as spans.

### Block Attributes

| Attribute | Type | Default | Values |
|-----------|------|---------|--------|
| style | string | "abbreviation" | abbreviation, initial, numeric |
| divider | string | "/" | any string |

### Rendering Flow

1. `archive_blocks_get_data()` queries `wp_posts` for distinct year/month combinations with published posts
2. Results are cached using `wp_cache_set()` for 1 hour
3. `render.php` generates HTML with linked months (posts exist) or spans (no posts)
