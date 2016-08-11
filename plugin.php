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

define("WPTRIPOLIS_REQUIREMENTS_FULLFILLED",version_compare(PHP_VERSION, '5.6.0', '>='));
define("WPTRIPOLIS_BASEDIR",__DIR__);

// Minimum requirements for systems (PHP version 5.6 required) for traits/short array notation
// lower than 5.4
if (!WPTRIPOLIS_REQUIREMENTS_FULLFILLED) {
	add_action('admin_notices', function() {
		?>
		<div class="error">
			<p><?php echo sprintf(__('The plugin %s requires PHP version 5.4. To avoid errors on your system, the plugin is not fully activated.','tripols'),basename(__DIR__)) ?></p>
		</div>
	<?php
	});

	return;
}

require_once('lib/files/bootstrap-front.php');

if ( is_admin() ) {
	require_once('lib/files/bootstrap-admin.php');
}

// EOF
