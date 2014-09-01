<?php
/**
 * Created by PhpStorm.
 * User: petereussen
 * Date: 25/08/14
 * Time: 16:10
 */

namespace WPTripolis\Tripolis\Service;

/**
 * Adds functionality to allow the use of databases in API calls
 *
 * @package WPTripolis\Tripolis\Service
 */
trait DatabaseTrait
{
	/**
	 * The selected database
	 *
	 * @var string
	 */
	protected $db;

	/**
	 * Sets the "default" database for multiple queries to the API
	 *
	 * @param $db
	 *
	 * @return $this
	 */
	public function database($db)
	{
		$this->db = $db;
		return $this;
	}

	/**
	 * Checks whether to use the previously selected db string or the one passed to the API call itself
	 *
	 * @param $sent
	 *
	 * @return string
	 * @throws \RuntimeException
	 */
	public function negotiateDB($sent)
	{
		// Sanity check, if none is selected, then let the user know he is misusing the client
		if ( $sent === null && $this->db === null ) {
			throw new \RuntimeException("No ContactDatabase ID Specified");
		}

		if ( $sent === null ) {
			return $this->db;
		}
		return $sent;
	}
} 