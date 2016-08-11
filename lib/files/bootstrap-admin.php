<?php

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


//		$_wptripolis['admin_options'] 		= new \WPTripolis\OptionsScreen(__FILE__);	// Here be the Settings page
//		$_wptripolis['legacy_generator']	= new \WPTripolis\WPTripolisShortcodeGenerator(__FILE__,'WP-Tripolis Generator','Generate Tripolis code'); // Here be the Shortcode generator tool
$_wptripolis['admin']			      = new \WPTripolis\Administration();

/**
 * @deprecated since 2.0 in favor of $_wptripolis['legacy_generator']
 */
//$wptripolis_tool  = $_wptripolis['legacy_generator'];

// Meta box setup
\WPTripolis\Admin\FormMetaBox::boot();