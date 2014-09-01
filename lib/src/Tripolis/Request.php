<?php
/**
 *
 * @package WP-Tripolis
 * @author  Peter Eussen <peter.eussen@harperjones.nl>
 */

namespace WPTripolis\Tripolis;

use WPTripolis\Tripolis\Response;
use WPTripolis\Tripolis\Response\GenericResponse;
use WPTripolis\Tripolis\Service\AbstractService;
use WPTripolis\TripolisProvider;

/**
 * Wrapper to create a request to Tripolis, and understand the response
 *
 * @package WPTripolis\Tripolis
 */
class Request
{
	/**
	 *
	 * @var \WPTripolis\TripolisProvider
	 */
	protected $provider;

	/**
	 *
	 * @var Service\AbstractService
	 */
	protected $service;

	/**
	 * @var \SoapClient
	 */
	protected $soap;

	/**
	 * Should the request use the Authentication or not
	 *
	 * @var bool
	 */
	protected $needsAuthentication = true;

	/**
	 * Initialises your request
	 *
	 * @param TripolisProvider $provider
	 * @param AbstractService  $service
	 * @param bool             $protected
	 */
	public function __construct(TripolisProvider $provider, AbstractService $service, $protected = true)
	{
		$this->provider            = $provider;
		$this->service             = $service;
		$this->needsAuthentication = $protected;
	}

	/**
	 * Catch All method calls and wraps it to a response object
	 *
	 * @param string $method
	 * @param string $arguments
	 *
	 * @return Response
	 * @throws SoapFault, UnauthorizedException
	 */
	public function __call($method,$arguments)
	{
		$soap     = $this->getSoapClient();

		// Although this function could have multiple arguments, we do not know what to do with them
		// so we only use the first
		if ( isset($arguments[0])) {
			$arguments = $arguments[0];
		}

		try {
			$response = $soap->__soapCall($method,$arguments);
		} catch (\SoapFault $f) {

			/* Although spec says that you can tell what is what by errorcode, you can't actually
			 * tell anything from the SoapFault::getCode(). You have to dig down all the way to the
			 * actual error before you can see the actual error code.
			 *
			 * e.g. Already exists (401), is categorized as an errorResponse 400, and yields 0 when
			 * probing SoapFault::getCode()
			 */
			// The subnamespaces drove me crazy, so lets just get rid of them all.
			$xml = $soap->__getLastResponse();

			// Gets rid of all namespace definitions
			$xml = preg_replace('/\s+xmlns[^=]*="[^"]*"/i', '', $xml);
			// Gets rid of all namespace references
			$xml = preg_replace('/[a-z0-9]+:([a-z0-9]+[=>])/i', '$1', $xml);
			$envelope = new \SimpleXMLElement($xml);
			$detail   = $envelope->Body->Fault->detail;

			if (isset($detail->errorResponse->errors->error->message)) {
				$errorMessage = (string)$detail->errorResponse->errors->error->message;
			}
			else {
				$errorMessage = $f->getMessage();
			}

			$error = $detail->errorResponse->errors->error;
			$code  = (int)$error->errorCode;

			switch( $code ) {
				case 300:
					$e = new UnauthorizedException(
							$errorMessage,
							$code
					);
					break;
				case 401:
					$e = new AlreadyExistsException(
						$errorMessage,
						$code
					);
					$e->setId((string)$detail->errorResponse->errors->error->identifierId);
					break;
				case 407:
					$e = new NotFoundException(
							$errorMessage,
							$code,
							$f
					);

					if (isset($error->identifiervalue)) {
						$e->setId((string)$error->identifiervalue);
					}
					break;
				// There are more, but at the moment I don't care too much
				default:
					$e = new TripolisException(
							$errorMessage,
							$code,
							$f
					);
			}

			if ( $e instanceof TripolisException ) {
				$e->setDetail($detail);
			}
			throw $e;
		}

		if ( $response ) {
			// Try to locate a specific implementation based on the method
			$class = __NAMESPACE__ . '\\Response\\' . $this->service->getServiceName() . '\\' . ucfirst($method) . 'Response';

			if ( class_exists($class,true)) {
				return new $class($response,$method);
			} else {
				// If no implementation was found, we will assume the "basic" is good enough
				return new GenericResponse($response,$method);
			}
		}
		return $response;
	}


	/**
	 * Creates a SOAP client and initializes the headers etc
	 *
	 * @return \SoapClient|SoapClient
	 */
	protected function getSoapClient()
	{
		if ( $this->soap === null ) {
			// Refactor to somewhere
			$options = array(
				// Stuff for development.
					'trace' => 1,
					'exceptions' => true,
					'cache_wsdl' => WSDL_CACHE_NONE,
					'features' => SOAP_SINGLE_ELEMENT_ARRAYS,
			);
			$this->soap = new \SoapClient($this->provider->getBaseURL() . '/' . $this->service->getServiceURI(),$options);

			if ( $this->needsAuthentication ) {
				$this->soap->__setSoapHeaders(array($this->provider->getAuthentication()->getHeader()));
			}
		}
		return $this->soap;
	}


} 