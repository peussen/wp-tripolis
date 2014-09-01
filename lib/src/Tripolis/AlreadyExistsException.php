<?php
/**
 * Created by PhpStorm.
 * User: petereussen
 * Date: 27/08/14
 * Time: 15:36
 */

namespace WPTripolis\Tripolis;

/**
 * Record already exists, with the added bonus of having the id at hand
 *
 * @package WPTripolis\Tripolis
 */
class AlreadyExistsException extends TripolisException
{
	protected $id = '';

	public function setId($id)
	{
		$this->id = $id;
	}

	public function getId()
	{
		return $this->id;
	}
} 