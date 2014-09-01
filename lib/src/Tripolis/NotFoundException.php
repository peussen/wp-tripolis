<?php
/**
 * Created by PhpStorm.
 * User: petereussen
 * Date: 29/08/14
 * Time: 16:03
 */

namespace WPTripolis\Tripolis;


class NotFoundException extends TripolisException
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