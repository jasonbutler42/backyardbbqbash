<?php

// If uninstall is not called from WordPress exit 
if( !defined( 'WP_UNINSTALL_PLUGIN' ) )
exit ();

// Delete options from options table ONLY if free version of plugin is not in plugins directory
if( !file_exists( WP_PLUGIN_DIR . '/fancier-author-box/ts-fab.php' ) ) {
	delete_option( 'ts_fab_display_settings' );
	delete_option( 'ts_fab_tabs_settings' );
	
	$users = get_users();
	foreach( $users as $user ) {
		delete_user_meta( $user->ID, 'ts_fab_twitter' );
		delete_user_meta( $user->ID, 'ts_fab_facebook' );
		delete_user_meta( $user->ID, 'ts_fab_googleplus' );
		delete_user_meta( $user->ID, 'ts_fab_position' );
		delete_user_meta( $user->ID, 'ts_fab_company' );
		delete_user_meta( $user->ID, 'ts_fab_company_url' );
	}
}