<?php
/**
 * Server-side rendering of the On This Day block.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block default content.
 * @param WP_Block $block      Block instance.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$max_posts     = isset( $attributes['maxPosts'] ) ? (int) $attributes['maxPosts'] : 1;
$empty_message = isset( $attributes['emptyMessage'] ) ? $attributes['emptyMessage'] : 'No posts were published on this day in the past.';

// Get current day and month
$current_day   = (int) current_time( 'j' );
$current_month = (int) current_time( 'n' );
$current_year  = (int) current_time( 'Y' );

// Query all posts published on this day/month in previous years
global $wpdb;

$posts = $wpdb->get_results(
    $wpdb->prepare(
        "SELECT ID, post_title, YEAR(post_date) AS year
         FROM {$wpdb->posts}
         WHERE post_type = 'post'
         AND post_status = 'publish'
         AND DAY(post_date) = %d
         AND MONTH(post_date) = %d
         AND YEAR(post_date) < %d
         ORDER BY post_date ASC",
        $current_day,
        $current_month,
        $current_year
    ),
    ARRAY_A
);

// No posts found - display empty message
if ( empty( $posts ) ) {
    if ( ! empty( $empty_message ) ) {
        echo '<div ' . get_block_wrapper_attributes() . '>' . esc_html( $empty_message ) . '</div>';
    }
    return;
}

// Randomly select up to max posts from the pool
if ( count( $posts ) > $max_posts ) {
    $random_keys = array_rand( $posts, $max_posts );
    if ( ! is_array( $random_keys ) ) {
        $random_keys = array( $random_keys );
    }
    $selected_posts = array();
    foreach ( $random_keys as $key ) {
        $selected_posts[] = $posts[ $key ];
    }
    $posts = $selected_posts;
}

// Sort by year (oldest to newest)
usort( $posts, function( $a, $b ) {
    return (int) $a['year'] - (int) $b['year'];
} );

// Generate output
$output = '<div ' . get_block_wrapper_attributes() . '>';

foreach ( $posts as $post ) {
    $permalink = get_permalink( $post['ID'] );
    $year      = esc_html( $post['year'] );
    $title     = esc_html( $post['post_title'] );
    $output   .= '<div>' . $year . ': <a href="' . esc_url( $permalink ) . '">' . $title . '</a></div>';
}

$output .= '</div>';

echo $output;
