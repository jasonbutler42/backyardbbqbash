<?php

/**
 * Add Fanciest Author Box display additional user fields
 *
 * @since 1.0
 */
add_action( 'edit_user_profile', 'ts_fab_extra_user_details' );
add_action( 'show_user_profile', 'ts_fab_extra_user_details' );
function ts_fab_extra_user_details( $user ) { ?>

	<?php if( user_can( $user, 'edit_posts') ) { ?>

	<h3>Fanciest Author Box <?php _e( 'User Details', 'ts-fab' ); ?></h3>
	
		<table class="form-table">
			<?php
				$userid = $user->ID;
				$user_hide = get_user_meta( $userid, 'ts_fab_user_hide', false );
				( $user_hide == true ) ? $checked = 'checked="checked"' : $checked = '';
			?>
			<tr>
				<th><?php _e( 'Display Fanciest Author Box', 'ts-fab' ); ?></th>
				<td>
					<label for="ts_fab_user_hide">
						<input type="checkbox" name="ts_fab_user_hide" id="ts_fab_user_hide" value="true" <?php echo $checked; ?> />
						<?php _e( 'Do not automatically add Fanciest Author Box to your posts, pages and custom posts.', 'ts-fab' ); ?>
					</label>
				</td>
			</tr>
	
			<tr>
				<th><label for="ts_fab_photo_url">Photo URL</label></th>
	
				<td>
					<div class="uploader">
						<input type="text" class="regular-text" name="ts_fab_photo_url" id="ts_fab_photo_url" value="<?php echo esc_attr( get_the_author_meta( 'ts_fab_photo_url', $user->ID ) ); ?>" />
						<input class="ts-fab-upload-button button" name="ts_fab_photo_url_button" id="ts_fab_photo_url_button" value="<?php _e( 'Upload', 'ts-fab' ); ?>" />
					</div>
					<?php if ( '' != get_the_author_meta( 'ts_fab_photo_url', $user->ID ) ) { ?>
						<img src="<?php echo esc_attr( get_the_author_meta( 'ts_fab_photo_url', $user->ID ) ); ?>" width="64" style="display:inline-block;margin-top:3px" />
					<?php } ?>
					<div><span class="description"><?php _e( 'Photo URL (optional, if left empty Gravatar image will be used), ideally image should be 64x64 px.', 'ts-fab' ); ?></span></div>

				</td>
			</tr>
	
			<tr>
				<th><label for="ts_fab_twitter">Twitter</label></th>
	
				<td>
					<input type="text" name="ts_fab_twitter" id="ts_fab_twitter" value="<?php echo esc_attr( get_the_author_meta( 'ts_fab_twitter', $user->ID ) ); ?>" class="regular-text" /><br />
					<span class="description"><?php _e( 'Your Twitter username (example: thematosoup).', 'ts-fab' ); ?></span>
				</td>
			</tr>

			<?php
				$userid = $user->ID;
				$facebook_widget = get_user_meta( $userid, 'ts_fab_facebook_button', true );
			?>
			<tr>
				<th><?php _e( 'Facebook widget type', 'ts-fab' ); ?></th>
				<td>
					<label style="margin-right: 15px" for="ts_fab_facebook_subscribe">
						<input type="radio" name="ts_fab_facebook_button" id="ts_fab_facebook_subscribe" value="subscribe" <?php checked( $facebook_widget, 'subscribe', true ); ?> />
						<?php _e( 'Follow', 'ts-fab' ); ?>
					</label>
					<label for="ts_fab_facebook_like">
						<input type="radio" name="ts_fab_facebook_button" id="ts_fab_facebook_like" value="like" <?php checked( $facebook_widget, 'like' ); ?> />
						<?php _e( 'Like', 'ts-fab' ); ?>
					</label>
					<div><span class="description"><?php _e( 'Use Follow for profiles (must <a href="https://www.facebook.com/help/201148673283205" target="_blank">start letting people follow you</a> first) and Like for pages.', 'ts-fab' ); ?></span></div>
				</td>
			</tr>

			<tr>
				<th><label for="ts_fab_facebook">Facebook</label></th>
	
				<td>
					<input type="text" name="ts_fab_facebook" id="ts_fab_facebook" value="<?php echo esc_attr( get_the_author_meta( 'ts_fab_facebook', $user->ID ) ); ?>" class="regular-text" /><br />
					<span class="description"><?php _e( 'Your Facebook username or ID. (example: thematosoup)', 'ts-fab' ); ?></span>
				</td>
			</tr>

			<tr>
				<th><label for="ts_fab_googleplus">Google+</label></th>
	
				<td>
					<input type="text" name="ts_fab_googleplus" id="ts_fab_googleplus" value="<?php echo esc_attr( get_the_author_meta( 'ts_fab_googleplus', $user->ID ) ); ?>" class="regular-text" /><br />
					<span class="description"><?php _e( 'Your Google+ ID. (example: 104360438826479763912 or +SlobodanManic)', 'ts-fab' ); ?></span>
				</td>
			</tr>

			<tr>
				<th><label for="ts_fab_linkedin">LinkedIn</label></th>
	
				<td>
					<input type="text" name="ts_fab_linkedin" id="ts_fab_linkedin" value="<?php echo esc_attr( get_the_author_meta( 'ts_fab_linkedin', $user->ID ) ); ?>" class="regular-text" /><br />
					<span class="description"><?php _e( 'Your LinkedIn username or full profile URL. (example: slobodanmanic or http://www.linkedin.com/in/slobodanmanic)', 'ts-fab' ); ?></span>
				</td>
			</tr>

			<tr>
				<th><label for="ts_fab_youtube">YouTube</label></th>
	
				<td>
					<input type="text" name="ts_fab_youtube" id="ts_fab_youtube" value="<?php echo esc_attr( get_the_author_meta( 'ts_fab_youtube', $user->ID ) ); ?>" class="regular-text" /><br />
					<span class="description"><?php _e( 'Your YouTube username. (example: ThematoSoupChannel)', 'ts-fab' ); ?></span>
				</td>
			</tr>

			<tr>
				<th><label for="ts_fab_pinterest">Pinterest</label></th>
	
				<td>
					<input type="text" name="ts_fab_pinterest" id="ts_fab_pinterest" value="<?php echo esc_attr( get_the_author_meta( 'ts_fab_pinterest', $user->ID ) ); ?>" class="regular-text" /><br />
					<span class="description"><?php _e( 'Your Pinterest username. (example: slobodanmanic)', 'ts-fab' ); ?></span>
				</td>
			</tr>

			<tr>
				<th><label for="ts_fab_position"><?php _e( 'Position', 'ts-fab' ); ?></label></th>
	
				<td>
					<input type="text" name="ts_fab_position" id="ts_fab_position" value="<?php echo esc_attr( get_the_author_meta( 'ts_fab_position', $user->ID ) ); ?>" class="regular-text" /><br />
					<span class="description"><?php _e( 'Your position.', 'ts-fab' ); ?></span>
				</td>
			</tr>
	
			<tr>
				<th><label for="ts_fab_company"><?php _e( 'Company', 'ts-fab' ); ?></label></th>
	
				<td>
					<input type="text" name="ts_fab_company" id="ts_fab_company" value="<?php echo esc_attr( get_the_author_meta( 'ts_fab_company', $user->ID ) ); ?>" class="regular-text" /><br />
					<span class="description"><?php _e( 'Your company.', 'ts-fab' ); ?></span>
				</td>
			</tr>
	
			<tr>
				<th><label for="ts_fab_company_url"><?php _e( 'Company URL', 'ts-fab' ); ?></label></th>
	
				<td>
					<input type="text" name="ts_fab_company_url" id="ts_fab_company_url" value="<?php echo esc_attr( get_the_author_meta( 'ts_fab_company_url', $user->ID ) ); ?>" class="regular-text" /><br />
					<span class="description"><?php _e( 'Your company URL.', 'ts-fab' ); ?></span>
				</td>
			</tr>
	
			<?php
				$options = ts_fab_get_tabs_settings();
				if( isset( $options['custom_tab_override'] ) && isset( $options['custom'] ) ) {
					if( $options['custom_tab_override'] == 1 || $options['custom_tab_override'] == 'content' ) { ?>
					<tr>
						<th>
							<?php
								// If users can only edit content, show them custom tab title set by admin
								if( $options['custom_tab_override'] == 'content' && isset( $options['custom_tab_title'] ) ) {
							?>
								<label for="ts_fab_custom_tab_title"><?php _e( 'Custom tab', 'ts-fab' ); ?> <?php if ( isset ( $options['custom_tab_title'] ) && '' != $options['custom_tab_title'] ) { echo '(' . $options['custom_tab_title'] . ')'; } ?></label>
							<?php } else { ?>
								<label for="ts_fab_custom_tab_title"><?php _e( 'Custom tab', 'ts-fab' ); ?></label>
							<?php } ?>
						</th>
			
						<td>
							<?php if( $options['custom_tab_override'] == 1 ) { ?>
								<input type="text" name="ts_fab_custom_tab_title" id="ts_fab_custom_tab_title" value="<?php echo esc_attr( get_the_author_meta( 'ts_fab_custom_tab_title', $user->ID ) ); ?>" class="regular-text" />
								<div><span class="description"><?php _e( 'Custom tab title (if not provided by either you or website admin, custom tab will not be visible)', 'ts-fab' ); ?></span></div>
							<?php } ?>
							
							<textarea id="ts_fab_custom_tab_content" style="margin-bottom:1px" rows="5" cols="50" name="ts_fab_custom_tab_content"><?php echo esc_attr( get_the_author_meta( 'ts_fab_custom_tab_content', $user->ID ) ); ?></textarea>
							<div><span class="description"><?php _e( 'Custom tab content', 'ts-fab' ); ?></span></div>
						</td>
					</tr>
				<?php }
				}
			?>
		</table>
	
	<?php } // end if ?>

<?php }



/**
 * Save Fanciest Author Box additional user fields
 *
 * @since 1.0
 */
add_action( 'personal_options_update', 'ts_fab_save_extra_profile_fields' );
add_action( 'edit_user_profile_update', 'ts_fab_save_extra_profile_fields' );
function ts_fab_save_extra_profile_fields( $user_id ) {

	if ( !current_user_can( 'edit_user', $user_id ) )
		return false;

	if( isset( $_POST['ts_fab_user_hide'] ) ) {
		update_user_meta( $user_id, 'ts_fab_user_hide', $_POST['ts_fab_user_hide'] );
	} else {
		delete_user_meta( $user_id, 'ts_fab_user_hide' );
	}

	if( isset( $_POST['ts_fab_photo_url'] ) )
		update_user_meta( $user_id, 'ts_fab_photo_url', esc_url_raw( $_POST['ts_fab_photo_url'] ) );
		
	if( isset( $_POST['ts_fab_twitter'] ) )
		update_user_meta( $user_id, 'ts_fab_twitter', strip_tags( $_POST['ts_fab_twitter'] ) );
		
	if( isset( $_POST['ts_fab_facebook_button'] ) )
		update_user_meta( $user_id, 'ts_fab_facebook_button', strip_tags( $_POST['ts_fab_facebook_button'] ) );	
		
	if( isset( $_POST['ts_fab_facebook'] ) )
		update_user_meta( $user_id, 'ts_fab_facebook', strip_tags( $_POST['ts_fab_facebook'] ) );
		
	if( isset( $_POST['ts_fab_googleplus'] ) )
		update_user_meta( $user_id, 'ts_fab_googleplus', strip_tags( $_POST['ts_fab_googleplus'] ) );

	if( isset( $_POST['ts_fab_linkedin_widget'] ) )
		update_user_meta( $user_id, 'ts_fab_linkedin_widget', strip_tags( $_POST['ts_fab_linkedin_widget'] ) );	

	if( isset( $_POST['ts_fab_linkedin'] ) )
		update_user_meta( $user_id, 'ts_fab_linkedin', strip_tags( $_POST['ts_fab_linkedin'] ) );
		
	if( isset( $_POST['ts_fab_youtube'] ) )
		update_user_meta( $user_id, 'ts_fab_youtube', strip_tags( $_POST['ts_fab_youtube'] ) );

	if( isset( $_POST['ts_fab_pinterest'] ) )
		update_user_meta( $user_id, 'ts_fab_pinterest', strip_tags( $_POST['ts_fab_pinterest'] ) );

	if( isset( $_POST['ts_fab_position'] ) )
		update_user_meta( $user_id, 'ts_fab_position', strip_tags( $_POST['ts_fab_position'] ) );
		
	if( isset( $_POST['ts_fab_company'] ) )
		update_user_meta( $user_id, 'ts_fab_company', strip_tags( $_POST['ts_fab_company'] ) );
		
	if( isset( $_POST['ts_fab_company_url'] ) )
		update_user_meta( $user_id, 'ts_fab_company_url', esc_url_raw( $_POST['ts_fab_company_url'] ) );
		
	// Strip all tags from custom tab title
	if( isset( $_POST['ts_fab_custom_tab_title'] ) ) {
		update_user_meta( $user_id, 'ts_fab_custom_tab_title', strip_tags( stripslashes( $_POST['ts_fab_custom_tab_title'] ) ) );
	}
	
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

	if( isset( $_POST['ts_fab_custom_tab_content'] ) )
		update_user_meta( $user_id, 'ts_fab_custom_tab_content', wp_kses( stripslashes( $_POST['ts_fab_custom_tab_content'] ), $ts_fab_allowed_tags ) );
	
}


/**
 * Enqueue media upload scripts in Profile screens
 *
 * @since 1.8.1
 */
add_action( 'admin_enqueue_scripts', 'ts_fab_admin_media_scripts' );
function ts_fab_admin_media_scripts() {
	global $pagenow;
	if ( 'profile.php' == $pagenow || 'user-edit.php' == $pagenow ) :
		wp_enqueue_media();

		$js_url = plugins_url( 'js/ts-fab-image-upload.js', dirname(__FILE__) );
		wp_enqueue_script( 'ts_fab_image_upload_js', $js_url );
	endif;
}