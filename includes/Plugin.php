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
		$iti_js_url = apply_filters( 'eip_assets_iti_js', EIP_PLUGIN_URL . 'assets/vendor/intl-tel-input/js/intlTelInput.min.js' );
		$custom_js_url = apply_filters( 'eip_assets_custom_js', EIP_PLUGIN_URL . 'assets/js/intl-phone-field.js' );
		$utils_js_url = apply_filters( 'eip_assets_utils_js', EIP_PLUGIN_URL . 'assets/vendor/intl-tel-input/js/utils.js' );

		// Register intl-tel-input core library
		wp_register_script(
			'intl-tel-input',
			$iti_js_url,
			[],
			'17.0.0',
			true
		);

		// Register custom field script
		wp_register_script(
			'eip-intl-phone-field',
			$custom_js_url,
			[ 'jquery', 'intl-tel-input' ],
			EIP_VERSION,
			true
		);

		// Pass i18n and utils URL to JS
		wp_localize_script( 'eip-intl-phone-field', 'eip_intl_phone_i18n', apply_filters( 'eip_i18n_strings', [
			'invalid_number' => esc_html__( 'Invalid phone number.', 'elementor-intl-phone' ),
			'required_field' => esc_html__( 'This field is required.', 'elementor-intl-phone' ),
		] ) );

		wp_localize_script( 'eip-intl-phone-field', 'eip_intl_phone_utils_url', $utils_js_url );
	}

	/**
	 * Register Frontend Styles
	 */
	public function register_frontend_styles() {
		$iti_css_url = apply_filters( 'eip_assets_iti_css', EIP_PLUGIN_URL . 'assets/vendor/intl-tel-input/css/intlTelInput.min.css' );
		$custom_css_url = apply_filters( 'eip_assets_custom_css', EIP_PLUGIN_URL . 'assets/css/intl-phone-field.css' );

		wp_register_style(
			'intl-tel-input',
			$iti_css_url,
			[],
			'17.0.0'
		);
		
		wp_register_style(
			'eip-intl-phone-field',
			$custom_css_url,
			[ 'intl-tel-input' ],
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
