<?php
/**
 * Created by PhpStorm.
 * User: petereussen
 * Date: 25/08/14
 * Time: 16:09
 */

namespace WPTripolis\Tripolis\Service;


class ContactGroupService extends AbstractService
{
	use DatabaseTrait;

	public function getByContactDatabaseId($db = null )
	{
		$db = $this->negotiateDB($db);

		$body = array(
			'paging' => array('pageNr' => 1, 'pageSize' => 1000),
			'sorting'=> array('sortBy' => 'label', 'sortOrder' => 'ASC'),
			'contactDatabaseId' => $db,
			'groupType' => '',
		);

		return $this->invoke(__FUNCTION__,$body);
	}

	public function all( $db = null )
	{
		return $this->getByContactDatabaseId($db);
	}
} 