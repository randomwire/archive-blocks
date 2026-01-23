<?php
/**
 * Server-side rendering of the Monthly Archives block.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block default content.
 * @param WP_Block $block      Block instance.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$style = isset( $attributes['style'] ) ? $attributes['style'] : 'abbreviation';
$divider = isset( $attributes['divider'] ) ? $attributes['divider'] : '/';
$show_post_count = isset( $attributes['showPostCount'] ) ? $attributes['showPostCount'] : false;
$archive_data = archive_blocks_get_data();
$yearly_counts = $show_post_count ? archive_blocks_get_yearly_counts() : array();

if ( empty( $archive_data ) ) {
    echo '<p>' . esc_html__( 'No posts found.', 'archive-blocks' ) . '</p>';
    return;
}

$output = '<div ' . get_block_wrapper_attributes() . '>';
$output .= '<style>.compact-archive-row { margin-bottom: 1em; } .compact-archive-year { font-variant-numeric: tabular-nums; display: block; } .compact-archive-months { display: block; }</style>';

foreach ( $archive_data as $year => $months_with_posts ) {
    $year_url = get_year_link( $year );
    $output .= '<div class="compact-archive-row">';
    $year_output = '<a href="' . esc_url( $year_url ) . '">' . esc_html( $year ) . '</a>';
    if ( $show_post_count && isset( $yearly_counts[ $year ] ) ) {
        $year_output .= ' (' . esc_html( $yearly_counts[ $year ] ) . ')';
    }
    $output .= '<strong class="compact-archive-year">' . $year_output . '</strong>';

    $month_links = array();

    foreach ( $months_with_posts as $month ) {
        // Create a timestamp for the month to use with date_i18n
        $timestamp = mktime( 0, 0, 0, $month, 1, $year );

        // Get month label based on style
        switch ( $style ) {
            case 'initial':
                // Get full month name and take first character
                $full_month = date_i18n( 'F', $timestamp );
                $month_label = mb_substr( $full_month, 0, 1 );
                break;
            case 'numeric':
                $month_label = date_i18n( 'm', $timestamp );
                break;
            case 'abbreviation':
            default:
                $month_label = date_i18n( 'M', $timestamp );
                break;
        }

        $month_url = get_month_link( $year, $month );
        $month_links[] = '<a href="' . esc_url( $month_url ) . '">' . esc_html( $month_label ) . '</a>';
    }

    $output .= '<span class="compact-archive-months">' . implode( ' ' . esc_html( $divider ) . ' ', $month_links ) . '</span>';
    $output .= '</div>';
}

$output .= '</div>';

echo $output;
