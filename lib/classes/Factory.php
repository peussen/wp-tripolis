<?php
/*
 * @author: petereussen
 * @package: wp-tripolis
 */

namespace WPTripolis;


use HarperJones\Tripolis\TripolisProvider;

class Factory
{
  static public function createProvider($config = [])
  {
    $defaults = [
      'environment' => Config::get('client_environment'),
      'account'     => Config::get('client_account'),
      'username'    => Config::get('client_username'),
      'password'    => Config::get('client_password')
    ];

    $settings = array_merge($defaults,$config);

    return new TripolisProvider(
      $settings['account'],
      $settings['username'],
      $settings['password'],
      $settings['environment']
    );
  }
}