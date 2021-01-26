'use strict';

// Class Definition
var WebAuth = (function () {
	var _buttonSpinnerClasses = 'spinner spinner-right spinner-white pr-15';
	var _pharmacyDocument;

	var _handleFormSignin = function () {
		console.log('Signin');

		var form = KTUtil.getById('kt_login_singin_form');
		var url = KTUtil.attr(form, 'action');
		var data = $(form).serializeJSON();

		if (!form) {
			console.log('No Form');
			return;
		}
		WebApp.post(url, data);

		// firebase
		// 	.auth()
		// 	.signInWithEmailAndPassword(form.querySelector('[name="email"]').value, form.querySelector('[name="password"]').value)
		// 	.catch(function (error) {
		// 		// Handle Errors here.
		// 		KTUtil.btnRelease(formSubmitButton);
		// 		Swal.fire({
		// 			text: error.message,
		// 			icon: 'error',
		// 			buttonsStyling: false,
		// 			confirmButtonText: WebAppLocals.getMessage('error_confirmButtonText'),
		// 			customClass: {
		// 				confirmButton: 'btn font-weight-bold btn-light-primary',
		// 			},
		// 		}).then(function () {
		// 			KTUtil.scrollTop();
		// 		});
		// 	});

		// return;

		// FormValidation.formValidation(form, {
		// 	fields: {
		// 		email: {
		// 			validators: {
		// 				notEmpty: {
		// 					message: WebAppLocals.getMessage('error_emailNotEmpty'),
		// 				},
		// 				emailAddress: {
		// 					message: WebAppLocals.getMessage('error_emailFormat'),
		// 				},
		// 			},
		// 		},
		// 		password: {
		// 			validators: {
		// 				notEmpty: {
		// 					message: WebAppLocals.getMessage('error_passwordNotEmpty'),
		// 				},
		// 				/*regexp: {
		// 							regexp: /^(?=.*[A-Z])(?=.*[!@#$&*])(?=.*[0-9].*[0-9])(?=.*[a-z].*[a-z].*[a-z]).{8}$/,
		// 							message: 'Ensure string has two uppercase letters.<br/>Ensure string has one special case letter.<br/>Ensure string has two digits.<br/>Ensure string has three lowercase letters.<br/>Ensure string is of length 8.'
		// 						}*/
		// 			},
		// 		},
		// 	},
		// 	plugins: {
		// 		trigger: new FormValidation.plugins.Trigger(),
		// 		submitButton: new FormValidation.plugins.SubmitButton(),
		// 		//defaultSubmit: new FormValidation.plugins.DefaultSubmit(), // Uncomment this line to enable normal button submit after form validation
		// 		bootstrap: new FormValidation.plugins.Bootstrap({
		// 			//	eleInvalidClass: '', // Repace with uncomment to hide bootstrap validation icons
		// 			//	eleValidClass: '',   // Repace with uncomment to hide bootstrap validation icons
		// 		}),
		// 	},
		// })
		// 	.on('core.form.valid', function () {
		// 		console.log('valid');
		// 		// Show loading state on button
		// 		KTUtil.btnWait(formSubmitButton, _buttonSpinnerClasses, 'Please wait');

		// 		// Form Validation & Ajax Submission: https://formvalidation.io/guide/examples/using-ajax-to-submit-the-form
		// 	})
		// 	.on('core.form.invalid', function () {
		// 		console.log('invalid');
		// 		KTUtil.scrollTop();
		// 	});

		// console.log('done');
	};

	var _handleGoogleSignin = function () {
		var formSubmitButton = KTUtil.getById('kt_login_singin_google_submit_button');

		// Show loading state on button
		KTUtil.btnWait(formSubmitButton, _buttonSpinnerClasses, 'Please wait');

		var provider = new firebase.auth.GoogleAuthProvider();
		firebase
			.auth()
			.signInWithPopup(provider)
			.catch(function (error) {
				// Handle Errors here.
				var errorCode = error.code;
				var errorMessage = error.message;
				// The email of the user's account used.
				var email = error.email;
				// The firebase.auth.AuthCredential type that was used.
				var credential = error.credential;
				// ...

				// Handle Errors here.
				KTUtil.btnRelease(formSubmitButton);
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
	};

	var _handleFormForgot = function () {
		var form = KTUtil.getById('kt_login_forgot_form');
		var formSubmitUrl = KTUtil.attr(form, 'action');
		var formSubmitButton = KTUtil.getById('kt_login_forgot_form_submit_button');

		if (!form) {
			return;
		}

		FormValidation.formValidation(form, {
			fields: {
				email: {
					validators: {
						notEmpty: {
							message: 'Email is required',
						},
						emailAddress: {
							message: 'The value is not a valid email address',
						},
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

				var url = KTUtil.attr(form, 'action');
				var data = $(form).serializeJSON();

				if (!form) {
					console.log('No Form');
					return;
				}
				WebApp.post(url, data, function (){
					KTUtil.btnRelease(formSubmitButton);
				});

			})
			.on('core.form.invalid', function () {
				Swal.fire({
					text: 'Sorry, looks like there are some errors detected, please try again.',
					icon: 'error',
					buttonsStyling: false,
					confirmButtonText: 'Ok, got it!',
					customClass: {
						confirmButton: 'btn font-weight-bold btn-light-primary',
					},
				}).then(function () {
					KTUtil.scrollTop();
				});
			});
	};


	var _handleFormReset = function () {
		// Base elements
		var form = KTUtil.getById('kt_login_reset_form');
		var formSubmitButton = KTUtil.getById('kt_login_reset_form_submit_button');

		if (!form) {
			return;
		}

		FormValidation.formValidation(form, {
			fields: {
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
							message: "Password confirmation doesn't",
						},
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

				var url = KTUtil.attr(form, 'action');
				var data = $(form).serializeJSON();

				if (!form) {
					console.log('No Form');
					return;
				}
				WebApp.post(url, data, function (){
					KTUtil.btnRelease(formSubmitButton);
					Swal.fire({
						text: 'Password changed successfully!',
						icon: 'success',
						buttonsStyling: false,
						confirmButtonText: 'Login!',
						customClass: {
							confirmButton: 'btn font-weight-bold btn-light-primary',
						},
					}).then(function () {
                        window.location.href = '/web/auth/signin';
						KTUtil.btnRelease(formSubmitButton);
					});
				});

			})
			.on('core.form.invalid', function () {
				Swal.fire({
					text: 'Sorry, looks like there are some errors detected, please try again.',
					icon: 'error',
					buttonsStyling: false,
					confirmButtonText: 'Ok, got it!',
					customClass: {
						confirmButton: 'btn font-weight-bold btn-light-primary',
					},
				}).then(function () {
					KTUtil.scrollTop();
					KTUtil.btnRelease(formSubmitButton);
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
								message: "Password confirmation doesn't",
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
		validations.push(
			FormValidation.formValidation(form, {
				fields: {
					entityName: {
						validators: {
							notEmpty: {
								message: 'Pharmacy Name is required',
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
		);

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
							};
							WebApp.post('/web/auth/signup/validate/email', body, function () {
								wizard.goTo(wizard.getNewStep());
							});
						} else {
							wizard.goTo(wizard.getNewStep());
						}

						KTUtil.scrollTop();
					} else {
						Swal.fire({
							text: 'Sorry, looks like there are some errors detected, please try again.',
							icon: 'error',
							buttonsStyling: false,
							confirmButtonText: 'Ok, got it!',
							customClass: {
								confirmButton: 'btn font-weight-bold btn-light',
							},
						}).then(function () {
							KTUtil.scrollTop();
						});
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
						Swal.fire({
							text: 'Sorry, looks like there are some errors detected, please try again.',
							icon: 'error',
							buttonsStyling: false,
							confirmButtonText: 'Ok, got it!',
							customClass: {
								confirmButton: 'btn font-weight-bold btn-light',
							},
						}).then(function () {
							KTUtil.scrollTop();
						});
					}
				});
			}
		});

		// On country change event, fill city dropdown
		$('select[name=country]').on('change', function () {
			var countryId = $('select[name=country]').val();
			if (countryId) {
				WebApp.get('/web/auth/signup/cities/' + countryId, function (webResponse) {
					$('select[name=city]')
						.empty()
						.append('<option value="">' + WebAppLocals.getMessage('city') + '</option>');
					var allCities = webResponse.data;
					allCities.forEach((city) => {
						$('select[name=city]').append(new Option(city.name, city.id));
					});
					$('select[name=city]').prop('disabled', false);
				});
			} else {
				$('select[name=city]').prop('disabled', true);
				$('select[name=city]')
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

	var _initializeDropzone = function () {
		// Set the dropzone container id
		var id = '#kt_dropzone';

		// Set the preview element template
		var previewNode = $(id + ' .dropzone-item');
		previewNode.id = '';
		var previewTemplate = previewNode.parent('.dropzone-items').html();
		previewNode.remove();

		var myDropzone = new Dropzone(id, {
			// Make the whole body a dropzone
			url: '/web/auth/signup/document/upload', // Set the url for your upload script location
			acceptedFiles: '.pdf, .ppt, .xcl, .docx, .jpeg, .jpg, .png',
			maxFilesize: 10, // Max filesize in MB
			maxFiles: 1,
			previewTemplate: previewTemplate,
			previewsContainer: id + ' .dropzone-items', // Define the container to display the previews
			clickable: id + ' .dropzone-select', // Define the element that should be used as click trigger to select files.
		});

		myDropzone.on('addedfile', function (file) {
			// Hookup the start button
			$(document)
				.find(id + ' .dropzone-item')
				.css('display', '');
		});

		// Update the total progress bar
		myDropzone.on('totaluploadprogress', function (progress) {
			$(id + ' .progress-bar').css('width', progress + '%');
		});

		myDropzone.on('sending', function (file) {
			// Show the total progress bar when upload starts
			$(id + ' .progress-bar').css('opacity', '1');
		});

		// Hide the total progress bar when nothing's uploading anymore
		myDropzone.on('complete', function (progress) {
			var thisProgressBar = id + ' .dz-complete';
			setTimeout(function () {
				$(thisProgressBar + ' .progress-bar, ' + thisProgressBar + ' .progress').css('opacity', '0');
			}, 300);
		});

		// Add file to the list if success
		myDropzone.on('success', function (file, response) {
			_pharmacyDocument = response;
		});

		// Remove file from the list
		myDropzone.on('removedfile', function (file) {
			if (file.status === 'success') {
				_pharmacyDocument = null;
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
			entityName: 'input',
			tradeLicenseNumber: 'input',
			country: 'select',
			city: 'select',
			address: 'textarea',
		};

		Object.keys(mapKeyElement).forEach((key) => {
			body[key] = $('#kt_login_signup_form ' + mapKeyElement[key] + '[name=' + key + ']').val();
		});

		WebApp.post('/web/auth/signup', body, _signUpCallback);
	};

	var _signUpCallback = function (webResponse) {
		KTUtil.scrollTop();
		$('#signupContainer').remove();
		$('#thankyouContainer').css('display', 'block');
	};

	// Public Functions
	return {
		init: function () {
			if (window.location.href.indexOf('signin') > -1) {
				_setupFirebase();
			}

			_handleFormForgot();
			_handleFormReset();
			_handleFormSignup();

			_initializeDropzone();
		},
		signIn: function () {
			_handleFormSignin();
		},
		googleSignIn: function () {
			_handleGoogleSignin();
		},
	};
})();

// Class Initialization
jQuery(document).ready(function () {
	WebAuth.init();
});
