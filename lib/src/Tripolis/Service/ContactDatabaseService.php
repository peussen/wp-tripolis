<?php
/**
 * Created by PhpStorm.
 * User: petereussen
 * Date: 25/08/14
 * Time: 12:02
 */

namespace WPTripolis\Tripolis\Service;


class ContactDatabaseService extends AbstractService
{
	public function all()
	{
		$body = array(
			'getAll' => array('getAllRequest' => '')
		);

		return $this->getAll($body);
	}
} 