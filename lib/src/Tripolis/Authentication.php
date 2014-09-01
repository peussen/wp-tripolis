<?php
/**
 *
 * @package WP-Tripolis
 * @author  Peter Eussen <peter.eussen@harperjones.nl>
 */

namespace WPTripolis\Tripolis;

/**
 * Class Authentication
 *
 * @package WPTripolis\Tripolis
 */
class Authentication
{
	protected $client;
	protected $username;
	protected $password;

	public function __construct($client,$username,$password)
	{
		$this->setClient($client);
		$this->setUser($username);
		$this->setPassword($password);
	}

	public function setClient($client)
	{
		$this->client = $client;
	}

	public function setUser($username)
	{
		$this->username = $username;
	}

	public function setPassword($password)
	{
		$this->password = $password;
	}

	public function getHeader()
	{
		return new \SoapHeader("http://services.tripolis.com/", 'authInfo', array(
			'client' => $this->client,
			'username' => $this->username,
			'password' => $this->password,
		));
	}

	public function __toString()
	{
		return sprintf(
			'<ser/:authInfo><client>%s</client><username>%s</username><password>%s</password></ser:authInfo',
			$this->client,
			$this->username,
			$this->password
		);
	}
} 