<?php
/**
 * Plugin Name:       Archive Blocks
 * Plugin URI:        https://github.com/randomwire/archive-blocks
 * Description:       Gutenberg blocks for displaying post archives in a simple format.
 * Version:           1.8.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            David Gilbert
 * Author URI:        https://randomwire.com
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       archive-blocks
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require_once __DIR__ . '/includes/updater.php';
randomwire_init_github_updater( __FILE__ );

function archive_blocks_init() {
    register_block_type( __DIR__ . '/build/monthly-archives' );
    register_block_type( __DIR__ . '/build/popular-terms' );
    register_block_type( __DIR__ . '/build/on-this-day' );
    register_block_type( __DIR__ . '/build/category-nav-buttons' );
    register_block_type( __DIR__ . '/build/popular-posts' );
}
add_action( 'init', 'archive_blocks_init' );

/**
 * Append Donate + GitHub links to this plugin's row on the Plugins page.
 */
function archive_blocks_plugin_row_meta( $links, $file ) {
    if ( plugin_basename( __FILE__ ) === $file ) {
        $links[] = '<a href="https://ko-fi.com/randomwire" target="_blank" rel="noopener noreferrer">'
            . esc_html__( 'Donate', 'archive-blocks' )
            . '</a>';
    }
    return $links;
}
add_filter( 'plugin_row_meta', 'archive_blocks_plugin_row_meta', 10, 2 );

/**
 * Register rewrite rule for the /random permalink.
 */
function archive_blocks_random_post_rewrite() {
    add_rewrite_rule( '^random/?$', 'index.php?archive_blocks_random=1', 'top' );
}
add_action( 'init', 'archive_blocks_random_post_rewrite' );

/**
 * Register the custom query var so WordPress doesn't strip it.
 */
function archive_blocks_random_query_vars( $vars ) {
    $vars[] = 'archive_blocks_random';
    return $vars;
}
add_filter( 'query_vars', 'archive_blocks_random_query_vars' );

/**
 * Handle the /random redirect — pick a random published post and 302 to it.
 */
function archive_blocks_random_post_redirect() {
    if ( ! get_query_var( 'archive_blocks_random' ) ) {
        return;
    }

    $posts = get_posts( array(
        'post_type'      => 'post',
        'post_status'    => 'publish',
        'posts_per_page' => 1,
        'orderby'        => 'rand',
    ) );

    if ( ! empty( $posts ) ) {
        wp_redirect( get_permalink( $posts[0] ), 302 );
        exit;
    }
}
add_action( 'template_redirect', 'archive_blocks_random_post_redirect' );

/**
 * Flush rewrite rules on plugin activation.
 */
function archive_blocks_activate() {
    archive_blocks_random_post_rewrite();
    flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'archive_blocks_activate' );

/**
 * Flush rewrite rules on plugin deactivation.
 */
function archive_blocks_deactivate() {
    flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'archive_blocks_deactivate' );

/**
 * Get archive data with caching.
 *
 * @return array Array of year => months with posts
 */
function archive_blocks_get_data() {
    $cache_key = 'archive_blocks_data';
    $data = wp_cache_get( $cache_key );

    if ( false === $data ) {
        global $wpdb;

        $results = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT DISTINCT YEAR(post_date) AS year, MONTH(post_date) AS month
                 FROM {$wpdb->posts}
                 WHERE post_type = %s
                 AND post_status = %s
                 ORDER BY year DESC, month ASC",
                'post',
                'publish'
            ),
            ARRAY_A
        );

        $data = array();
        foreach ( $results as $row ) {
            $year = (int) $row['year'];
            $month = (int) $row['month'];
            if ( ! isset( $data[ $year ] ) ) {
                $data[ $year ] = array();
            }
            $data[ $year ][] = $month;
        }

        wp_cache_set( $cache_key, $data, '', HOUR_IN_SECONDS );
    }

    return $data;
}

/**
 * Get yearly post counts with caching.
 *
 * @return array Array of year => post count
 */
function archive_blocks_get_yearly_counts() {
    $cache_key = 'archive_blocks_yearly_counts';
    $data = wp_cache_get( $cache_key );

    if ( false === $data ) {
        global $wpdb;

        $results = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT YEAR(post_date) AS year, COUNT(*) AS count
                 FROM {$wpdb->posts}
                 WHERE post_type = %s
                 AND post_status = %s
                 GROUP BY year
                 ORDER BY year DESC",
                'post',
                'publish'
            ),
            ARRAY_A
        );

        $data = array();
        foreach ( $results as $row ) {
            $year = (int) $row['year'];
            $count = (int) $row['count'];
            $data[ $year ] = $count;
        }

        wp_cache_set( $cache_key, $data, '', HOUR_IN_SECONDS );
    }

    return $data;
}

/**
 * Get popular terms with weighted popularity score.
 *
 * @param array $args {
 *     Arguments for retrieving popular terms.
 *
 *     @type string $taxonomy Taxonomy to retrieve terms from. Default 'category'.
 *     @type int    $count    Number of terms to return. Default 10.
 *     @type string $orderBy  Order by 'popular' or 'name'. Default 'popular'.
 *     @type string $exclude  Comma-separated list of term names to exclude.
 * }
 * @return array Array of term data with name, link, and score.
 */
function archive_blocks_get_popular_terms( $args ) {
    $defaults = array(
        'taxonomy' => 'category',
        'count'    => 10,
        'orderBy'  => 'popular',
        'exclude'  => '',
    );
    $args = wp_parse_args( $args, $defaults );

    $cache_key = 'archive_blocks_popular_' . md5( serialize( $args ) );
    $data = wp_cache_get( $cache_key );

    if ( false === $data ) {
        global $wpdb;

        // Get all terms for the taxonomy
        $terms = get_terms( array(
            'taxonomy'   => $args['taxonomy'],
            'hide_empty' => true,
        ) );

        if ( is_wp_error( $terms ) || empty( $terms ) ) {
            return array();
        }

        // Get comment counts per term
        $term_ids = wp_list_pluck( $terms, 'term_id' );
        $term_ids_placeholder = implode( ',', array_map( 'intval', $term_ids ) );

        $comment_counts = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT tt.term_id, COUNT(c.comment_ID) as comment_count
                FROM {$wpdb->term_taxonomy} tt
                JOIN {$wpdb->term_relationships} tr ON tt.term_taxonomy_id = tr.term_taxonomy_id
                JOIN {$wpdb->comments} c ON tr.object_id = c.comment_post_ID
                WHERE tt.taxonomy = %s AND c.comment_approved = '1' AND tt.term_id IN ({$term_ids_placeholder})
                GROUP BY tt.term_id",
                $args['taxonomy']
            ),
            OBJECT_K
        );

        // Build exclude list (case-insensitive)
        $exclude_list = array();
        if ( ! empty( $args['exclude'] ) ) {
            $exclude_list = array_map( 'trim', explode( ',', $args['exclude'] ) );
            $exclude_list = array_map( 'strtolower', $exclude_list );
        }

        // Calculate popularity scores
        $term_data = array();
        foreach ( $terms as $term ) {
            // Skip excluded terms
            if ( in_array( strtolower( $term->name ), $exclude_list, true ) ) {
                continue;
            }

            $post_count = (int) $term->count;
            $comment_count = isset( $comment_counts[ $term->term_id ] ) ? (int) $comment_counts[ $term->term_id ]->comment_count : 0;
            $popularity_score = ( $post_count * 0.7 ) + ( $comment_count * 0.3 );

            $term_data[] = array(
                'name'  => $term->name,
                'link'  => get_term_link( $term ),
                'score' => $popularity_score,
            );
        }

        // Sort by orderBy
        if ( 'popular' === $args['orderBy'] ) {
            usort( $term_data, function( $a, $b ) {
                return $b['score'] <=> $a['score'];
            } );
        } else {
            usort( $term_data, function( $a, $b ) {
                return strcasecmp( $a['name'], $b['name'] );
            } );
        }

        // Limit to count
        $data = array_slice( $term_data, 0, $args['count'] );

        wp_cache_set( $cache_key, $data, '', HOUR_IN_SECONDS );
    }

    return $data;
}

/**
 * Get popular posts from Jetpack Stats with caching.
 *
 * @param int $days Number of days to look back (0 for all time).
 * @return array Array of post data with post_id, title, permalink, and views.
 */
function archive_blocks_get_popular_posts( $days ) {
    $cache_key = 'archive_blocks_popular_posts_' . md5( (string) $days );
    $data = wp_cache_get( $cache_key );

    if ( false !== $data ) {
        return $data;
    }

    if ( ! function_exists( 'stats_get_csv' ) ) {
        return array();
    }

    $stats = stats_get_csv( 'postviews', "days={$days}&limit=200" );

    if ( empty( $stats ) || ! is_array( $stats ) ) {
        $data = array();
        wp_cache_set( $cache_key, $data, '', HOUR_IN_SECONDS );
        return $data;
    }

    $data = array();
    foreach ( $stats as $row ) {
        if ( count( $data ) >= 100 ) {
            break;
        }

        $post_id = isset( $row['post_id'] ) ? (int) $row['post_id'] : 0;

        if ( 0 === $post_id ) {
            continue;
        }

        $post = get_post( $post_id );

        if ( ! $post || 'publish' !== $post->post_status || 'post' !== $post->post_type ) {
            continue;
        }

        $data[] = array(
            'post_id'   => $post_id,
            'title'     => get_the_title( $post ),
            'permalink' => get_permalink( $post ),
            'views'     => isset( $row['views'] ) ? (int) $row['views'] : 0,
        );
    }

    wp_cache_set( $cache_key, $data, '', HOUR_IN_SECONDS );

    return $data;
}
