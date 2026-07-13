/**
 * International Phone Field JavaScript Initialization
 */
(function ($) {
	'use strict';

	var EIP_Intl_Phone = {
		init: function () {
			// Toggle dropdown
			$(document).on('click', '.eip-combobox-selected', function (e) {
				e.stopPropagation();
				var $dropdown = $(this).siblings('.eip-combobox-dropdown');
				$('.eip-combobox-dropdown').not($dropdown).hide(); // close others
				$dropdown.toggle();
			});

			// Close dropdown when clicking outside
			$(document).on('click', function (e) {
				if (!$(e.target).closest('.eip-combobox').length) {
					$('.eip-combobox-dropdown').hide();
				}
			});

			// Select a country
			$(document).on('click', '.eip-combobox-item', function () {
				var $item = $(this);
				var $wrapper = $item.closest('.mf-phone');
				var $combobox = $item.closest('.eip-combobox');
				var $selectedContainer = $combobox.find('.eip-combobox-selected');

				var dialCode = $item.data('dial');
				var countryCode = $item.data('code');

				// Update selected UI
				$selectedContainer.html('<span class="fi fi-' + countryCode + '"></span><span class="eip-dial-code">' + dialCode + '</span>');

				// Update checkmarks in dropdown
				$combobox.find('.eip-combobox-item').removeClass('eip-selected');
				$combobox.find('.eip-check').css('visibility', 'hidden');

				$item.addClass('eip-selected');
				$item.find('.eip-check').css('visibility', 'visible');

				// Hide dropdown
				$combobox.find('.eip-combobox-dropdown').hide();

				// Update hidden input if tel has value
				EIP_Intl_Phone.updateHiddenField($wrapper);
			});

			// Update hidden field when tel input changes
			$(document).on('input change', '.mf-tel', function () {
				var $wrapper = $(this).closest('.mf-phone');
				EIP_Intl_Phone.updateHiddenField($wrapper);
			});

			// Hook into Elementor form submission to validate and populate hidden fields
			$(document).on('submit_action', function (event, $form) {
				var isValid = true;
				var firstErrorField = null;

				$form.find('.mf-phone').each(function () {
					var $wrapper = $(this);
					var $tel = $wrapper.find('.mf-tel');
					var telValue = $tel.val().trim();

					EIP_Intl_Phone.updateHiddenField($wrapper);

					var $fieldGroup = $wrapper.closest('.elementor-field-group');
					$fieldGroup.removeClass('elementor-error');
					$fieldGroup.find('.elementor-message-danger').remove();

					if (telValue) {
						// Basic regex validation for phone numbers
						var pattern = /^[0-9\s\-\(\)\.]{7,20}$/;
						if (!pattern.test(telValue)) {
							isValid = false;
							$fieldGroup.addClass('elementor-error');
							var errorMsg = window.eip_intl_phone_i18n ? window.eip_intl_phone_i18n.invalid_number : 'Invalid phone number';
							$fieldGroup.append('<span class="elementor-message elementor-message-danger">' + errorMsg + '</span>');

							if (!firstErrorField) {
								firstErrorField = $tel;
							}
						}
					} else if ($tel.prop('required')) {
						isValid = false;
						$fieldGroup.addClass('elementor-error');
						var reqMsg = window.eip_intl_phone_i18n ? window.eip_intl_phone_i18n.required_field : 'This field is required.';
						$fieldGroup.append('<span class="elementor-message elementor-message-danger">' + reqMsg + '</span>');

						if (!firstErrorField) {
							firstErrorField = $tel;
						}
					}
				});

				if (!isValid) {
					event.preventDefault(); // Stop submission
					if (firstErrorField) {
						firstErrorField.focus();
					}
				}
			});
		},

		updateHiddenField: function ($wrapper) {
			var $tel = $wrapper.find('.mf-tel');
			var hiddenId = $wrapper.data('hidden-id');
			var $selectedItem = $wrapper.find('.eip-combobox-item.eip-selected');

			if (hiddenId && $selectedItem.length) {
				var dialCode = $selectedItem.data('dial');
				var telValue = $tel.val().trim();
				var fullNumber = telValue ? dialCode + telValue : '';
				$('#' + hiddenId).val(fullNumber);
			}
		}
	};

	$(window).on('elementor/frontend/init', function () {
		EIP_Intl_Phone.init();
	});

	// Also init on load for non-elementor contexts
	$(document).ready(function () {
		if (typeof elementorFrontend === 'undefined') {
			EIP_Intl_Phone.init();
		}
	});

})(jQuery);
