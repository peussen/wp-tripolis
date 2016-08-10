<?php
/**
 * Created by PhpStorm.
 * User: petereussen
 * Date: 29/08/14
 * Time: 14:50
 */

namespace WPTripolis;
use WPTripolis\Tripolis\Response\AbstractIteratorResponse;


/**
 * Wrapper to make the templating more "wordpressy"
 *
 * @package WPTripolis
 */
class TemplateWrapper
{
	static $loop    = array();
	static $current = array();
	static $instance= array();

	static public function setSubscriptions( array $groups )
	{
		self::$instance['subscriptions'] = $groups;
	}

	static public function getSubscriptions()
	{
		return isset(self::$instance['subscriptions']) ? self::$instance['subscriptions'] : array();
	}

	public static function setContactId( $contactId )
	{
		self::$instance['contactid'] = $contactId;
	}

	public static function getContactId()
	{
		return isset(self::$instance['contactid']) ? self::$instance['contactid'] : false;
	}

	public static function setContactMeta($contact)
	{
		self::$instance['contact'] = $contact;
	}

	public static function getContactMeta($meta = false)
	{
		if ( isset(self::$instance['contact'])) {
			if ( $meta === false ) {
				return self::$instance['contact'];
			}

			if ( isset(self::$instance['contact']->$meta)) {
				return self::$instance['contact']->$meta;
			}
		}
		return false;
	}
	static public function registerInstance($name,$instance)
	{
		self::$instance[$name] = $instance;
	}

	static public function getInstance($name)
	{
		if ( isset(self::$instance[$name])) {
			return self::$instance[$name];
		}
		return null;
	}

	static public function createFieldName($name)
	{
		if (isset(self::$instance['shortcode'])) {
			return self::$instance['shortcode']->getUniqueId() . '[' . esc_attr($name) . ']';
		}
		return $name;
	}

	static public function createFieldId($name)
	{
		if (isset(self::$instance['shortcode'])) {
			return self::$instance['shortcode']->getUniqueId() . '_' . preg_replace('/[ \\"\']/','',$name);
		}
		return $name;
	}

	static public function getPluginName()
	{
		if ( isset(self::$instance['shortcode'])) {
			self::$instance['shortcode']->getPluginName();
		}
		return null;
	}

	static public function setFieldLoop($data)
	{
		self::$loop['fields'] 			= $data;
	}

	static public function haveFields()
	{
		if ( isset(self::$loop['fields'])) {

			if ( !isset(self::$current['fields'])) {
				self::$current['fields']= current(self::$loop['fields']);
			} else {
				self::$current['fields'] = next(self::$loop['fields']);
			}

			return self::$current['fields'] !== false;
		}
		return false;
	}

	static public function getField()
	{
		return self::$current['fields'];
	}


	static public function findTemplate($template)
	{
		if ( isset(self::$instance['shortcode'])) {
			return self::$instance['shortcode']->findTemplate($template);
		}
		return false;
	}

	static public function getFieldProperty($property)
	{
		if (isset(self::$current['fields'][$property])) {
			return self::$current['fields'][$property];
		}
		return false;
	}

}

