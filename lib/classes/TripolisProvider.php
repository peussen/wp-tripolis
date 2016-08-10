<?php
/**
 * Created by PhpStorm.
 * User: petereussen
 * Date: 22/08/14
 * Time: 15:09
 */

namespace WPTripolis;

/**
 * Provider for all Tripolis services.
 *
 * @author Peter Eussen <peter.eussen@harperjones.nl>
 * @version 1.0
 */
class TripolisProvider
{

	const GROUP_TYPE_SUBSCRIPTION = Tripolis\Service\ContactGroupService::GROUP_SUBSCRIPTION;
	const GROUP_TYPE_TEST = Tripolis\Service\ContactGroupService::GROUP_TEST;
	const GROUP_TYPE_STATIC = Tripolis\Service\ContactGroupService::GROUP_STATIC;

	/**
	 * URL to the Tripolis Instance you are using (see documentation)
	 *
	 * @var null|string
	 */
	protected $dialogue_instance_url = 'https://td43.tripolis.com/';

	/**
	 * Authentication object
	 *
	 * @var Tripolis\Authentication
	 */
	protected $authentication;

	/**
	 * Internal cache to handle multiple requests for the same service
	 *
	 * @var array
	 */
	protected $services = array();

	/**
	 * Initialises the Tripolis Client Provider, and handles authentication information
	 *
	 * @param      $client
	 * @param      $username
	 * @param      $password
	 * @param null $dialogue_url
	 */
	public function __construct($client,$username,$password,$dialogue_url = null)
	{
		$this->authentication = new Tripolis\Authentication($client,$username,$password);

		if ( $dialogue_url !== null) {
			$this->dialogue_instance_url = $dialogue_url;
		}
	}

	/**
	 * Wrapper to access all underlying Tripolis sub-services
	 * Available services are:
	 * - ClientDomain
	 * - FtpAccount
	 * - User
	 * - Contact
	 * - ContactDatabase
	 * - ContactDatabaseField
	 * - ContactDatabaseFieldGroup
	 * - ContactGroup
	 * - SmartGroup
	 * - Import
	 * - Subscription
	 *
	 * @param $service
	 * @param $args
	 *
	 * @return Tripolis\Service\AbstractService
	 */
	public function __call($service,$args)
	{
		return $this->getService( __NAMESPACE__ . "\\Tripolis\\Service\\" . ucfirst($service) . "Service");
	}

	/**
	 * Returns the Base URL for the Tripolis Service
	 *
	 * @return null|string
	 */
	public function getBaseURL()
	{
		return $this->dialogue_instance_url;
	}

	/**
	 * Returns an authenticiation object containing the credentials
	 *
	 * @return Tripolis\Authentication
	 */
	public function getAuthentication()
	{
		return $this->authentication;
	}

	/**
	 * returns an instance of a specific Service
	 *
	 * @param      $serviceclass
	 * @param null $instance
	 *
	 * @return mixed
	 */
	protected function getService($serviceclass,$instance = null)
	{
		// Only create if we have not done so before.
		if ( empty($instance) ) {
			$instance = substr($serviceclass,strrpos($serviceclass,"\\") + 1);
		}

		if ( !isset($this->services[$instance])) {
			$this->services[$instance] = new $serviceclass($this);
			$this->services[$instance]->setServiceName($instance);
		}
		return $this->services[$instance];
	}

}