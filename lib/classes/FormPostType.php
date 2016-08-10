<?php
/*
 * @author: petereussen
 * @package: wp-tripolis
 */

namespace WPTripolis;


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
      'supports'           => array( 'title', 'editor' )
    );

    register_post_type( 'wptripolis-form', $args );
  }
}