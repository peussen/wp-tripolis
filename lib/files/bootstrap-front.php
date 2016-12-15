<?php

// ===================================================================
// Setup here
// ===================================================================
require(WPTRIPOLIS_BASEDIR . '/vendor/autoload.php');
require(WPTRIPOLIS_BASEDIR . '/vendor/michaeluno/admin-page-framework/admin-page-framework-loader.php');

$_wptripolis 						 = [];
//$_wptripolis['shortcode'] = new \WPTripolis\WPTripolisShortcode(__FILE__);
$_wptripolis['_short']   = \WPTripolis\FormShortcode::create();
$_wptripolis['url']			 = plugins_url() . '/' . basename(WPTRIPOLIS_BASEDIR) . '/';
$_wptripolis['dir']			 = WPTRIPOLIS_BASEDIR;

// Register The Post Type
add_action('init', '\\WPTripolis\\FormPostType::register');

/**
 * @deprecated since 2.0 in favor of $_wptripolis['shortcode']
 */
//$wptripolis_shortcode  = $_wptripolis['shortcode'];

// ===================================================================
// Boring definitions here
// ===================================================================

// To not scare away all the people, have some wrappers for templates
// Setup the wrapper
//\WPTripolis\TemplateWrapper::registerInstance('shortcode',$wptripolis_shortcode);

// Load legacy alias functions
require_once(__DIR__ . '/legacy_aliases.php');

$_wptripolis['template'] = new \WPTripolis\Wordpress\Template();
$_wptripolis['template']->addSearchPath(WPTRIPOLIS_BASEDIR);
