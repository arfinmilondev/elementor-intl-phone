<?php
namespace ElementorIntlPhone;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Main Plugin Class
 *
 * @since 1.0.0
 */
final class Plugin {

	/**
	 * Instance
	 *
	 * @var Plugin The single instance of the class.
	 */
	private static $instance = null;

	/**
	 * Instance
	 *
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @return Plugin An instance of the class.
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor
	 */
	private function __construct() {
		$this->init_hooks();
	}

	/**
	 * Initialize Hooks
	 */
	private function init_hooks() {
		add_action( 'init', [ $this, 'i18n' ] );
		
		// Register the custom Elementor Pro Form field
		add_action( 'elementor_pro/forms/fields/register', [ $this, 'register_form_field' ] );

		// Register Frontend Scripts and Styles
		add_action( 'elementor/frontend/after_register_scripts', [ $this, 'register_frontend_scripts' ] );
		add_action( 'elementor/frontend/after_register_styles', [ $this, 'register_frontend_styles' ] );
	}

	/**
	 * Register Frontend Scripts
	 */
	public function register_frontend_scripts() {
		$custom_js_url = apply_filters( 'eip_assets_custom_js', EIP_PLUGIN_URL . 'assets/js/intl-phone-field.js' );

		// Register custom field script
		wp_register_script(
			'eip-intl-phone-field',
			$custom_js_url,
			[ 'jquery' ],
			EIP_VERSION,
			true
		);

		// Pass i18n
		wp_localize_script( 'eip-intl-phone-field', 'eip_intl_phone_i18n', apply_filters( 'eip_i18n_strings', [
			'invalid_number' => esc_html__( 'Invalid phone number.', 'elementor-intl-phone' ),
			'required_field' => esc_html__( 'This field is required.', 'elementor-intl-phone' ),
		] ) );
	}

	/**
	 * Register Frontend Styles
	 */
	public function register_frontend_styles() {
		$flag_css_url = apply_filters( 'eip_assets_flag_css', EIP_PLUGIN_URL . 'assets/flag-icons/css/flag-icons.min.css' );
		$custom_css_url = apply_filters( 'eip_assets_custom_css', EIP_PLUGIN_URL . 'assets/css/intl-phone-field.css' );

		wp_register_style(
			'eip-flag-icons',
			$flag_css_url,
			[],
			'1.0.0'
		);
		
		wp_register_style(
			'eip-intl-phone-field',
			$custom_css_url,
			[ 'eip-flag-icons' ],
			EIP_VERSION
		);
	}

	/**
	 * Register Custom Form Field
	 *
	 * @param \ElementorPro\Modules\Forms\Registrars\Form_Fields_Registrar $form_fields_registrar
	 */
	public function register_form_field( $form_fields_registrar ) {
		// Register the new field
		require_once EIP_PLUGIN_DIR . 'includes/Elementor/Fields/Intl_Phone.php';
		$form_fields_registrar->register( new \ElementorIntlPhone\Elementor\Fields\Intl_Phone() );
	}

	/**
	 * Load Textdomain
	 *
	 * Load plugin localization files.
	 */
	public function i18n() {
		load_plugin_textdomain( 'elementor-intl-phone', false, dirname( plugin_basename( EIP_PLUGIN_FILE ) ) . '/languages/' );
	}

	/**
	 * Prevent cloning.
	 */
	private function __clone() {}

	/**
	 * Prevent unserializing.
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', 'elementor-intl-phone' ), '1.0.0' );
	}
}
