<?php
/**
 * Created by PhpStorm.
 * User: petereussen
 * Date: 22/08/14
 * Time: 15:36
 */

namespace WPTripolis\Tripolis\Service;


use WPTripolis\Tripolis\Request;
use WPTripolis\TripolisProvider;

abstract class AbstractService
{
	protected $serviceURI;

	/**
	 * @var \WPTripolis\TripolisProvider
	 */
	protected $provider;
	protected $client = null;
	protected $module = null;
	protected $service = null;

	public function __construct(TripolisProvider $provider)
	{
		$class          = get_class($this);
		$this->provider = $provider;
		$this->setServiceName(substr($class,strrpos($class,'\\')+1));
		$this->setServiceURI('/api2/soap/' . $this->getServiceName() . '?wsdl');
	}

	protected function setModule($module)
	{
		$this->module = $module;
	}

	public function getServiceURI()
	{
		return $this->serviceURI;
	}

	protected function setServiceURI($uri)
	{
		$this->serviceURI = $uri;
	}

	protected function getRequest($protected = true)
	{
		return new Request($this->provider,$this, $protected);
	}

	public function getServiceName()
	{
		return $this->service;
	}

	public function setServiceName($name)
	{
		$this->service = $name;
		return $this;
	}

	public function __call($method,$args)
	{
		return $this->invoke($method,(array)$args);
	}

	protected function invoke($method, $content,$protected = true)
	{
		$request = $this->getRequest($protected);
		$body    = array(
			array(
				$method => array(
						$method . 'Request' => $content
				)
			)
		);

		return $this->execute($request,$method,$body);
	}

	protected function execute(Request $request,$method,$args)
	{
		if ( is_array($args)) {
			return call_user_func_array(array($request,$method),$args);
		} else {
			return call_user_func(array($request,$method));
		}
	}

	/**
	 *
	 * @return WPTripolis\Tripolis\Response
	 */
	public function info()
	{
		return $this->invoke('getServiceInfo',array(),false);
	}

}