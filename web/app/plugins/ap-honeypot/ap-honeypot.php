<?php
/*
Plugin Name: AP HoneyPot
Plugin URI: http://wordpress.org/extend/plugins/ap-honeypot/
Description: AP HoneyPot Plugin allows you to verify IP addresses of clients connecting to your blog against the <a href="http://www.projecthoneypot.org?rf=101236">Project Honey Pot</a> database. Based on <a href="http://stepien.cc/~jan">Jan Stępień</a>'s http:BL WordPress Plugin.
Author: Artprima
Version: 1.4
Author URI: http://artprima.cz/
License: This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
*/

if ( ! defined( 'ABSPATH' ) )
    die();

/* TODO:
 * * Use nonce in settings
 * * Rewrite AP_HoneyPot::check_log_table() to work more effectively
 * * Better WordPress MU support
 */

require_once(dirname (__FILE__) . '/ap-honeypot.class.php');

if ( ! defined( 'APHP_PLUGIN_FULL_PATH' ) )
        define( 'APHP_PLUGIN_FULL_PATH', __FILE__ );

if ( ! defined( 'APHP_PLUGIN_BASENAME' ) )
        define( 'APHP_PLUGIN_BASENAME', plugin_basename( APHP_PLUGIN_FULL_PATH ) );

if ( ! defined( 'APHP_PLUGIN_MENU_PARENT' ) )
        define( 'APHP_PLUGIN_MENU_PARENT', 'options-general.php' );

if ( ! defined( 'APHP_PLUGIN_SETTINGS_URL' ) )
		define( 'APHP_PLUGIN_SETTINGS_URL', admin_url(APHP_PLUGIN_MENU_PARENT . '?page=' . APHP_PLUGIN_BASENAME) );

$ap_honeypot = new AP_HoneyPot();
