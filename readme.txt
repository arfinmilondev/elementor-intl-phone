=== International Phone Field for Elementor ===
Contributors: R A Milon
Tags: elementor, forms, phone, intl-tel-input, international
Requires at least: 6.0
Tested up to: 6.5
Requires PHP: 8.0
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Adds a completely new International Phone field to Elementor Pro Forms with intl-tel-input integration.

== Description ==

The **International Phone Field for Elementor** plugin extends the native Elementor Pro Forms widget by introducing a completely new "International Phone" field type. 

Built using the robust `intl-tel-input` library, this field provides users with a seamless, highly recognizable UI to select their country code with flags, search for countries in a dropdown, and automatically validate international phone numbers.

### Features
*   **Complete Elementor Integration**: Appears natively inside Elementor Pro's Form widget fields.
*   **Country Dropdown**: Searchable country list with flags and dial codes.
*   **Full Customization**: Control allowed countries, excluded countries, default country, and preferred countries directly from Elementor.
*   **Auto-Detect IP**: Automatically detects the user's country using IP geolocation (when configured).
*   **Strict Validation**: Real-time frontend and backend validation ensuring submitted numbers match international dialing standards.
*   **Action Compatible**: Works flawlessly with all Elementor Actions After Submit (Emails, Webhooks, Mailchimp, ActiveCampaign, etc.) by submitting the correctly formatted number (+1234567890).
*   **Developer Friendly**: Highly extensible through a comprehensive suite of WordPress hooks and filters.

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/elementor-intl-phone` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Edit any page with Elementor.
4. Add or edit a **Form** widget (requires Elementor Pro).
5. Add a new field and select **International Phone** from the field type dropdown.
6. Configure the country and validation settings from the field's settings panel.

== Frequently Asked Questions ==

= Does this require Elementor Pro? =
Yes. Because this plugin extends the Elementor Pro Form widget, Elementor Pro is required to use this field.

= How do I modify the allowed countries via code? =
While you can select allowed countries in the Elementor panel, developers can modify the configuration array dynamically using the `eip_iti_options` filter.

= Can I change the placeholder text? =
Yes, via the Elementor panel or programmatically using the `eip_placeholder` filter.

== Developer Hooks ==

This plugin was built with a SOLID architecture and provides numerous filters for deep customization:

*   `eip_countries_list`: Modify the global list of available countries.
*   `eip_iti_options`: Modify the configuration object passed to `intl-tel-input`.
*   `eip_backend_validation`: Intercept or override backend phone validation logic.
*   `eip_placeholder`: Filter the input placeholder.
*   `eip_assets_iti_js`: Filter the core intl-tel-input JS URL.
*   `eip_assets_custom_js`: Filter the custom initialization JS URL.
*   `eip_assets_utils_js`: Filter the utils.js URL.
*   `eip_assets_iti_css`: Filter the core intl-tel-input CSS URL.
*   `eip_assets_custom_css`: Filter the custom Elementor integration CSS URL.
*   `eip_i18n_strings`: Filter the translation strings used in frontend validation errors.

== Changelog ==

= 1.0.0 =
* Initial Release.
