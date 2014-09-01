<?php
/**
 * Created by PhpStorm.
 * User: petereussen
 * Date: 01/09/14
 * Time: 13:08
 */

namespace WPTripolis\Tripolis\Response\UserService;


use WPTripolis\Tripolis\Response\GenericResponse;

/**
 * Returns information about the current user
 *
 * @package WPTripolis\Tripolis\Response\UserService
 */
class GetByAuthInfoResponse extends GenericResponse
{
	const ROLE_MODULE_ADMIN = 'MODULE_ADMIN';
	const ROLE_MODULE_CAMPAIGN = 'MODULE_CAMPAIGN';
	const ROLE_MODULE_CONTACT = 'MODULE_CONTACT';
	const ROLE_MODULE_CONTENT = 'MODULE_CONTENT';
	const ROLE_MODULE_OUTBOUND = 'MODULE_OUTBOUND';
	const ROLE_MODULE_REPORT = 'MODULE_REPORT';
	const ROLE_MODULE_SETUP = 'MODULE_SETUP';
	const ROLE_CONTACTDATABASE_ALL = 'CONTACTDATABASE_ALL';
	const ROLE_WORKSPACE_ALL = 'WORKSPACE_ALL';
	const ROLE_SUBMODULE_ALL = 'SUBMODULE_ALL';

	protected function parseResponse( $reply ) {
		if ( isset($reply->response->user) ) {
			$this->data = $reply->response->user;
			$this->data->roles = $this->data->roles->role;
		}
	}

	public function hasRole($role)
	{
		if ( isset($this->data->roles)) {
			return in_array($role,$this->data->roles);
		}
		return false;
	}
} 