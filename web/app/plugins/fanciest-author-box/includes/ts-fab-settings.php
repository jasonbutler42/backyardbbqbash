<?php

/*
 * Add settings page, under Tools menu
 * Contextual help callback function
 * Register settings
 * Settings sections and fields callback functions
 * Settings page callback function
 */



/**
 * Add settings page, under Tools menu
 *
 * @since 1.0
 */
function ts_fab_add_settings_page() {

	global $ts_fab_settings_page;

	$ts_fab_settings_page = add_options_page(
		'Fanciest Author Box by ThematoSoup',
		'Fanciest Author Box',
		'manage_options',
		'fanciest_author_box',
		'ts_fab_show_settings_page'
	);
	add_action( 'admin_print_styles-' . $ts_fab_settings_page, 'ts_fab_admin_scripts' );

	// Check if WP version is 3.3 or higher, add contextual help
	global $wp_version;
	if ( version_compare( $wp_version, '3.3') >= 0 ) {
		add_action( 'load-' . $ts_fab_settings_page, 'ts_fab_add_help_tab' );
	}

}
add_action( 'admin_menu', 'ts_fab_add_settings_page' );



/**
 * Enqueue admin scripts for color picker
 *
 * @since 1.0
 */
function ts_fab_admin_scripts() {

	wp_enqueue_style( 'farbtastic' );
	wp_enqueue_script( 'farbtastic' );
	
	$js_url = plugins_url( 'js/ts-fab-admin.js', dirname(__FILE__) );
	wp_enqueue_script( 'ts_fab_admin_js', $js_url, array( 'farbtastic', 'jquery' ) );

	$css_url = plugins_url( 'css/ts-fab-admin.css', dirname(__FILE__) );
	wp_enqueue_style( 'ts_fab_admin_css', $css_url );
	
}



/**
 * Callback function for contextual help, requires WP 3.3
 *
 * @since 1.0
*/
function ts_fab_add_help_tab () {

	global $wp_version;
	if ( version_compare( $wp_version, '3.3') >= 0 ) {
	
		global $ts_fab_settings_page;

		$screen = get_current_screen();

		/*
		* Check if current screen is My Admin Page
		* Don't add help tab if it's not
		*/
		if ( $screen->id != $ts_fab_settings_page )
			return;

		// Add my_help_tab if current screen is Fanciest Author Box settings page
		$screen->add_help_tab( array(
			'id'	=> 'ts_fab_help_tab',
			'title'	=> __( 'Display settings', 'ts-fab' ),
			'content'	=> __( '
				<p>
					<strong>Automatically add to posts</strong><br />
					Show in posts, pages and custom posts options allow you to automatically add Fanciest Author Box before or after your posts, pages and custom posts.
				</p>
				<p>
					<strong>Shortcode</strong><br />
					You can use [ts_fab] shortcode to add Fanciest Author Box inside your posts.
				</p>				
				<p>
					<strong>Widget</strong><br />
					If you\'re using a theme with widgetized sidebars Fanciest Author Box widget allows you to add Fanciest Author Box to your site sidebar.
				</p>				
				<p>
					<strong>Template tag</strong><br />
					Fanciest Author Box can be added anywhere in your theme by using ts_fab() template tag.
				</p>				
			', 'ts-fab' ),
		) );

		$screen->add_help_tab( array(
			'id'	=> 'ts_fab_another_tab',
			'title'	=> __( 'Tabs settings', 'ts-fab' ),
			'content'	=> __( '
				<p>
					<strong>Twitter cache interval</strong><br />
					Because of Twitter API limits of 150 unauthenticated calls per hour Twitter info for each user is stored as a cache object.
				</p>
				<p>
					<strong>Facebook Subscribe</strong><br />
					You must <a href="https://www.facebook.com/about/subscribe">enable subscriptions for your Facebook profile</a> before Subscribe button can appear.
				</p>				
				<p>
					<strong>Default tabs in shortcode and template tag</strong><br />
					You can override default tabs settings in shortcode and template tag by using \'tabs\' parameter:<br />
					<strong>Shortcode:</strong> [ts_fab tabs="bio,twitter,facebook,googleplus,latest_posts"]<br />
					<strong>Template tag:</strong> ts_fab( $context = \'\', $authorid = \'\', $tabs = \'bio,twitter,facebook,googleplus,latest_posts\' )
				</p>				
			', 'ts-fab' ),
		) );

		$screen->set_help_sidebar(
			'
			<p>' . __( 'Check out Fanciest Author Box ', 'ts-fab' ) . '<strong><a href="http://docs.thematosoup.com/plugins/fanciest-author-box/">' . __( 'plugin documentation', 'ts-fab' ) . '</a></strong> ' . __( 'or get in touch with us on:', 'ts-fab' ) . '</p>
			<ul>
				<li><a href="http://thematosoup.com">' . __( 'Our website', 'ts-fab' ) . '</a></li>
				<li><a href="http://twitter.com/#!/thematosoup">Twitter</a></li>
				<li><a href="http://www.facebook.com/ThematoSoup">Facebook</a></li>
				<li><a href="http://plus.google.com/104360438826479763912">Google+</a></li>
				<li><a href="http://www.linkedin.com/company/thematosoup">LinkedIn</a></li>
			</ul>
			'
		);

	}

}




/**
 * Register settings
 *
 * Plugin stores two options arrays, one for each tab in settings page, each one has its own settings section as well
 *
 * @since 1.0
 */
add_action( 'admin_init', 'ts_fab_initialize_plugin_options' );
function ts_fab_initialize_plugin_options() {

	// If the theme options don't exist, create them.
	if( false == get_option( 'ts_fab_display_settings' ) ) {
		add_option( 'ts_fab_display_settings' );
	}

	if( false == get_option( 'ts_fab_tabs_settings' ) ) {
		add_option( 'ts_fab_tabs_settings' );
	}


	// Add Display Settings section
	add_settings_section(
		'ts_fab_display_settings_section',
		__( 'Display Settings', 'ts-fab' ),
		'ts_fab_display_settings_callback',
		'ts_fab_display_settings'
	);

	// Add Display Settings fields
	add_settings_field(
		'show_in_posts',
		__( 'Show in posts', 'ts-fab' ),
		'ts_fab_show_in_posts_callback',
		'ts_fab_display_settings',
		'ts_fab_display_settings_section',
		array(
			__( 'Select where to display.', 'ts-fab' )
		)
	);

	add_settings_field(
		'show_in_pages',
		__( 'Show in pages', 'ts-fab' ),
		'ts_fab_show_in_pages_callback',
		'ts_fab_display_settings',
		'ts_fab_display_settings_section',
		array(
			__( 'Select where to display.', 'ts-fab' )
		)
	);
	
	// Add a settings field for each public custom post type
	$args = array(
		'public'   => true,
		'_builtin' => false
	); 
	$output = 'names';
	$operator = 'and';
	$custom_post_types = get_post_types( $args, $output, $operator ); 
	foreach ( $custom_post_types  as $custom_post_type ) {
	
		$custom_post_type_object = get_post_type_object( $custom_post_type );
		add_settings_field(
			'show_in_' . $custom_post_type,
			__( 'Show in', 'ts-fab' ) . ' ' . $custom_post_type_object->label,
			'ts_fab_show_in_custom_post_type_callback',
			'ts_fab_display_settings',
			'ts_fab_display_settings_section',
			array(
				__( 'Display Fanciest Author Box in ' . $custom_post_type_object->label . ' custom post type.', 'ts-fab' ),
				$custom_post_type
			)
		);
		
	}	

	add_settings_field(
		'show_in_feeds',
		__( 'Show in feeds', 'ts-fab' ),
		'ts_fab_show_in_feeds_callback',
		'ts_fab_display_settings',
		'ts_fab_display_settings_section',
		array(
			__( 'Display simplified author box in RSS feeds', 'ts-fab' )
		)
	);

	add_settings_field(
		'execution_priority',
		__( 'Execution priority', 'ts-fab' ),
		'ts_fab_execution_priority_callback',
		'ts_fab_display_settings',
		'ts_fab_display_settings_section',
		array(
			__( 'If everything is working fine and Fanciest Author Box is showing in your posts, no need to touch this. If author box is not displaying, try lowering or raising this number.', 'ts-fab' )
		)
	);

	add_settings_field(
		'tabs_style',
		__( 'Tabs style', 'ts-fab' ),
		'ts_fab_tabs_style_callback',
		'ts_fab_display_settings',
		'ts_fab_display_settings_section',
		array(
			__( 'Show only icons or icons and text in tabs', 'ts-fab' )
		)
	);

	add_settings_field(
		'float_photo',
		__( 'Avatar position', 'ts-fab' ),
		'ts_fab_float_photo_callback',
		'ts_fab_display_settings',
		'ts_fab_display_settings_section',
		array(
			__( 'Avatar can be shown left to the text or above it', 'ts-fab' )
		)
	);

	// Add Display Settings section
	add_settings_section(
		'ts_fab_color_settings_section',
		__( 'Color Settings', 'ts-fab' ),
		'ts_fab_color_settings_callback',
		'ts_fab_display_settings'
	);

	add_settings_field(
		'inactive_tab',
		__( 'Inactive tab colors', 'ts-fab' ),
		'ts_fab_color_picker_callback',
		'ts_fab_display_settings',
		'ts_fab_color_settings_section',
		array(
			'inactive_tab',
			array(
				'_background'	=> __( 'Background', 'ts-fab' ),
				'_border'		=> __( 'Border', 'ts-fab' ),
				'_color'		=> __( 'Text', 'ts-fab' )
			)
		)
	);

	add_settings_field(
		'active_tab',
		__( 'Active tab colors', 'ts-fab' ),
		'ts_fab_color_picker_callback',
		'ts_fab_display_settings',
		'ts_fab_color_settings_section',
		array(
			'active_tab',
			array(
				'_background'	=> __( 'Background', 'ts-fab' ),
				'_border'		=> __( 'Border', 'ts-fab' ),
				'_color'		=> __( 'Text', 'ts-fab' )
			)
		)
	);

	add_settings_field(
		'tab_content',
		__( 'Tab content colors', 'ts-fab' ),
		'ts_fab_color_picker_callback',
		'ts_fab_display_settings',
		'ts_fab_color_settings_section',
		array(
			'tab_content',
			array(
				'_background'	=> __( 'Background', 'ts-fab' ),
				'_border'		=> __( 'Border', 'ts-fab' ),
				'_color'		=> __( 'Text', 'ts-fab' )
			)
		)
	);
	// End adding Display Settings fields

	// Register Display Settings setting
	register_setting(
		'ts_fab_display_settings',
		'ts_fab_display_settings',
		'ts_fab_validate_fields'
	);


	// Add Tabs Settings section
	add_settings_section(
		'ts_fab_tabs_settings_section',
		__( 'Tabs Settings', 'ts-fab' ),
		'ts_fab_tabs_settings_callback',
		'ts_fab_tabs_settings'
	);

	// Add Tabs Settings fields
	add_settings_field(
		'show_bio_tab',
		__( 'Bio', 'ts-fab' ),
		'ts_fab_show_bio_tab_callback',
		'ts_fab_tabs_settings',
		'ts_fab_tabs_settings_section',
		array(
			__( 'Display bio tab', 'ts-fab' )
		)
	);

	add_settings_field(
		'show_twitter_tab',
		'Twitter',
		'ts_fab_show_twitter_tab_callback',
		'ts_fab_tabs_settings',
		'ts_fab_tabs_settings_section',
		array(
			__( 'Display Twitter tab', 'ts-fab' ),
			__( 'Twitter Consumer Key', 'ts-fab' ),
			__( 'Twitter Consumer Secret', 'ts-fab' ),
			__( 'Twitter cache interval (minutes): ', 'ts-fab' ),
			__( 'Show Twitter bio', 'ts-fab' ),
			__( 'Show latest tweet', 'ts-fab' ),
			__( 'Show follower count', 'ts-fab' ),
			'style="margin-right:15px; display: inline-block"',
			__( 'Since Twitter has made the transition to API 1.1 it is necessary to jump through a few extra hoops to get latest tweet and Twitter bio to show. You need to go to <a href="https://dev.twitter.com/apps/new">https://dev.twitter.com/apps/new</a> and log in, if necessary, supply the necessary required fields, accept the TOS, and solve the CAPTCHA. Then submit the form, copy the consumer key (API key) and consumer secret from the screen into fields above this text and you\'re done. Cause we all love simplicity, right?', 'ts-fab' )
		)
	);

	add_settings_field(
		'facebook',
		'Facebook',
		'ts_fab_show_facebook_tab_callback',
		'ts_fab_tabs_settings',
		'ts_fab_tabs_settings_section',
		array(
			__( 'Display Facebook tab', 'ts-fab' ),
			__( 'Do not load Facebook Javascript SDK script (useful if other plugins already do it and there is a conflict)', 'ts-fab' ),
		)
	);

	add_settings_field(
		'googleplus',
		'Google+',
		'ts_fab_show_googleplus_tab_callback',
		'ts_fab_tabs_settings',
		'ts_fab_tabs_settings_section',
		array(
			__( 'Display Google+ tab', 'ts-fab' )
		)
	);

	add_settings_field(
		'linkedin',
		'LinkedIn',
		'ts_fab_show_linkedin_tab_callback',
		'ts_fab_tabs_settings',
		'ts_fab_tabs_settings_section',
		array(
			__( 'Display LinkedIn tab', 'ts-fab' )
		)
	);

	add_settings_field(
		'youtube',
		'YouTube',
		'ts_fab_show_youtube_tab_callback',
		'ts_fab_tabs_settings',
		'ts_fab_tabs_settings_section',
		array(
			__( 'Display YouTube tab', 'ts-fab' )
		)
	);

	add_settings_field(
		'pinterest',
		'Pinterest',
		'ts_fab_show_pinterest_tab_callback',
		'ts_fab_tabs_settings',
		'ts_fab_tabs_settings_section',
		array(
			__( 'Display Pinterest tab', 'ts-fab' )
		)
	);

	add_settings_field(
		'latest_posts',
		__( 'Latest posts', 'ts-fab' ),
		'ts_fab_show_latest_posts_tab_callback',
		'ts_fab_tabs_settings',
		'ts_fab_tabs_settings_section',
		array(
			__( 'Display latest posts tab', 'ts-fab' ),
			__( 'Number of latest posts to show:', 'ts-fab' )
		)
	);

	add_settings_field(
		'custom_tab',
		__( 'Custom tab', 'ts-fab' ),
		'ts_fab_show_custom_tab_callback',
		'ts_fab_tabs_settings',
		'ts_fab_tabs_settings_section',
		array(
			__( 'Display custom tab', 'ts-fab' ),
			__( 'Custom tab heading', 'ts-fab' ),
			__( 'Custom tab content', 'ts-fab' ),
			__( 'Allow authors to override custom tab in their user profiles', 'ts-fab' ),
			__( '(if not provided in either plugin or user settings, custom tab will not be visible)', 'ts-fab' ),
		)
	);
	
	// Add setting fields for tabs added by theme or other plugins
	$additional_tabs = ts_fab_additional_tabs();
	// Check if there are any additional tabs
	if( !empty( $additional_tabs ) ) {
		foreach( $additional_tabs as $additional_tab_key => $additional_tab_value ) {
			
			add_settings_field(
				$additional_tab_key,
				$additional_tab_value['name'],
				$additional_tab_value['field_callback'],
				'ts_fab_tabs_settings',
				'ts_fab_tabs_settings_section',
				array(
					__( 'Display ' . $additional_tab_value['name'] . ' tab', 'ts-fab' )
				)
			);
			
		} // end foreach
	}
	
	// Hidden field to determine if settings have been saved already
	add_settings_field(
		'tabs_settings_saved',
		'',
		'ts_fab_tabs_settings_saved_callback',
		'ts_fab_tabs_settings',
		'ts_fab_tabs_settings_section'
	);
	// End adding Tabs Settings fields

	// Register Tabs Settings setting
	register_setting(
		'ts_fab_tabs_settings',
		'ts_fab_tabs_settings',
		'ts_fab_validate_fields'
	);

}




/**
 * Display Settings add_settings_section function callback
 *
 * @since 1.0
 */
function ts_fab_display_settings_callback() { 

	'<p>' . _e( 'Select where and how Fanciest Author Box appears in your posts, pages and custom posts. For individual tab settings, go to Tabs Settings.', 'ts-fab' ) . '</p>';
	
}



/**
 * Color Settings add_settings_section function callback
 *
 * @since 1.0
 */
function ts_fab_color_settings_callback() { 

	// Returns nothing	
	
}



/**
 * Tabs Settings add_settings_section function callback
 *
 * @since 1.0
 */
function ts_fab_tabs_settings_callback() { 

	'<p>' . _e( 'These options can be overriden when Fanciest Author is displayed using widget, shortcode or template tag. For display settings, go to Display Settings tab.', 'ts-fab' ) . '</p>';
	
}



/**
 * Show in posts field callback
 *
 * @since 1.0
 */
function ts_fab_show_in_posts_callback( $args ) { 

	$options = ts_fab_get_display_settings(); ?>

	<select id="show_in_posts" name="ts_fab_display_settings[show_in_posts]">
		<option value="above" <?php selected( $options['show_in_posts'], 'above', true); ?>><?php _e( 'Above', 'ts-fab' ); ?></option>
		<option value="below" <?php selected( $options['show_in_posts'], 'below', true); ?>><?php _e( 'Below', 'ts-fab' ); ?></option>
		<option value="both" <?php selected( $options['show_in_posts'], 'both', true); ?>><?php _e( 'Both', 'ts-fab' ); ?></option>
		<option value="no" <?php selected( $options['show_in_posts'], 'no', true); ?>><?php _e( 'No', 'ts-fab' ); ?></option>
	</select>

<?php }



/**
 * Show in pages field callback
 *
 * @since 1.0
 */
function ts_fab_show_in_pages_callback( $args ) { 

	$options = ts_fab_get_display_settings(); ?>

	<select id="show_in_pages" name="ts_fab_display_settings[show_in_pages]">
		<option value="above" <?php selected( $options['show_in_pages'], 'above', true); ?>><?php _e( 'Above', 'ts-fab' ); ?></option>
		<option value="below" <?php selected( $options['show_in_pages'], 'below', true); ?>><?php _e( 'Below', 'ts-fab' ); ?></option>
		<option value="both" <?php selected( $options['show_in_pages'], 'both', true); ?>><?php _e( 'Both', 'ts-fab' ); ?></option>
		<option value="no" <?php selected( $options['show_in_pages'], 'no', true); ?>><?php _e( 'No', 'ts-fab' ); ?></option>
	</select>
	
<?php }



/**
 * Show in custom post types callback
 *
 * @since 1.0
 */
function ts_fab_show_in_custom_post_type_callback( $args ) { 

	$options = ts_fab_get_display_settings();
	$custom_post_type = 'show_in_' . $args[1]; ?>

	<select id="<?php echo $custom_post_type; ?>" name="ts_fab_display_settings[<?php echo $custom_post_type; ?>]">
		<option value="above" <?php selected( $options["$custom_post_type"], 'above', true); ?>><?php _e( 'Above', 'ts-fab' ); ?></option>
		<option value="below" <?php selected( $options["$custom_post_type"], 'below', true); ?>><?php _e( 'Below', 'ts-fab' ); ?></option>
		<option value="both" <?php selected( $options["$custom_post_type"], 'both', true); ?>><?php _e( 'Both', 'ts-fab' ); ?></option>
		<option value="no" <?php selected( $options["$custom_post_type"], 'no', true); ?>><?php _e( 'No', 'ts-fab' ); ?></option>
	</select>

<?php }



/**
 * Show in feeds field callback
 *
 * @since 1.3
 */
function ts_fab_show_in_feeds_callback( $args ) { 

	$options = ts_fab_get_display_settings(); ?>

	<select id="show_in_feeds" name="ts_fab_display_settings[show_in_feeds]">
		<option value="yes" <?php selected( $options['show_in_feeds'], 'yes', true); ?>><?php _e( 'Yes', 'ts-fab' ); ?></option>
		<option value="no" <?php selected( $options['show_in_feeds'], 'no', true); ?>><?php _e( 'No', 'ts-fab' ); ?></option>
	</select>
	
<?php }



/**
 * Display priority field callback
 *
 * @since 1.3.4
 */
function ts_fab_execution_priority_callback( $args ) { 

	$options = ts_fab_get_display_settings(); ?>

	<input name="ts_fab_display_settings[execution_priority]" type="number" step="1" min="1" id="execution_priority" value="<?php echo $options['execution_priority']; ?>" class="small-text" />
	<p class="description"><?php echo $args[0]; ?></p>
	
<?php }



/**
 * Tabs style
 *
 * @since 1.3
 */
function ts_fab_tabs_style_callback( $args ) { 

	$options = ts_fab_get_display_settings(); ?>

	
	<div>
		<div>
			<label for="tabs_style_full">
				<input type="radio" id="tabs_style_full" name="ts_fab_display_settings[tabs_style]" value="full" <?php if( isset( $options['tabs_style'] ) ) checked( 'full', $options['tabs_style'], true ); ?> />
				Icons and text
			</label>
		</div>
		<div>
			<label for="tabs_style_icons">
				<input type="radio" id="tabs_style_icons" name="ts_fab_display_settings[tabs_style]" value="icons" <?php if( isset( $options['tabs_style'] ) ) checked( 'icons', $options['tabs_style'], true ); ?> />
				Only icons
			</label>
		</div>
		<div>
			<label for="tabs_style_text">
				<input type="radio" id="tabs_style_text" name="ts_fab_display_settings[tabs_style]" value="text" <?php if( isset( $options['tabs_style'] ) ) checked( 'text', $options['tabs_style'], true ); ?> />
				Only text
			</label>
		</div>
	</div>
	
<?php }



/**
 * Float photo
 *
 * @since 1.4.6
 */
function ts_fab_float_photo_callback( $args ) { 

	$options = ts_fab_get_display_settings(); ?>

	<div>
		<div>
			<label for="float_photo_floated">
				<input type="radio" id="float_photo_floated" name="ts_fab_display_settings[float_photo]" value="floated" <?php if( isset( $options['float_photo'] ) ) checked( 'floated', $options['float_photo'], true ); ?> />
				Floated left
			</label>
		</div>
		<div>
			<label for="float_photo_above">
				<input type="radio" id="float_photo_above" name="ts_fab_display_settings[float_photo]" value="above" <?php if( isset( $options['float_photo'] ) ) checked( 'above', $options['float_photo'], true ); ?> />
				Above text
			</label>
		</div>
	</div>
	
<?php }



/**
 * Color picker callback
 *
 * @since 1.0
 */
function ts_fab_color_picker_callback( $args ) { 

	$options = ts_fab_get_display_settings(); 
	$background = $args[0] . '_background';
	$border = $args[0] . '_border_color';
	$color = $args[0] . '_color';
	
	foreach( $args[1] as $key => $value ) {
		$field = $args[0] . $key;	
		?>

		<span>
			<input type="text" id="<?php echo $field; ?>" name="ts_fab_display_settings[<?php echo $field; ?>]" class="ts-fab-color-input"  value="<?php echo $options[$field]; ?>" />
			<a href="#" id="pickcolor_<?php echo $field; ?>" class="pickcolor" style="box-sizing:content-box;-moz-box-sizing:content-box;padding: 3px 11px; border: 1px solid #dfdfdf; margin: 0 7px 0 3px; background-color: <?php echo $options[$field]; ?>;"></a>
			<div style="z-index: 100; background:#eee; border:1px solid #ccc; position:absolute; display:none;"></div>
			<span class="description"><?php echo $value; ?></span>
		</span><br />
		
		<?php
	}
    		
}



/**
 * Show bio tab field callback
 *
 * @since 1.0
 */
function ts_fab_show_bio_tab_callback( $args ) { 

	$options = ts_fab_get_tabs_settings(); ?>

	<label for="bio">
		<input type="checkbox" id="bio" name="ts_fab_tabs_settings[bio]" value="1" <?php checked( 1, $options['bio'], true ); ?> />
		<?php echo $args[0]; ?>
	</label>

<?php }



/**
 * Show Twitter tab field callback
 *
 * @since 1.0
 */
function ts_fab_show_twitter_tab_callback( $args ) { 

	$options = ts_fab_get_tabs_settings(); ?>

	<label for="twitter">
		<input type="checkbox" id="twitter" name="ts_fab_tabs_settings[twitter]" value="1" <?php checked( 1, $options['twitter'], true ); ?> />
		<?php echo $args[0]; ?>
	</label><br />

	<label for="twitter_consumer_key">
		<input type="text" id="twitter_consumer_key" name="ts_fab_tabs_settings[twitter_consumer_key]"  size="45" <?php if( isset( $options['twitter_consumer_key'] ) ) echo 'value="' . $options['twitter_consumer_key'] . '"'; ?> />
		<span class="description"><?php echo $args[1]; ?></span>
	</label><br />

	<?php $twitter_consumer_secret = ( isset( $options['twitter_consumer_secret'] ) ? $options['twitter_consumer_secret'] : '' ); ?>
	<label for="twitter_consumer_secret">
		<input type="text" id="twitter_consumer_secret" name="ts_fab_tabs_settings[twitter_consumer_secret]" size="45" <?php if( isset( $options['twitter_consumer_secret'] ) ) echo 'value="' . $options['twitter_consumer_secret'] . '"'; ?> />
		<span class="description"><?php echo $args[2]; ?></span>
	</label><br />
	
	<div class="description"><?php echo $args[8]; ?></div>

	<div style="margin-top: 5px;">
		<label for="twitter_bio" <?php echo $args[7]; ?>>
			<input type="checkbox" id="twitter_bio" name="ts_fab_tabs_settings[twitter_bio]" value="1" <?php checked( 1, $options['twitter_bio'], true ); ?> />
			<?php echo ' ' . $args[4]; ?>
		</label>
	
		<label for="twitter_tweet" <?php echo $args[7]; ?>>
			<input type="checkbox" id="twitter_tweet" name="ts_fab_tabs_settings[twitter_tweet]" value="1" <?php checked( 1, $options['twitter_tweet'], true ); ?> />
			<?php echo ' ' . $args[5]; ?>
		</label>
	
		<label for="twitter_count" <?php echo $args[7]; ?>>
			<input type="checkbox" id="twitter_count" name="ts_fab_tabs_settings[twitter_count]" value="1" <?php checked( 1, $options['twitter_count'], true ); ?> />
			<?php echo ' ' . $args[6]; ?>
		</label><br />
	
		<span><?php echo $args[3]; ?></span>
		<select id="twitter_cache_interval" name="ts_fab_tabs_settings[twitter_cache_interval]">
			<option value="1" <?php selected( $options['twitter_cache_interval'], 1, true); ?>>1</option>
			<option value="5" <?php selected( $options['twitter_cache_interval'], 5, true); ?>>5</option>
			<option value="10" <?php selected( $options['twitter_cache_interval'], 10, true); ?>>10</option>
			<option value="15" <?php selected( $options['twitter_cache_interval'], 15, true); ?>>15</option>
			<option value="20" <?php selected( $options['twitter_cache_interval'], 20, true); ?>>20</option>
			<option value="30" <?php selected( $options['twitter_cache_interval'], 30, true); ?>>30</option>
			<option value="60" <?php selected( $options['twitter_cache_interval'], 60, true); ?>>60</option>
		</select>
	</div>
			
<?php }



/**
 * Show Facebook tab field callback
 *
 * @since 1.0
 */
function ts_fab_show_facebook_tab_callback( $args ) { 

	$options = ts_fab_get_tabs_settings(); ?>

	<label for="facebook">
		<input type="checkbox" id="facebook" name="ts_fab_tabs_settings[facebook]" value="1" <?php checked( 1, $options['facebook'], true ); ?> />
		<?php echo $args[0]; ?>
	</label><br />

	<label for="facebook_sdk">
		<input type="checkbox" id="facebook_sdk" name="ts_fab_tabs_settings[facebook_sdk]" value="1" <?php checked( 1, $options['facebook_sdk'], true ); ?> />
		<?php echo $args[1]; ?>
	</label>

<?php }



/**
 * Show Google+ tab field callback
 *
 * @since 1.0
 */
function ts_fab_show_googleplus_tab_callback( $args ) { 

	$options = ts_fab_get_tabs_settings(); ?>

	<label for="googleplus">
		<input type="checkbox" id="googleplus" name="ts_fab_tabs_settings[googleplus]" value="1" <?php checked( 1, $options['googleplus'], true ); ?> />
		<?php echo $args[0]; ?>
	</label>

<?php }



/**
 * Show LinkedIn tab field callback
 *
 * @since 1.3
 */
function ts_fab_show_linkedin_tab_callback( $args ) { 

	$options = ts_fab_get_tabs_settings(); ?>

	<label for="linkedin">
		<input type="checkbox" id="linkedin" name="ts_fab_tabs_settings[linkedin]" value="1" <?php checked( 1, $options['linkedin'], true ); ?> />
		<?php echo $args[0]; ?>
	</label>

<?php }


/**
 * Show YouTube tab field callback
 *
 * @since 1.9
 */
function ts_fab_show_youtube_tab_callback( $args ) { 

	$options = ts_fab_get_tabs_settings(); ?>

	<label for="youtube">
		<input type="checkbox" id="youtube" name="ts_fab_tabs_settings[youtube]" value="1" <?php checked( 1, $options['youtube'], true ); ?> />
		<?php echo $args[0]; ?>
	</label>

<?php }


/**
 * Show Pinterest tab field callback
 *
 * @since 1.9
 */
function ts_fab_show_pinterest_tab_callback( $args ) { 

	$options = ts_fab_get_tabs_settings(); ?>

	<label for="pinterest">
		<input type="checkbox" id="pinterest" name="ts_fab_tabs_settings[pinterest]" value="1" <?php checked( 1, $options['pinterest'], true ); ?> />
		<?php echo $args[0]; ?>
	</label>

<?php }



/**
 * Show latest posts tab field callback
 *
 * @since 1.0
 */
function ts_fab_show_latest_posts_tab_callback( $args ) { 

	$options = ts_fab_get_tabs_settings(); ?>

	<label for="latest_posts">
		<input type="checkbox" id="latest_posts" name="ts_fab_tabs_settings[latest_posts]" value="1" <?php checked( 1, $options['latest_posts'], true ); ?> />
		<?php echo $args[0]; ?>
	</label><br />

	<span><?php echo $args[1]; ?></span>
	<select id="latest_posts_count" name="ts_fab_tabs_settings[latest_posts_count]">
		<option value="1" <?php selected( $options['latest_posts_count'], 1, true); ?>>1</option>
		<option value="2" <?php selected( $options['latest_posts_count'], 2, true); ?>>2</option>
		<option value="3" <?php selected( $options['latest_posts_count'], 3, true); ?>>3</option>
		<option value="4" <?php selected( $options['latest_posts_count'], 4, true); ?>>4</option>
		<option value="5" <?php selected( $options['latest_posts_count'], 5, true); ?>>5</option>
	</select>
	
<?php }



/**
 * Show latest posts tab field callback
 *
 * @since 1.0
 */
function ts_fab_show_custom_tab_callback( $args ) { 

	$options = ts_fab_get_tabs_settings(); ?>

	<label for="custom" style="display:block;margin-bottom:10px">
		<input type="checkbox" id="custom" name="ts_fab_tabs_settings[custom]" value="1" <?php checked( 1, $options['custom'], true ); ?> />
		<?php echo $args[0]; ?>
	</label>

	<?php $hide_custom_extra = ( $options['custom'] != 1 ? 'style="display:none"' : '' ); ?>
	<div id="ts_fab_custom_tab_extra" <?php echo $hide_custom_extra; ?>>
		<label for="custom_tab_title" style="display:block;margin-bottom:10px">
			<?php echo $args[1]; ?><br />
			<input type="text" id="custom_tab_title" name="ts_fab_tabs_settings[custom_tab_title]" <?php if( isset( $options['custom_tab_title'] ) ) echo 'value="' . $options['custom_tab_title'] . '"'; ?> /><br />
			<span class="description"><?php echo $args[4]; ?></span>
		</label>
	
		<label for="custom_tab_content" style="display:block;margin-bottom:10px">
			<?php echo $args[2]; ?><br />
			<textarea id="custom_tab_content" rows="5" cols="50" name="ts_fab_tabs_settings[custom_tab_content]"><?php if( isset( $options['custom_tab_content'] ) ) echo $options['custom_tab_content']; ?></textarea>
		</label>

		<div><?php echo $args[3]; ?></div>
		<div>
			<label for="custom_tab_override_no">
				<input type="radio" id="custom_tab_override_no" name="ts_fab_tabs_settings[custom_tab_override]" value="no" <?php if( isset( $options['custom_tab_override'] ) ) checked( 'no', $options['custom_tab_override'], true ); ?> />
				No
			</label>
		<div>
			<label for="custom_tab_override_content">
				<input type="radio" id="custom_tab_override_content" name="ts_fab_tabs_settings[custom_tab_override]" value="content" <?php if( isset( $options['custom_tab_override'] ) ) checked( 'content', $options['custom_tab_override'], true ); ?> />
				Only content
			</label>
		<div>
		</div>
			<label for="custom_tab_override_both">
				<input type="radio" id="custom_tab_override_both" name="ts_fab_tabs_settings[custom_tab_override]" value="1" <?php if( isset( $options['custom_tab_override'] ) ) checked( 1, $options['custom_tab_override'], true ); ?> />
				Both title and content
			</label>
		</div>
	</div>
	
<?php }



/**
 * Settings have been saved callback
 * Used so it doesn't revert to all checked (defaults) when no fields are checked
 *
 * @since 1.0
 */
function ts_fab_tabs_settings_saved_callback( $args ) { ?>

	<input type="hidden" id="tabs_settings_saved" name="ts_fab_tabs_settings[tabs_settings_saved]" value="1" />

<?php }



/**
 * Show settings page callback function
 *
 * @since 1.0
 */
function ts_fab_show_settings_page() { ?>

	<div class="wrap">
		<div id="icon-users" class="icon32"></div>
		<h2>Fanciest Author Box by ThematoSoup</h2>
		<p class="description"><?php _e( 'The only author box plugin you\'ll ever need.', 'ts-fab' ); ?></p>
	
		<div id="poststuff" class="ts-fab-poststuff">
			<div id="post-body" class="columns-2">
				<div id="post-body-content">
					<?php $active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'display_settings'; ?>
				
					<h2 class="nav-tab-wrapper" style="padding-bottom:0;">
						<a href="?page=fanciest_author_box&tab=display_settings" class="nav-tab <?php echo $active_tab == 'display_settings' ? 'nav-tab-active' : ''; ?>"><?php _e( 'Display Settings', 'ts-fab' ); ?></a>
						<a href="?page=fanciest_author_box&tab=tabs_settings" class="nav-tab <?php echo $active_tab == 'tabs_settings' ? 'nav-tab-active' : ''; ?>"><?php _e( 'Tabs Settings', 'ts-fab' ); ?></a>
					</h2>
				
					<form method="post" action="options.php">
						<?php
				
							if( $active_tab == 'display_settings' ) {
								settings_fields( 'ts_fab_display_settings' );
								do_settings_sections( 'ts_fab_display_settings' );
								echo '<a id="ts-fab-reset-colors" href="#" style="margin:15px 0 0 230px;display:inline-block">' . __( 'Reset all color settings', 'ts-fab' ) . '</a>';
							} else {
								settings_fields( 'ts_fab_tabs_settings' );
								do_settings_sections( 'ts_fab_tabs_settings' );
							}
				
							submit_button();
				
						?>
					</form>
				</div><!-- #post-body-content -->
				
				<div id="postbox-container-1" class="postbox-container">
					<div class="metabox-holder">	
						<div class="meta-box">
							<div id="fab-promo" class="postbox">
								<h3 style="padding:8px 12px;margin-top:0">ThematoSoup</h3>
								<div class="inside">
									<div>
										<div style="margin-bottom:10px;">
										<a href="https://twitter.com/ThematoSoup" class="twitter-follow-button" data-show-count="false">Follow @ThematoSoup</a><script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>
										</div>
										
										<div style="margin-bottom:10px;">
										<iframe src="//www.facebook.com/plugins/like.php?href=https%3A%2F%2Fwww.facebook.com%2FThematoSoup&amp;width=256&amp;height=35&amp;colorscheme=light&amp;layout=standard&amp;action=like&amp;show_faces=false&amp;send=false" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:256px; height:35px;" allowTransparency="true"></iframe>
										</div>
										
										<div style="margin-bottom:10px;">
										<!-- Place this tag where you want the widget to render. -->
										<div class="g-follow" data-annotation="none" data-height="20" data-href="//plus.google.com/104360438826479763912" data-rel="publisher"></div>
										
										<!-- Place this tag after the last widget tag. -->
										<script type="text/javascript">
										  (function() {
										    var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
										    po.src = 'https://apis.google.com/js/plusone.js';
										    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
										  })();
										</script>
										</div>

										<!-- Begin MailChimp Signup Form -->
										<div id="mc_embed_signup">
										<form style="margin-top:10px;padding-top:10px;border-top:1px solid #ccc;" action="http://thematosoup.us2.list-manage.com/subscribe/post?u=07d28c9976ef3fcdb23b1ed11&amp;id=5a17a1e006" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate>
											<div style="margin-bottom:5px;"><label for="mce-EMAIL">Subscribe to our mailing list</label></div>
											<div style="margin-bottom:5px;"><input type="email" value="" name="EMAIL" class="email" id="mce-EMAIL" placeholder="email address" required></div>
											<div class="clear"><input type="submit" value="Subscribe" name="subscribe" id="mc-embedded-subscribe" class="button button-primary"></div>
										</form>
										</div>
										<!--End mc_embed_signup-->
									</div>
								</div>
							</div>
						</div><!-- .metabox-sortables -->
					</div><!-- .metabox-holder -->
				</div><!-- #postbox-container-1 -->
			</div><!-- #post-body -->
		</div><!-- #poststuff -->
	</div>
<?php }



/**
 * Validate setting fields
 *
 * @since 1.2
 */
function ts_fab_validate_fields( $input) {

	// Create our array for storing the validated options
	$output = array();

	// Loop through each of the incoming options
	foreach( $input as $key => $value ) {

		// Check to see if the current option has a value. If so, process it.
		if( isset( $input[$key] ) ) {

			// Strip all HTML and PHP tags and properly handle quoted strings
			$ts_fab_allowed_tags = array(
				'a' => array(
					'href' => true,
					'title' => true,
					'data-pin-do' => true
				),
				'blockquote' => array(
					'cite' => true,
				),
				'br' => array(),
				'em' => array(),
				'i' => array(),
				'li' => array(),
				'ul' => array(),
				'ol' => array(),
				'p' => array(),
				'div' => array(),
				'strong' => array(),
				'img' => array(
					'alt' => true,
					'class' => true,
					'height' => true,
					'src' => true,
					'width' => true,
				)
			);
	
			$output[$key] = wp_kses( stripslashes( $input[ $key ] ), $ts_fab_allowed_tags );

		}

	}

	// Return the array processing any additional functions filtered by this action
	return apply_filters( 'ts_fab_validate_fields', $output, $input );
	
}