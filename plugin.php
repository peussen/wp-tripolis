<?php
/**
 * Plugin Name: WP-Tripolis
 * Plugin URI: https://github.com/HarperJones/wp-tripolis
 * Description: A plugin to perform all kinds of actions with the <a href="http://www.tripolis.com/" target="_blank">Tripolis Dialogue System</a>. Please be aware that this plugin is not developed by Tripolis themselves, and may therefore be incomplete or lagging in functionality.
 * Version: 0.3.1
 * Author: Peter Eussen
 * Author URI:  http://harperjones.nl
 * License: GPLv3
 */

// Check for presence of wordpress
if ( !defined("ABSPATH")) {
	die("Not your cup of tea");
}

define("WPTRIPOLIS_REQUIREMENTS_FULLFILLED",version_compare(PHP_VERSION, '5.4.0', '>='));

// Minimum requirements for systems (PHP version 5.4 required) for traits, and 5.3 for closure's. So do not accept
// lower than 5.4
if (!WPTRIPOLIS_REQUIREMENTS_FULLFILLED) {
	add_action('admin_notices', function() {
		?>
		<div class="error">
			<p><?php echo sprintf(__('The plugin %s requires PHP version 5.4. To avoid errors on your system, the plugin is not fully activated.','tripols'),basename(__DIR__)) ?></p>
		</div>
	<?php
	});
}

// Only really load the plugin if PHP version is higher than 5.4, to avoid showing errors no one can fix
if ( WPTRIPOLIS_REQUIREMENTS_FULLFILLED ){

	global $wp_version;

	if (version_compare($wp_version,'3.9.0','<')) {
		add_action('admin_notices',function(){
			global $wp_version;
			?>
			<div class="error">
				<p><?php echo sprintf(__('The plugin %s is only tested on the 3.9 version of wordpress. You may run into problems when using this plugin with the current version of Wordpress (%s)','tripols'),basename(__DIR__),$wp_version) ?></p>
			</div>
		<?php
		});
	}

	// ===================================================================
	// Setup here
	// ===================================================================
	require( __DIR__ . '/vendor/autoload.php');
	require('vendor/michaeluno/admin-page-framework/admin-page-framework-loader.php');

	add_action('init', '\\WPTripolis\\FormPostType::register');

	$_wptripols 						 = new \Pimple\Container();
	$_wptripols['shortcode'] = new \WPTripolis\WPTripolisShortcode(__FILE__);
	$_wptripols['url']			 = plugins_url() . '/wp-tripolis/';
	$_wptripols['dir']			 = __DIR__;

	// Register the Shortcode
	/**
	 * @deprecated since 2.0 in favor of $_wptripolis['shortcode']
	 */
	$wptripolis_shortcode  = $_wptripols['shortcode'];

	// Admin only stuff includes the "settings" page and the "Tools" page
	if ( is_admin()) {
		//$_wptripols['admin_options'] 		= new \WPTripolis\OptionsScreen(__FILE__);	// Here be the Settings page
		//$_wptripols['legacy_generator']	= new \WPTripolis\WPTripolisShortcodeGenerator(__FILE__,'WP-Tripolis Generator','Generate Tripolis code'); // Here be the Shortcode generator tool
		$_wptripols['admin']			      = new \WPTripolis\Administration();

		/**
		 * @deprecated since 2.0 in favor of $_wptripolis['legacy_generator']
		 */
		//$wptripolis_tool  = $_wptripols['legacy_generator'];
	}

	// ===================================================================
	// Boring definitions here
	// ===================================================================

	// To not scare away all the people, have some wrappers for templates
	// Setup the wrapper
	\WPTripolis\TemplateWrapper::registerInstance('shortcode',$wptripolis_shortcode);

	require_once(__DIR__ . '/lib/files/legacy_aliases.php');
}


// EOF
