<?php

/**
 * This file contains the functions related to enqueuing assets.
 *
 * @package quote-api
 * @since 1.0.0
 */

/**
 * Enqueues the `assets/quote-variation.js` file, which is used to create
 * the variation of core/quote that enables the user to fetch a random quote.
 *
 * @link  https://developer.wordpress.org/block-editor/reference-guides/block-api/block-variations/
 */
function qa__enqueue_block_variations_script()
{
	$script_asset = include PLUGIN_DIR . 'build/index.asset.php';
	wp_enqueue_script(
		'quote-variations-js',
		plugins_url('build/index.js', PLUGIN_FILE), 
		$script_asset['dependencies'],
		$script_asset['version'],
		true
	);
}
