<?php
/**
 * Server-side rendering of the Category Filter block.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block default content.
 * @param WP_Block $block      Block instance.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$filter_mode         = isset( $attributes['filterMode'] ) ? $attributes['filterMode'] : 'all';
$included_categories = isset( $attributes['includedCategories'] ) ? array_map( 'intval', $attributes['includedCategories'] ) : array();
$excluded_categories = isset( $attributes['excludedCategories'] ) ? array_map( 'intval', $attributes['excludedCategories'] ) : array();
$show_all            = isset( $attributes['showAll'] ) ? $attributes['showAll'] : true;
$all_button_url      = isset( $attributes['allButtonUrl'] ) ? $attributes['allButtonUrl'] : '';
$all_button_label    = isset( $attributes['allButtonLabel'] ) ? $attributes['allButtonLabel'] : __( 'All', 'archive-blocks' );

// Get all categories
$categories = get_categories( array(
    'hide_empty' => true,
    'orderby'    => 'name',
    'order'      => 'ASC',
) );

if ( empty( $categories ) && ! $show_all ) {
    return;
}

// Filter categories based on mode
$filtered_categories = array();
foreach ( $categories as $category ) {
    if ( 'include' === $filter_mode ) {
        if ( in_array( $category->term_id, $included_categories, true ) ) {
            $filtered_categories[] = $category;
        }
    } elseif ( 'exclude' === $filter_mode ) {
        if ( ! in_array( $category->term_id, $excluded_categories, true ) ) {
            $filtered_categories[] = $category;
        }
    } else {
        // 'all' mode - include everything
        $filtered_categories[] = $category;
    }
}

if ( empty( $filtered_categories ) && ! $show_all ) {
    return;
}

// Determine if we're on the homepage
$is_home = is_home() || is_front_page();

// Determine the "All" button URL
$all_url = ! empty( $all_button_url ) ? $all_button_url : home_url( '/' );

// Helper function to render a button using core/button block markup
// Active buttons use fill style, inactive use outline style
$render_button = function( $url, $text, $is_active = false ) {
    $style = $is_active ? '' : 'is-style-outline';

    $attrs = array();
    if ( $style ) {
        $attrs['className'] = $style;
    }
    $attrs_json = ! empty( $attrs ) ? ' ' . wp_json_encode( $attrs ) : '';

    $div_class = 'wp-block-button' . ( $style ? ' ' . esc_attr( $style ) : '' );
    $link_class = 'wp-block-button__link wp-element-button';

    $block_markup = sprintf(
        '<!-- wp:button%s --><div class="%s"><a class="%s" href="%s">%s</a></div><!-- /wp:button -->',
        $attrs_json,
        $div_class,
        $link_class,
        esc_url( $url ),
        esc_html( $text )
    );

    return do_blocks( $block_markup );
};

// Build output
$output = '<div ' . get_block_wrapper_attributes() . '>';

// "All" button
if ( $show_all ) {
    $output .= $render_button( $all_url, $all_button_label, $is_home );
}

// Category buttons
foreach ( $filtered_categories as $category ) {
    $is_active = is_category( $category->term_id );
    $cat_url   = get_category_link( $category->term_id );

    $output .= $render_button( $cat_url, $category->name, $is_active );
}

$output .= '</div>';

echo $output;
