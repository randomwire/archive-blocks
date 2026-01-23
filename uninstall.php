<?php
/**
 * Uninstall Archive Blocks
 *
 * @package ArchiveBlocks
 */

// Exit if accessed directly or not uninstalling.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Clear any cached data.
wp_cache_delete( 'archive_blocks_data' );
wp_cache_delete( 'archive_blocks_yearly_counts' );
