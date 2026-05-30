=== Archive Blocks ===
Contributors: randomwire
Donate link: https://ko-fi.com/randomwire
Tags: gutenberg, blocks, archives, posts
Requires at least: 6.0
Tested up to: 7.0
Requires PHP: 7.4
Stable tag: 1.8.1
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Gutenberg blocks for displaying post archives in a simple format.

== Description ==

Archive Blocks provides custom Gutenberg blocks for displaying your content archives:

* **Monthly Archives** - Display posts grouped by month in a compact format
* **On This Day** - Show posts published on this day in previous years
* **Popular Terms** - Display your most-used categories or tags
* **Category Nav Buttons** - Display categories as filter buttons linking to archive pages
* **Popular Posts** - Display top posts by views using Jetpack Stats data

The plugin also provides a `/random` permalink that redirects to a random published post.

All blocks are lightweight, accessible, and follow WordPress coding standards.

== Installation ==

1. Upload the plugin files to `/wp-content/plugins/archive-blocks/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Add blocks via the block editor (search for "archive")

== Frequently Asked Questions ==

= Does this work with custom post types? =

Currently supports posts only. Custom post type support planned for future release.

= Can I customize the styling? =

Yes, blocks output semantic HTML with CSS classes for easy customization.

== Screenshots ==

1. Monthly Archives block in the editor
2. On This Day block displaying historical posts
3. Popular Terms block showing categories

== Changelog ==

= 1.8.1 =
* Added GitHub-based automatic updates via Plugin Update Checker

= 1.8.0 =
* Added `/random` permalink that 302-redirects to a random published post
* Rewrite rules flushed automatically on plugin activation/deactivation

= 1.7.0 =
* Added Popular Posts block powered by Jetpack Stats view data
* Configurable post count (1-100), time period, and popular vs random ordering
* Post ID exclusion setting for Popular Posts block

= 1.6.0 =
* Added Category Nav Buttons block for displaying categories as filter buttons
* Active category buttons use filled style, inactive use outline style
* Fixed deprecation warnings for WordPress 6.7+ compatibility

= 1.0.0 =
* Initial public release
* Monthly Archives block
* On This Day block
* Popular Terms block
* Translation ready

== Upgrade Notice ==

= 1.8.1 =
Plugin now updates itself directly from GitHub releases.

= 1.8.0 =
New /random permalink for random post discovery.

= 1.7.0 =
New Popular Posts block with Jetpack Stats integration.

= 1.6.0 =
New Category Nav Buttons block and WordPress 6.7+ compatibility fixes.

= 1.0.0 =
First stable release.
