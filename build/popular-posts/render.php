<?php
/**
 * Server-side rendering of the Popular Posts block.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block default content.
 * @param WP_Block $block      Block instance.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$number_of_posts = isset( $attributes['numberOfPosts'] ) ? (int) $attributes['numberOfPosts'] : 10;
$order_mode      = isset( $attributes['orderMode'] ) ? $attributes['orderMode'] : 'popular';
$time_period     = isset( $attributes['timePeriod'] ) ? (int) $attributes['timePeriod'] : 30;
$exclude         = isset( $attributes['exclude'] ) ? $attributes['exclude'] : '';

// Check for Jetpack Stats
if ( ! function_exists( 'stats_get_csv' ) ) {
    echo '<div ' . get_block_wrapper_attributes() . '>'
        . esc_html__( 'This block requires Jetpack with the Stats module enabled.', 'archive-blocks' )
        . '</div>';
    return;
}

$posts = archive_blocks_get_popular_posts( $time_period );

// No data available
if ( empty( $posts ) ) {
    echo '<div ' . get_block_wrapper_attributes() . '>'
        . esc_html__( 'No popular posts data available yet.', 'archive-blocks' )
        . '</div>';
    return;
}

// Filter out excluded post IDs
if ( ! empty( $exclude ) ) {
    $exclude_ids = array_map( 'intval', array_map( 'trim', explode( ',', $exclude ) ) );
    $posts = array_values( array_filter( $posts, function( $post ) use ( $exclude_ids ) {
        return ! in_array( $post['post_id'], $exclude_ids, true );
    } ) );
}

// Shuffle for random mode
if ( 'random' === $order_mode ) {
    shuffle( $posts );
}

// Limit to requested number
$posts = array_slice( $posts, 0, $number_of_posts );

// Generate output
$output = '<div ' . get_block_wrapper_attributes() . '>';

foreach ( $posts as $post ) {
    $output .= '<div><a href="' . esc_url( $post['permalink'] ) . '">' . esc_html( $post['title'] ) . '</a></div>';
}

$output .= '</div>';

echo $output;
