<?php
/**
 * The WordPress Plugin Boilerplate.
 *
 * A foundation off of which to build well-documented WordPress plugins that
 * also follow WordPress Coding Standards and PHP best practices.
 *
 * @package   Honeypot Comments
 * @author    gh0stpr3ss <gh0stpr3ss@gmail.com>
 * @license   GPL-2.0+
 * @link      http://www.twitter.com/gh0stpr3ss
 * @copyright 2014 gh0stpr3ss
 *
 * @wordpress-plugin
 * Plugin Name:       Honeypot Comments
 * Plugin URI:        http://www.twitter.com/gh0stpr3ss
 * Description:       Add a hidden input field that only spambots will fill in, effectively nuking any comments not made by a human.
 * Version:           1.1.0
 * Author:            gh0stpr3ss
 * Author URI:        http://www.twitter.com/gh0stpr3ss
 * Text Domain:       honeypot-comments
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 * GitHub Plugin URI: https://github.com/gh0stpr3ss/honeypot-comments
 * WordPress-Plugin-Boilerplate: v2.6.1
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/

require_once( plugin_dir_path( __FILE__ ) . 'public/class-honeypot-comments.php' );

/*
 * Register hooks that are fired when the plugin is activated or deactivated.
 * When the plugin is deleted, the uninstall.php file is loaded.
 */
register_activation_hook( __FILE__, array( 'Honeypot_Comments', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'Honeypot_Comments', 'deactivate' ) );

add_action( 'plugins_loaded', array( 'Honeypot_Comments', 'get_instance' ) );

/**
 * Generating a random string of letters and numbers to add to the hidden 
 * input field as an ID name, replacing the previous version that utilized
 * a regular rand(1,99999) output.
 *
 * @since  1.1.0
 * @access public
 * @return void
 */

function RandomString($length) {
    $keys = array_merge(range(0,9), range('a', 'z'), range('A', 'Z'));

    for($i=0; $i < $length; $i++) {
        $key .= $keys[array_rand($keys)];
    }
    return $key;
}

/**
 * Checks if a spambot stuck his hand in the honeypot.  If so, we'll cut off the user registration 
 * process so that the spam user account never gets registered.
 *
 * @since  1.0.0
 * @access public
 * @return void
 */

function honeypotcomments_more_comments( $post_id ) {
	echo '<!-- the following input field has been added by the Honeypot Comments plugin to thwart spambots -->
	<input type="hidden" id="'. RandomString(25) .'" name="'. RandomString(25) .'" />';
}

add_action( 'comment_form', 'honeypotcomments_more_comments' );

/**
 * Checks if a spambot found the honeypot.  If so, we'll cut off the comment
 * process so that the spam user account never gets registered.
 *
 * @since  1.0.0
 * @access public
 * @return void
 */

add_filter( 'preprocess_comment', 'verify_comment_meta_data' );

function verify_comment_meta_data( $commentdata ) {
	if ( ! empty( $_POST['honeypot-comments'] ) )
        	wp_die( __( 'Error: We think you are some form of bot since you filled out a hidden field. Please contact the website owner if you feel this was in error.' ) );
    	return $commentdata;
}
