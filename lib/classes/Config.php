<?php
/*
 * @author: petereussen
 * @package: wp-tripolis
 */
namespace WPTripolis;

class Config
{
  static $cachedOptions = array();

  static public function get($key)
  {
    return static::getOptionKey(
      'APF_AddFields',
      $key,
      static::getOptionKey('wptripolis_optionsscreen',$key)
    );
  }

  static protected function getOptionKey($option,$key,$default = '')
  {
    if ( !isset(static::$cachedOptions[$option])) {
      static::$cachedOptions[$option] = get_option($option);
    }
    $optionSet = static::$cachedOptions[$option];

    if ( isset($optionSet[$key])) {
      return $optionSet[$key];
    }
    return $default;
  }

}