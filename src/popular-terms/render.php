<?php
/**
 * Server-side rendering of the Popular Terms block.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block default content.
 * @param WP_Block $block      Block instance.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$taxonomy  = isset( $attributes['taxonomy'] ) ? $attributes['taxonomy'] : 'category';
$count     = isset( $attributes['count'] ) ? (int) $attributes['count'] : 10;
$order_by  = isset( $attributes['orderBy'] ) ? $attributes['orderBy'] : 'popular';
$exclude   = isset( $attributes['exclude'] ) ? $attributes['exclude'] : '';
$separator = isset( $attributes['separator'] ) ? $attributes['separator'] : ' / ';

$args = array(
    'taxonomy' => $taxonomy,
    'count'    => $count,
    'orderBy'  => $order_by,
    'exclude'  => $exclude,
);

$terms = archive_blocks_get_popular_terms( $args );

if ( empty( $terms ) ) {
    return;
}

$term_links = array();
foreach ( $terms as $term ) {
    $term_links[] = '<a href="' . esc_url( $term['link'] ) . '">' . esc_html( $term['name'] ) . '</a>';
}

$output = '<div ' . get_block_wrapper_attributes() . '>';
$output .= implode( esc_html( $separator ), $term_links );
$output .= '</div>';

echo $output;
