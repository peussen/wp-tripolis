<?php
/**
 * Created by PhpStorm.
 * User: petereussen
 * Date: 29/08/14
 * Time: 14:50
 */

namespace WPTripolis;


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

	static public function registerInstance($name,$instance)
	{
		self::$instance[$name] = $instance;
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

