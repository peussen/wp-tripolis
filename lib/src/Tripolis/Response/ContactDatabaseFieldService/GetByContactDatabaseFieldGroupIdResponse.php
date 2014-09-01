<?php
/**
 * Created by PhpStorm.
 * User: petereussen
 * Date: 28/08/14
 * Time: 12:03
 */

namespace WPTripolis\Tripolis\Response\ContactDatabaseFieldService;


use WPTripolis\Tripolis\Response\AbstractIteratorResponse;

class GetByContactDatabaseFieldGroupIdResponse extends AbstractIteratorResponse
{
	public function parseResponse($reply)
	{
		$this->populate($reply,'contactDatabaseFields','contactDatabaseField');
	}
} 