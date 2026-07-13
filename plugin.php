<?php
/**
 * Plugin Name:       International Phone Field for Elementor
 * Description:       Adds a completely new International Phone field to Elementor Pro Forms with intl-tel-input integration.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      8.0
 * Author:            R A Milon
 * Text Domain:       elementor-intl-phone
 * Domain Path:       /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Define plugin constants.
define( 'EIP_VERSION', '1.0.0' );
define( 'EIP_PLUGIN_FILE', __FILE__ );
define( 'EIP_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'EIP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * Autoloader for the ElementorIntlPhone namespace.
 *
 * @param string $class The fully-qualified class name.
 */
spl_autoload_register( function ( $class ) {
	$prefix = 'ElementorIntlPhone\\';
	$base_dir = EIP_PLUGIN_DIR . 'includes/';

	$len = strlen( $prefix );
	if ( strncmp( $prefix, $class, $len ) !== 0 ) {
		return; // no, move to the next registered autoloader
	}

	$relative_class = substr( $class, $len );
	$file = $base_dir . str_replace( '\\', '/', $relative_class ) . '.php';

	if ( file_exists( $file ) ) {
		require $file;
	}
} );

/**
 * Initialize the plugin.
 */
function eip_init_plugin() {
	// Check if Elementor and Elementor Pro are loaded and active.
	if ( ! did_action( 'elementor/loaded' ) || ! defined( 'ELEMENTOR_PRO_VERSION' ) ) {
		add_action( 'admin_notices', 'eip_admin_notice_missing_dependencies' );
		return;
	}

	// Initialize the main plugin class.
	\ElementorIntlPhone\Plugin::instance();
}
add_action( 'plugins_loaded', 'eip_init_plugin', 20 );

/**
 * Admin notice if Elementor or Elementor Pro is missing.
 */
function eip_admin_notice_missing_dependencies() {
	if ( isset( $_GET['activate'] ) ) {
		unset( $_GET['activate'] );
	}

	$message = sprintf(
		/* translators: 1: Elementor, 2: Elementor Pro */
		esc_html__( '"%1$s" requires "%2$s" to be installed and activated.', 'elementor-intl-phone' ),
		'<strong>' . esc_html__( 'International Phone Field for Elementor', 'elementor-intl-phone' ) . '</strong>',
		'<strong>' . esc_html__( 'Elementor Pro', 'elementor-intl-phone' ) . '</strong>'
	);

	printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', wp_kses_post( $message ) );
}
