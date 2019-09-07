<?php
/**
 * Display Widgets conditionally.
 *
 * @package toolbelt
 */

if ( is_admin() ) {

	require 'module-admin.php';

} else {

	require 'module-frontend.php';

}


/**
 * Check if the widget should be displayed or not.
 *
 * @param string $logic Logic to check.
 * @return bool
 */
function toolbelt_widget_display( $logic ) {

	// Turn the logic properties into a list.
	$logic = trim( (string) $logic );

	// No logic rules to follow so quit.
	if ( empty( $logic ) ) {
		return true;
	}

	// Split the logic out into tokens that we can iterate over.
	$logic = str_replace( ' ', '', $logic );
	$logic = strtolower( $logic );
	$logic_tokens = explode( ';', $logic );

	foreach ( $logic_tokens as $token ) {

		if ( toolbelt_widget_display_check_token( $token ) ) {
			return true;
		}
	}

	/**
	 * Didn't match any tokens so return false, to hide the widget, by default.
	 */
	return false;

}


/**
 * Check if the widget with the specified token should be visible.
 *
 * @param string $token The token to check.
 * @return bool
 */
function toolbelt_widget_display_check_token( $token = '' ) {

	/**
	 * Key = the primary action.
	 * Properties = optional list of parameters that are related to the Key.
	 *
	 * Adds an extra : on the end of the token so that the $properties value
	 * does not create an error. If this means there's more than 2 properties
	 * the third will be ignored.
	 */
	list( $key, $properties ) = explode( ':', $token . ':' );
	if ( ! empty( $properties ) ) {
		$properties = explode( ',', $properties );
	}

	switch ( $key ) {

		case 'single':
			if ( empty( $properties ) && is_singular() ) {
				return true;
			}

			break;

		case 'archive':
			if ( empty( $properties ) && is_archive() ) {
				return true;
			}

			break;

		case 'home':
		case 'frontpage':
			if ( empty( $properties ) && is_front_page() ) {
				return true;
			}

			break;

		case 'posttype':
			if ( is_array( $properties ) ) {

				if ( in_array( get_post_type(), $properties, true ) ) {
					return true;
				}
			}

			break;

	}

	return false;

}


/**
 * Get the widget logic property for the specified id.
 *
 * @param int $widget_id The id to get the information for.
 * @return string
 */
function toolbelt_widget_display_by_id( $widget_id ) {

	// Set the default values.
	$widget_base = $widget_id;
	$widget_number = null;

	// If the id is in the form 'string-##' (eg string-number).
	if ( preg_match( '/^(.+)-(\d+)$/', $widget_id, $m ) ) {

		$widget_base = $m[1];
		$widget_number = $m[2];

	}

	// Load the option.
	$info = get_option( 'widget_' . $widget_base );

	/**
	 * If the number is the default then the option is stored in the old format
	 * so we need to grab the value in a different way.
	 */
	if ( null !== $widget_number ) {
		if ( ! empty( $info[ $widget_number ] ) ) {
			$info = $info[ $widget_number ];
		}
	}

	if ( isset( $info['toolbelt_widget_display'] ) ) {

		return $info['toolbelt_widget_display'];

	}

	return '';

}

