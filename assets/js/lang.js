'use strict';

// Class Definition
var WebAppLocals = (function () {
	var lang = 'ar';

	var _arrLocals = {
		success: {
			en: 'test en',
			ar: 'test ar',
			fr: '',
		},
		error: {
			en: '',
			ar: '',
			fr: '',
		},
	};

	var _buttonSpinnerClasses = 'spinner spinner-right spinner-white pr-15';

	var _loadPage = function (vURL, vItemId, callback) {
		blockElement(vItemId);
		$.ajax({
			url: vURL,
			type: 'GET',
			dataType: 'html',
			async: true,
		})
			.done(function (data) {
				if (data === 'SESSION-ERROR') {
					window.location = '/member/auth/signout';
				} else {
					// window.history.pushState(data, document.title, vURL);
					$(vItemId).fadeOut('fast', function () {
						$(vItemId).html(data);

						if (vItemId === '#pageContent') {
							window.history.pushState(
								{
									id: '#pageContent',
									url: vURL,
								},
								document.title,
								vURL
							);
						}

						$(vItemId).fadeIn();
						if (typeof callback === 'function') {
							callback();
						}
						unblockElement(vItemId);
					});
					// Animation complete
				}
			})
			.fail(function (jqXHR, textStatus, errorThrown) {
				unblockElement(vItemId);
				$(vItemId).html('Something went wrong, Try again later.');
			});
	};

	var _loadPage = function () {
		var form = KTUtil.getById('kt_login_singin_form');
		var formSubmitUrl = KTUtil.attr(form, 'action');
		var formSubmitButton = KTUtil.getById('kt_login_singin_form_submit_button');

		if (!form) {
			return;
		}

		FormValidation.formValidation(form, {
			fields: {
				email: {
					validators: {
						notEmpty: {
							message: 'عنوان البريد الإلكتروني إلزامي',
						},
						emailAddress: {
							message: 'عنوان البريد الإلكتروني غير صالح',
						},
					},
				},
				password: {
					validators: {
						notEmpty: {
							message: 'يجب ادخال كلمة السر',
						},
						/*regexp: {
									regexp: /^(?=.*[A-Z])(?=.*[!@#$&*])(?=.*[0-9].*[0-9])(?=.*[a-z].*[a-z].*[a-z]).{8}$/,
									message: 'Ensure string has two uppercase letters.<br/>Ensure string has one special case letter.<br/>Ensure string has two digits.<br/>Ensure string has three lowercase letters.<br/>Ensure string is of length 8.'
								}*/
					},
				},
			},
			plugins: {
				trigger: new FormValidation.plugins.Trigger(),
				submitButton: new FormValidation.plugins.SubmitButton(),
				//defaultSubmit: new FormValidation.plugins.DefaultSubmit(), // Uncomment this line to enable normal button submit after form validation
				bootstrap: new FormValidation.plugins.Bootstrap({
					//	eleInvalidClass: '', // Repace with uncomment to hide bootstrap validation icons
					//	eleValidClass: '',   // Repace with uncomment to hide bootstrap validation icons
				}),
			},
		})
			.on('core.form.valid', function () {
				// Show loading state on button
				KTUtil.btnWait(formSubmitButton, _buttonSpinnerClasses, 'Please wait');

				// Simulate Ajax request
				setTimeout(function () {
					KTUtil.btnRelease(formSubmitButton);
				}, 2000);

				// Form Validation & Ajax Submission: https://formvalidation.io/guide/examples/using-ajax-to-submit-the-form

				FormValidation.utils
					.fetch(formSubmitUrl, {
						method: 'POST',
						dataType: 'json',
						params: {
							email: form.querySelector('[name="email"]').value,
							password: form.querySelector('[name="password"]').value,
						},
					})
					.then(function (response) {
						// Return valid JSON
						// Release button
						KTUtil.btnRelease(formSubmitButton);

						if (response && typeof response === 'object' && response.status) {
							window.location.href = '/web';
						} else {
							Swal.fire({
								text: response.message,
								icon: 'error',
								buttonsStyling: false,
								confirmButtonText: 'Ok, got it!',
								customClass: {
									confirmButton: 'btn font-weight-bold btn-light-primary',
								},
							}).then(function () {
								KTUtil.scrollTop();
							});
						}
					});
			})
			.on('core.form.invalid', function () {
				KTUtil.scrollTop();

				/*Swal.fire({
					text: "Sorry, looks like there are some errors detected, please try again.",
					icon: "error",
					buttonsStyling: false,
					confirmButtonText: "Ok, got it!",
					customClass: {
						confirmButton: "btn font-weight-bold btn-light-primary"
					}
				}).then(function() {
					KTUtil.scrollTop();
				});*/
			});
	};

	// Public Functions
	return {
		init: function () {},
		getMessage(key) {
			return _arrLocals[key][docLang];
		},
	};
})();

// Class Initialization
jQuery(document).ready(function () {
	WebAppLocals.init();

	alert(WebAppLocals.getMessage('success'));
});
