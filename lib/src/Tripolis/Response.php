<?php
/**
 *
 * @package WP-Tripolis
 * @author  Peter Eussen <peter.eussen@harperjones.nl>
 */

namespace WPTripolis\Tripolis;

/**
 * Base Tripolis Respons class
 * This class wraps the default response XML to make it a bit more readable
 *
 * @package WPTripolis\Tripolis
 */
abstract class Response
{
	/**
	 * The method called
	 *
	 * @var string
	 */
	protected $method;

	/**
	 * If the response had a "message", it will be stored here.
	 * @var (string)
	 */
	protected $message;

	/**
	 * the actual contents of the response
	 *
	 * @var mixed
	 */
	protected $data;

	/**
	 * The raw XML in case we have not figured out how to read the response yet
	 *
	 * @var string
	 */
	protected $raw;

	/**
	 * Constructs a Response based on the reply XML and the called method
	 *
	 * @param string $reply
	 * @param string $method
	 */
	public function __construct($reply,$method)
	{
		$this->method = $method;
		$this->raw    = $reply;
		if ( isset($reply->response)) {
			$this->parseResponse($reply);
		} else {
			throw new \RuntimeException("Invalid response from server");
		}
	}

	/**
	 * Checks if the response had an attribute called $attr (magic)
	 *
	 * @param $attr
	 *
	 * @return bool
	 */
	public function __isset($attr)
	{
		if ( is_object($this->data))  {
			return isset($this->data->$attr);
		} else {
			return isset($this->data[$attr]);
		}
	}

	/**
	 * Returns the value of $attr (magic)
	 *
	 * @param $attr
	 *
	 * @return mixed
	 * @throws \InvalidArgumentException
	 */
	public function __get($attr)
	{
		if ( is_object($this->data))  {
			if (isset($this->data->$attr)) {
				return $this->data->$attr;
			}
		} else if ( isset($this->data[$attr])) {
			return $this->data[$attr];
		}

		throw new \InvalidArgumentException("No such attribute: $attr");
	}

	/**
	 * Returns the message string if any was found, or null otherwise
	 * @return string|null
	 */
	public function getMessage()
	{
		return $this->message;
	}

	/**
	 * Returns the Raw XML response
	 * @return string
	 */
	public function getRaw()
	{
		return $this->raw;
	}

	/**
	 * Return the response payload
	 *
	 * @return mixed
	 */
	public function getData()
	{
		return $this->data;
	}

	/**
	 * Parses the response on our best knowledge of the structure of the response
	 *
	 * @param $reply
	 */
	protected function parseResponse($reply)
	{
		// Find a message
		if ( isset($reply->response->message)) {
			$this->message = $reply->response->message;
		}

		// If it was a get function, we should look for certain elements to find the actual list of items
		if ( substr($this->method,0,3) === 'get') {
			$cat = substr($this->method,3);

			$cat[0]   = strtolower($cat[0]);
			$itemList = $cat . 'Items';
			$item     = $cat . 'Item';

			if ( isset($reply->response->$itemList) && $reply->response->$itemList->$item) {
				$tmp = array();

				foreach( $reply->response->$itemList->$item as $element ) {
					if ( isset($element->key) && isset($element->value)) {
						$tmp[(string)$element->key] = $element->value;
					}
				}

				$this->setData($tmp);
			}
		} else {
			// We don't know, so just store the whole response
			$this->data = $reply->response;
		}
	}

	protected function setData($data)
	{
		$this->data = $data;
	}


}