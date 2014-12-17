<?php
/*
Plugin Name: NuDiag MetaBox
Plugin URI: http://huxburyquinn.com.au/
Description: Nutrition Diagnostics Members Content Restrictions
Version: 1.1
Author: Robert Wilde
Author URI: http://huxburyquinn.com.au/
*/

//include the main class file
require_once( "meta-box-class/nd-meta-box-class.php" );
require_once( "meta-box-class/simple_html_dom.php");

if ( is_admin() ) {
	/*
	   * prefix of meta keys, optional
	   * use underscore (_) at the beginning to make keys hidden, for example $prefix = '_ba_';
	   *  you also can make prefix empty to disable it
	   *
	   */
	$prefix = 'nudiag_';

	/**
	 * Create a second metabox
	 */
	/*
	   * configure your meta box
	   */
	$config2 = array (
		'id'             => 'ndm_meta_box',          // meta box id, unique per meta box
		'title'          => 'Membership Access',          // meta box title
		'pages'          => array ( 'post', 'page' ),      // post types, accept custom post types as well, default is array('post'); optional
		'context'        => 'side',            // where the meta box appear: normal (default), advanced, side; optional
		'priority'       => 'high',            // order of meta box: high (default), low; optional
		'fields'         => array (),            // list of meta fields (can be added by field arrays)
		'local_images'   => FALSE,          // Use local or hosted images (meta box images for add/remove)
		'use_with_theme' => FALSE          //change path if used with theme set to true, false for a plugin or anything else for a custom path(default false).
	);

	/*
	   * Initiate your 2nd meta box
	   */
	$my_meta2 = new AT_Meta_Box( $config2 );

	/*
	   * To Create a conditinal Block first create an array of fields
	   * use the same functions as above but add true as a last param (like the repater block)
	   */
	$Conditinal_fields[ ] = $my_meta2->addRadio( $prefix . 'member_type', array (
		'subscriber'   => 'Subscriber',
		'student'      => 'Student',
		'practitioner' => 'Practitioner'
	), array (
		'name' => 'Member Types',
		'std'  => array ( 'subscriber' )
	), TRUE );

	/*
	   * Then just add the fields to the repeater block
	   */
	//repeater block
	$my_meta2->addCondition( 'conditinal_fields', array (
		'name'   => __( 'Enable Protection? ', 'mmb' ),
		'desc'   => __( '<small>Turn ON to select access to <strong>member content</strong>.</small>', 'mmb' ),
		'fields' => $Conditinal_fields,
		'std'    => FALSE
	) );

	/*
	   * Don't Forget to Close up the meta box Declaration
	   */
	//Finish Meta Box Declaration
	$my_meta2->Finish();

	/**
	 *
	 * Content filter
	 */


}

/**
 * @param $the_post_id
 *
 * @return string member level
 */
function nudiag_content_role( $the_post_id ) {

//	$the_post_id = ( $the_post_id = ''
//		? get_the_ID( )
//		: $the_post_id );

	$post_meta          = get_post_meta( $the_post_id );
	$conditional_fields = unserialize( $post_meta[ 'conditinal_fields' ][ 0 ] );

	if ( isset ( $conditional_fields[ 'enabled' ] ) ) {
		return $conditional_fields[ 'nudiag_member_type' ];
	} else {
		return 'public';
	}
}

/**
 * Return the current user role
 * @return mixed
 */
function nudiag_user_role() {
	global $current_user;

	$user_roles = $current_user->roles;
	$user_role  = array_shift( $user_roles );

	return ( $user_role == ''
		? 'public'
		: $user_role );

}

/**
 * Take the a user or access role and convert to level
 *
 * @param $role
 *
 * @return int
 */
function nudiag_access_level( $role ) {

	switch ( $role ) {
		case 'subscriber':
			return 1;
			break;
		case 'student':
			return 2;
			break;
		case 'practitioner':
			return 3;
			break;
		case 'administrator':
			return 4;
			break;
		default:
			return 0;
	}
}

/**
 * @param $string
 * @param int $length
 * @param string $append
 *
 * @return array|string
 */
function truncate( $content, $excerpt_length = 30, $include_image = FALSE, $append = "&hellip;" ) {
	$content = trim( $content );

	if ($include_image == FALSE )  $content = remove_html_tags('img', $content);

	$words = explode( ' ', $content, $excerpt_length + 1 );

	if ( count( $words ) > $excerpt_length ) :
		array_pop( $words );
		array_push( $words, '...' );
		$content = implode( ' ', $words );
	endif;
	$content = '<p>' . $content . '</p>';

	return $content;
}

/**
 * Finding tag in HTML
 * @param $tag
 * @param $content
 *
 * @return array of clean content
 */
function remove_html_tags ($tag, $content  ){

	$html = str_get_html($content);

	// Find all images and remove tag
	foreach($html->find($tag) as $element){
		$element->outertext = '';
	}

	$return = $html->save();

	// clean up memory
	$html->clear();
	unset($html);

	return $return;
}

/**
 * @param $content
 *
 * @return mixed restricted content view
 */
function nudiag_content_filter( $content ) {

	$content_role = nudiag_content_role( get_the_ID() );
	$user_role    = nudiag_user_role();

//	if ( is_single() || is_home() ) {

		if ( $content_role !== 'public' && nudiag_access_level( $user_role ) < nudiag_access_level( $content_role ) ) {
			$truncated = truncate( $content );

			$message = '<div class="ndm-signup-message">';
			$message .= '<p>Sorry You need to be ' . ucfirst( nudiag_content_role( get_the_ID() ) ) . ' register to access this content</p>';
			$message .= '<a href="http://wpplugins.dev/wp-login.php">Login in</a></div>';

			return $truncated . $message;

		}
//	}

	return $content;
}

add_filter( 'the_content', 'nudiag_content_filter' );

