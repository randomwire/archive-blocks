# Block Specification: Popular Tags/Categories

## Overview
Add an additional Gutenberg block to the plugin that displays a single-line list of popular tags or categories based on a weighted popularity score combining post count and comment count.

## Block Information
- **Block Name**: `popular-tags-categories` (or similar namespace)
- **Block Title**: "Popular Tags/Categories"
- **Icon**: tag or category icon

## Functional Requirements

### 1. Popularity Calculation
- **Formula**: `popularity_score = (post_count × 0.7) + (comment_count × 0.3)`
- **Post Count**: Number of posts assigned to the tag/category
- **Comment Count**: Total number of comments across all posts in that tag/category
- **Calculation**: Performed when block is rendered or from cache

### 2. Block Settings

#### Type Selection
- **Control**: Select dropdown
- **Options**: 
  - "Tags"
  - "Categories"
- **Default**: "Categories"

#### Number of Items
- **Control**: Select dropdown
- **Options**: 5, 10, 25, 50, 100
- **Default**: 10

#### Order By
- **Control**: Select dropdown
- **Options**:
  - "Most Popular" (sort by popularity_score DESC)
  - "Alphabetical" (sort by name ASC)
- **Default**: "Most Popular"

#### Exclude Items
- **Control**: Text input field
- **Format**: Comma-separated list of tag/category names (exact match, case-insensitive)
- **Example**: "Uncategorized, General, Test"
- **Default**: Empty string

#### Separator
- **Control**: Text input field
- **Default**: " / " (space-slash-space)
- **Allow**: Any string input

### 3. Frontend Output

#### Format
```
Tag1 / Tag2 / Tag3 / Tag4 / Tag5
```

#### HTML Structure
- Minimal markup (e.g., `<div class="wp-block-popular-tags-categories">`)
- Each item as an anchor tag: `<a href="{archive_url}">{name}</a>`
- Separators as plain text between links
- No list elements (`<ul>`, `<li>`)
- No heading/title element

#### Link Behavior
- Standard WordPress archive links (tag or category archive)
- Open in same tab (no `target="_blank"`)
- No special attributes (no `rel="nofollow"`)

#### Styling
- **No custom CSS** - inherit all styling from theme
- Use semantic class names for block wrapper if needed for targeting

### 4. Edge Cases

#### No Results Found
- If no tags/categories exist after filtering: **output nothing** (empty/null)
- If fewer items than requested exist: display only available items
- If all items are excluded: output nothing

#### Empty Settings
- If separator is empty: concatenate items with no separator
- If exclude field is empty: don't exclude anything

### 5. Performance (Optional Enhancement)

#### Caching Strategy
- **If simple to implement**: Use WordPress transients API
- **Cache Key**: Based on block settings (type, count, order, exclude list)
- **Cache Duration**: 1 hour (3600 seconds) or similar reasonable duration
- **Cache Invalidation**: Optional - on post publish/update/delete
- **Fallback**: If caching is complex, calculate on every render

## Technical Implementation Notes

### WordPress APIs to Use
- `get_tags()` or `get_categories()` for retrieving taxonomies
- `get_term_link()` for archive URLs
- Query posts with `WP_Query` to get comment counts per term
- `get_transient()` / `set_transient()` for caching

### Block Registration
- Use `register_block_type()` with PHP render callback
- Define block.json with all attributes
- Server-side rendering (not dynamic React component)

## Testing Checklist
- [ ] Block appears in block inserter
- [ ] Switching between Tags/Categories works
- [ ] All count options display correct number
- [ ] Most Popular ordering shows highest scores first
- [ ] Alphabetical ordering works correctly
- [ ] Exclude list filters items (case-insensitive)
- [ ] Custom separator displays correctly
- [ ] Empty separator works (no gaps)
- [ ] Links point to correct archive pages
- [ ] No output when no results found
- [ ] Inherits theme link styling
- [ ] Caching works (if implemented)

## Success Criteria
- Block installs and activates without errors
- All settings are functional and persist correctly
- Frontend output matches specification
- Performance is acceptable on sites with many posts/tags/categories