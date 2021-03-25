<?php

/**
 * The plugin bootstrap file
 *
 * The official Petje.af WordPress plugin to connect your WordPress website 
 * with your Petje.af page and create exclusive content on your own
 * website.
 *
 * @link              https://petje.af
 * @since             1.0.0
 * @package           Petje_Af
 *
 * @wordpress-plugin
 * Plugin Name:       Petje.af
 * Plugin URI:        https://docs.petje.af/wordpress-plugin/
 * Description:       The official Petje.af WordPress plugin.
 * Version:           2.1.8
 * Author:            Petje.af
 * Author URI:        https://petje.af/petjeaf
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       petje-af
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
  die;
}

/**
 * Currently plugin version.
 */
define( 'PETJE_AF_VERSION', '2.1.8' );

/**
 * Plugin base name
 */
define( 'PETJE_AF_PLUGIN_BASENAME',  basename( dirname( __FILE__ ) ) . '/' . basename( __FILE__ ) );


/**
 * Root url of the plugin
 */
define( 'PETJE_AF_PLUGIN_ROOT_URL', plugins_url('', PETJE_AF_PLUGIN_BASENAME));

/**
 * The main base url used in all functions.
 */
define( 'PETJE_AF_BASE_URL', 'https://petje.af/');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-petje-af-activator.php
 */
function activate_petje_af() {
  require_once plugin_dir_path( __FILE__ ) . 'includes/class-petje-af-activator.php';
  Petje_Af_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-petje-af-deactivator.php
 */
function deactivate_petje_af() {
  require_once plugin_dir_path( __FILE__ ) . 'includes/class-petje-af-deactivator.php';
  Petje_Af_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_petje_af' );
register_deactivation_hook( __FILE__, 'deactivate_petje_af' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-petje-af.php';

/**
 * Begins execution of the plugin.
 *
 * @since    1.0.0
 */
function run_petje_af() {

  $plugin = new Petje_Af();
  $plugin->run();

}
run_petje_af();
