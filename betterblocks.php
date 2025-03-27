<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://thriveweb.com.au
 * @since             1.0.0
 * @package           BetterBlocks
 *
 * @wordpress-plugin
 * Plugin Name:       BetterBlocks
 * Plugin URI:        https://thriveweb.com.au/the-lab/betterblocks/
 * Description:       Handy improvements for the Wordpress block editor interface such as post type support, hiding blocks, adjustable sidebar, and more.
 * Version:           1.0.0
 * Author:            Thrive Digital
 * Author URI:        https://thriveweb.com.au/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       betterblocks-main
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Current plugin version.
 */
define( 'BETTERBLOCKS_VERSION', '1.0.0' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-betterblocks.php';

/**
 * Implement the activation and deactivation functions.
 */
register_activation_hook(__FILE__, array('BetterBlocks', 'activate'));

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function betterblocks_run() {

	$plugin = new BetterBlocks();
	$plugin->run();

}
betterblocks_run();

