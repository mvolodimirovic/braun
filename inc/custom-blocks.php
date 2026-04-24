<?php

/**
 * Register Blocks
 */
function register_acf_blocks() {
	$acf_blocks = glob( get_template_directory() . '/build/blocks/*' );

	foreach ( $acf_blocks as $block ) {
		register_block_type( $block );
	}
}
add_action( 'acf/init', 'register_acf_blocks' );

/**
 * Returns arrays of Block defaults.
 *
 * @param array  $abs_args Array of arguments from the print_block() function.
 * @param array  $abs_defaults Array of defaults from the block.
 * @param object $abs_block Object containing the block's values.
 */
function setup_block_defaults( $abs_args, $abs_defaults = array(), $abs_block = null ) {
	// Parse the $abs_args if we're rendering this with print_block() from a theme.
	if ( ! empty( $abs_args ) ) :
		$abs_defaults = get_formatted_args( $abs_args, $abs_defaults );
	endif;

	// Get custom classes for the block and/or for block colors.
	$abs_block_classes = isset( $abs_block ) ? get_block_classes( $abs_block ) : array();

	if ( ! empty( $abs_block_classes ) ) :
		$abs_defaults['class'] = array_merge( $abs_defaults['class'], $abs_block_classes );
	endif;

	// Set up element attributes.
	$abs_atts = get_formatted_atts( array( 'class', 'id' ), $abs_defaults );

	return array( $abs_defaults, $abs_atts );
}

/**
 * Returns a string of formatted element attributes.
 *
 * @param array $atts Array of elements to format from the args.
 * @param array $args Args for the element.
 */
function get_formatted_atts( $atts, $args ) {
	$atts_formatted = array();

	foreach ( $atts as $att ) :
		if ( array_key_exists( $att, $args ) && $args[ $att ] ) :

			// Handle aria attributes.
			if ( 'aria' === $att && is_array( $args['aria'] ) ) :

				foreach ( $args['aria'] as $key => $val ) :
					if ( $val ) :
						$atts_formatted[] = 'aria-' . $key . '="' . esc_attr( $val ) . '"';
					endif;
				endforeach;

			else :

				// Handle multiple classes.
				if ( 'class' === $att && is_array( $args['class'] ) ) :
					$args['class'] = implode( ' ', $args['class'] );
				endif;

				$atts_formatted[] = $att . '="' . esc_attr( $args[ $att ] ) . '"';

			endif;

		endif;
	endforeach;

	return implode( ' ', $atts_formatted );
}

/**
 * Merges the element defaults with the passed args.
 *
 * @param array $args Array of settings passed to the element.
 * @param array $defaults Array of default settings for the element.
 */
function get_formatted_args( $args = array(), $defaults = array() ) {

	// Set the 'class' array key if it doesn't exist.
	$args['class'] = array_key_exists( 'class', $args ) ? $args['class'] : array();

	// Allow class to be passed as a string or an array.
	$args['class'] = ( $args['class'] && ! is_array( $args['class'] ) ) ? explode( ' ', $args['class'] ) : $args['class'];

	// Merge with defaults.
	$classes = is_array( $args['class'] ) ? array_merge( $defaults['class'], $args['class'] ) : $defaults['class'];

	$formatted_args = wp_parse_args( $args, $defaults );

	$formatted_args['class'] = $classes;

	return $formatted_args;
}

/**
 * Returns an updated array of classes.
 *
 * @param array $block Array of block attributes.
 *
 * return array The updated array of classes.
 */
function get_block_classes( $block ) {
	$abs_block_classes = array();

	if ( ! empty( $block['className'] ) ) :
		$abs_block_classes[] = $block['className'];
	endif;

	if ( ! empty( $block['backgroundColor'] ) ) {
		$abs_block_classes[] = 'has-background';
		$abs_block_classes[] = 'has-' . $block['backgroundColor'] . '-background-color';
	}

	if ( ! empty( $block['textColor'] ) ) {
		$abs_block_classes[] = 'has-text-color';
		$abs_block_classes[] = 'has-' . $block['textColor'] . '-color';
	}

	return $abs_block_classes;
}

/**
 * Returns an array of ACF fields.
 *
 * @param array $fields Array of field names ie: [ 'layout', 'eyebrow_heading', 'content', 'heading' ].
 * @param int   $block_id (optional) ID of the post or of the block ($block[id]).
 */
function get_acf_fields( $fields = array(), $block_id = false ) {

	if ( ! function_exists( 'get_field' ) ) :
		return;
	endif;

	$block_id      = $block_id ? $block_id : get_the_ID();
	$return_fields = array();
	foreach ( $fields as $field ) :
		$value                   = get_field( $field, $block_id );
		$return_fields[ $field ] = $value;
	endforeach;

	return $return_fields;
}