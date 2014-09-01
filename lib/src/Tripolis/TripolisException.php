<?php
/**
 * Created by PhpStorm.
 * User: petereussen
 * Date: 27/08/14
 * Time: 15:37
 */

namespace WPTripolis\Tripolis;


use Exception;

class TripolisException extends \RuntimeException {
	protected $detail;

	public function setDetail(\SimpleXMLElement $xml)
	{
		$this->detail = $xml;
	}

	public function getDetail()
	{
		return $this->detail;
	}
} 