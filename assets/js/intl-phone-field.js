/**
 * International Phone Field JavaScript Initialization (Native Version)
 */

(function ($) {
	'use strict';

	var EIP_Intl_Phone = {
		init: function () {
			// Update hidden field when select or input changes
			$(document).on('change input', '.mf-cc, .mf-tel', function () {
				var $wrapper = $(this).closest('.mf-phone');
				var $cc = $wrapper.find('.mf-cc');
				var $tel = $wrapper.find('.mf-tel');
				
				var hiddenId = $cc.data('hidden-id');
				if (hiddenId) {
					var fullNumber = '';
					if ($tel.val().trim() !== '') {
						fullNumber = $cc.val() + $tel.val().trim();
					}
					$('#' + hiddenId).val(fullNumber);
				}
			});

			// Hook into Elementor form submission to validate and populate hidden fields
			$(document).on('submit_action', function (event, $form) {
				var isValid = true;
				var firstErrorField = null;

				$form.find('.mf-phone').each(function () {
					var $wrapper = $(this);
					var $cc = $wrapper.find('.mf-cc');
					var $tel = $wrapper.find('.mf-tel');
					var hiddenId = $cc.data('hidden-id');
					
					var telValue = $tel.val().trim();
					
					if (hiddenId) {
						var fullNumber = telValue ? $cc.val() + telValue : '';
						$('#' + hiddenId).val(fullNumber);
					}

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
		}
	};

	$(window).on('elementor/frontend/init', function () {
		EIP_Intl_Phone.init();
	});

})(jQuery);
