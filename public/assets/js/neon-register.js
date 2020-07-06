/**
 *	Neon Register Script
 *
 *	Developed by Arlind Nushi - www.laborator.co
 */

var neonRegister = neonRegister || {};

;(function($, window, undefined)
{
	"use strict";
	
	$(document).ready(function()
	{
		neonRegister.$container = $("#form_register");
		
		neonRegister.$container.validate({
			rules: {
				name: {
					required: true
				},
				
				email: {
					required: true,
					email: true
				},
				password: {
					required: true
				},
				
			},
			
			messages: {
				
				email: {
					email: 'Invalid E-mail.'
				}	
			},
			
			highlight: function(element){
				$(element).closest('.input-group').addClass('validate-has-error');
			},
			
			
			unhighlight: function(element)
			{
				$(element).closest('.input-group').removeClass('validate-has-error');
			},
			
			submitHandler: function(ev)
			{
				$(".login-page").addClass('logging-in');
				
				// We consider its 30% completed form inputs are filled
				neonRegister.setPercentage(30, function()
				{
					// Lets move to 98%, meanwhile ajax data are sending and processing
					neonRegister.setPercentage(98, function()
					{
						// Send data to the server
						$.ajax({
							headers: {'X-CSRF-Token': Laravel.csrfToken},
							url: baseurl + 'register',
							method: 'POST',
							dataType: 'json',
							data: {
								name: 		$("input#name").val(),
								email: 		$("input#email").val(),
								password:	$("input#password").val()
							},
							error: function()
							{
								alert("An error occoured!");
							},
							success: function(response)
							{
								
								var register_status = response.register_status;
								
								// Form is fully completed, we update the percentage
								neonRegister.setPercentage(100);
								
								
								// We will give some time for the animation to finish, then execute the following procedures	
								setTimeout(function()
								{

									// If login is invalid, we store the 
									if(register_status == 'invalid')
									{
										$(".login-page").removeClass('logging-in');
										neonRegister.resetProgressBar(true, response.register_error);
									}
									else
									if(register_status == 'success')
									{
										// Redirect to login page
										setTimeout(function()
										{
											var redirect_url = baseurl;

											if(response.redirect_url && response.redirect_url.length)
											{
												redirect_url = response.redirect_url;
											}

											window.location.href = redirect_url;
										}, 400);
									}
									
								}, 1000);
							}
						});
					});
				});
			}
		});

		// Login Form Setup
		neonRegister.$body = $(".login-page");
		neonRegister.$login_progressbar_indicator = $(".login-progressbar-indicator h3");
		neonRegister.$login_progressbar = neonRegister.$body.find(".login-progressbar div");
		
		neonRegister.$login_progressbar_indicator.html('0%');
		
		if(neonRegister.$body.hasClass('login-form-fall'))
		{
			var focus_set = false;
			
			setTimeout(function(){ 
				neonRegister.$body.addClass('login-form-fall-init')
				
				setTimeout(function()
				{
					if( !focus_set)
					{
						neonRegister.$container.find('input:first').focus();
						focus_set = true;
					}
					
				}, 550);
				
			}, 0);
		}
		else
		{
			neonRegister.$container.find('input:first').focus();
		}
		
		
		// Functions
		$.extend(neonRegister, {
			setPercentage: function(pct, callback)
			{
				pct = parseInt(pct / 100 * 100, 10) + '%';
				
				// Normal Login
				neonRegister.$login_progressbar_indicator.html(pct);
				neonRegister.$login_progressbar.width(pct);
				
				var o = {
					pct: parseInt(neonRegister.$login_progressbar.width() / neonRegister.$login_progressbar.parent().width() * 100, 10)
				};
				
				TweenMax.to(o, .7, {
					pct: parseInt(pct, 10),
					roundProps: ["pct"],
					ease: Sine.easeOut,
					onUpdate: function()
					{
						neonRegister.$login_progressbar_indicator.html(o.pct + '%');
					},
					onComplete: callback
				});
			},
			resetProgressBar: function(display_errors, error_text)
			{
				TweenMax.set(neonRegister.$container, {css: {opacity: 0}});

				setTimeout(function()
				{
					TweenMax.to(neonRegister.$container, .6, {css: {opacity: 1}, onComplete: function()
					{
						neonRegister.$container.attr('style', '');
					}});

					neonRegister.$login_progressbar_indicator.html('0%');
					neonRegister.$login_progressbar.width(0);

					if(display_errors)
					{
						var $errors_container = $(".form-login-error");

						$(".form-login-error h3").text(error_text);

						$errors_container.show();
						var height = $errors_container.outerHeight();

						$errors_container.css({
							height: 0
						});

						TweenMax.to($errors_container, .45, {css: {height: height}, onComplete: function()
						{
							$errors_container.css({height: 'auto'});
						}});

						// Reset password fields
						neonRegister.$container.find('input[type="password"]').val('');
					}

				}, 800);
			}
		});
	});
	
})(jQuery, window);