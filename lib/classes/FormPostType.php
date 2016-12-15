<?php
/*
 * @author: petereussen
 * @package: wp-tripolis
 */

namespace WPTripolis;


use WPTripolis\Admin\FormMetaBox;

class FormPostType
{
  static public function register()
  {
    $labels = array(
      'name'               => _x( 'Forms', 'post type general name', 'tripolis' ),
      'singular_name'      => _x( 'Form', 'post type singular name', 'tripolis' ),
      'menu_name'          => _x( 'Forms', 'admin menu', 'tripolis' ),
      'name_admin_bar'     => _x( 'Form', 'add new on admin bar', 'tripolis' ),
      'add_new'            => _x( 'New Form', 'Vacature', 'tripolis' ),
      'add_new_item'       => __( 'New Form', 'tripolis' ),
      'new_item'           => __( 'New Form', 'tripolis' ),
      'edit_item'          => __( 'Edit Form', 'tripolis' ),
      'view_item'          => __( 'View form', 'tripolis' ),
      'all_items'          => __( 'All forms', 'tripolis' ),
      'search_items'       => __( 'Search Forms', 'tripolis' ),
      'parent_item_colon'  => __( 'Parent Form:', 'tripolis' ),
      'not_found'          => __( 'No forms created.', 'tripolis' ),
      'not_found_in_trash' => __( 'No forms found in the trash.', 'tripolis' )
    );

    $args = array(
      'labels'             => $labels,
      'description'        => __( 'Omschrijving.', 'tripolis' ),
      'public'             => false,
      'publicly_queryable' => false,
      'show_ui'            => true,
      'show_in_menu'       => 'WPTripolis_Administration',
      'query_var'          => false,
      'rewrite'            => array( 'slug' => 'wptripolis-form' ),
      'capability_type'    => 'post',
      'has_archive'        => false,
      'hierarchical'       => false,
      'menu_position'      => null,
      'exclude_from_search'=> true,
      'menu_icon'          => 'dashicons-id-alt',
      'supports'           => array( 'title')
    );

    register_post_type( 'wptripolis-form', $args );

    add_action('load-post.php', __CLASS__ . '::setupMetaBox');
    add_action('load-post-new.php',__CLASS__ . '::setupMetaBox');
    add_action('manage_wptripolis-form_posts_custom_column', __CLASS__ . '::fillShortCodeColumn',10,2);

    add_filter('manage_wptripolis-form_posts_columns',__CLASS__ . '::addShortCodeColumn');
  }

  static public function setupMetaBox()
  {
    add_action('add_meta_boxes',__CLASS__ . '::createMetaBox');
  }

  static public function createMetaBox()
  {
    add_meta_box('wptripolis-editor',__('Form','tripolis'), 'WPTripolis\\Admin\\FormMetaBox::render','wptripolis-form', 'normal', 'high');
  }

  static public function addShortCodeColumn($columns)
  {
      $lastColKey =  key( array_slice( $columns, -1, 1, TRUE ) );
      $lastColVal = array_pop($columns);

      $columns['wptripolis_shortcode'] = __('Shortcode','wptripolis');
      $columns[$lastColKey] = $lastColVal;

      return $columns;
  }

  static public function fillShortCodeColumn($column,$post_id)
  {
    if ( $column === 'wptripolis_shortcode' ) {
      echo '<input type="text" readonly="readonly" onfocus="this.select()" value="[wptripolis-form form=' . $post_id . ']" class="large-text code">';
    }
  }
}