<?php
/**
 *
 * @package WP-Tripolis
 * @author  Peter Eussen <peter.eussen@harperjones.nl>
 */

namespace WPTripolis\Wordpress;

/**
 * Utility class to handle shortcode creation and handling
 *
 * @package WPTripolis\Wordpress
 */
abstract class Shortcode
{
	/**
	 * The shortcode Tag that will be used for this shortcode, derived from plugin
	 *
	 * @var mixed|null
	 */
	protected $tag;

	/**
	 * plugin_basename()-ed base file of the plugin
	 * @var string
	 */
	protected $pluginFile;

	/**
	 * The plugin name
	 *
	 * @var string
	 */
	protected $plugin;

	/**
	 * Initialises the shortcode, and registers it with wordpress
	 *
	 * @param string $pluginFile
	 * @param string $tag
	 */
	public function __construct($pluginFile, $tag = null)
	{
		if ( $tag === null ) {
			$tag = str_replace('\\','-',strtolower(__NAMESPACE__));
		}
		$this->tag        = $tag;
		$this->pluginFile = $pluginFile;
		$this->plugin     = basename(dirname($pluginFile));

		add_action('init',array($this,'register'));
	}

	/**
	 * Return the name of the plugin
	 *
	 * @return string
	 */
	public function getPluginName()
	{
		return $this->plugin;
	}

	/**
	 * Method called on 'init' to actually register the shortcode
	 *
	 */
	public function register()
	{
		add_shortcode($this->tag,array($this,'captureRender'));

		// As per (@link http://stephanieleary.com/2010/02/using-shortcodes-everywhere/)
		add_filter( 'widget_text', 'shortcode_unautop');
		add_filter( 'widget_text', 'do_shortcode');
	}

	/**
	 * Captures the shortcode render() output and returns the output for wordpress to place
	 *
	 * @param $attr
	 *
	 * @return string
	 */
	public function captureRender($attr)
	{
		if ( isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
			$attr['submit'] = $this->handleFormSubmit($attr,$_POST);
		}

		ob_start();
		$this->render($attr);
		return ob_get_clean();
	}

	/**
	 * Generates the HTML for the shortcode.
	 *
	 * Please be advised, that this function does not need to worry about return/output.
	 * All output will be captured and returned as the value of the shortcode by the wrapper.
	 *
	 * @param $attr
	 *
	 * @return void
	 */
	abstract public function render($attr);


	/**
	 * If the page was submitted, this method will be called so you can check if your shortcode need to take action
	 *
	 * @param array $attr
	 * @param array $postData
	 *
	 * @return bool
	 */
	protected function handleFormSubmit($attr, $postData)
	{
		return true;
	}

	/**
	 * Locates a template in theme or plugin directory
	 * Will look in the themes first, but if not found there, it will look in the current plugin 'templates' directory
	 *
	 * @param $template
	 *
	 * @return string
	 */
	public function findTemplate($template)
	{
		$file = locate_template($this->plugin. '/' . $template);

		if ( '' === $file ) {
			$pluginTemplate = dirname($this->pluginFile) . '/templates/' . $template;

			if ( file_exists($pluginTemplate)) {
				return $pluginTemplate;
			}
		}
		return $file;
	}

	/**
	 * Tries to generate a unique ID for this shortcode.
	 *
	 * Currently the ID is only unique per post. So if you have more than one shortcode in the post
	 *
	 * @todo factor in widget ID in here somewhere
	 *
	 * @return string
	 */
	protected function getUniqueId()
	{
		$id = get_the_id();
		return $this->plugin . $id;
	}
}