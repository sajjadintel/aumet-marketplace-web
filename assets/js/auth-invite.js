'use strict';

// Class Definition
var WebAuth = (function () {
	var _buttonSpinnerClasses = 'spinner spinner-right spinner-white pr-15';
	var _pharmacyDocument;

	var _handleTrimEmail = function () {
		$("input[type = 'email']").each(function (i, obj) {
			$(this).keyup(function () {
				$(this).val($(this).val().replace(/ +?/g, ''));
			});
		});
	};

	var _handleFormSignup = function () {
		// Base elements
		var wizardEl = KTUtil.getById('kt_login');
		var form = KTUtil.getById('kt_login_signup_form');
		var wizardObj;
		var validations = [];

		if (!form) {
			return;
		}

		// Init form validation rules. For more info check the FormValidation plugin's official documentation:https://formvalidation.io/
		// Step 1
		validations.push(
			FormValidation.formValidation(form, {
				fields: {
					name: {
						validators: {
							notEmpty: {
								message: 'Name is required',
							},
						},
					},
					mobile: {
						validators: {
							notEmpty: {
								message: 'Phone Number is required',
							},
						},
					},
					email: {
						validators: {
							notEmpty: {
								message: 'Email is required',
							},
							emailAddress: {
								message: 'Invalid email address',
							},
						},
					},
					password: {
						validators: {
							notEmpty: {
								message: 'Password is required',
							},
						},
					},
					passwordConfirmation: {
						validators: {
							identical: {
								compare: function () {
									return form.querySelector('[name="password"]').value;
								},
								message: "Password confirmation doesn't match",
							},
						},
					},
				},
				plugins: {
					trigger: new FormValidation.plugins.Trigger(),
					// Bootstrap Framework Integration
					bootstrap: new FormValidation.plugins.Bootstrap({
						//eleInvalidClass: '',
						eleValidClass: '',
					}),
				},
			})
		);

		// Step 2
		/*validations.push(
			FormValidation.formValidation(form, {
				fields: {
					pharmacyName: {
						validators: {
							callback: {
								message: 'Pharmacy Name is required',
								callback: function(value, validator, $field) {
									if($('input[name="companyType"]:checked').val() === 'pharmacy' && $('#pharmacyName').val() === ''){
										return false;
									}
									return true;
								}
							},
						},
					},
					distributorName: {
						validators: {
							callback: {
								message: 'Distributor Name is required',
								callback: function(value, validator, $field) {
									if($('input[name="companyType"]:checked').val() === 'distributor' && $('#distributorName').val() === ''){
										return false;
									}
									return true;
								}
							},
						},
					},
					country: {
						validators: {
							notEmpty: {
								message: 'Country is required',
							},
						},
					},
					city: {
						validators: {
							notEmpty: {
								message: 'City is required',
							},
						},
					},
					address: {
						validators: {
							notEmpty: {
								message: 'Address is required',
							},
						},
					},
				},
				plugins: {
					trigger: new FormValidation.plugins.Trigger(),
					// Bootstrap Framework Integration
					bootstrap: new FormValidation.plugins.Bootstrap({
						//eleInvalidClass: '',
						eleValidClass: '',
					}),
				},
			})
		);*/

		// Initialize form wizard
		wizardObj = new KTWizard(wizardEl, {
			startStep: 1, // initial active step number
			clickableSteps: false, // allow step clicking
		});

		// Validation before going to next page
		wizardObj.on('change', function (wizard) {
			if (wizard.getStep() > wizard.getNewStep()) {
				return; // Skip if stepped back
			}

			// Validate form before change wizard step
			var validator = validations[wizard.getStep() - 1]; // get validator for currnt step

			if (validator) {
				validator.validate().then(function (status) {
					if (status == 'Valid') {
						if (wizard.getStep() == 1) {
							let body = {
								email: $('#kt_login_signup_form input[name=email]').val(),
								mobile: $('#kt_login_signup_form input[name=mobile]').val(),
								companyType: $('#kt_login_signup_form input[name=companyType]:checked').val(),
							};
							WebApp.post('/web/auth/signup/validate/step1', body, function () {
								wizard.goTo(wizard.getNewStep());

								let companyType = $('#kt_login_signup_form input[name=companyType]:checked').val();
								if (companyType == 'pharmacy') {
									$('.distributor').hide();
									$('.pharmacy').show();
								} else if (companyType == 'distributor') {
									$('.pharmacy').hide();
									$('.distributor').show();
								}
							});
						} else {
							wizard.goTo(wizard.getNewStep());
						}

						KTUtil.scrollTop();
					} else {
						/*Swal.fire({
							text: 'Sorry, looks like there are some errors detected, please try again.',
							icon: 'error',
							buttonsStyling: false,
							confirmButtonText: 'Ok, got it!',
							customClass: {
								confirmButton: 'btn font-weight-bold btn-light',
							},
						}).then(function () {
							KTUtil.scrollTop();
						});*/
					}
				});
			}

			return false; // Do not change wizard step, further action will be handled by he validator
		});

		// Change event
		wizardObj.on('changed', function (wizard) {
			KTUtil.scrollTop();
		});

		// Submit event
		wizardObj.on('submit', function (wizard) {
			// Validate form before change wizard step
			var validator = validations[validations.length - 1]; // get validator for currnt step

			if (validator) {
				validator.validate().then(function (status) {
					if (status == 'Valid') {
						Swal.fire({
							text: 'All is good! Please confirm the form submission.',
							icon: 'success',
							showCancelButton: true,
							buttonsStyling: false,
							confirmButtonText: 'Yes, submit!',
							cancelButtonText: 'No, cancel',
							customClass: {
								confirmButton: 'btn font-weight-bold btn-primary',
								cancelButton: 'btn font-weight-bold btn-default',
							},
						}).then(function (result) {
							if (result.value) {
								_signUp();
							} else if (result.dismiss === 'cancel') {
								Swal.fire({
									text: 'Your form has not been submitted!.',
									icon: 'error',
									buttonsStyling: false,
									confirmButtonText: 'Ok, got it!',
									customClass: {
										confirmButton: 'btn font-weight-bold btn-primary',
									},
								});
							}
						});
					} else {
						/*Swal.fire({
							text: 'Sorry, looks like there are some errors detected, please try again.',
							icon: 'error',
							buttonsStyling: false,
							confirmButtonText: 'Ok, got it!',
							customClass: {
								confirmButton: 'btn font-weight-bold btn-light',
							},
						}).then(function () {
							KTUtil.scrollTop();
						});*/
					}
				});
			}
		});

		// On country change event, fill city dropdown
		$('#kt_login_signup_form select[name=country]').on('change', function () {
			var countryId = $('#kt_login_signup_form select[name=country]').val();
			if (countryId) {
				WebApp.get('/web/auth/city/list/' + countryId, function (webResponse) {
					$('#kt_login_signup_form select[name=city]')
						.empty()
						.append('<option value="">' + WebAppLocals.getMessage('city') + '</option>');
					var allCities = webResponse.data;
					allCities.forEach((city) => {
						$('#kt_login_signup_form select[name=city]').append(new Option(city.name, city.id));
					});
					$('#kt_login_signup_form select[name=city]').prop('disabled', false);
				});
			} else {
				$('#kt_login_signup_form select[name=city]').prop('disabled', true);
				$('#kt_login_signup_form select[name=city]')
					.empty()
					.append('<option value="">' + WebAppLocals.getMessage('city') + '</option>');
			}
		});

		// Fill default values from query params
		var params = new URLSearchParams(window.location.search);
		$('#kt_login_signup_form input[name=name]').val(params.get('name'));
		$('#kt_login_signup_form input[name=email]').val(params.get('email'));
		$('#kt_login_signup_form input[name=uid]').val(params.get('uid'));
	};

	var _setupFirebase = function () {
		firebase.auth().languageCode = docLang;

		firebase.auth().onAuthStateChanged(function (user) {
			if (user) {
				console.log('FB SignIn: ', user);

				let userInfo = {
					displayName: user.displayName,
					email: user.email,
					emailVerified: user.emailVerified,
					photoURL: user.photoURL,
					isAnonymous: user.isAnonymous,
					uid: user.uid,
					providerData: user.providerData,
				};

				firebase
					.auth()
					.currentUser.getIdToken(true)
					.then(function (idToken) {
						userInfo.idToken = idToken;

						$.ajax({
							url: '/web/auth/signin?_t=' + Date.now(),
							type: 'POST',
							dataType: 'json',
							data: {
								token: idToken,
								userInfo: userInfo,
							},
							async: true,
						})
							.done(function (webResponse) {
								if (webResponse && typeof webResponse === 'object') {
									if (webResponse.errorCode == 0) {
										firebase.analytics().logEvent('auth_ok');
										window.location.href = '/web';
									} else {
										// REMOVE CURRENT USER SESSION
										firebase.auth().signOut();

										if (webResponse.data) {
											var url = '/web/auth/signup';
											url += '?name=' + webResponse.data.displayName;
											url += '&email=' + webResponse.data.email;
											url += '&uid=' + webResponse.data.uid;
											window.location.href = url;
										} else {
											Swal.fire({
												text: webResponse.message,
												icon: 'error',
												buttonsStyling: false,
												confirmButtonText: WebAppLocals.getMessage('error_confirmButtonText'),
												customClass: {
													confirmButton: 'btn font-weight-bold btn-light-primary',
												},
											}).then(function () {
												KTUtil.scrollTop();
											});
										}
									}
								} else {
									// REMOVE CURRENT USER SESSION
									firebase.auth().signOut();

									Swal.fire({
										text: webResponse.message,
										icon: 'error',
										buttonsStyling: false,
										confirmButtonText: WebAppLocals.getMessage('error_confirmButtonText'),
										customClass: {
											confirmButton: 'btn font-weight-bold btn-light-primary',
										},
									}).then(function () {
										KTUtil.scrollTop();
									});
								}
							})
							.fail(function (jqXHR, textStatus, errorThrown) {
								// REMOVE CURRENT USER SESSION
								firebase.auth().signOut();

								Swal.fire({
									text: WebAppLocals.getMessage('error'),
									icon: 'error',
									buttonsStyling: false,
									confirmButtonText: WebAppLocals.getMessage('error_confirmButtonText'),
									customClass: {
										confirmButton: 'btn font-weight-bold btn-light-primary',
									},
								}).then(function () {
									KTUtil.scrollTop();
								});
							});
					})
					.catch(function (error) {
						// REMOVE CURRENT USER SESSION
						firebase.auth().signOut();

						Swal.fire({
							text: error.message,
							icon: 'error',
							buttonsStyling: false,
							confirmButtonText: WebAppLocals.getMessage('error_confirmButtonText'),
							customClass: {
								confirmButton: 'btn font-weight-bold btn-light-primary',
							},
						}).then(function () {
							KTUtil.scrollTop();
						});
					});

				// ...
			} else {
				// User is signed out.
				// ...
			}
		});
	};

	var _signUp = function () {
		let body = {
			pharmacyDocument: _pharmacyDocument,
		};

		let mapKeyElement = {
			uid: 'input',
			name: 'input',
			mobile: 'input',
			email: 'input',
			password: 'input',
			token: 'input',
		};

		Object.keys(mapKeyElement).forEach((key) => {
			body[key] = $('#kt_login_signup_form ' + mapKeyElement[key] + '[name=' + key + ']').val();
		});

		WebApp.post('/web/auth/signup/invite', body, _signUpCallback);
	};

	var _signUpCallback = function (webResponse) {
		KTUtil.scrollTop();
		$('#signupContainer').remove();

		var allValues = webResponse.data;
		console.log(allValues);

		var unitName = allValues.roleId == 40 ? 'Pharmacy Name' : 'Distributor Name';
		var arrFields = {
			Name: allValues.name,
			Mobile: allValues.mobile,
			Email: allValues.email,
			[unitName]: allValues.entityName,
		};

		var output = '';
		for (var key in arrFields) {
			if (arrFields.hasOwnProperty(key)) {
				console.log(key + ' -> ' + arrFields[key]);
				output +=
					'<tr>' +
					'    <td class="o_bg-white o_px-md o_py o_sans o_text-xs o_text-light" align="center" style="font-family: Helvetica, Arial, sans-serif;margin-top: 0px;margin-bottom: 0px;font-size: 14px;line-height: 21px;color: #82899a;padding-left: 24px;padding-right: 24px;padding-top: 16px;padding-bottom: 16px;">' +
					'        <p class="o_mb" style="margin-top: 0px;margin-bottom: 16px;"><strong>' +
					key +
					'</strong></p>' +
					'        <table role="presentation" cellspacing="0" cellpadding="0" border="0">' +
					'            <tbody>' +
					'            <tr>' +
					'                <td width="284" class="o_bg-ultra_light o_br o_text-xs o_sans o_px-xs o_py" align="center" style="font-family: Helvetica, Arial, sans-serif;margin-top: 0px;margin-bottom: 0px;font-size: 14px;line-height: 21px;background-color: #ebf5fa;border-radius: 4px;padding-left: 8px;padding-right: 8px;padding-top: 16px;padding-bottom: 16px;">' +
					'                    <p class="o_text-dark" style="color: #242b3d;margin-top: 0px;margin-bottom: 0px;">' +
					'                        <strong>' +
					arrFields[key] +
					'</strong>' +
					'                    </p>' +
					'                </td>' +
					'            </tr>' +
					'            </tbody>' +
					'        </table>' +
					'    </td>' +
					'</tr>';
			}
		}

		$('#signupDetailData').html(output);

		$('#thankyouContainer').css('display', 'block');
	};

	// Public Functions
	return {
		init: function () {
			if (window.location.href.indexOf('signin') > -1) {
				_setupFirebase();
			}

			_handleTrimEmail();
			//_handleFormForgot();
			//_handleFormReset();
			_handleFormSignup();
			//_handleFormSignin();

			//_initializeDropzone();
		},
		/*signIn: function () {
			_handleFormSignin();
		},*/
		googleSignIn: function () {
			_handleGoogleSignin();
		},
	};
})();

// Class Initialization
jQuery(document).ready(function () {
	WebAuth.init();
});
