<?php
/**
 * Plugin Name: WP-Tripolis
 * Plugin URI: http://www.tripolis.com
 * Description: A plugin to perform all kinds of actions with the <a href="http://www.tripolis.com/" target="_blank">Tripolis Dialogue System</a>. Please be aware that this plugin is not developed by Tripolis themselves, and may therefore be incomplete or lagging in functionality.
 * Version: 0.1.1
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
	require( 'lib/bootstrap.php' );	// Use a PSR-4 autoloader for our classes

	// Register the Shortcode
	$wptripolis_shortcode  = new \WPTripolis\WPTripolisShortcode(__FILE__);

	// Admin only stuff includes the "settings" page and the "Tools" page
	if ( is_admin()) {
		$wptripolis_admin = new \WPTripolis\OptionsScreen(__FILE__);	// Here be the Settings page
		$wptripolis_tool  = new \WPTripolis\WPTripolisShortcodeGenerator(__FILE__,'WP-Tripolis Generator','Generate Tripolis code'); // Here be the Shortcode generator tool
	}

	// ===================================================================
	// Boring definitions here
	// ===================================================================

	// To not scare away all the people, have some wrappers for templates
	// Setup the wrapper
	\WPTripolis\TemplateWrapper::registerInstance('shortcode',$wptripolis_shortcode);

	// Define functions
	/**
	 * Field Loop function. Initializes loop, or iterates to next field
	 *
	 * @return bool
	 */
	function wptripolis_have_fields() { return \WPTripolis\TemplateWrapper::haveFields(); }

	/**
	 * Returns the complete field definition as an array
	 *
	 * @return array|FALSE
	 */
	function wptripolis_get_field() { return \WPTripolis\TemplateWrapper::getField(); }

	/**
	 * Returns the type of field as dictated by Tripolis
	 *
	 * @return string|FALSE
	 */
	function wptripolis_get_field_type() { return \WPTripolis\TemplateWrapper::getFieldProperty('type'); }

	/**
	 * Returns an unique identifier for the field
	 *
	 * @return string|FALSE
	 */
	function wptripolis_get_field_id() { return \WPTripolis\TemplateWrapper::getFieldProperty('id'); }

	/**
	 * Returns the label as dictated by Tripolis
	 * @return string|FALSE
	 */
	function wptripolis_get_field_label() { return \WPTripolis\TemplateWrapper::getFieldProperty('label'); }

	/**
	 * Returns the field message in case of an error
	 *
	 * @return string|FALSE
	 */
	function wptripolis_get_field_message() { return \WPTripolis\TemplateWrapper::getFieldProperty('message'); }

	/**
	 * Returns the current value for the field
	 *
	 * @return string|FALSE
	 */
	function wptripolis_get_field_value() { return \WPTripolis\TemplateWrapper::getFieldProperty('value'); }

	/**
	 * Locates a template in either the theme, child-theme or plugin
	 *
	 * @param $template
	 *
	 * @return string|FALSE
	 */
	function wptripolis_find_template($template) { return \WPTripolis\TemplateWrapper::findTemplate($template); }

	/**
	 * Returns a unique identifier for the plugin
	 *
	 * @return string
	 */
	function wptripolis_plugin_name() { echo \WPTripolis\TemplateWrapper::getPluginName(); }

	/**
	 * displays the unique field ID
	 */
	function wptripolis_field_id() { echo \WPTripolis\TemplateWrapper::getFieldProperty('id'); }

	/**
	 * Displays the field label
	 */
	function wptripolis_field_label() { echo \WPTripolis\TemplateWrapper::getFieldProperty('label'); }

	/**
	 * Displays the field name
	 */
	function wptripolis_field_name() { echo \WPTripolis\TemplateWrapper::getFieldProperty('name'); }

	/**
	 * Displays any field message
	 */
	function wptripolis_field_message() { echo \WPTripolis\TemplateWrapper::getFieldProperty('message'); }

	/**
	 * Displays "required" if the field is required
	 */
	function wptripolis_field_required() { echo \WPTripolis\TemplateWrapper::getFieldProperty('required') ? 'required' : ''; }

	/**
	 * Displays a list of classes assigned to the field
	 *
	 * @param array $extra
	 */
	function wptripolis_field_classes($extra = array()) { $all = $extra + (array)\WPTripolis\TemplateWrapper::getFieldProperty('class'); echo implode(' ',$all); }

}


// EOF