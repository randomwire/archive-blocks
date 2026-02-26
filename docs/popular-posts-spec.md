# Popular Posts Block — Implementation Plan

## Context

The plugin currently has 4 blocks. We want to add a 5th — "Popular Posts" — that pulls view data from Jetpack Stats via `stats_get_csv()` and displays a list of linked post titles. The user can configure how many posts to show (1–100), the time period, and whether the list is ordered by popularity or randomised.

## New Files to Create

### `src/popular-posts/block.json`
- Name: `archive-blocks/popular-posts`
- Icon: `chart-bar`
- Attributes:
  - `numberOfPosts` (number, default: 10)
  - `orderMode` (string, default: `"popular"`) — `"popular"` or `"random"`
  - `timePeriod` (string, default: `"30"`) — `"7"`, `"30"`, `"365"`, or `"0"` (all time)
- Supports: `html: false`, `align: ["wide", "full"]`
- Standard: `apiVersion: 3`, `category: "widgets"`, `textdomain: "archive-blocks"`

### `src/popular-posts/index.js`
- Standard boilerplate: import `registerBlockType`, metadata, and Edit component.

### `src/popular-posts/edit.js`
- `InspectorControls` with a single `PanelBody`:
  - `RangeControl` for number of posts (min 1, max 100)
  - `SelectControl` for order mode (Popular / Random)
  - `SelectControl` for time period (7 days / 30 days / 365 days / All time)
- `ServerSideRender` for live preview in the editor
- All controls use `__nextHasNoMarginBottom` and `__next40pxDefaultSize` props

### `src/popular-posts/render.php`
1. Extract attributes with defaults
2. Check `function_exists('stats_get_csv')` — if not, render a message: "This block requires Jetpack with the Stats module enabled."
3. Call `archive_blocks_get_popular_posts( $days )` to get cached top-100 list
4. If empty, render: "No popular posts data available yet."
5. If `orderMode === 'random'`, `shuffle()` the array (after cache retrieval, so each uncached page load gets a fresh shuffle)
6. `array_slice()` to `numberOfPosts`
7. Output as a `<div>` wrapper (using `get_block_wrapper_attributes()`) with each post as a `<div>` containing a linked title — matching the "On This Day" block's markup pattern

## Files to Modify

### `archive-blocks.php`
1. **Add registration** in `archive_blocks_init()`:
   ```php
   register_block_type( __DIR__ . '/build/popular-posts' );
   ```

2. **Add helper function** `archive_blocks_get_popular_posts( $days )`:
   - Cache key: `'archive_blocks_popular_posts_' . md5( (string) $days )`
   - On cache miss: call `stats_get_csv( 'postviews', "days={$days}&limit=200" )`
   - Fetch 200 from stats to have headroom after filtering
   - Filter each result: skip if `post_id` is 0, skip if post doesn't exist / isn't published / isn't `post_type === 'post'`
   - Keep first 100 valid results
   - Cache array of `[ post_id, title, permalink, views ]` for `HOUR_IN_SECONDS`
   - Cache empty results too (avoids hammering the Stats API)

### `src/index.js`
- Add `import './popular-posts';`

## Edge Cases

| Case | Handling |
|------|----------|
| Jetpack not installed/active | `function_exists('stats_get_csv')` check in render.php shows message |
| Stats module disabled / no data | Helper returns empty array; render.php shows empty state |
| Trashed/deleted posts in stats | Helper validates each post with `get_post()` — skips non-published |
| Pages/attachments in stats | Helper filters to `post_type === 'post'` only |
| Fewer results than requested | `array_slice()` returns what's available |
| `post_id` of 0 (homepage) | Skipped in helper loop |
| Random mode + page caching | Shuffle is server-side; accepted that page cache freezes selection until expiry |

## Verification

1. Run `npm run build` — confirm `build/popular-posts/` is generated with block.json, index.js, render.php
2. Activate the plugin in WordPress with Jetpack active and Stats enabled
3. Add the "Popular Posts" block to a page in the editor
4. Confirm the live preview renders via ServerSideRender
5. Test each inspector control: change number of posts, order mode, time period — confirm preview updates
6. Test random mode: reload the frontend page (with page cache cleared) and confirm order changes
7. Deactivate Jetpack — confirm the block shows "requires Jetpack" message
8. Test with a site that has no stats data — confirm empty state message
