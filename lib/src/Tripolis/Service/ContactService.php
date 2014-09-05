<?php
/**
 * Created by PhpStorm.
 * User: petereussen
 * Date: 22/08/14
 * Time: 15:37
 */

namespace WPTripolis\Tripolis\Service;

use WPTripolis\Tripolis\AlreadyExistsException;
use WPTripolis\TripolisProvider;

/**
 * ContactService Client (Partial)
 * Implemented: 7 of 17 calls
 *
 * @package WPTripolis\Tripolis\Service
 */
class ContactService extends AbstractService
{
	use DatabaseTrait;

	public function __construct(TripolisProvider $provider)
	{
		parent::__construct($provider);
		$this->setServiceURI('/api2/soap/ContactService?wsdl');
	}

	/**
	 * Returns the number of contacts in a database
	 *
	 * @param string|null $database
	 *
	 * @return mixed
	 */
	public function countByContactDatabaseId( $database = null )
	{
		$db = $this->negotiateDB($database);

		$body = array(
			'contactDatabaseId' => $db
		);
		return $this->invoke(__FUNCTION__,$body);
	}

	/**
	 * Adds a contact to a group
	 *
	 * @spec 20121101_Dialogue_API_2.0.pdf:56
	 * @param      $contactId
	 * @param      $groupId
	 * @param null $database
	 *
	 * @return mixed
	 */
	public function addToContactGroup($contactId,$groupId,$database = null)
	{
		$db = $this->negotiateDB($database);

		// Handle multiple subscriptions at once
		if ( is_array($groupId)) {
			$tmp = array();

			foreach($groupId as $group) {
				$tmp[] = array(
					'contactGroupId' => $group,
					'confirmed' => '1',
				);
			}
			$groupId = $tmp;
		} else {
			$groupId = array(
					'contactGroupId' => $groupId,
					'confirmed' => '1',
			);
		}

		$body = array(
			'contactId' => $contactId,
			'contactGroupSubscriptions' => array(
				'contactGroupSubscription' => $groupId
			)
		);

		return $this->invoke(__FUNCTION__,$body);
	}

	/**
	 * Obtains information about a single contact item
	 *
	 * @param string $contactId
	 *
	 * @return mixed
	 */
	public function getById($contactId,$fields = array(), $db = null)
	{
		if ( empty($fields) ) {
			$db = $this->negotiateDB($db);

			$fieldset = $this->provider->contactDatabaseField()->database($db)->all();
			$fields   = array();

			foreach($fieldset as $id => $data ) {
				$fields[] = $data->name;
			}
		}

		$body = array(
			'id' => $contactId,
			'returnContactFields' => array(
				'contactDatabaseFieldNames' => array(
					'contactDatabaseFieldName' => $fields
				)
			)
		);

		return $this->invoke(__FUNCTION__,$body);
	}

	/**
	 * Removes a contact from a contact Group
	 *
	 * @param      $contactId
	 * @param      $groupId
	 * @param null $reference
	 * @param null $database
	 *
	 * @return mixed
	 */
	public function removeFromContactGroup($contactId,$groupId,$reference = null, $database = null)
	{
		$db = $this->negotiateDB($database);

		if ( $reference === null ) {
			$reference = 'WPTripolis' . (isset($_SERVER['REMOTE_ADDR']) ? '::' . $_SERVER['REMOTE_ADDR'] : '::CLI');
		}

		$body = array(
			'contactId' => $contactId,
			'contactGroupIds' => array(
				'contactGroupId' => $groupId
			),
			'reference' => $reference
		);

		return $this->invoke(__FUNCTION__,$body);
	}

	/**
	 * Gets all groups the user is a member of
	 *
	 * @param $contactId
	 * @param $groupType (STATIC/TEST/SUBSCRIPTION)
	 */
	public function getContactGroupSubscriptions($contactId,$groupType = 'SUBSCRIPTION')
	{
		$body = array(
			'id' => $contactId,
			'groupTypes' => array(
				'groupType' => (array)$groupType
			)
		);

		return $this->invoke(__FUNCTION__,$body);
	}

	/**
	 * Creates a new entry in the database
	 *
	 * Keyfield can be id or name, based on what field you use in your code.
	 * id is the most unique one. name can is only unique in combination with
	 * the fieldGroup. So if you have fieldgroups, you will be better off using
	 * ID's.
	 *
	 * @param        $fields
	 * @param string $keyField
	 * @param null   $database
	 *
	 * @return mixed
	 */
	public function create($fields, $keyField = 'name', $database = null)
	{
		$db   	= $this->negotiateDB($database);
		$values = $this->arrayToComplex($fields,$keyField);

		$body = array(
			'contactDatabaseId' => $db,
			'contactFields' => array(
				'contactField' => $values
			)
		);

		return $this->invoke(__FUNCTION__,$body);
	}

	/**
	 * Tries to delete a user from the database
	 *
	 * @param string $contactId
	 *
	 * @return WPTripolis/Tripolis/Response
	 */
	public function delete($contactId)
	{
		$body = array('id' => $contactId);

		return $this->invoke(__FUNCTION__,$body);
	}

	/**
	 * Updates a record based on the contactID
	 *
	 * @param        $contactId
	 * @param        $fields
	 * @param string $keyField
	 *
	 * @return mixed
	 */
	public function update($contactId, $fields,$keyField = 'name' )
	{
		$values = $this->arrayToComplex($fields,$keyField);

		$body = array(
				'id' => $contactId,
				'contactFields' => array(
						'contactField' => $values
				)
		);

		return $this->invoke(__FUNCTION__,$body);
	}

	/**
	 * Non-API method, adds or updates an entry in the database
	 *
	 * @param        $fields
	 * @param string $keyField
	 * @param null   $database
	 */
	public function replace($fields,$keyField = 'name',$database = null )
	{
		try {
			$response = $this->create($fields,$keyField,$database);
		} catch (AlreadyExistsException $e) {
			$response = $this->update($e->getId(),$fields,$keyField);
		}

		if ( isset($response->id)) {
			return $response->id;
		}
		return false;
	}

	/**
	 * Converts an array into a "Complex" Soap Variable structure
	 * The value of the array will be converted to an entry called "value", and the key
	 * of the array will be converted to what is supplied by $keyField. Usually this is
	 * "id" or "name".
	 *
	 * @param string $array
	 * @param string $keyField
	 *
	 * @return array
	 */
	protected function arrayToComplex($array,$keyField)
	{
		$ps = array();

		foreach( $array as $key => $val) {
			$ps[] = array(
					$keyField => $key,
					'value' => $val
			);
		}
		return $ps;
	}
}