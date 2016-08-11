<?php
/**
 *
 * @package WP-Tripolis
 * @author  Peter Eussen <peter.eussen@harperjones.nl>
 */

namespace WPTripolis\Wordpress;

/**
 * Class AdminSettings
 * Wraps the creation of an Admin Options/settings page, by taking away some of the common pains.
 * When implementing your own option page, you will need to extend this class and fill in the blanks.
 * Implementation is based on the Codex Ref (http://codex.wordpress.org/Creating_Options_Pages).
 *
 * This class will register the page, in the Settings section as well as adding a link to the plugins
 * page. It also handles the basic rendering of the options page, leaving the configuration of the
 * page to you, as well as any sanitizing you may need to do.
 *
 * It takes its hints for slugs etc from the Plugin description. So make sure those are correct!
 *
 * @package WPTripolis\Wordpress
 * @deprecated
 */
abstract class AdminSettings
{
	/**
	 * The name of the plugin we are creating a settings page for
	 * @var string
	 */
	protected $plugin;

	/**
	 * The core plugin file
	 * @var string
	 */
	protected $pluginFile;

	/**
	 * The settings as found in the core plugin file.
	 * @var array
	 */
	protected $defaults = array();

	/**
	 * The ID used to store all your settings
	 *
	 * @var string
	 */
	protected $optionId;

	/**
	 * The group ID of all settings
	 * @var
	 */
	protected $groupId;

	/**
	 * All form sections
	 *
	 * @var array
	 */
	protected $sections = array();

	/**
	 * The stored settings
	 *
	 * @var array
	 */
	protected $data = array();

	/**
	 * Base initialisation
	 * You will probably need to pass __FILE__ as parameter
	 *
	 * @param $pluginFile
	 */
	public function __construct($pluginFile)
	{
		$this->optionId   = strtolower(str_replace('\\','_',get_class($this)));
		$this->pluginFile = $pluginFile;
		$this->plugin     = basename(dirname($pluginFile));
		add_action( 'admin_menu', array( $this, 'registerAdminMenu' ) );
		add_action( 'admin_init', array( $this, 'initializeAdminPage' ) );
		add_filter( 'plugin_action_links_'. plugin_basename($this->pluginFile), array($this,'addSettingsLinkToPlugin'));
	}

	/**
	 * Overrideable config for the menu.
	 * If you wish to have your own naming scheme, override this method. Make sure you set
	 * the following options:
	 * - Name
	 * - menu_slug
	 *
	 * @return array
	 */
	function getMenuConfig()
	{
		$this->defaults = \get_plugin_data($this->pluginFile);
		return array();
	}

	/**
	 * Returns the configuration of the options page
	 * This method should return an array consisting of sections and fields
	 * <pre>
	 * array(
	 *  'sections'=> array(
	 *     array(
	 *        'title'    => 'Your title',
	 * 				'callback' => 'your callback,
	 * 				'fields'		=> Array( See Field definitions at renderXXXField )
	 *     ),
	 *  );
	 * </pre>
	 * @return array
	 */
	abstract function getOptionsForm();

	/**
	 * Registers the menu and page in the wordpress menu
	 *
	 * @return void
	 */
	public function registerAdminMenu()
	{
		$menuOptions = $this->getMenuConfig();
		extract($menuOptions,EXTR_SKIP);

		if ( !isset($menu_slug)) {
			$menu_slug = strtolower(str_replace('\\','_',get_class($this)));
		}

		if ( !isset($menu_title)) {
			$menu_title = $this->defaults['Name'];
		}

		if ( !isset($page_title)) {
			$page_title = $menu_title . ' ' . __('settings');
		}

		if ( !isset($capability)) {
			$capability = 'manage_options';
		}

		add_options_page( $page_title, $menu_title, $capability, $menu_slug, array( $this, 'renderSettingsPage' ) );

		$this->defaults['page_title'] = $page_title;
		$this->defaults['menu_slug']  = $menu_slug;
	}

	/**
	 * Sets up the Admin page, based on the configuration it gets from getOptionsForm()
	 * @see getOptionsForm()
	 * @see getMenuConfig()
	 */
	public function initializeAdminPage()
	{
		if ( !$this->defaults ) {
			$this->registerAdminMenu();
		}

		$setup = $this->getOptionsForm();
		$group = $this->optionId . '_group';

		register_setting(
				$group, // Option group
				$this->optionId, // Option name
				array( $this, 'sanitize' ) // Sanitize
		);

		foreach( $setup['sections'] as $idx => $data) {
			$curSection = $this->optionId . '_section' . $idx;

			add_settings_section(
					$curSection, // ID
					$data['title'], // Title
					$data['callback'], // Callback
					$this->defaults['menu_slug'] // Page
			);

			foreach( $data['fields'] as $offset => $fieldDefinition) {
				$args = (isset($fieldDefinition['args']) ? $fieldDefinition['args'] : array());

				$args['_meta'] = array(
					'id' => $fieldDefinition['name'],
				);

				if ( !isset($fieldDefinition['type'])) {
					$fieldDefinition['type'] = 'text';
				}

				add_settings_field(
						$fieldDefinition['name'],  // ID
						$fieldDefinition['label'], // Title
						array($this,'render' . $fieldDefinition['type'] . 'Field'),
						$this->defaults['menu_slug'], // Page
						$curSection, // Section
						$args
				);
			}

			$this->sections[] = $curSection;
		}
		$this->groupId = $group;
	}

	/**
	 * Renders the settings page
	 * As per spec (http://codex.wordpress.org/Creating_Options_Pages)
	 */
	public function renderSettingsPage()
	{
		$this->data = get_option($this->optionId);
		?>
		<div class="wrap">
			<h2><?php echo $this->defaults['page_title'] ?></h2>
			<form method="post" action="options.php">
				<?php
				do_action('optionform-before');
				// This prints out all hidden setting fields
				settings_fields( $this->groupId );

				do_settings_sections( $this->defaults['menu_slug'] );
				submit_button();
				do_action('optionform-after');
				?>
			</form>
		</div>
	<?php
	}

	/**
	 * Renders the text field
	 * Field definition:
	 * <pre>
	 * array(
	 *		'name' 		=> 'config field',
	 * 		'label'		=> __('config label'), // Don't forget Multilingual!
	 *  ),
	 * </pre>
	 *
	 * @param array  $args
	 * @param string $type
	 */
	public function renderTextField($args,$type = 'text')
	{
		$id = $args['_meta']['id'];

		if ( !isset($args['default'])) {
			$args['default'] = '';
		}

		echo sprintf(
				'<input type="%s" id="%s" name="%s[%s]" value="%s" />',
				$type,
				$id,
				$this->optionId,
				$id,
				isset( $this->data[$id] ) ? esc_attr( $this->data[$id]) : $args['default']
		);
	}

	/**
	 * Renders a password field
	 * Field definition matches that of textField, but you need to add a type => password
	 *
	 * @param $args
	 */
	public function renderPasswordField($args)
	{
		echo $this->renderTextField($args,'password');
	}

	/**
	 * Renders a hidden field
	 * Field definition matches that of a textfield, but add type => hidden.
	 *
	 * @param $args
	 */
	public function renderHiddenField($args)
	{
		echo $this->renderTextField($args,'hidden');
	}

	/**
	 * Renders a select/dropdown box
	 * Field definition:
	 * <pre>
	 * array(
	 *		'name'		=> 'config option',
	 * 		'label'		=> __('Label'),
	 *		'type'		=> 'select',
	 *		'args'		=> array(
	 *			'options' => array( // Associative array, of only values if used with val_is_key ),
 	 * 			'val_is_key' => true, // if false, we will use the key of the array as value
	 *      'default' => 'pre-selected value',
	 *		)
	 * ),
	 * </pre>
	 * @param $args
	 */
	public function renderSelectField($args)
	{
		if ( !isset($args['options'])) {
			wp_die(sprintf('Option field %s needs an option list',$args['meta']['id']));
		}

		if ( !isset($args['default'])) {
			$args['default'] = '';
		}

		if ( isset($args['val_is_key']) && $args['val_is_key']) {
			$options = array_combine($args['options'],$args['options']);
		} else {
			$options = $args['options'];
		}
		echo sprintf(
				'<select id="%s" name="%s[%s]">',
				$args['_meta']['id'],
				$this->optionId,
				$args['_meta']['id']
		);

		$selected = isset($this->data['id']) ? $this->data['id'] : $args['default'];

		foreach( $options as $k => $v ) {
			echo '<option value="' . $k . '" ' . selected($selected,$k) . '>' . esc_html($v) . '</option>';
		}
		echo "</select>";
	}

	/**
	 * Checks input and performs validations
	 *
	 * @param $input
	 *
	 * @return array
	 */
	public function sanitize( $input )
	{
		return $input;
	}

	/**
	 * Filter for the Plugins page
	 * Use add_settings_error() to display any possible errors
	 *
	 * @param $links
	 *
	 * @return mixed
	 */
	public function addSettingsLinkToPlugin($links)
	{
		$settings = '<a href="options-general.php?page=' . $this->defaults['menu_slug'] . '">' . __('Settings') . '</a>';
		array_unshift($links,$settings);
		return $links;
	}

}