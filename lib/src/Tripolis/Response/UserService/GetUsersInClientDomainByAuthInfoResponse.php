<?php
/**
 * Created by PhpStorm.
 * User: petereussen
 * Date: 01/09/14
 * Time: 13:03
 */

namespace WPTripolis\Tripolis\Response\UserService;


use WPTripolis\Tripolis\Response\AbstractIteratorResponse;

class GetUsersInClientDomainByAuthInfoResponse extends AbstractIteratorResponse
{
	public function parseResponse($reply)
	{
		$this->populate($reply,'users','user');
	}
}