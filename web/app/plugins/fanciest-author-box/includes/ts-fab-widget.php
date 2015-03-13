<?php

add_action( 'init', 'ts_fab_widget_init', 1 );
function ts_fab_widget_init() {

	register_widget('ts_fab_widget');

}



class ts_fab_widget extends WP_Widget {

	function __construct() {

		$widget_ops = array(
			'classname'		=> 'ts-fab-widget',
			'description'	=> 'Fanciest Author Box ' . __( 'widget', 'ts-fab' )
		);
		parent::__construct( 'ts-fab-widget', 'Fanciest Author Box', $widget_ops );

		if ( is_active_widget( false, false, $this->id_base ) ) :
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_fab_styles' ) );
			add_action( 'wp_head', array( $this, 'print_generated_styles' ) );
		endif;

	}

	function widget( $args, $instance ) {

		extract( $args );

		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? __( 'Author Spotlight', 'ts-fab' ) : $instance['title'], $instance, $this->id_base );

		$author = !empty( $instance['author'] ) ? $instance['author'] : '';

		$show_tabs = array();

		!empty( $instance['bio'] ) ? $show_tabs[] = 'bio' : '';
		!empty( $instance['twitter'] ) ? $show_tabs[] = 'twitter' : '';
		!empty( $instance['facebook'] ) ? $show_tabs[] = 'facebook' : '';
		!empty( $instance['googleplus'] ) ? $show_tabs[] = 'googleplus' : '';
		!empty( $instance['linkedin'] ) ? $show_tabs[] = 'linkedin' : '';
		!empty( $instance['youtube'] ) ? $show_tabs[] = 'youtube' : '';
		!empty( $instance['pinterest'] ) ? $show_tabs[] = 'pinterest' : '';
		!empty( $instance['latest_posts'] ) ? $show_tabs[] = 'latest_posts' : '';
		!empty( $instance['custom'] ) ? $show_tabs[] = 'custom' : '';

		echo $before_widget;
		if ( $title )
			echo $before_title . $title . $after_title;

		?>

		<?php
			if( $instance['float_photo'] != 1 ) echo '<div class="ts-fab-no-float">';
			// First check total number of users, to avoid expensive queries
			$ts_fab_user_count = count_users();
			if ( $ts_fab_user_count['total_users'] < 200 ) :
				// If set to show random author, get random author ID
				if( $instance['author'] == 'random' ) :
					// Get subscriber IDs, so they can be excluded
					$subscriber_ids = new WP_User_Query( array( 'role' => 'subscriber', 'fields' => 'ID' ) );

					// Get IDs of authors with surpressed author boxes, so they can be excluded as well
					$surpressed_ids = new WP_User_Query( array( 'meta_key' => 'ts_fab_user_hide', 'fields' => 'ID' ) );
					
					$authors_to_exclude = array_merge( $subscriber_ids->results, $surpressed_ids->results );

					$args = array(
						'blog_id'	=> $GLOBALS['blog_id'],
						'exclude'	=> $authors_to_exclude
					);
					$users = get_users( $args );
					$random_user = array_rand( $users, 1 );
					$authorobject = $users[$random_user];
					$author = $authorobject->ID;
				endif;
			endif;

			echo ts_fab_construct_fab( 'widget-' . $this->number, $author, $show_tabs );
			if( $instance['float_photo'] != 1 ) echo '</div>';
		?>

		<?php

		echo $after_widget;

	}

	function update( $new_instance, $old_instance ) {

		$instance = $old_instance;
		$new_instance = wp_parse_args(
			(array) $new_instance, 
			array(
				'title'			=> '',
				'author'		=> '',
				'bio'			=> '',
				'twitter'		=> '',
				'facebook'		=> '',
				'googleplus'	=> '',
				'linkedin'		=> '',
				'youtube'		=> '',
				'pinterest'		=> '',
				'latest_posts'	=> '',
				'custom'		=> '',
				'float_photo'	=> ''
			)
		);
		$instance['title'] 			= strip_tags( $new_instance['title'] );

		$instance['author'] 		= intval( $new_instance['author'] );

		$instance['bio']			= $new_instance['bio'] ? 1 : 0;
		$instance['twitter']		= $new_instance['twitter'] ? 1 : 0;
		$instance['facebook']		= $new_instance['facebook'] ? 1 : 0;
		$instance['googleplus']		= $new_instance['googleplus'] ? 1 : 0;
		$instance['linkedin']		= $new_instance['linkedin'] ? 1 : 0;
		$instance['youtube']		= $new_instance['youtube'] ? 1 : 0;
		$instance['pinterest']		= $new_instance['pinterest'] ? 1 : 0;
		$instance['latest_posts']	= $new_instance['latest_posts'] ? 1 : 0;
		$instance['custom']			= $new_instance['custom'] ? 1 : 0;
		$instance['float_photo']	= $new_instance['float_photo'] ? 1 : 0;

		return $instance;

	}

	function form( $instance ) {

		$tabs_settings = ts_fab_get_tabs_settings();
		
		$instance = wp_parse_args(
			(array) $instance,
			array(
				'title'			=> '',
				'author'		=> '',
				'bio'			=> $tabs_settings['bio'],
				'twitter'		=> $tabs_settings['twitter'],
				'facebook'		=> $tabs_settings['facebook'],
				'googleplus'	=> $tabs_settings['googleplus'],
				'linkedin'		=> $tabs_settings['linkedin'],
				'youtube'		=> $tabs_settings['youtube'],
				'pinterest'		=> $tabs_settings['pinterest'],
				'latest_posts'	=> $tabs_settings['latest_posts'],
				'custom'		=> $tabs_settings['custom'],
				'float_photo'	=> ''
			)
		);

		$title = strip_tags( $instance['title'] );

		$author = $instance['author'];

		$bio			= $instance['bio'] ? 'checked="checked"' : '';
		$twitter		= $instance['twitter'] ? 'checked="checked"' : '';
		$facebook		= $instance['facebook'] ? 'checked="checked"' : '';
		$googleplus		= $instance['googleplus'] ? 'checked="checked"' : '';
		$linkedin		= $instance['linkedin'] ? 'checked="checked"' : '';
		$youtube		= $instance['youtube'] ? 'checked="checked"' : '';
		$pinterest		= $instance['pinterest'] ? 'checked="checked"' : '';
		$latest_posts	= $instance['latest_posts'] ? 'checked="checked"' : '';
		$custom			= $instance['custom'] ? 'checked="checked"' : '';
		$float_photo	= $instance['float_photo'] ? 'checked="checked"' : '';

		?>

		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>">
				<?php _e( 'Title: ', 'ts-fab' ); ?>
			</label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
		</p>

		<p>
			<?php
			// If there's more than 200 users, show a number field, to avoid looping through all users
			$ts_fab_user_count = count_users();
			if ( $ts_fab_user_count['total_users'] < 200 ) :

				$blogusers = get_users( array(
					'blog_id' => $GLOBALS['blog_id'],
					'orderby' => 'nicename'
				) );
				?>
				<label for="<?php echo $this->get_field_id('author'); ?>">
					<?php _e( 'Select Author:', 'ts-fab' ); ?>
				</label>
				<select class="widefat" id="<?php echo $this->get_field_id('author'); ?>" name="<?php echo $this->get_field_name('author'); ?>">
					<?php
					$blogusers = get_users( array(
						'blog_id'		=> $GLOBALS['blog_id'],
						'orderby'		=> 'nicename'
					) );
					foreach ( $blogusers as $user ) {
						$selected = ( $instance['author'] == $user->ID ) ? 'selected="selected"' : '';
						echo '<option value="' . $user->ID . '"' . $selected . '>' . $user->display_name . '</option>';
					}
					
					$selected = ( $instance['author'] == 'random' ) ? 'selected="selected"' : '';
					echo '<option value="random"' . $selected . '>' . __( 'Random author', 'ts-fab' ) . '</option>';
					?>
				</select>

			<?php else : ?>

				<label for="<?php echo $this->get_field_id('author'); ?>">
					<?php _e( 'User ID:', 'ts-fab' ); ?>
				</label>
				<input type="number" class="widefat" id="<?php echo $this->get_field_id('author'); ?>" name="<?php echo $this->get_field_name('author'); ?>" value="<?php echo esc_attr($author); ?>" />
			
			<?php endif; ?>
		</p>

		<p>
			<input class="checkbox" type="checkbox" <?php echo $bio; ?> id="<?php echo $this->get_field_id('bio'); ?>" name="<?php echo $this->get_field_name('bio'); ?>" />
			<label for="<?php echo $this->get_field_id('bio'); ?>"><?php _e( 'Bio tab', 'ts-fab' ); ?></label>
			<br/>

			<input class="checkbox" type="checkbox" <?php echo $twitter; ?> id="<?php echo $this->get_field_id('twitter'); ?>" name="<?php echo $this->get_field_name('twitter'); ?>" />
			<label for="<?php echo $this->get_field_id('twitter'); ?>"><?php _e( 'Twitter tab', 'ts-fab' ); ?></label>
			<br/>

			<input class="checkbox" type="checkbox" <?php echo $facebook; ?> id="<?php echo $this->get_field_id('facebook'); ?>" name="<?php echo $this->get_field_name('facebook'); ?>" />
			<label for="<?php echo $this->get_field_id('facebook'); ?>"><?php _e( 'Facebook tab', 'ts-fab' ); ?></label>
			<br/>

			<input class="checkbox" type="checkbox" <?php echo $googleplus; ?> id="<?php echo $this->get_field_id('googleplus'); ?>" name="<?php echo $this->get_field_name('googleplus'); ?>" />
			<label for="<?php echo $this->get_field_id('googleplus'); ?>"><?php _e( 'Google+ tab', 'ts-fab' ); ?></label>
			<br/>

			<input class="checkbox" type="checkbox" <?php echo $linkedin; ?> id="<?php echo $this->get_field_id('linkedin'); ?>" name="<?php echo $this->get_field_name('linkedin'); ?>" />
			<label for="<?php echo $this->get_field_id('linkedin'); ?>"><?php _e( 'LinkedIn tab', 'ts-fab' ); ?></label>
			<br/>

			<input class="checkbox" type="checkbox" <?php echo $youtube; ?> id="<?php echo $this->get_field_id('youtube'); ?>" name="<?php echo $this->get_field_name('youtube'); ?>" />
			<label for="<?php echo $this->get_field_id('youtube'); ?>"><?php _e( 'YouTube tab', 'ts-fab' ); ?></label>
			<br/>

			<input class="checkbox" type="checkbox" <?php echo $pinterest; ?> id="<?php echo $this->get_field_id('pinterest'); ?>" name="<?php echo $this->get_field_name('pinterest'); ?>" />
			<label for="<?php echo $this->get_field_id('pinterest'); ?>"><?php _e( 'Pinterest tab', 'ts-fab' ); ?></label>
			<br/>

			<input class="checkbox" type="checkbox" <?php echo $latest_posts; ?> id="<?php echo $this->get_field_id('latest_posts'); ?>" name="<?php echo $this->get_field_name('latest_posts'); ?>" />
			<label for="<?php echo $this->get_field_id('latest_posts'); ?>"><?php _e( 'Latest posts tab', 'ts-fab' ); ?></label>
			<br/>

			<input class="checkbox" type="checkbox" <?php echo $custom; ?> id="<?php echo $this->get_field_id('custom'); ?>" name="<?php echo $this->get_field_name('custom'); ?>" />
			<label for="<?php echo $this->get_field_id('custom'); ?>"><?php _e( 'Custom tab', 'ts-fab' ); ?></label>
			<br/>
			<br/>
			
			<input class="checkbox" type="checkbox" <?php echo $float_photo; ?> id="<?php echo $this->get_field_id('float_photo'); ?>" name="<?php echo $this->get_field_name('float_photo'); ?>" />
			<label for="<?php echo $this->get_field_id('float_photo'); ?>"><?php _e( 'Float photo (uncheck for narrow sidebars)', 'ts-fab' ); ?></label>
			<br/>
		</p>

	<?php }
	
	function enqueue_fab_styles() {
		wp_enqueue_style( 'ts_fab_css' );
		wp_enqueue_script( 'ts_fab_js' );
	}
	
	function print_generated_styles() {
		if ( '' != ts_fab_generate_color_settings() ) :
			echo ts_fab_generate_color_settings();
		endif;		
	}
	
}