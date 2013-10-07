/**
 * Travel Assistant Trip fulfillment plugin
 *
 * This plugin will be used on the trip needed and available pages
 * and will be triggered whenever the "I Can Help" or "I Need this Ride"
 * buttons are pressed. This plugin will create a confirmation dialog
 * for the user to confirm his or her choice to provide or request 
 * a ride. This request will then be sent to the server for processing.
 *
 * @author    Oliver Spryn
 * @copyright Copyright (c) 2013 and Onwards, ForwardFour Innovations
 * @license   MIT
 * @namespace FFI_TA
 * @since     1.0
*/

(function($) {
	$.fn.FFI_TA_Trip = function(options) {
	//Merge the passed options with defaults
		$.extend($.fn.FFI_TA_Trip.defaults, options);
		
		return this.click(function() {
			if (!$(this).hasClass('disabled')) {
			//The button which triggered the event
				$.fn.FFI_TA_Trip.button = $(this);
				
			//Several properties of the button
				$.fn.FFI_TA_Trip.ID = $.fn.FFI_TA_Trip.button.attr('data-id');
				$.fn.FFI_TA_Trip.mode = $.fn.FFI_TA_Trip.button.attr('data-mode');
				$.fn.FFI_TA_Trip.total = parseInt($.fn.FFI_TA_Trip.button.attr('data-total'));
				$.fn.FFI_TA_Trip.user = $.fn.FFI_TA_Trip.htmlEntitiesDecode($.fn.FFI_TA_Trip.button.attr('data-name'));
				
			//The modal dialog and some of its components
				$.fn.FFI_TA_Trip.comments = null;
				$.fn.FFI_TA_Trip.modal = null;
				$.fn.FFI_TA_Trip.password = null;
				$.fn.FFI_TA_Trip.submit = null;
				$.fn.FFI_TA_Trip.username = null;
				$.fn.FFI_TA_Trip.validationPrompt = null;
				
			//Bootstrap this plugin by calling its instance methods
				$.fn.FFI_TA_Trip.buildDialog();
				$.fn.FFI_TA_Trip.submitHandler();
			}
		});
	}
	
/**
 * Build the confirmation modal dialog
 * 
 * @access public
 * @return void
 * @since  1.0
*/
	
	$.fn.FFI_TA_Trip.buildDialog = function() {
	//Generate the dialog HTML
		var HTML = '<div class="modal hide fade trip-dialog" role="dialog">';
		HTML += '<div class="modal-header">';
		HTML += '<button type="button" class="close" data-dismiss="modal">×</button>';
		HTML += '<h3>' + ($.fn.FFI_TA_Trip.mode == 'assist' ? 'Provide Ride' : 'Request Ride') + '</h3>';
		HTML += '</div>';
		HTML += '<div class="modal-body trip-details">';
		
		if ($.fn.FFI_TA_Trip.mode == 'assist') {
			HTML += '<p>You are about to assist ' + $.fn.FFI_TA_Trip.user + ($.fn.FFI_TA_Trip.total > 1 ? ' and ' + ($.fn.FFI_TA_Trip.total - 1) + ' others on their' : ' on a') + ' trip. Please <strong>carefully examine the information on this page</strong> and make sure you can fulfill all of the trip\'s requirements before comfirming your decision.</p>';
		} else {
			HTML += '<p>You are about to request 1 seat on a ride provided by ' + $.fn.FFI_TA_Trip.user + '. Please <strong>carefully examine the information on this page</strong> and make sure this trip fulfills all of your requirements before comfirming your decision.</p>';
		}
		
		HTML += '</div>';
		
		if ($.fn.FFI_TA_Trip.defaults.showComments) {
			HTML += '<div class="modal-body comments">';
			HTML += '<h4>Share comments with ' + $.fn.FFI_TA_Trip.user + ' (optional):</h4>';
			HTML += '<textarea name="content" />';
			HTML += '</div>';
		}
		
		if ($.fn.FFI_TA_Trip.defaults.showLogin) {
			if ($.fn.FFI_TA_Trip.placeholderSupported()) {
				HTML += '<div class="modal-footer login">';
			} else {
				HTML += '<div class="modal-footer login fallback">';
				HTML += '<h4 class="placeholder">Username &amp; password:</h4>';
			}
		
			HTML += '<input class="username" placeholder="Username" type="text">';
			HTML += '<input class="password" placeholder="Password" type="password">';
			HTML += '</div>';
		}
		
		HTML += '<div class="modal-footer">';
		HTML += '<span class="validate"></span>';
		HTML += '<button class="btn btn-warning confirm">' + ($.fn.FFI_TA_Trip.defaults.showLogin ? 'Login &amp;' : '') + ' Confirm</button>';
		HTML += '<button class="btn close-dialog" data-dismiss="modal">Close</button>';
		HTML += '</div>';
		HTML += '</div>';

	//Create the Twitter Bootstrap dialog
		$.fn.FFI_TA_Trip.modal = $(HTML);
		
		$.fn.FFI_TA_Trip.modal.on('shown', function() {
			if ($.fn.FFI_TA_Trip.defaults.showComments) {
				$.fn.FFI_TA_Trip.comments = $.fn.FFI_TA_Trip.modal.find('textarea');

				if ($.fn.FFI_TA_Trip.comments.parent().is(':visible')) {
					tinymce.init({
						selector: 'textarea',
						plugins: [
						     'autolink contextmenu image link table'
					    ],
						menubar: false,
						statusbar: false,
						toolbar: false
					});
			
					tinymce.activeEditor.focus();
				}
			}
		}).modal();
		
	//Share a few components of the dialog with the plugin
		if ($.fn.FFI_TA_Trip.defaults.showLogin) {
			$.fn.FFI_TA_Trip.password = $.fn.FFI_TA_Trip.modal.find('input.password');
			$.fn.FFI_TA_Trip.username = $.fn.FFI_TA_Trip.modal.find('input.username');
		}
		
		$.fn.FFI_TA_Trip.submit = $.fn.FFI_TA_Trip.modal.find('button.confirm');
		$.fn.FFI_TA_Trip.validationPrompt = $.fn.FFI_TA_Trip.modal.find('span.validate');
	}
	
/**
 * Determine whether or not the user's browser supports 
 * the "placeholder" attribute on input elements.
 *
 * @access public
 * @return bool   Whether the user's browser supports placeholders
 * @since  1.0
*/

	$.fn.FFI_TA_Trip.placeholderSupported = function() {
		var test = document.createElement('input');
		return ('placeholder' in test);
	}
	
/**
 * Validate the user's input (if any) and submit the request to 
 * the server. The user will be alerted of any errors which the
 * server encounters during validation and purchase processing.
 *
 * @access public
 * @return void
 * @since  1.0
*/

	$.fn.FFI_TA_Trip.submitHandler = function() {
		$.fn.FFI_TA_Trip.submit.click(function() {
		//Validate the login (if available)
			if ($.fn.FFI_TA_Trip.defaults.showLogin && !$.fn.FFI_TA_Trip.validate()) {
				$.fn.FFI_TA_Trip.msg('Please enter your login credentials');
				return;
			}
			
		//Clear the dialog's message alert
			$.fn.FFI_TA_Trip.clearMsg();
			
		//Disable the submit button
			$.fn.FFI_TA_Trip.submit.attr('disabled', 'disabled').addClass('disabled').html('Please wait...');
			
		//Save the TinyMCE content to the textarea
			tinymce.activeEditor.save();
			
		//Generate the POST data object for the purchase request
			var POST = {
				'id'   : $.fn.FFI_TA_Trip.ID,
				'mode' : $.fn.FFI_TA_Trip.mode
			};
			
			if ($.fn.FFI_TA_Trip.defaults.showComments) {
				POST.comments = $.fn.FFI_TA_Trip.comments.val();
			}
			
			if ($.fn.FFI_TA_Trip.defaults.showLogin) {
				POST.username = $.fn.FFI_TA_Trip.username.val();
				POST.password = $.fn.FFI_TA_Trip.password.val();
			}
			
		//Send the process request to the server
			$.ajax({
				'data' : POST, 
				'type' : 'POST',
				'url' : $.fn.FFI_TA_Trip.defaults.processURL,
				'success' : function(data) {
				//If the transaction was successful, close the dialog and disable the purchase button
					if (data == 'success') {
					//Hide the modal dialog
						$.fn.FFI_TA_Trip.modal.modal('hide');
						
					//Update the UI of the button which triggered the event to indicate that the action is complete
						$.fn.FFI_TA_Trip.button.attr('disabled', 'disabled').addClass('disabled').html('Request Sent');
						
					//Update the user's login status
						if ($.fn.FFI_TA_Trip.defaults.showLogin) {
							$.fn.FFI_TA_Trip.defaults.showLogin = false;
						}
						
					//Create a success message
						var message = $('<span class="success"/>');
						message.appendTo('body').text('Your request is on its way! Keep an eye on your inbox. ;-)');
						
						setTimeout(function() {
							message.fadeOut(500, function() {
								message.remove();
							});
						}, 5000);
					} else {
					//Show the message from the server
						$.fn.FFI_TA_Trip.msg(data);
						
					//Restore the submit button
						$.fn.FFI_TA_Trip.submit.removeAttr('disabled').removeClass('disabled').html(($.fn.FFI_TA_Trip.defaults.showLogin ? 'Login &amp;' : '') + ' Confirm');
					}
				}
			});
		});
	}
	
/**
 * Validate the user's username and password. This method will not
 * validate whether or not they are correct, but if they have been
 * filled in.
 *
 * @access public
 * @return bool   Whether or not both the username and password have been provided
 * @since  1.0
*/

	$.fn.FFI_TA_Trip.validate = function() {
		return $.fn.FFI_TA_Trip.username.val() != '' && $.fn.FFI_TA_Trip.password.val() != '';
	};
	
/**
 * Update the value of the dialog's message alert
 *
 * @access public
 * @param  string text The text to fill in as the dialog's validation message
 * @return void
 * @since  1.0
*/

	$.fn.FFI_TA_Trip.msg = function(text) {
	//Populate the message alert with text
		$.fn.FFI_TA_Trip.validationPrompt.text(text);
		
	//Only after it contains text can jQuery evaluate whether or not it is visible (responsive CSS styles may hide it)
		if ($.fn.FFI_TA_Trip.validationPrompt.is(':hidden')) {
			alert(text);
		}
	};
	
/**
 * Clear the dialog's message alert
 *
 * @access public
 * @return void
 * @since  1.0
*/

	$.fn.FFI_TA_Trip.clearMsg = function() {
		$.fn.FFI_TA_Trip.validationPrompt.text('');
	};
	
/**
 * Decode all applicable characters from HTML entities
 *
 * @access public
 * @param  string input The string to be decoded from HTML entities
 * @return string       The input string decoded from HTML entities
 * @since  1.0
*/

	$.fn.FFI_TA_Trip.htmlEntitiesDecode = function(input) {
		return $('<div/>').html(input).text();
	};
	
/**
 * Plugin default settings
 *
 * @access public
 * @type   object<bool|string>
*/

	$.fn.FFI_TA_Trip.defaults = {
		processURL   : document.location.href.substring(0, document.location.href.indexOf('travel-assistant')) + 'wp-content/plugins/travel-assistant/app/includes/ajax/trip.php',
		showComments : true,  //Whether or not to show the comments section
		showLogin    : true   //Whether or not to show the login section
	};
})(jQuery)