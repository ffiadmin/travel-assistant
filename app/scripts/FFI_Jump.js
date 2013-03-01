/**
 * HTML select menu URL jumper
 *
 * This plugin will read the value of a given option in an HTML
 * <select> menu and navigate to the URL listed as the value when
 * a submitter button has been clicked.
 *
 * @author    Oliver Spryn
 * @copyright Copyright (c) 2013 and Onwards, ForwardFour Innovations
 * @license   MIT
 * @namespace FFI
 * @since     v1.0 Dev
*/

(function($){
	$(function() {		
		$.fn.FFI_Jump = function(options) {
		//Allow the user to override default options
			var opts = $.extend($.fn.FFI_Jump.defaults, options);
			
		//Select the submitter button
			var submitter = $($.fn.FFI_Jump.defaults.submitter);
			
		//Check to see if the URL has a trailing slash and build the URL accordingly
			var location = document.location.href;
			
			if (location.substr(-1) != '/') {
				location += '/';
			}
			
			return this.each(function() {
				var menu = $(this);
				
			//Navigate to the URL in the menu item, when the submitter button is clicked
				submitter.click(function() {
					if (menu.val() != '') {
						document.location.href = location + menu.val();
					}
				});
			});
		};
		
/**
 * The plugin settings
 *
 * @access public
 * @since  v1.0 Dev
 * @type   object 
*/
		$.fn.FFI_Jump.defaults = {
			'submitter' : '#jumper'
		};
    });	
})(jQuery)