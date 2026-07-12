<?php
namespace ElementorIntlPhone\Elementor\Fields;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use ElementorPro\Modules\Forms\Fields\Field_Base;
use ElementorPro\Plugin;

/**
 * International Phone Field for Elementor Pro Forms.
 *
 * @since 1.0.0
 */
class Intl_Phone extends Field_Base {

	/**
	 * Get field type.
	 *
	 * @since 1.0.0
	 * @return string Field type.
	 */
	public function get_type() {
		return 'intl_phone';
	}

	/**
	 * Get field name.
	 *
	 * @since 1.0.0
	 * @return string Field name.
	 */
	public function get_name() {
		return esc_html__( 'International Phone', 'elementor-intl-phone' );
	}

	/**
	 * Get field script dependencies.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function get_script_depends(): array {
		return [ 'eip-intl-phone-field' ];
	}

	/**
	 * Get field style dependencies.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function get_style_depends(): array {
		return [ 'eip-flag-icons', 'eip-intl-phone-field' ];
	}

	public function render( $item, $item_index, $form ) {
		$form->add_render_attribute( 'input' . $item_index, 'class', 'mf-tel elementor-field-textual' );
		$form->add_render_attribute( 'input' . $item_index, 'type', 'tel' );
		$form->add_render_attribute( 'input' . $item_index, 'pattern', '[0-9]*' );
		$form->add_render_attribute( 'input' . $item_index, 'inputmode', 'numeric' );
		$form->add_render_attribute( 'input' . $item_index, 'oninput', "this.value = this.value.replace(/[^0-9]/g, '');" );
		
		if ( empty( $item['custom_id'] ) ) {
			$item['custom_id'] = 'form_field_' . $item_index;
		}

		$form->add_render_attribute( 'input' . $item_index, 'id', $item['custom_id'] );
		
		if ( ! empty( $item['eip_placeholder'] ) ) {
			$placeholder = apply_filters( 'eip_placeholder', $item['eip_placeholder'], $item );
			$form->add_render_attribute( 'input' . $item_index, 'placeholder', $placeholder );
		} elseif ( ! empty( $item['placeholder'] ) ) {
			$placeholder = apply_filters( 'eip_placeholder', $item['placeholder'], $item );
			$form->add_render_attribute( 'input' . $item_index, 'placeholder', $placeholder );
		} else {
			$form->add_render_attribute( 'input' . $item_index, 'placeholder', 'XX XXX XXXX' );
		}

		if ( ! empty( $item['required'] ) ) {
			$form->add_render_attribute( 'input' . $item_index, 'required', 'required' );
			$form->add_render_attribute( 'input' . $item_index, 'aria-required', 'true' );
		}

		$hidden_name = 'form_fields[' . $item['custom_id'] . ']';

		$countries = require EIP_PLUGIN_DIR . 'includes/Data/countries.php';
		
		$allowed = ! empty( $item['eip_allowed_countries'] ) ? (array) $item['eip_allowed_countries'] : [];
		$excluded = ! empty( $item['eip_excluded_countries'] ) ? (array) $item['eip_excluded_countries'] : [];
		$default = ! empty( $item['eip_default_country'] ) ? strtolower( $item['eip_default_country'] ) : 'ae'; // default to AE based on screenshot

		// Remove the name attribute from the tel input so it doesn't conflict with the hidden input on submission
		$form->remove_render_attribute( 'input' . $item_index, 'name' );

		?>
		<div class="mf-phone" data-hidden-id="<?php echo esc_attr( $item['custom_id'] . '_hidden' ); ?>">
			<div class="eip-combobox">
				<div class="eip-combobox-selected">
					<?php
					$default_dial_code = '';
					if ( isset( $countries[ strtoupper( $default ) ]['dial_code'] ) ) {
						$default_dial_code = '+' . $countries[ strtoupper( $default ) ]['dial_code'];
					}
					?>
					<span class="fi fi-<?php echo esc_attr( strtolower( $default ) ); ?>"></span>
					<span class="eip-dial-code"><?php echo esc_html( $default_dial_code ); ?></span>
				</div>
				<div class="eip-combobox-dropdown" style="display: none;">
					<?php 
					foreach ( $countries as $code => $data ) {
						$code_lower = strtolower( $code );
						if ( ! empty( $allowed ) && ! in_array( $code_lower, $allowed, true ) ) {
							continue;
						}
						if ( ! empty( $excluded ) && in_array( $code_lower, $excluded, true ) ) {
							continue;
						}
						
						$dial_code = isset( $data['dial_code'] ) ? '+' . $data['dial_code'] : '';
						$is_selected = ( $code_lower === $default );
						$selected_class = $is_selected ? ' eip-selected' : '';
						$check_visibility = $is_selected ? 'visible' : 'hidden';
						
						?>
						<div class="eip-combobox-item<?php echo esc_attr( $selected_class ); ?>" data-code="<?php echo esc_attr( $code_lower ); ?>" data-dial="<?php echo esc_attr( $dial_code ); ?>">
							<span class="eip-check" style="visibility: <?php echo esc_attr( $check_visibility ); ?>;">&#10003;</span>
							<span class="fi fi-<?php echo esc_attr( $code_lower ); ?>"></span>
							<span class="eip-dial-code"><?php echo esc_html( $dial_code ); ?></span>
						</div>
						<?php
					}
					?>
				</div>
			</div>
			<input <?php $form->print_render_attribute_string( 'input' . $item_index ); ?>>
		</div>
		<input type="hidden" name="<?php echo esc_attr( $hidden_name ); ?>" id="<?php echo esc_attr( $item['custom_id'] . '_hidden' ); ?>" class="eip-hidden-phone" value="">
		<?php
	}

	/**
	 * Update Controls.
	 *
	 * Registers the controls for this field type in the Elementor panel.
	 *
	 * @since 1.0.0
	 * @param \Elementor\Widget_Base $widget The form widget instance.
	 */
	public function update_controls( $widget ) {
		$control_data = $widget->get_controls( 'form_fields' );

		if ( ! is_array( $control_data ) || ! isset( $control_data['fields'] ) ) {
			return;
		}

		$fields = $control_data['fields'];

		$countries = require EIP_PLUGIN_DIR . 'includes/Data/countries.php';
		$country_options = [];
		foreach ( $countries as $code => $data ) {
			$country_options[ strtolower( $code ) ] = $data['name'];
		}

		$condition = [
			'field_type' => $this->get_type(),
		];

		$fields['eip_placeholder'] = [
			'name' => 'eip_placeholder',
			'type' => \Elementor\Controls_Manager::TEXT,
			'label' => esc_html__( 'Placeholder', 'elementor-intl-phone' ),
			'default' => 'XX XXX XXXX',
			'condition' => $condition,
			'tab' => 'content',
			'inner_tab' => 'form_fields_content_tab',
			'tabs_wrapper' => 'form_fields_tabs',
		];

		$fields['eip_default_country'] = [
			'name' => 'eip_default_country',
			'type' => \Elementor\Controls_Manager::SELECT,
			'label' => esc_html__( 'Default Country', 'elementor-intl-phone' ),
			'options' => $country_options,
			'default' => 'ae',
			'condition' => $condition,
			'tab' => 'content',
			'inner_tab' => 'form_fields_content_tab',
			'tabs_wrapper' => 'form_fields_tabs',
		];

		$fields['eip_allowed_countries'] = [
			'name' => 'eip_allowed_countries',
			'type' => \Elementor\Controls_Manager::SELECT2,
			'label' => esc_html__( 'Allowed Countries', 'elementor-intl-phone' ),
			'options' => $country_options,
			'multiple' => true,
			'default' => [],
			'description' => esc_html__( 'If empty, all countries are shown.', 'elementor-intl-phone' ),
			'condition' => $condition,
			'tab' => 'content',
			'inner_tab' => 'form_fields_content_tab',
			'tabs_wrapper' => 'form_fields_tabs',
		];

		$fields['eip_excluded_countries'] = [
			'name' => 'eip_excluded_countries',
			'type' => \Elementor\Controls_Manager::SELECT2,
			'label' => esc_html__( 'Excluded Countries', 'elementor-intl-phone' ),
			'options' => $country_options,
			'multiple' => true,
			'default' => [],
			'condition' => $condition,
			'tab' => 'content',
			'inner_tab' => 'form_fields_content_tab',
			'tabs_wrapper' => 'form_fields_tabs',
		];

		$fields['eip_validation'] = [
			'name' => 'eip_validation',
			'type' => \Elementor\Controls_Manager::SWITCHER,
			'label' => esc_html__( 'Validation', 'elementor-intl-phone' ),
			'return_value' => 'yes',
			'default' => 'yes',
			'description' => esc_html__( 'Reject invalid numbers based on the selected country.', 'elementor-intl-phone' ),
			'condition' => $condition,
			'tab' => 'content',
			'inner_tab' => 'form_fields_content_tab',
			'tabs_wrapper' => 'form_fields_tabs',
		];

		$widget->update_control( 'form_fields', [
			'fields' => $fields,
		] );
	}

	/**
	 * Field Validation.
	 *
	 * @since 1.0.0
	 * @param array $field
	 * @param \ElementorPro\Modules\Forms\Classes\Form_Record $record
	 * @param \ElementorPro\Modules\Forms\Classes\Ajax_Handler $ajax_handler
	 */
	public function validation( $field, $record, $ajax_handler ) {
		if ( empty( $field['value'] ) ) {
			// Required validation is natively handled by Elementor, but we double-check.
			if ( ! empty( $field['required'] ) ) {
				$ajax_handler->add_error( $field['id'], esc_html__( 'This field is required.', 'elementor-intl-phone' ) );
			}
			return;
		}

		// Check if strict validation is enabled in the field settings
		if ( ! empty( $field['eip_validation'] ) && 'yes' === $field['eip_validation'] ) {
			// Defensively handle array values just in case
			$number = is_array( $field['value'] ) ? end( $field['value'] ) : $field['value'];
			
			// Basic backend regex to ensure it looks like an international phone number.
			// Detailed strict validation (using libphonenumber) is handled by intl-tel-input via JS on the frontend.
			// This regex allows an optional leading +, followed by 7 to 20 characters (digits, spaces, dashes, parentheses).
			$pattern = '/^\+?[0-9\s\-\(\)\.]{7,20}$/';
			
			$is_valid = preg_match( $pattern, $number );
			
			// Allow developers to override or implement stricter backend validation logic (e.g., integrating libphonenumber-for-php)
			$is_valid = apply_filters( 'eip_backend_validation', $is_valid, $number );
			
			if ( ! $is_valid ) {
				$ajax_handler->add_error( $field['id'], esc_html__( 'Invalid phone number.', 'elementor-intl-phone' ) );
			}
		}
	}
}
