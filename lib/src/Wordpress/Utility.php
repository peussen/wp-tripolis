<?php
/**
 *
 * @package WP-Tripolis
 * @author  Peter Eussen <peter.eussen@harperjones.nl>
 */

namespace WPTripolis\Wordpress;


/**
 * Wrapper for creating a utility page
 *
 * @package WPTripolis\Wordpress
 */
abstract class Utility
{
	/**
	 * The menu title
	 *
	 * @var string
	 */
	protected $menuTitle;

	/**
	 * The page title
	 * @var string
	 */
	protected $pageTitle;

	/**
	 * The Unique slug used
	 *
	 * @var string
	 */
	protected $slug;

	/**
	 * The plugin name
	 *
	 * @var string
	 */
	protected $plugin;

	/**
	 * The base plugin file
	 *
	 * @var string
	 */
	protected $pluginBase;

	/**
	 * Registers a utility page under "Tools"
	 *
	 * @param string $pluginFile
	 * @param string $menutitle
	 * @param string $pagetitle
	 */
	public function __construct($pluginFile,$menutitle,$pagetitle)
	{
		$this->menuTitle = $menutitle;
		$this->pageTitle = $pagetitle;
		$this->pluginBase= plugin_basename($pluginFile);
		$this->plugin    = dirname($this->pluginBase);
		$this->slug      = strtolower(str_replace('\\','',get_class($this)));

		add_action('admin_menu',array($this,'registerUtilityPage'));
	}

	/**
	 * Registers the page
	 * You can alter the capability needed to view the page by adding a filter on <plugin>-utility-capability.
	 * All other fields are supplied by means of construction and should need no modification. if you need
	 * to add more hooks, you can override this method.
	 *
	 */
	public function registerUtilityPage()
	{
		add_submenu_page('tools.php',$this->pageTitle,$this->menuTitle,apply_filters($this->plugin . '-utility-capability','publish_pages'),$this->slug,array($this,'render'));
		//add_utility_page($this->pageTitle,$this->menuTitle,'publish_pages',$this->slug,array($this,'render'));
	}

	/**
	 * You should implement this page to render the actual utility page
	 *
	 * @return
	 */
	abstract function render();
} 