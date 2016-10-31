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

//     header('Content-Type: text/json');
//     echo '
// {"db":"E7UQdAG70nSdt1r29a3uqA","fieldgroups":[{"id":"+oKI2z1O0Usvr3FGx_Oj5Q","label":"default"}],"fields":[{"id":"AhDu43H0K2fMtw07oMKmsQ","label":"E-mail","combinedlabel":"default \/ E-mail","required":true,"fieldgroup":"+oKI2z1O0Usvr3FGx_Oj5Q","indexfield":true,"default":"","type":"general","options":{}},{"id":"ZVNqOw+oOtP3u+EDUP+okA","label":"voornaam","combinedlabel":"default \/ voornaam","required":false,"fieldgroup":"+oKI2z1O0Usvr3FGx_Oj5Q","indexfield":false,"default":"","type":"general","options":{}},{"id":"KKxylAOu0z_8psK6wbCHZg","label":"Tussenvoegsel","combinedlabel":"default \/ Tussenvoegsel","required":false,"fieldgroup":"+oKI2z1O0Usvr3FGx_Oj5Q","indexfield":false,"default":"","type":"general","options":{}},{"id":"bKSfE3Uw8fA84kibxWnHXw","label":"Achternaam","combinedlabel":"default \/ Achternaam","required":false,"fieldgroup":"+oKI2z1O0Usvr3FGx_Oj5Q","indexfield":false,"default":"","type":"general","options":{}},{"id":"m4EzaJ8EZGUg0JAEI3onGA","label":"Aanmelddatum","combinedlabel":"default \/ Aanmelddatum","required":false,"fieldgroup":"+oKI2z1O0Usvr3FGx_Oj5Q","indexfield":false,"default":"","type":"general","options":{}},{"id":"_Pk7J3NQXYGmsE7XpXTCIw","label":"Aanmeld IP","combinedlabel":"default \/ Aanmeld IP","required":false,"fieldgroup":"+oKI2z1O0Usvr3FGx_Oj5Q","indexfield":false,"default":"","type":"general","options":{}},{"id":"5yIZWHadbpjgnc_js1tiog","label":"Geslacht","combinedlabel":"default \/ Geslacht","required":false,"fieldgroup":"+oKI2z1O0Usvr3FGx_Oj5Q","indexfield":false,"default":"","type":"general","options":{}},{"id":"4unvFsjLwFnhFnZaISyyrQ","label":"Initials","combinedlabel":"default \/ Initials","required":false,"fieldgroup":"+oKI2z1O0Usvr3FGx_Oj5Q","indexfield":false,"default":"","type":"general","options":{}},{"id":"pLa4l+Mld2vNGElf_gO4UA","label":"Aanhef","combinedlabel":"default \/ Aanhef","required":false,"fieldgroup":"+oKI2z1O0Usvr3FGx_Oj5Q","indexfield":false,"default":"","type":"general","options":{}}]}';
// exit();

    if ( $db ) {
      $fieldGroups = $this->api->contactDatabaseFieldGroup()->all($db);
      $response    = [
        'db'          => $db,
        'fieldgroups' => [],
        'fields'      => [],
      ];

      foreach( $fieldGroups as $fg ) {
        $response['fieldgroups'][] = [
          'id'    => $fg->id,
          'label' => $fg->label
        ];

        $fieldsInGroup = $this->api->contactDatabaseField()->getByContactDatabaseFieldGroupId($fg->id);

        foreach( $fieldsInGroup as $field ) {
          $response['fields'][] = [
            'id'            => $field->id,
            'label'         => $field->label,
            'combinedlabel' => $fg->label . ' / ' . $field->label,
            'required'      => $field->required,
            'fieldgroup'    => $fg->id,
            'indexfield'    => $field->key,
            'default'       => $field->defaultValue,
            'type'          => $field->type,
            'options'       => $field->picklistItems,
          ];
        }
      }

      echo json_encode($response);
      exit(0);
    }
  }

  public function do_render()
  {
    global $post;
    global $_wptripolis;

    $dbs = $this->api->ContactDatabase()->all();

    // $db = new \stdClass();
    // $db->id = 'a';
    // $db->label = "Dummy";
    // $dbs= [ $db ];
    // $db = new \stdClass();
    // $db->id = 'b';
    // $db->label = "Dummy 2";
    // $dbs[] = $db;

    // @Todo, fetch post and fill the json_content attribute
    ?>
    <input type="hidden" data-tripolis="send-data" name="json_content" value="<?= esc_attr($post->post_content) ?>" />
    <div class="field-container">
      <label for="wptripolis_type">create a</label>
      <select name="wptripolis_type" data-tripolis="type">
        <option value="subscribe"><?php _e('Subscription form','tripolis') ?></option>
        <option value="unsubscribe"><?php _e('Unsubscribe form','tripolis') ?></option>
        <option value="profile"><?php _e('Profile update form','tripolis') ?></option>
      </select>
    </div>
    <div class="field-container">
      <label for="wptripolis_database">for database</label>
      <select name="wptripolis_database" data-tripolis="db" id="database">
        <option value="choose field" selected disabled>--choose field--</option>
        <?php foreach( $dbs as $db ):?>
          <option value="<?php echo $db->id ?>"><?php echo $db->label ?></option>
        <?php endforeach; ?>
      </select>
      <img class="load" src="<?= $_wptripolis['url'] ?>img/loading.gif" alt="loading...">
    </div>

    <div class="field-container" data-tripolis="fields-parent">
      <label for="wptripolis_fields">add fields</label>
      <select name="wptripolis_fields" data-tripolis="fields">
        <option value="choose field" selected disabled>--choose field--</option>
      </select>
   
      <ul data-tripolis="fields-selected" data-sortable class="fields--selected sortable">
   
      </ul>
          <table>
            <thead>
              <tr>
                <th></th>
                <th>Veld</th>
                <th>Label</th>
                <th></th>
                <th></th>
              </tr>     
            </thead>
            <tbody data-tripolis="fields-selected" class="sortable">
                <tr data-id="field-1" data-value="">
                  <td ><button class="handle" type="button">move</button></td>
                  <td>Email</td>
                  <td><input type="text" name="field-1"  value="E-mail" readonly></td>
                  <td><button type="button" data-edit="field-1">edit label</button></td>
                  <td ><button  type="button" data-deselect="field-2">delete</button></td>
                </tr>
                <tr data-selected="" data-value="">
                  <td ><button class="handle" type="button">move</button></td>
                  <td>Voornaam</td>
                  <td><input type="text" name="field-2" value="Voornaamste" data-id="field-2" readonly></td>
                  <td><button type="button" data-edit="field-2">edit label</button></td>
                  <td ><button  type="button" data-deselect="field-2">delete</button></td>
                </tr> 
            </tbody>
          </table>

    </div>

    <div id="confirm_msg" style="display:none;">
       <p>
         Are you sure you want to switch database?
       </p>
       <button type="button" data-confirm>confirm</button>
    </div>
  
    <?php
  }

  public function do_savePost($post_id)
  {
    if ( isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['json_content'])) {
      remove_action('save_post', __CLASS__ . '::savePost',20,2);
      wp_update_post( [ 'ID' => $post_id, 'post_content' => $_POST['json_content'] ] );
      add_action('save_post', __CLASS__ . '::savePost',20,2);
    }
    return true;
  }

  public function do_loadScripts()
  {
    global $_wptripolis;
    add_thickbox();
    wp_enqueue_script('wptripolis-js',$_wptripolis['url'] . 'js/admin.js');
    wp_enqueue_style('wptripolis-css',$_wptripolis['url'] . 'css/admin.css');
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
    add_action('admin_enqueue_scripts', __CLASS__ . '::loadScripts');
  }

  static public function instance()
  {
    if ( static::$instance === null ) {
      static::$instance = new static();
    }
    return static::$instance;
  }
}