<?php
/**
 * Plugin page essentials class
 *
 * This class is used at the top of every plugin page script.
 * Its abilities are central to the structing and security of
 * each page. Its abilities include:
 *  - user access control
 *  - provide quick access to the current user's information
 *  - importing necessary PHP scripts
 *  - setting the page title
 *  - including PHP, CSS, or JS files
 *  - adding HTML to the <head> section of a page
 *  - integrating with the Interception_Manager class to make
 *    data avaliable from custom, SEO-friendly URLs
 * 
 * @author    Oliver Spryn
 * @copyright Copyright (c) 2013 and Onwards, ForwardFour Innovations
 * @license   MIT
 * @namespace FFI\TA
 * @package   lib
 * @since     1.0
*/

namespace FFI\TA;

class Essentials {
/**
 * Hold a private reference to the requests for the CSS stylesheets for 
 * the filter hook to grab.
 *
 * @access private
 * @type   array<string>
*/

	private $CSS = array();
	
/**
 * Hold a private reference to the requests for the JS scriptss for the 
 * filter hook to grab.
 *
 * @access private
 * @type   array<string>
*/

	private $JS = array();
	
/**
 * Hold a private reference to the HTML to add to the <head> section of a
 * page.
 *
 * @access private
 * @type   array<string>
*/

	private $HTML = "";
	
/**
 * Hold a reference to the parameters from the URL fetched by 
 * Interception_Manager::registerException()
 *
 * @access private
 * @type   boolean|array<string>
*/
	
	public $params = false;
	
/**
 * Hold a private reference to the title of the page for the filter
 * hook to grab.
 *
 * @access private
 * @type   string
*/

	private $title;
	
/**
 * Hold the current user's information
 *
 * @access public
 * @type   boolean|object
*/
	
	public $user = false;
	
/**
 * CONSTRUCTOR
 *
 * Share any parameters from the URL fetched by 
 * Interception_Manager::registerException() with the rest of
 * the class. Also, register an action hook with wp_head() to
 * allow HTML to be added to the <head> section of a webpage.
 * 
 * @access public
 * @param  boolean|array<string> $params An array of parameters from the URL fetched by Interception_Manager::registerException(), or false if none
 * @return void
 * @since  1.0
*/

	public function __construct($params) {
		$this->params = $params;
		add_action("wp_head", array($this, "actionHookIncludeHTML"));
	}
	
/**
 * The method called by Wordpress to include the CSS in the HTML page.
 * 
 * @access public
 * @return void
 * @since  1.0
*/
	
	public function actionHookIncludeCSS($CSS) {		
		for($i = 0; $i < count($this->CSS); ++$i) {
			$styleName = "STYLE_ID_" . mt_rand();
			
		//Local stylesheets will need their address modified
		//The address for external stylesheets begin with "//"
			if (substr($this->CSS[$i], 0, 2) != "//") {
				$this->CSS[$i] = REAL_ADDR . "app/system/styles/" . $this->CSS[$i];
			}
			
			wp_register_style($styleName, $this->CSS[$i], array(), NULL); //NULL removes the ?ver from the URL
			wp_enqueue_style($styleName);
		}
	}
	
/**
 * The method called by Wordpress to include the HTML in the <head> 
 * section of a page.
 * 
 * @access public
 * @return void
 * @since  1.0
*/
	
	public function actionHookIncludeHTML() {
		echo $this->HTML . ($this->HTML != "" ? "\n" : "");
	}
	
/**
 * The method called by Wordpress to include the JS in the HTML page.
 * 
 * @access public
 * @return void
 * @since  1.0
*/
	
	public function actionHookIncludeJS() {
		for($i = 0; $i < count($this->JS); ++$i) {
			$styleName = "SCRIPT_ID_" . mt_rand();
			
		//Local scripts will need their address modified
		//The address for external scripts begin with "//"
			if (substr($this->JS[$i], 0, 2) != "//") {
				$this->JS[$i] = REAL_ADDR . "app/system/scripts/" . $this->JS[$i];
			}
			
			wp_register_script($styleName, $this->JS[$i], array(), NULL); //NULL removes the ?ver from the URL
			wp_enqueue_script($styleName);
		}
	}
	
/**
 * The method called by Wordpress to set the <title> of the HTML page.
 * 
 * @access public
 * @return void
 * @since  1.0
*/
	
	public function actionHookSetTitle($title) {
		return $this->title;
	}
	
/**
 * This method will take a URL relative to the plugin's "data"
 * folder and append the actual physical address to this file.
 * So a request such as "info/images/bkg.jpg" would rewrite
 * the URL like so: .../<plugin-name>/data/info/images/bkg.jpg.
 *
 * @access public
 * @param  string   $address The URL with respect to the "data" folder
 * @return string   $address The normalized version of the given URL
 * @since  1.0
*/

	public function dataURL($address) {
		return REAL_ADDR . "data/" . $address;
	}
	
/**
 * This method will take a URL relative with respect to the "app" 
 * folder and give it an absolute URL with respect to the friendly 
 * URL of this plugin. So a request such as "subpage/details.php" 
 * would rewrite the URL like so: 
 * //<wordpress-site>/<plugin-name>/listings/details.php
 *
 * @access public
 * @param  string   $address The URL with respect to the "app" folder
 * @return string   $address The friendly version of the given URL
 * @since  1.0
*/

	public function friendlyURL($address) {
		return FAKE_ADDR . $address;
	}
	
/**
 * Include the requested stylesheet in the <head> section of the page.
 * Local stylesheets are requested with respect to the "app/system/styles"
 * folder. So a request such as "style.css" would include the stylesheet
 * like so: .../<plugin-name>/app/system/styles/style.css, regardless of
 * the address of the PHP file which requested the script.
 *
 * External stylesheets must be prefixed with a "//" for this class to 
 * know the request is for an external CSS stylesheet.
 * 
 * Since this method may be called multiple times, each address must be 
 * stored in the $this->CSS variable, since the stylesheet isn't added 
 * right away, but during the construction of the <head> section of the 
 * template. These addresses are stored in an array and are later added
 * to the template in the order they were requested.
 *
 * @access public
 * @param  string   $address The URL of the external stylesheet or the URL with respect to the "app/system/styles" folder
 * @return void
 * @since  1.0
*/

	public function includeCSS($address) {
	//Store this address for later
		array_push($this->CSS, $address);
		
		add_action("wp_print_styles", array($this, "actionHookIncludeCSS"));
	}
	
/**
 * Add HTML to the <head> section of a page. This could be most useful
 * for including content such as custom <meta> tags in the head section
 * of a page.
 *
 * @access public
 * @return void
 * @since  1.0
*/
	
	public function includeHeadHTML($HTML) {
		$this->HTML .= $HTML;
	}
	
/**
 * Include the requested script in the <head> section of the page. Local 
 * scripts are requested with respect to the "app/system/scripts" folder.
 * So a request such as "script.js" would include the script like so:
 * .../<plugin-name>/app/system/scripts/script.js, regardless of the 
 * address of the PHP file which requested the script.
 *
 * External scripts must be prefixed with a "//" for this class to 
 * know the request is for an external JS file.
 * 
 * Since this method may be called multiple times, each address must be 
 * stored in the $this->JS variable, since the script isn't added 
 * right away, but during the construction of the <head> section of the 
 * template. These addresses are stored in an array and are later added
 * to the template in the order they were requested.
 *
 * @access public
 * @param  string   $address The URL of the external script or the URL with respect to the "app/system/scripts" folder
 * @return void
 * @since  1.0
*/

	public function includeJS($address) {
	//Store this address for later
		array_push($this->JS, $address);
		
		add_action("wp_enqueue_scripts", array($this, "actionHookIncludeJS"));
	}
	
/**
 * Include the requested PHP script with respect to the app folder.
 * So a request like this "lib/processing/Validate.php" will include
 * the script like so: .../<plugin-name>/app/lib/processing/Validate.php,
 * regardless of the address of the PHP file which requested the script.
 *
 * This method uses the "require_once()" function to import the 
 * script.
 *
 * @access public
 * @param  string   $address The of the PHP script URL with respect to the "app" folder
 * @return void
 * @since  1.0
*/

	public function includePHP($address) {
		require_once(PATH . "app/" . $address);
	}
	
/**
 * Include the requested PHP class script with respect to the includes
 * folder. So a request like this "processing/Validate" will include the 
 * class script like so: .../<plugin-name>/lib/processing/Validate.php,
 * regardless of the address of the PHP file which requested the script.
 *
 * This method uses the "require_once()" function to import the 
 * script.
 *
 * @access public
 * @param  string   $class The name of of the PHP plugin class to import
 * @return void
 * @since  1.0
*/

	public function includePluginClass($class) {
		require_once(PATH . "lib/" . $class . ".php");
	}
	
/**
 * This method will take a URL relative to the plugin's "app"
 * folder and append the actual physical address to this file.
 * So a request such as "system/images/bkg.jpg" would rewrite
 * the URL like so: .../<plugin-name>/app/system/images/bkg.jpg.
 *
 * If this plugin is configured to use a CDN, the appended URL
 * will point to a CDN. A request such as "system/images/bkg.jpg"
 * would rewrite the URL like so:
 * //<wordpress-site or CDN>/.../<plugin-name>/app/system/images/
 * bkg.jpg
 *
 * This function is probably best used for URLs pointing to items
 * such as images, downloadable file, or other content which would
 * typically be stored on a CDN
 *
 * @access public
 * @param  string   $address The URL with respect to the "app" folder
 * @return string   $address The normalized version of the given URL
 * @since  1.0
*/
	
	public function normalizeURL($address) {
		 return (CDN ? RESOURCE_PATH : REAL_ADDR) . "app/" . $address;
	}

/**
 * Check if the user is logged in. If so, then grant access to 
 * this page, otherwise, redirect to the login page.
 *
 * This method will also obtain access the the current user's 
 * information, if they are logged in.
 * 
 * @access public
 * @return void
 * @since  1.0
*/

	public function requireLogin() {
		if (!is_user_logged_in()) {
			wp_redirect(get_site_url() . "/wp-login.php?redirect_to=" . urlencode($_SERVER['REQUEST_URI']));
			exit;
		} else {
			global $current_user;
			get_currentuserinfo();
			
			$this->user = $current_user;
		}
	}
	
/**
 * Set the <title> of the HTML page.
 * 
 * @access public
 * @param  string   $title The title of the HTML page
 * @return void
 * @since  1.0
*/

	public function setTitle($title) {
		$this->title = $title;
		
		add_filter("wp_title", array($this, "actionHookSetTitle"));
	}
	
/**
 * This method will obtain access the the current user's information, 
 * if they are logged in.
 * 
 * @access public
 * @return boolean  Whether or not the user's information could be obtained, based on their login status
 * @since  1.0
*/
	
	public function storeUserInfo() {
		if (is_user_logged_in()) {
			global $current_user;
			get_currentuserinfo();
			
			$this->user = $current_user;
			return true;
		} else {
			return false;
		}
	}
}
?>