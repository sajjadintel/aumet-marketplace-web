"use strict";

// Class Definition
var WebAuth = function () {
	var _buttonSpinnerClasses = 'spinner spinner-right spinner-white pr-15';

	var _handleFormSignin = function () {

		console.log("Signin");

		var form = KTUtil.getById('kt_login_singin_form');
		var formSubmitUrl = KTUtil.attr(form, 'action');
		var formSubmitButton = KTUtil.getById('kt_login_singin_form_submit_button');

		if (!form) {
			console.log("No Form");
			return;
		}

		firebase.auth().signInWithEmailAndPassword(
			form.querySelector('[name="email"]').value,
			form.querySelector('[name="password"]').value).catch(function (error) {
				// Handle Errors here.
				KTUtil.btnRelease(formSubmitButton);
				Swal.fire({
					text: error.message,
					icon: "error",
					buttonsStyling: false,
					confirmButtonText: WebAppLocals.getMessage("error_confirmButtonText"),
					customClass: {
						confirmButton: "btn font-weight-bold btn-light-primary"
					}
				}).then(function () {
					KTUtil.scrollTop();
				});
			});

		return;

		FormValidation
			.formValidation(
				form,
				{
					fields: {
						email: {
							validators: {
								notEmpty: {
									message: WebAppLocals.getMessage("error_emailNotEmpty")
								},
								emailAddress: {
									message: WebAppLocals.getMessage("error_emailFormat")
								}
							}
						},
						password: {
							validators: {
								notEmpty: {
									message: WebAppLocals.getMessage("error_passwordNotEmpty")
								},
								/*regexp: {
									regexp: /^(?=.*[A-Z])(?=.*[!@#$&*])(?=.*[0-9].*[0-9])(?=.*[a-z].*[a-z].*[a-z]).{8}$/,
									message: 'Ensure string has two uppercase letters.<br/>Ensure string has one special case letter.<br/>Ensure string has two digits.<br/>Ensure string has three lowercase letters.<br/>Ensure string is of length 8.'
								}*/
							}
						}
					},
					plugins: {
						trigger: new FormValidation.plugins.Trigger(),
						submitButton: new FormValidation.plugins.SubmitButton(),
						//defaultSubmit: new FormValidation.plugins.DefaultSubmit(), // Uncomment this line to enable normal button submit after form validation
						bootstrap: new FormValidation.plugins.Bootstrap({
							//	eleInvalidClass: '', // Repace with uncomment to hide bootstrap validation icons
							//	eleValidClass: '',   // Repace with uncomment to hide bootstrap validation icons
						})
					}
				}
			)
			.on('core.form.valid', function () {
				console.log("valid");
				// Show loading state on button
				KTUtil.btnWait(formSubmitButton, _buttonSpinnerClasses, "Please wait");

				// Form Validation & Ajax Submission: https://formvalidation.io/guide/examples/using-ajax-to-submit-the-form


			})
			.on('core.form.invalid', function () {
				console.log("invalid");
				KTUtil.scrollTop();
			});

		console.log("done");
	}

	var _handleGoogleSignin = function () {
		var formSubmitButton = KTUtil.getById('kt_login_singin_google_submit_button');

		// Show loading state on button
		KTUtil.btnWait(formSubmitButton, _buttonSpinnerClasses, "Please wait");

		var provider = new firebase.auth.GoogleAuthProvider();
		firebase.auth().signInWithPopup(provider).
			catch(function (error) {
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
					icon: "error",
					buttonsStyling: false,
					confirmButtonText: WebAppLocals.getMessage("error_confirmButtonText"),
					customClass: {
						confirmButton: "btn font-weight-bold btn-light-primary"
					}
				}).then(function () {
					KTUtil.scrollTop();
				});
			});
	}

	var _handleFormForgot = function () {
		var form = KTUtil.getById('kt_login_forgot_form');
		var formSubmitUrl = KTUtil.attr(form, 'action');
		var formSubmitButton = KTUtil.getById('kt_login_forgot_form_submit_button');

		if (!form) {
			return;
		}

		FormValidation
			.formValidation(
				form,
				{
					fields: {
						email: {
							validators: {
								notEmpty: {
									message: 'Email is required'
								},
								emailAddress: {
									message: 'The value is not a valid email address'
								}
							}
						}
					},
					plugins: {
						trigger: new FormValidation.plugins.Trigger(),
						submitButton: new FormValidation.plugins.SubmitButton(),
						//defaultSubmit: new FormValidation.plugins.DefaultSubmit(), // Uncomment this line to enable normal button submit after form validation
						bootstrap: new FormValidation.plugins.Bootstrap({
							//	eleInvalidClass: '', // Repace with uncomment to hide bootstrap validation icons
							//	eleValidClass: '',   // Repace with uncomment to hide bootstrap validation icons
						})
					}
				}
			)
			.on('core.form.valid', function () {
				// Show loading state on button
				KTUtil.btnWait(formSubmitButton, _buttonSpinnerClasses, "Please wait");

				// Simulate Ajax request
				setTimeout(function () {
					KTUtil.btnRelease(formSubmitButton);
				}, 2000);
			})
			.on('core.form.invalid', function () {
				Swal.fire({
					text: "Sorry, looks like there are some errors detected, please try again.",
					icon: "error",
					buttonsStyling: false,
					confirmButtonText: "Ok, got it!",
					customClass: {
						confirmButton: "btn font-weight-bold btn-light-primary"
					}
				}).then(function () {
					KTUtil.scrollTop();
				});
			});
	}

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
		validations.push(FormValidation.formValidation(
			form,
			{
				fields: {
					fname: {
						validators: {
							notEmpty: {
								message: 'First name is required'
							}
						}
					},
					lname: {
						validators: {
							notEmpty: {
								message: 'Last Name is required'
							}
						}
					},
					phone: {
						validators: {
							notEmpty: {
								message: 'Phone is required'
							}
						}
					},
					email: {
						validators: {
							notEmpty: {
								message: 'Email is required'
							},
							emailAddress: {
								message: 'The value is not a valid email address'
							}
						}
					}
				},
				plugins: {
					trigger: new FormValidation.plugins.Trigger(),
					// Bootstrap Framework Integration
					bootstrap: new FormValidation.plugins.Bootstrap({
						//eleInvalidClass: '',
						eleValidClass: '',
					})
				}
			}
		));

		// Step 2
		validations.push(FormValidation.formValidation(
			form,
			{
				fields: {
					address1: {
						validators: {
							notEmpty: {
								message: 'Address is required'
							}
						}
					},
					postcode: {
						validators: {
							notEmpty: {
								message: 'Postcode is required'
							}
						}
					},
					city: {
						validators: {
							notEmpty: {
								message: 'City is required'
							}
						}
					},
					state: {
						validators: {
							notEmpty: {
								message: 'State is required'
							}
						}
					},
					country: {
						validators: {
							notEmpty: {
								message: 'Country is required'
							}
						}
					}
				},
				plugins: {
					trigger: new FormValidation.plugins.Trigger(),
					// Bootstrap Framework Integration
					bootstrap: new FormValidation.plugins.Bootstrap({
						//eleInvalidClass: '',
						eleValidClass: '',
					})
				}
			}
		));

		// Step 3
		validations.push(FormValidation.formValidation(
			form,
			{
				fields: {
					delivery: {
						validators: {
							notEmpty: {
								message: 'Delivery type is required'
							}
						}
					},
					packaging: {
						validators: {
							notEmpty: {
								message: 'Packaging type is required'
							}
						}
					},
					preferreddelivery: {
						validators: {
							notEmpty: {
								message: 'Preferred delivery window is required'
							}
						}
					}
				},
				plugins: {
					trigger: new FormValidation.plugins.Trigger(),
					// Bootstrap Framework Integration
					bootstrap: new FormValidation.plugins.Bootstrap({
						//eleInvalidClass: '',
						eleValidClass: '',
					})
				}
			}
		));

		// Step 4
		validations.push(FormValidation.formValidation(
			form,
			{
				fields: {
					ccname: {
						validators: {
							notEmpty: {
								message: 'Credit card name is required'
							}
						}
					},
					ccnumber: {
						validators: {
							notEmpty: {
								message: 'Credit card number is required'
							},
							creditCard: {
								message: 'The credit card number is not valid'
							}
						}
					},
					ccmonth: {
						validators: {
							notEmpty: {
								message: 'Credit card month is required'
							}
						}
					},
					ccyear: {
						validators: {
							notEmpty: {
								message: 'Credit card year is required'
							}
						}
					},
					cccvv: {
						validators: {
							notEmpty: {
								message: 'Credit card CVV is required'
							},
							digits: {
								message: 'The CVV value is not valid. Only numbers is allowed'
							}
						}
					}
				},
				plugins: {
					trigger: new FormValidation.plugins.Trigger(),
					// Bootstrap Framework Integration
					bootstrap: new FormValidation.plugins.Bootstrap({
						//eleInvalidClass: '',
						eleValidClass: '',
					})
				}
			}
		));

		// Initialize form wizard
		wizardObj = new KTWizard(wizardEl, {
			startStep: 1, // initial active step number
			clickableSteps: false  // allow step clicking
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
						wizard.goTo(wizard.getNewStep());

						KTUtil.scrollTop();
					} else {
						Swal.fire({
							text: "Sorry, looks like there are some errors detected, please try again.",
							icon: "error",
							buttonsStyling: false,
							confirmButtonText: "Ok, got it!",
							customClass: {
								confirmButton: "btn font-weight-bold btn-light"
							}
						}).then(function () {
							KTUtil.scrollTop();
						});
					}
				});
			}

			return false;  // Do not change wizard step, further action will be handled by he validator
		});

		// Change event
		wizardObj.on('changed', function (wizard) {
			KTUtil.scrollTop();
		});

		// Submit event
		wizardObj.on('submit', function (wizard) {
			Swal.fire({
				text: "All is good! Please confirm the form submission.",
				icon: "success",
				showCancelButton: true,
				buttonsStyling: false,
				confirmButtonText: "Yes, submit!",
				cancelButtonText: "No, cancel",
				customClass: {
					confirmButton: "btn font-weight-bold btn-primary",
					cancelButton: "btn font-weight-bold btn-default"
				}
			}).then(function (result) {
				if (result.value) {
					form.submit(); // Submit form
				} else if (result.dismiss === 'cancel') {
					Swal.fire({
						text: "Your form has not been submitted!.",
						icon: "error",
						buttonsStyling: false,
						confirmButtonText: "Ok, got it!",
						customClass: {
							confirmButton: "btn font-weight-bold btn-primary",
						}
					});
				}
			});
		});
	}

	var _setupFirebase = function () {


		firebase.auth().languageCode = docLang;

		firebase.auth().onAuthStateChanged(function (user) {
			if (user) {

				console.log("FB SignIn: ", user);

				let userInfo = {
					displayName: user.displayName,
					email: user.email,
					emailVerified: user.emailVerified,
					photoURL: user.photoURL,
					isAnonymous: user.isAnonymous,
					uid: user.uid,
					providerData: user.providerData
				}

				firebase.auth().currentUser.getIdToken(true).then(function (idToken) {

					userInfo.idToken = idToken;

					$.ajax({
						url: "/web/auth/signin?_t=" + Date.now(),
						type: "POST",
						dataType: "json",
						data: {
							token: idToken,
							userInfo: userInfo
						},
						async: true
					}).done(function (webResponse) {
						if (webResponse && typeof webResponse === 'object') {
							if (webResponse.errorCode == 0) {
								firebase.analytics().logEvent('auth_ok');
								window.location.href = "/web";
							} else {
								Swal.fire({
									text: webResponse.message,
									icon: "error",
									buttonsStyling: false,
									confirmButtonText: WebAppLocals.getMessage("error_confirmButtonText"),
									customClass: {
										confirmButton: "btn font-weight-bold btn-light-primary"
									}
								}).then(function () {
									KTUtil.scrollTop();
								});

							}
						}
						else {
							Swal.fire({
								text: webResponse.message,
								icon: "error",
								buttonsStyling: false,
								confirmButtonText: WebAppLocals.getMessage("error_confirmButtonText"),
								customClass: {
									confirmButton: "btn font-weight-bold btn-light-primary"
								}
							}).then(function () {
								KTUtil.scrollTop();
							});
						}
					}).fail(function (jqXHR, textStatus, errorThrown) {
						Swal.fire({
							text: WebAppLocals.getMessage("error"),
							icon: "error",
							buttonsStyling: false,
							confirmButtonText: WebAppLocals.getMessage("error_confirmButtonText"),
							customClass: {
								confirmButton: "btn font-weight-bold btn-light-primary"
							}
						}).then(function () {
							KTUtil.scrollTop();
						});
					});

				}).catch(function (error) {
					Swal.fire({
						text: error.message,
						icon: "error",
						buttonsStyling: false,
						confirmButtonText: WebAppLocals.getMessage("error_confirmButtonText"),
						customClass: {
							confirmButton: "btn font-weight-bold btn-light-primary"
						}
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

	}

	// Public Functions
	return {
		init: function () {
			_setupFirebase();

			_handleFormForgot();
			_handleFormSignup();
		},
		signIn: function () {

			_handleFormSignin();
		},
		googleSignIn: function () {
			_handleGoogleSignin();
		}
	};
}();

// Class Initialization
jQuery(document).ready(function () {
	WebAuth.init();
});