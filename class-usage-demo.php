<?php
/*
Plugin Name: Demo MetaBox
Plugin URI:member'secontentternet.info
Description: My Meta Box Class usage demo
Version: 3.1.1
Author: Bainternet, Ohad Raz
Author URI: http://en.bainternet.info
*/

//include the main class file
require_once("meta-box-class/my-meta-box-class.php");
if (is_admin()){
  /* 
   * prefix of meta keys, optional
   * use underscore (_) at the beginning to make keys hidden, for example $prefix = '_ba_';
   *  you also can make prefix empty to disable it
   * 
   */
  $prefix = 'ndm_';

  /**
   * Create a second metabox
   */
  /*
   * configure your meta box
   */
  $config2 = array(
      'id'             => 'ndm_meta_box',          // meta box id, unique per meta box
      'title'          => 'Members Content',          // meta box title
      'pages'          => array('post', 'page'),      // post types, accept custom post types as well, default is array('post'); optional
      'context'        => 'side',            // where the meta box appear: normal (default), advanced, side; optional
      'priority'       => 'high',            // order of meta box: high (default), low; optional
      'fields'         => array(),            // list of meta fields (can be added by field arrays)
      'local_images'   => false,          // Use local or hosted images (meta box images for add/remove)
      'use_with_theme' => false          //change path if used with theme set to true, false for a plugin or anything else for a custom path(default false).
  );


  /*
   * Initiate your 2nd meta box
   */
  $my_meta2 =  new AT_Meta_Box($config2);


  /*
   * To Create a conditinal Block first create an array of fields
   * use the same functions as above but add true as a last param (like the repater block)
   */
  $Conditinal_fields[] =   $my_meta2->addRadio(
      $prefix.'con_radio_field_id',
      array(
          'member1' =>  'Subscriber',
          'member2' =>  'Student',
          'member3' =>  'Practitioner'
      ),
      array(
          'name'=> 'Member Types',
          'std'=> array('member1')),
      true
  );


  /*
   * Then just add the fields to the repeater block
   */
  //repeater block
  $my_meta2->addCondition('conditinal_fields',
      array(
          'name'   => __('Enable Restrictions? ','mmb'),
          'desc'   => __('<small>Turn ON to select access to <strong>member content</strong>.</small>','mmb'),
          'fields' => $Conditinal_fields,
          'std'    => false
      ));

  /*
   * Don't Forget to Close up the meta box Declaration 
   */
  //Finish Meta Box Declaration 
  $my_meta2->Finish();

}
