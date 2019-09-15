<?php
/**
 * Random Redirect
 *
 * @package toolbelt
 */

/**
 * Randomly redirect to a blog post.
 *
 * If the post url has ?random on it.
 */
function toolbelt_random_redirect() {

	if ( ! isset( $_GET['random'] ) ) {
		return;
	}

	/**
	 * Ignore POST requests.
	 *
	 * We ignore the phpcs warning because it thinks we're processing form data.
	 * However we are simply checking if there is any form data, we're not
	 * actually doing anything with it. So we don't need to check for a nonce.
	 */
	if ( ! empty( $_POST ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
		return;
	}

	/**
	 * Persistent AppEngine abuse. ORDER BY RAND is expensive.
	 *
	 * We ignore the phpcs warning because we're not processing the server value.
	 * Similar to the _POST value problem above.
	 */
	if ( isset( $_SERVER['HTTP_USER_AGENT'] ) && strstr( (string) wp_unslash( $_SERVER['HTTP_USER_AGENT'] ), 'AppEngine-Google' ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		return;
	}

	$permalink = toolbelt_random_get_post();

	if ( ! $permalink ) {
		return;
	}

	wp_safe_redirect( $permalink );

	die();

}

add_action( 'template_redirect', 'toolbelt_random_redirect' );


/**
 * Get the id of a random post that we can redirect to.
 */
function toolbelt_random_get_post() {

	$post_count = 0;
	if ( wp_count_posts()->publish ) {
		$post_count = wp_count_posts()->publish;
	}

	$random_post = wp_rand( 1, $post_count );

	$the_post = new WP_Query(
		array(
			'post_type' => 'post',
			'paged' => $random_post,
			'posts_per_page' => 1,
		)
	);

	$permalink = false;

	if ( $the_post->have_posts() ) {
		while ( $the_post->have_posts() ) {
			$the_post->the_post();
			$permalink = get_permalink();
		}
	}

	wp_reset_postdata();

	return $permalink;

}
