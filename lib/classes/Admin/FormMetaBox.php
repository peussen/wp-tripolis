<?php

namespace WPTripolis\Admin;

use WPTripolis\Factory;

class FormMetaBox
{
  static $instance = null;
  protected $api;

  protected function __construct()
  {
    $this->api = Factory::createProvider();

  }

  public function do_getDatabaseFields()
  {
    $db = (isset($_GET['db']) ? $_GET['db'] : null);

    if ( $db ) {
      $fieldGroups = $this->api->contactDatabaseFieldGroup()->all($db);
      $response    = [];

      foreach( $fieldGroups as $fg ) {

        $response[] = [
          'id'    => $fg->id,
          'label' => $fg->label,
          'fields'=> $this->api->contactDatabaseField()->getByContactDatabaseFieldGroupId($fg->id)
        ];
      }

      echo json_encode($response);
      exit(0);
    }
  }

  public function do_render()
  {
    $dbs = $this->api->ContactDatabase()->all();

    // @Todo, fetch post and fill the json_content attribute
    ?>
    <input type="hidden" name="json_content" value="<?php ?>" />
    <div class="field-container">
      <label for="wptripolis_type">create a</label>
      <select name="wptripolis_type">
        <option value="subscribe"><?php _e('Subscription form','tripolis') ?></option>
        <option value="unsubscribe"><?php _e('Unsubscribe form','tripolis') ?></option>
        <option value="profile"><?php _e('Profile update form','tripolis') ?></option>
      </select>
    </div>
    <div class="field-container">
      <label for="wptripolis_type">for database</label>
      <select name="wptripolis_database">
        <?php foreach( $dbs as $db ):?>
          <option value="<?php echo $db->id ?>"><?php echo $db->label ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <?php
  }

  public function do_savePost($post_id)
  {
    if ( isset($_POST['json_content'])) {
      remove_action('save_post', __CLASS__ . '::savePost',20,2);

      wp_update_post( [ 'ID' => $post_id, 'post_content' => $_POST['json_content'] ] );

      add_action('save_post', __CLASS__ . '::savePost',20,2);
    }
  }

  public function do_loadScripts()
  {
    global $_wptripolis;

    wp_enqueue_script('wptripolis-admin-js',$_wptripolis['url'] . 'js/admin.js',['jquery']);
    wp_enqueue_style('wptripolis-admin-css',$_wptripolis['url'] . 'js/admin.css');
  }

  static public function __callStatic($name,$arguments)
  {
    $instance = static::instance();

    return call_user_func_array([$instance,'do_' .$name],$arguments);
  }

  static public function boot()
  {
    add_action('wp_ajax_wptripolis_get_database_fields', __CLASS__ . '::getDatabaseFields');
    add_action('save_post', __CLASS__ . '::savePost',20,2);
    add_action('wp_enqueue_scripts', __CLASS__ . '::loadScripts');
  }

  static public function instance()
  {
    if ( static::$instance === null ) {
      static::$instance = new static();
    }
    return static::$instance;
  }
}