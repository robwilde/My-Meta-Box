<?php
/**
Plugin Name: Nu Diag Members
Version: 0.1
Description: Nutrition Diagnostics Members plugin for controlling posts, pages and products
Author: Huxburyquinn
Author URI: http://www.huxburyquinn.com.au/
 */

//include the main class file
require_once("meta-box-class/my-meta-box-class.php");
if (is_admin()) {
  /* 
   * prefix of meta keys, optional
   * use underscore (_) at the beginning to make keys hidden, for example $prefix = '_ba_';
   *  you also can make prefix empty to disable it
   * 
   */
  $prefix = 'ndm_';
  /* 
   * configure your meta box
   */
  $config = array (
      'id'             => 'ndm_meta_box',          // meta box id, unique per meta box
      'title'          => 'Members Content',          // meta box title
      'pages'          => array ( 'post', 'page' ),      // post types, accept custom post types as well, default is array('post'); optional
      'context'        => 'side',            // where the meta box appear: normal (default), advanced, side; optional
      'priority'       => 'high',            // order of meta box: high (default), low; optional
      'fields'         => array (),            // list of meta fields (can be added by field arrays)
      'local_images'   => FALSE,          // Use local or hosted images (meta box images for add/remove)
      'use_with_theme' => FALSE          //change path if used with theme set to true, false for a plugin or anything else for a custom path(default false).
  );


  /*
   * Initiate your meta box
   */
  $my_meta = new AT_Meta_Box( $config );

  /*
   * Add fields to your meta box
   */

  //checkbox field
  $my_meta->addCheckbox( $prefix . 'member1', array ( 'name' => 'Subscriber ' ) );

  $my_meta->addCheckbox( $prefix . 'member2', array ( 'name' => 'Student ' ) );

  $my_meta->addCheckbox( $prefix . 'member3', array ( 'name' => 'Practitioner ' ) );

  /*
   * Don't Forget to Close up the meta box Declaration 
   */
  //Finish Meta Box Declaration 
  $my_meta->Finish();

}