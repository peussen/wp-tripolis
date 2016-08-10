<?php

namespace WPTripolis\Admin;
/*
 * @author: petereussen
 * @package: wp-tripolis
 */

class Settings extends \AdminPageFrameworkLoader_AdminPage
{
  public function setUp()
  {
    $this->setRootMenuPageBySlug('wptripolis-main');
  }
}