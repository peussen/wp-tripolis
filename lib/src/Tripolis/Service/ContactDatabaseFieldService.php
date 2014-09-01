<?php
/**
 * Created by PhpStorm.
 * User: petereussen
 * Date: 25/08/14
 * Time: 15:14
 */

namespace WPTripolis\Tripolis\Service;


class ContactDatabaseFieldService extends AbstractService
{
  use DatabaseTrait;

	public function getByContactDatabaseId($id = null)
	{
		$id = $this->negotiateDB($id);

		$body = array(
			'paging' => array(
					'pageNr' => 1,
					'pageSize' => 1000
			),
			'sorting' => array(
					'sortBy'	=> 'label',
					'sortOrder' => 'ASC'
			),
			'contactDatabaseId' => $id
		);

		return $this->invoke(__FUNCTION__,$body);
	}

	public function getByContactDatabaseFieldGroupId($groupId )
	{
		$body = array(
				'contactDatabaseFieldGroupId' => $groupId
		);
		return $this->invoke(__FUNCTION__,$body);
	}

	public function all($db = null)
	{
		return $this->getByContactDatabaseId($db);
	}
} 