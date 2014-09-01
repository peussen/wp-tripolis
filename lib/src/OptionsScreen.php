<?php
/**
 * Created by PhpStorm.
 * User: petereussen
 * Date: 26/08/14
 * Time: 10:57
 */

namespace WPTripolis;


use WPTripolis\Tripolis\Response\UserService\GetByAuthInfoResponse;
use WPTripolis\Tripolis\UnauthorizedException;
use WPTripolis\Wordpress\AdminSettings;


/**
 * WP-Tripolis Settings page Setup & Handling class
 *
 * @package WPTripolis
 */
class OptionsScreen extends AdminSettings
{
	public function __construct( $pluginFile ) {
		parent::__construct( $pluginFile );

		add_action( 'admin_enqueue_scripts', array( $this, 'registerStyles' ) );
	}

	public function registerStyles()
	{
		// Only stylesheets required for the settings page
		wp_register_style( $this->plugin . '-admin', plugins_url( $this->plugin . '/css/admin.css' ),false);
		wp_enqueue_style( $this->plugin . '-admin' );
	}

	public function getOptionsForm()
	{
		// Hook to add stuff after the options form
		add_action('optionform-after', array($this,'renderStatus'));

		register_setting(
				'wptripolis-group', // Option group
				'wptripolis-options', // Option name
				array( $this, 'sanitize' ) // Sanitize
		);

		return array(
			'sections'=> array(
				array(
					'title' 		=> 'API Account Settings',
					'callback'	=> array($this,'validateAccount'),
					'fields'		=> Array(
						array(
							'name'		=> 'client_environment',
							'label'		=> __('Tripolis Environment','tripolis'),
							'type'		=> 'select',
							'args'		=> array(
								'options' => array(
										'https://td42.tripolis.com',
										'https://td43.tripolis.com'
								),
								'val_is_key' => true,
								'default' => 'https://td43.tripolis.com',
							)
						),
						array(	// Client Account Field
							'name' 		=> 'client_account',
							'label'		=> __('Client Account','tripolis'),
						),
						array( // Client username field
							'name' 		=> 'client_username',
							'label'		=> __('Username','tripolis'),
						),
						array( // Client password field
							'name' 		=> 'client_password',
							'label'		=> __('Password','tripolis'),
							'type'		=> 'password'
						),
						array( // Hidden field that stores if the settings where checked and working
								'name' 		=> 'client_validated',
								'label'		=> '',
								'type'		=> 'hidden',
								'args'    => array('default' => 0),
						),
					)
				),
			)
		);
	}

	public function validateAccount()
	{
		echo "<p>Please enter your API account login</p>";
	}

	/**
	 * Checks the user input for validity
	 * In our case, this means check if the credentials can be used to access the API client
	 *
	 * @param $input
	 *
	 * @return array
	 */
	public function sanitize( $input ) {
		$input = parent::sanitize( $input );

		if ( $this->checkConnection($input) )	{
			$input['client_validated'] = 1;
		}
		return $input;
	}

	/**
	 * Show the status of the most commenly used (sub)services of Tripolis
	 *
	 */
	public function renderStatus()
	{
		if ($this->data['client_username'] &&
				$this->data['client_password'] &&
				$this->data['client_account'] ) {
			$tp = new TripolisProvider(
					$this->data['client_username'],
					$this->data['client_password'],
					$this->data['client_account'],
					$this->data['client_environment']
			);

			echo "<h2>Service Status</h2>";

			$services = array('Contact','ContactGroup','ContactDatabase','ContactDatabaseField');

			foreach( $services as $service ) {
				$info = $tp->$service()->info();

				?>
					<div class="<?php echo $this->plugin ?>-servicestatus">
						<h3><?php echo $service ?></h3>
						<span class="message"><?php echo $info->getMessage() ?></span>
						<ul>
							<li><strong>API</strong>: <?php echo $info->apiVersion ?></li>
							<li><strong>Dialogue</strong> : <?php echo $info->dialogueVersion ?></li>
							<li><strong>Build</strong> : <?php echo  $info->buildNumber ?></li></ul>
						</ul>
					</div>
				<?php
			}

		}

	}

	/**
	 * Checks the user credentials for validty
	 * Unfortunately i have not found a proper way to check for validity other than calling a function
	 * that requires authorisation. So we do some basic checks by calling several methods to figure out
	 * what we can do and what not.
	 * I probably need to check on submodule, but seems your API user needs quite a high authentication
	 * level to actually do anything at all.
	 *
	 * @param $data
	 *
	 * @return bool
	 */
	protected function checkConnection($data)
	{
		if ($data['client_environment'] &&
				$data['client_account'] &&
				$data['client_username'] &&
				$data['client_password']
		) {
			try {
				$tp = new TripolisProvider(
						$data['client_account'],
						$data['client_username'],
						$data['client_password'],
						$data['client_environment']
				);

				// Check if server is working
				$infoResponse = $tp->contact()->info();

				// Check the user
				$user      = $tp->user()->getByAuthInfo();

				if ( !$user->hasRole(GetByAuthInfoResponse::ROLE_MODULE_CONTACT)) {
					add_settings_error('client_account','unauthorized', __('Your user does not have acces to the Contact Module'));
					return false;
				}

				// Check if we have access to the required components
				$databases = $tp->ContactDatabase()->all();
				$db        = $databases->first();


				if ( $db) {
					// Test the COntact Group Service
					$groups = $tp->ContactGroup()->all($db->id);

					// Test the Contact service
					$result = $tp->Contact()->countByContactDatabaseId($db->id);
					add_settings_error('client_account','nodatabases',__('Settings ok, and validated'),'updated');
					return true;
				} else {
					add_settings_error('client_account','nodatabases',__('You do not have access to any databases in this environment'));
				}
			} catch (UnauthorizedException $e) {
				add_settings_error('client_username', 'unauthorized', __('Wrong credentials, access was denied by the API'));
			} catch (\SoapFault $f) {
				add_settings_error('client_environment', $f->getCode(), $f->getMessage());
			} catch (\Exception $e) {
				add_settings_error('client_environment', $e->getCode(), $e->getMessage());
			}
			return false;
		}
		return false;
	}
} 