<?php

/**
 * Plugin Name:         Quote API
 * Description:         Get popular quotes from the Quotes API.
 * Version:             0.1.0
 * Requires at least:   6.6
 * Requires PHP:        7.4
 * Author:              JuanMa Garrido
 * Author URI:          https://juanma.codes/
 * License:             GPLv2
 * License URI:         https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * Text Domain:         quote-api
 * Domain Path:         /languages
 *
 * @package quote-api
 */

defined('ABSPATH') || exit;

// Setup.
if (!defined('PLUGIN_DIR')) {
	define('PLUGIN_DIR', plugin_dir_path(__FILE__));
}
if (!defined('PLUGIN_FILE')) {
	define('PLUGIN_FILE', __FILE__);
}

require_once PLUGIN_DIR . '/includes/enqueue-assets.php';
require_once PLUGIN_DIR . '/includes/register-source-block-binding.php';

add_action('enqueue_block_editor_assets', 'qa__enqueue_block_variations_script');
add_action('init', 'qa__register_block_bindings_source');
