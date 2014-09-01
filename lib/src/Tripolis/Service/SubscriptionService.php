<?php
/**
 * Created by PhpStorm.
 * User: petereussen
 * Date: 28/08/14
 * Time: 15:20
 */

namespace WPTripolis\Tripolis\Service;

/**
 * SubscriptionService Implementation
 * 100% implementation of the API
 *
 * @package WPTripolis\Tripolis\Service
 */
class SubscriptionService extends AbstractService
{
	use DatabaseTrait;

	/**
	 * Subscribe a user to groups and send a mail if required.
	 * @param        $data
	 * @param        $groups
	 * @param string $keyField
	 * @param null   $directMail
	 * @param null   $ip
	 * @param null   $db
	 *
	 * @return mixed
	 */
	public function subscribeContact($data, $groups, $keyField = 'name', $directMail = null, $ip = null, $db = null)
	{
		$db     = $this->negotiateDB($db);
		$groups = (array)$groups;

		$groupList = array();
		foreach($groups as $group) {
			$groupList[] = array(
				'contactGroup' => array('id' => $group),
				'confirmed'		 => 1
			);
		}

		$fields = array();

		foreach( $data as $fieldName => $fieldValue ) {
			$fields[] = array(
				$keyField => $fieldName,
				'value'		=> $fieldValue
			);
		}

		if ( $ip === null && isset($_SERVER['REMOTE_ADDR'])) {
			$ip = $_SERVER['REMOTE_ADDR'];
		} else {
			$ip = '127.0.0.1';
		}

		$body = array(
			'contactDatabase' => array('id' => $db),
			'contactFields'		=> array('contactField' => $fields),
			'contactGroupSubscriptions' => array('contactGroupSubscription' => $groupList)
		);

		if ( $directMail ) {
			$body['directEmail'] = array('name' => $directMail);
		}

		return $this->invoke(__FUNCTION__,$body);
	}
} 