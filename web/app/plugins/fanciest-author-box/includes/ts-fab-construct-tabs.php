<?php


/**
 * Returns social fields for get_user_meta
 * Uses apply_filters to allow users to hook into the function and change names of social meta fields
 *
 * @since 1.7
 */
function ts_fab_return_social_field( $social_field = 'twitter' ) {
	switch ( $social_field ) {
		case 'twitter' :
			return apply_filters( 'ts_fab_social_field', 'ts_fab_twitter', 'twitter' );
			break;
		case 'facebook' :
			return apply_filters( 'ts_fab_social_field', 'ts_fab_facebook', 'facebook' );
			break;
		case 'googleplus' :
			return apply_filters( 'ts_fab_social_field', 'ts_fab_googleplus', 'googleplus' );
			break;
		case 'linkedin' :
			return apply_filters( 'ts_fab_social_field', 'ts_fab_linkedin', 'linkedin' );
			break;
		case 'youtube' :
			return apply_filters( 'ts_fab_social_field', 'ts_fab_youtube', 'youtube' );
			break;
		case 'pinterest' :
			return apply_filters( 'ts_fab_social_field', 'ts_fab_pinterest', 'pinterest' );
			break;
	}
}


/**
 * Get author image
 * Returns author image, uses Gravatar as default, can be overridden using user custom field
 *
 * @since 1.2
 */
function ts_fab_get_author_image( $author ){

	if( get_user_meta( $author->ID, 'ts_fab_photo_url', true) ) {
		$authorimg = '<img src="' . get_user_meta( $author->ID, 'ts_fab_photo_url', true) . '" width="64" alt="' . esc_attr( $author->display_name ) . '" />';
	} else {
		$authorimg = get_avatar( $author->ID, 64, '', esc_attr( $author->display_name ) );
	}
	
	return $authorimg;
	
}



/**
 * Construct bio tab
 *
 * @since 1.0
 */
function ts_fab_show_bio( $context = '', $authorid = '' ) {

	// Grab settings
	$ts_fab_settings = ts_fab_get_tabs_settings();

	if( $authorid == '' ) {
		global $authordata;
		$author = $authordata;
	} else {
		$author = get_userdata( $authorid );
	}

	// Hook to allow changing of author bio
	$author = apply_filters( 'ts_fab_show_author_bio_hook', $author );
		
	// Create Fanciest Author Box output
	$ts_fab_bio = '
	<div class="ts-fab-tab" id="ts-fab-bio-' . $context . '">
		<div class="ts-fab-avatar">' . ts_fab_get_author_image( $author ) . '</div>
		<div class="ts-fab-text">
			<div class="ts-fab-header">';
			
			if( $author->user_url ) {
				$ts_fab_bio .= '<h4><a rel="nofollow" href="' . $author->user_url . '">' . $author->display_name . '</a></h4>';
			} else {
				$ts_fab_bio .= '<h4>' . $author->display_name . '</h4>';
			}
			
			if( get_user_meta( $author->ID, 'ts_fab_position', true) ) {
				$ts_fab_bio .= '<div class="ts-fab-description"><span>' . get_user_meta( $author->ID, 'ts_fab_position', true) . '</span>';
				
				if( get_user_meta( $author->ID, 'ts_fab_company', true) ) {
					if( get_user_meta( $author->ID, 'ts_fab_company_url', true) ) {
						$ts_fab_bio .= ' ' . __( 'at', 'ts-fab' ) . ' <a rel="nofollow" href="' . esc_url( get_user_meta( $author->ID, 'ts_fab_company_url', true) ) . '">';
							$ts_fab_bio .= '<span>' . get_user_meta( $author->ID, 'ts_fab_company', true) . '</span>';
						$ts_fab_bio .= '</a>';
					} else {
						$ts_fab_bio .= ' ' . __( 'at', 'ts-fab' ) . ' <span>' . get_user_meta( $author->ID, 'ts_fab_company', true) . '</span>';
					}
				}
				
				$ts_fab_bio .= '</div>';
			}
			
			$ts_fab_bio .= '</div><!-- /.ts-fab-header -->';
			$ts_fab_bio .= '<div class="ts-fab-content">' . $author->user_description . '</div>
		</div>
	</div>';

	return $ts_fab_bio;

}



/**
 * Add links to usernames, hashtags and URLs in latest tweet
 *
 * @since 1.0
 */
function ts_fab_link_twitter( $status, $target_blank = true, $max_link_length = 250 ) {
 
	$target = $target_blank ? " target=\"_blank\"" : "";

	$status = preg_replace( "/((http:\/\/|https:\/\/)[^ )\r\n]+)/e", "'<a rel=\"nofollow\" href=\"$1\" title=\"$1\"  $target >'. ( (strlen( '$1' ) >= $max_link_length ? substr( '$1', 0, $max_link_length ) . '...' : '$1' ) ).'</a>'", $status );
	
	$status = preg_replace( "/(@([_a-z0-9\-]+))/i", "<a rel=\"nofollow\" href=\"http://twitter.com/$2\" title=\"Follow $2\" $target >$1</a>", $status );
	
	$status = preg_replace( "/(#([_a-z0-9\-]+))/i", "<a rel=\"nofollow\" href=\"http://twitter.com/search/?q=%23$2\" title=\"Search $1\" $target >$1</a>", $status);
	
	return $status;
    
}



/**
 * Construct Twitter tab
 *
 * @since 1.0
 */
function ts_fab_show_twitter( $context = '', $authorid = '' ) {

	// Grab settings
	$ts_fab_settings = ts_fab_get_tabs_settings();

	if( $authorid == '' ) {
		global $authordata;
		$author = $authordata;
	} else {
		$author = get_userdata( $authorid );
	}
	
	// Check if author has entered twitter username into WordPress profile
	if ( get_user_meta( $author->ID, ts_fab_return_social_field( 'twitter' ), true ) ) {

		$screen_name = get_user_meta( $author->ID, ts_fab_return_social_field( 'twitter' ), true);

		if ( isset( $ts_fab_settings['twitter_consumer_key'] ) && isset( $ts_fab_settings['twitter_consumer_secret'] ) ) {
			// Include Twitter API 1.1 Client for WordPress
			require_once( dirname(__FILE__) . '/class-wp-twitter-api.php' );
			
			// Set your personal data retrieved at https://dev.twitter.com/apps
			$credentials = array(
				'consumer_key'		=> $ts_fab_settings['twitter_consumer_key'],
				'consumer_secret'	=> $ts_fab_settings['twitter_consumer_secret']
			);
			
			// Let's instantiate our class with our credentials
			$twitter_api = new Wp_Twitter_Api( $credentials );
			
			// Example b - Retrieve my follower with a cache of 24 hour (default 30 minutes)
			$query = 'count=1&include_entities=true&screen_name=' . $screen_name;
			$args = array(
				'type' => 'statuses/user_timeline',
				'cache' => ( $ts_fab_settings['twitter_cache_interval'] * 60 )
			);
			$result = $twitter_api->query( $query, $args );
		}

		// Store information we plan to use as variables
		if( isset( $result[0]->text ) && '' != $result[0]->text ) {
			$status = $result[0]->text;
			$tweet_time = $result[0]->created_at;
			if( isset( $result[0]->user->description ) ) $description = $result[0]->user->description;
		} else {
			$status = __( '<!-- Couldn\'t fetch latest tweet -->', 'ts-fab' );
		}

		// Create Fanciest Author Box output
		$ts_fab_twitter = '
		<div class="ts-fab-tab" id="ts-fab-twitter-' . $context . '">
			<div class="ts-fab-avatar">' . ts_fab_get_author_image( $author ) . '</div>
			<div class="ts-fab-text">
				<div class="ts-fab-header">
					<h4><a rel="nofollow" href="//twitter.com/' . $screen_name . '">@' . $screen_name . '</a></h4>';
					if( $ts_fab_settings['twitter_bio'] == 1 && isset( $description ) ) {
						$ts_fab_twitter .= '<div class="ts-fab-description">' . ts_fab_link_twitter( $description ) . '</div>';
					}
				$ts_fab_twitter .= '</div><!-- /.ts-fab-header -->';
				if( $ts_fab_settings['twitter_tweet'] == 1 ) {
					$ts_fab_twitter .= '<div class="ts-fab-content">';
						$ts_fab_twitter .= '<div class="ts-fab-twitter-tweet">' . ts_fab_link_twitter( $status ); 
						if( isset( $tweet_time ) ) {
							$ts_fab_twitter .= '<span class="ts-fab-twitter-time"> - ' . human_time_diff( strtotime( $tweet_time ), time( 'U' ) ) . ' ago</span>';
						}
						$ts_fab_twitter .= '</div>';
					$ts_fab_twitter .= '</div>';
				}
				$show_count = $ts_fab_settings['twitter_count'] == 1 ? ' data-show-count="true" ' : ' data-show-count="false" ';
				$ts_fab_twitter .= '<div class="ts-fab-twitter-widget-wrapper"></div>';
			$ts_fab_twitter .= '</div>
		</div>';
		
		return $ts_fab_twitter;

	}

}



/**
 * Construct Facebook tab
 *
 * @since 1.0
 */
function ts_fab_show_facebook( $context = '', $authorid = '' ) {

	// Grab settings
	$ts_fab_settings = ts_fab_get_tabs_settings();

	if( $authorid == '' ) {
		global $authordata;
		$author = $authordata;
	} else {
		$author = get_userdata( $authorid );
	}
	
	// In widget, show box_count version, because of width
	$pos = strpos( $context, 'widget' );
	if( $pos !== false ) {
		$layout = 'button_count';
	} else {
		$layout = 'standard';
	}
	
	// Check if author has entered Facebook ID into WordPress profile
	if( get_user_meta( $author->ID, ts_fab_return_social_field( 'facebook' ), true) ) {

		// Create Fanciest Author Box output
		$ts_fab_facebook = '';
		
		$ts_fab_facebook .= '<div class="ts-fab-tab" id="ts-fab-facebook-' . $context . '">
			<div class="ts-fab-avatar">' . ts_fab_get_author_image( $author ) . '</div>
			<div class="ts-fab-text">';

			if ( 'subscribe' == get_user_meta( $author->ID, 'ts_fab_facebook_button', true) ) { 
				$ts_fab_facebook .= '
				<div class="ts-fab-header">
					<h4><a rel="nofollow" href="//www.facebook.com/' . get_user_meta( $author->ID, ts_fab_return_social_field( 'facebook' ), true) . '">' . $author->display_name . '</a></h4>
				</div>';
			}

			$ts_fab_facebook .= '<div class="ts-fab-facebook-widget-wrapper"></div>';
			$ts_fab_facebook .= '</div>
		</div>';

		return $ts_fab_facebook;

	}

}



/**
 * Construct Google+ tab
 *
 * @since 1.0
 */
function ts_fab_show_googleplus( $context = '', $authorid = '' ) {

	// Grab settings
	$ts_fab_settings = ts_fab_get_tabs_settings();

	if( $authorid == '' ) {
		global $authordata;
		$author = $authordata;
	} else {
		$author = get_userdata( $authorid );
	}

	// Check if author has entered Google+ ID into WordPress profile
	if( get_user_meta( $author->ID, ts_fab_return_social_field( 'googleplus' ), true) ) {
	
		// Create Fanciest Author Box output
		$ts_fab_googleplus = '
		<div class="ts-fab-tab" id="ts-fab-googleplus-' . $context . '">
			<div class="ts-fab-avatar">' . ts_fab_get_author_image( $author ) . '</div>
			<div class="ts-fab-text">
				<div class="ts-fab-header">
					<h4><a href="//plus.google.com/' . get_user_meta( $author->ID, ts_fab_return_social_field( 'googleplus' ), true ) . '?rel=author">+' . $author->display_name . '</a></h4>
				</div><!-- /.ts-fab-header -->';

				$pos = strpos( $context, 'widget' );
				if( $pos !== false ) {
					$width = 170;
				} else {
					$width = 320;
				}
		
				$ts_fab_googleplus .= '
				<div class="ts-fab-googleplus-widget-wrapper">
					<g:follow href="//plus.google.com/' . get_user_meta( $author->ID, ts_fab_return_social_field( 'googleplus' ), true ) . '" rel="author"></g:follow>
				</div>
			</div>
		</div>';

		return $ts_fab_googleplus;

	}

}



/**
 * Add scripts required for Google+ add to circles button
 *
 * Called by ts_fab_construct_fab function, added to wp_print_footer_scripts if needed
 *
 * @since 1.0
 */
function ts_fab_googleplus_head() { ?>

	<script type="text/javascript">
		window.___gcfg = {lang: <?php echo '"' . get_locale() . '"'; ?>};

		(function() {
		var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
		po.src = 'https://apis.google.com/js/plusone.js';
		var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
		})();
	</script>

<?php }



/**
 * Construct LinkedIn tab
 *
 * @since 1.3
 */
function ts_fab_show_linkedin( $context = '', $authorid = '' ) {

	// Grab settings
	$ts_fab_settings = ts_fab_get_tabs_settings();

	if( $authorid == '' ) {
		global $authordata;
		$author = $authordata;
	} else {
		$author = get_userdata( $authorid );
	}

	// Check if author has entered LinkedIn username into WordPress profile
	if( get_user_meta( $author->ID, ts_fab_return_social_field( 'linkedin' ), true ) ) {
	
		if ( ts_fab_is_url( get_user_meta( $author->ID, ts_fab_return_social_field( 'linkedin' ), true ) ) ) {
			$ts_fab_linkedin_url = get_user_meta( $author->ID, ts_fab_return_social_field( 'linkedin' ), true );
		} else {
			$ts_fab_linkedin_url = '//www.linkedin.com/in/' . get_user_meta( $author->ID, ts_fab_return_social_field( 'linkedin' ), true );
		}

		// Create Fanciest Author Box output
		$ts_fab_linkedin = '
		<div class="ts-fab-tab" id="ts-fab-linkedin-' . $context . '">
			<div class="ts-fab-avatar">' . ts_fab_get_author_image( $author ) . '</div>
			<div class="ts-fab-text">		
				<div class="ts-fab-linkedin-widget-wrapper"></div>
			</div>
		</div>';

		return $ts_fab_linkedin;

	}

}


/**
 * Construct YouTube tab
 *
 * @since 1.9
 */
function ts_fab_show_youtube( $context = '', $authorid = '' ) {

	// Grab settings
	$ts_fab_settings = ts_fab_get_tabs_settings();

	if( $authorid == '' ) {
		global $authordata;
		$author = $authordata;
	} else {
		$author = get_userdata( $authorid );
	}

	// Check if author has entered Google+ ID into WordPress profile
	if( get_user_meta( $author->ID, ts_fab_return_social_field( 'youtube' ), true) ) {
	
		// Create Fanciest Author Box output
		$ts_fab_youtube = '
		<div class="ts-fab-tab" id="ts-fab-youtube-' . $context . '">
			<div class="ts-fab-avatar">' . ts_fab_get_author_image( $author ) . '</div>
			<div class="ts-fab-text">
				<div class="ts-fab-header">
					<h4><a rel="nofollow" href="//www.youtube.com/user/' . get_user_meta( $author->ID, ts_fab_return_social_field( 'youtube' ), true ) . '">' . $author->display_name . '</a></h4>
				</div><!-- /.ts-fab-header -->
				<div class="ts-fab-youtube-widget-wrapper"></div>
			</div>
		</div>';

		return $ts_fab_youtube;

	}

}


/**
 * Construct Pinterest tab
 *
 * @since 1.9
 */
function ts_fab_show_pinterest( $context = '', $authorid = '' ) {

	// Grab settings
	$ts_fab_settings = ts_fab_get_tabs_settings();

	if( $authorid == '' ) {
		global $authordata;
		$author = $authordata;
	} else {
		$author = get_userdata( $authorid );
	}

	// Check if author has entered Google+ ID into WordPress profile
	if( get_user_meta( $author->ID, ts_fab_return_social_field( 'pinterest' ), true) ) {
	
		// Create Fanciest Author Box output
		$ts_fab_pinterest = '
		<div class="ts-fab-tab" id="ts-fab-pinterest-' . $context . '">
			<div class="ts-fab-avatar">' . ts_fab_get_author_image( $author ) . '</div>
			<div class="ts-fab-text">
				<div class="ts-fab-header">
					<h4><a rel="nofollow" href="//www.pinterest.com/' . get_user_meta( $author->ID, ts_fab_return_social_field( 'pinterest' ), true ) . '"">' . $author->display_name . '</a></h4>
				</div><!-- /.ts-fab-header -->
				<div class="ts-fab-pinterest-widget-wrapper"></div>
			</div>
		</div>';

		return $ts_fab_pinterest;

	}

}


/**
 * Construct latest posts tab
 *
 * @since 1.0
 */
function ts_fab_show_latest_posts( $context = '', $authorid = '' ) {

	// Grab settings
	$ts_fab_settings = ts_fab_get_tabs_settings();

	if( $authorid == '' ) {
		global $authordata;
		$author = $authordata;
	} else {
		$author = get_userdata( $authorid );
	}
	
	// Hook for custom post types selection
	$post_types = apply_filters( 'ts_fab_show_latest_posts_type_hook', array( 'post' ) );

	// Check if Co-Authors Plus plugin is active and if this user is Co-Author in any posts
	if ( function_exists( 'get_coauthors' ) ) {
		$latest_by_author = new WP_Query( array(
			'posts_per_page' => $ts_fab_settings['latest_posts_count'],
			'author_name'    => $author->user_login,
			'post_type'      => $post_types,
		) );
	} else {
		$latest_by_author = new WP_Query( array(
			'posts_per_page' => $ts_fab_settings['latest_posts_count'],
			'author'         => $author->ID,
			'post_type'      => $post_types
		) );
	}

	// Create Fanciest Author Box output
	$ts_fab_latest = '
	<div class="ts-fab-tab" id="ts-fab-latest-posts-' . $context . '">
		<div class="ts-fab-avatar">' . ts_fab_get_author_image( $author ) . '</div>
		<div class="ts-fab-text">
			<div class="ts-fab-header">
				<h4>' . __( 'Latest posts by ', 'ts-fab' ) . $author->display_name . ' <span class="latest-see-all">(<a href="' . get_author_posts_url( $author->ID ) . '">' . __( 'see all', 'ts-fab' ) . '</a>)</span></h4>
			</div>
			<ul class="ts-fab-latest">';
		
			while ( $latest_by_author->have_posts() ) : $latest_by_author->the_post();
				global $post;
				$ts_fab_latest .= '
				<li>
					<a href="' . get_permalink() . '">' . get_the_title() . '</a><span> - ' . date_i18n( get_option( 'date_format' ), get_the_time( 'U' ) ) . '</span> 
				</li>';
			endwhile;
			wp_reset_postdata();
	
		$ts_fab_latest .= '
		</ul></div>
	</div>';

	return $ts_fab_latest;

}



/**
 * Construct custom tab
 *
 * @since 1.2
 */
function ts_fab_show_custom( $context = '', $authorid = '' ) {

	// Grab settings
	$ts_fab_settings = ts_fab_get_tabs_settings();

	if( $authorid == '' ) {
		global $authordata;
		$author = $authordata;
	} else {
		$author = get_userdata( $authorid );
	}
	
	// Set custom tab content, based on whether users can override it
	if( $ts_fab_settings['custom_tab_override'] == 1 || $ts_fab_settings['custom_tab_override'] == 'content' ) {
		if( get_user_meta( $author->ID, 'ts_fab_custom_tab_content', true) ) {
			$custom_content = get_user_meta( $author->ID, 'ts_fab_custom_tab_content', true );
		} elseif( $ts_fab_settings['custom_tab_content'] != '' ) {
			$custom_content = $ts_fab_settings['custom_tab_content'];
		}
	} elseif( $ts_fab_settings['custom_tab_content'] != '' ) {
		$custom_content = $ts_fab_settings['custom_tab_content'];
	}

	// Create Fanciest Author Box output
	$ts_fab_custom = '
	<div class="ts-fab-tab" id="ts-fab-custom-' . $context . '">
		<div class="ts-fab-avatar">' . ts_fab_get_author_image( $author ) . '</div>
		<div class="ts-fab-text">';
			if( isset( $custom_content ) ) $ts_fab_custom .= $custom_content;
		$ts_fab_custom .= '</div>
	</div>';

	return $ts_fab_custom;

}



/**
 * Construct Fanciest Author Box
 * Used as helper function, to generate Fanciest Author Box before or after posts, as shortcode, widget or template tag
 *
 * @since 1.0
 */
function ts_fab_construct_fab ( 
	$context = '',
	$authorid = '',
	$show_tabs = array(
		'bio',
		'twitter',
		'facebook',
		'googleplus',
		'linkedin',
		'youtube',
		'pinterest',
		'latest_posts',
		'custom'
	),
	$float_photo = 'floated'
) {

	if( $authorid == '' ) {
		global $authordata;
		$author = $authordata;
	} else {
		$author = get_userdata( $authorid );
	}

	$options = ts_fab_get_tabs_settings();
	$display_options = ts_fab_get_display_settings();
	
	// Add icons style class
	if( isset( $display_options['tabs_style'] ) && $display_options['tabs_style'] == 'icons' ) {
		$tabs_class = 'ts-fab-icons-only';
	} elseif ( isset( $display_options['tabs_style'] ) && $display_options['tabs_style'] == 'text' ) {
		$tabs_class = 'ts-fab-text-only';	
	} else {
		$tabs_class = 'ts-fab-icons-text';	
	}
	
	// Add non-floated avatar class
	if( ( isset( $display_options['float_photo'] ) && $display_options['float_photo'] == 'above' ) || 'above' == $float_photo ) {
		$tabs_class .= ' ts-fab-no-float';
	}
	
	// Set custom tab title, based on whether users can override it
	if(  isset( $options['custom_tab_override'] ) && $options['custom_tab_override'] == 1 ) {
		if( get_user_meta( $author->ID, 'ts_fab_custom_tab_title', true) ) {
			$custom_title = get_user_meta( $author->ID, 'ts_fab_custom_tab_title', true );
		} elseif( $options['custom_tab_title'] != '' ) {
			$custom_title = $options['custom_tab_title'];
		}
	} elseif( isset( $options['custom_tab_title'] ) ) {
		$custom_title = $options['custom_tab_title'];
	}
	
	$ts_fab = '<!-- Fanciest Author Box v' . FAB_VERSION . ' -->';
	$ts_fab .= '<div id="ts-fab-' . $context . '" class="ts-fab-wrapper ' . $tabs_class . '">';

		// Do not show tabs list if there's only one tab
		if( count( $show_tabs ) > 1 ) {

			// Construct tabs list
			$ts_fab .= '<ul class="ts-fab-list">';

				foreach( $show_tabs as $show_tab ) {

					// Check if it's a default tab
					if( in_array( $show_tab, ts_fab_default_tabs() ) ) {

						switch( $show_tab ) {
	
							case 'bio':
								$ts_fab .= '<li class="ts-fab-bio-link"><a href="#ts-fab-bio-' . $context . '">
									<span class="genericon genericon-user" data-tab="bio"></span> 
									<span class="ts-fab-tab-text">' . __( 'Bio', 'ts-fab' ) . '</span>
								</a></li>';
								break;
	
							case 'twitter':
								// Check if Twitter tab needs to be shown and user has entered Twitter details
								if( in_array( 'twitter', $show_tabs ) && get_user_meta( $author->ID, ts_fab_return_social_field( 'twitter' ), true) ) {
									$show_count = $options['twitter_count'] == 1 ? 'true' : 'false';
									$twitter_username = get_user_meta( $author->ID, ts_fab_return_social_field( 'twitter' ), true );

									$ts_fab .= '<li class="ts-fab-twitter-link"><a href="#ts-fab-twitter-' . $context . '" data-tab="twitter" data-twitter-username="' . $twitter_username . '" data-show-count="' . $show_count . '" data-locale="' . get_locale() . '">
										<span class="genericon genericon-twitter"></span> 
										<span class="ts-fab-tab-text">Twitter</span>
									</a></li>';
								}
								break;
	
							case 'googleplus':
								// Check if Google+ tab needs to be shown and user has entered Google+ details
								if( in_array( 'googleplus', $show_tabs ) && get_user_meta( $author->ID, ts_fab_return_social_field( 'googleplus' ), true) ) {

									$pos = strpos( $context, 'widget' );
									if( $pos !== false ) {
										$width = 170;
									} else {
										$width = 320;
									}

									// add_action( 'wp_print_footer_scripts', 'ts_fab_googleplus_head' );
									$ts_fab .= '<li class="ts-fab-googleplus-link"><a href="#ts-fab-googleplus-' . $context . '" data-tab="googleplus" data-width="' . $width . '" data-googleplus-username="' . get_user_meta( $author->ID, ts_fab_return_social_field( 'googleplus' ), true ) . '" data-locale="' . get_locale() . '">

										<span class="genericon genericon-googleplus"></span> 
										<span class="ts-fab-tab-text">Google+</span>
									</a></li>';
								}
								break;
	
							case 'facebook':
								// Check if Facebook tab needs to be shown and user has entered Facebook details
								if( in_array( 'facebook', $show_tabs ) && get_user_meta( $author->ID, ts_fab_return_social_field( 'facebook' ), true) ) {

									$facebook_username = get_user_meta( $author->ID, ts_fab_return_social_field( 'facebook' ), true );
									$load_sdk = $options['facebook_sdk'] == 1 ? 'yes' : 'no';
									$pos = strpos( $context, 'widget' );
									if( $pos !== false ) {
										$layout = 'button_count';
									} else {
										$layout = 'standard';
									}
									$widget_type = get_user_meta( $author->ID, 'ts_fab_facebook_button', true) == 'like' ? 'like' : 'subscribe';

									$ts_fab .= '<li class="ts-fab-facebook-link"><a href="#ts-fab-facebook-' . $context . '" data-tab="facebook" data-facebook-username="' . $facebook_username . '" data-load-sdk="' . $load_sdk . '" data-facebook-locale="' . get_locale() . '" data-facebook-layout="' . $layout . '" data-widget-type="' . $widget_type . '">
										<span class="genericon genericon-facebook"></span>
										<span class="ts-fab-tab-text">Facebook</span>
									</a></li>';
								}
								break;
	
							case 'linkedin':
								// Check if LinkedIn tab needs to be shown and user has entered LinkedIn details
								if( in_array( 'linkedin', $show_tabs ) && get_user_meta( $author->ID, ts_fab_return_social_field( 'linkedin' ), true) ) {

									if ( ts_fab_is_url( get_user_meta( $author->ID, ts_fab_return_social_field( 'linkedin' ), true ) ) ) {
										$ts_fab_linkedin_url = get_user_meta( $author->ID, ts_fab_return_social_field( 'linkedin' ), true );
									} else {
										$ts_fab_linkedin_url = 'http://www.linkedin.com/in/' . get_user_meta( $author->ID, ts_fab_return_social_field( 'linkedin' ), true );
									}

									$ts_fab .= '<li class="ts-fab-linkedin-link"><a href="#ts-fab-linkedin-' . $context . '" data-tab="linkedin" data-linkedin-url="' . $ts_fab_linkedin_url . '">
										<span class="genericon genericon-linkedin"></span>
										<span class="ts-fab-tab-text">LinkedIn</span>
									</a></li>';
								}
								break;

							case 'youtube':
								// Check if YouTube tab needs to be shown and user has entered LinkedIn details
								if( in_array( 'youtube', $show_tabs ) && get_user_meta( $author->ID, ts_fab_return_social_field( 'youtube' ), true ) ) {
									$ts_fab .= '<li class="ts-fab-youtube-link"><a href="#ts-fab-youtube-' . $context . '" data-tab="youtube" data-youtube-username="' . esc_attr( get_user_meta( $author->ID, ts_fab_return_social_field( 'youtube' ), true ) ) . '">
										<span class="genericon genericon-youtube"></span>
										<span class="ts-fab-tab-text">YouTube</span>
									</a></li>';
								}
								break;

							case 'pinterest':
								// Check if YouTube tab needs to be shown and user has entered LinkedIn details
								if( in_array( 'pinterest', $show_tabs ) && get_user_meta( $author->ID, ts_fab_return_social_field( 'pinterest' ), true) ) {
									$ts_fab .= '<li class="ts-fab-pinterest-link"><a href="#ts-fab-pinterest-' . $context . '" data-tab="pinterest" data-pinterest-username="' . get_user_meta( $author->ID, ts_fab_return_social_field( 'pinterest' ), true ) . '">
										<span class="genericon genericon-pinterest"></span>
										<span class="ts-fab-tab-text">Pinterest</span>
									</a></li>';
								}
								break;

							case 'latest_posts':
								$ts_fab .= '<li class="ts-fab-latest-posts-link"><a href="#ts-fab-latest-posts-' . $context . '" data-tab="latest-posts">
									<span class="genericon genericon-standard"></span>
									<span class="ts-fab-tab-text">' . __( 'Latest Posts', 'ts-fab' ) . '</span>
								</a></li>';
								break;
	
							case 'custom':
								if( $options['custom'] == 1 ) {
									
									if( in_array( 'custom', $show_tabs ) ) {
										if( isset( $custom_title ) ) {
											$ts_fab .= '<li class="ts-fab-custom-link"><a href="#ts-fab-custom-' . $context . '">' . strip_tags( stripslashes( $custom_title ) ) . '</a></li>';
										}
									}
								}
								break;
	
						} // end switch
					
					// else it's an additional tab
					} else {
						
						// Tabs added by themes or other plugins
						$additional_tabs = ts_fab_additional_tabs();
						// Check if there are any additional tabs
						if( !empty( $additional_tabs ) ) {
							foreach( $additional_tabs as $additional_tab_key => $additional_tab_value ) {
						
								// Check if checkbox for this tab is checked
								if( isset( $options[$additional_tab_key] ) && $show_tab == $additional_tab_key ) {

									// Check tab conditional function to determine whether tab should be shown for this user
									if( isset( $additional_tab_value['conditional_callback'] ) ) {
										// Sets a flag based on what conditional function returns
										$conditional_function_output = $additional_tab_value['conditional_callback']( $author->ID );
									} // end conditional function check
										
									// Show tab if conditional function doesn't return false
									if( isset( $conditional_function_output ) && !$conditional_function_output == false ) {
	
										$ts_fab .= '<li class="ts-fab-' . $additional_tab_key . '-link ts-fab-additional-link"><a href="#ts-fab-' . $additional_tab_key . '-' . $context . '">' . strip_tags( stripslashes( $additional_tab_value['name'] ) ) . '</a></li>';
										
									} // End conditional flag check
								
								} // end check if option is checked
								
							} // end foreach
						} // end if
						
					}

				} // end foreach
				
			$ts_fab .= '</ul>';

		} // End if only one tab check

		// Construct individual tabs
		$ts_fab .= '<div class="ts-fab-tabs">';

			foreach( $show_tabs as $show_tab ) {

				// Check if it's a default tab
				if( in_array( $show_tab, ts_fab_default_tabs() ) ) {
				
					switch( $show_tab ) {
	
						case 'bio':
							$ts_fab .= ts_fab_show_bio( $context, $author->ID );
							break;
	
						case 'twitter':
							// Check if Twitter tab needs to be shown and user has entered Twitter details
							if( get_user_meta( $author->ID, ts_fab_return_social_field( 'twitter' ), true) ) {
								$ts_fab .= ts_fab_show_twitter( $context, $author->ID );
							}
							break;
							
						case 'facebook':
							// Check if Facebook tab needs to be shown and user has entered Facebook details
							if( get_user_meta( $author->ID, ts_fab_return_social_field( 'facebook' ), true) ) {
								$ts_fab .= ts_fab_show_facebook( $context, $author->ID );
							}
							break;
	
						case 'googleplus':
							// Check if Google+ tab needs to be shown and user has entered Google+ details
							if( get_user_meta( $author->ID, ts_fab_return_social_field( 'googleplus' ), true) ) {
								$ts_fab .= ts_fab_show_googleplus( $context, $author->ID );
							}
							break;
	
						case 'linkedin':
							// Check if LinkedIn tab needs to be shown and user has entered LinkedIn details
							if( get_user_meta( $author->ID, ts_fab_return_social_field( 'linkedin' ), true) ) {
								$ts_fab .= ts_fab_show_linkedin( $context, $author->ID );
							}
							break;

						case 'youtube':
							// Check if YouTube tab needs to be shown and user has entered YouTube details
							if( get_user_meta( $author->ID, ts_fab_return_social_field( 'youtube' ), true) ) {
								$ts_fab .= ts_fab_show_youtube( $context, $author->ID );
							}
							break;

						case 'pinterest':
							// Check if YouTube tab needs to be shown and user has entered YouTube details
							if( get_user_meta( $author->ID, ts_fab_return_social_field( 'pinterest' ), true) ) {
								$ts_fab .= ts_fab_show_pinterest( $context, $author->ID );
							}
							break;

						case 'latest_posts':
							$ts_fab .= ts_fab_show_latest_posts( $context, $author->ID );
							break;
	
						case 'custom':
							$ts_fab .= ts_fab_show_custom( $context, $author->ID );
							break;
	
					} // end switch
					
				// else, it's an additional tab
				} else {

					// Tabs added by themes or other plugins
					$additional_tabs = ts_fab_additional_tabs();
					// Check if there are any additional tabs
					if( !empty( $additional_tabs ) ) {
						foreach( $additional_tabs as $additional_tab_key => $additional_tab_value ) {
						
							if( $show_tab == $additional_tab_key ) {
							
								// Check tab conditional function to determine whether tab should be shown for this user
								if( isset( $additional_tab_value['conditional_callback'] ) ) {
									// Sets a flag based on what conditional function returns
									$conditional_function_output = $additional_tab_value['conditional_callback']( $author->ID );
								} // end conditional function check

								// Show tab if conditional function doesn't return false
								if( isset( $conditional_function_output ) && !$conditional_function_output == false ) {

									$ts_fab .= '
									<div class="ts-fab-tab ts-fab-additional-tab" id="ts-fab-' . $additional_tab_key . '-' . $context . '">';
										// Additional tab callback function
										$ts_fab .= $additional_tab_value['callback']();
									$ts_fab .= '</div>';
								
								}  // End conditional flag check
							}
							
						} // end foreach
					} // end if
				
				}

			} // end foreach

		$ts_fab .= '
		</div>
	</div>';

	return $ts_fab;

}



/**
 * Construct Fanciest Author Box for feeds
 * Used as helper function, to generate simplified author box for feeds
 *
 * @since 1.3
 */
function ts_fab_construct_fab_feeds() {
	
	global $authordata;
	$author = $authordata;

	$ts_fab_feed = '<h3>' . __( 'Author information', 'ts-fab' ) . '</h3>';
	
	$ts_fab_feed .= '<div class="ts-fab-wrapper" style="overflow:hidden">';
	
		$ts_fab_feed .= '<div class="ts-fab-photo" style="float:left;width:64px">';
			$ts_fab_feed .= ts_fab_get_author_image( $author );
		$ts_fab_feed .= '</div><!-- /.ts-fab-photo -->';

		
		$ts_fab_feed .= '<div class="ts-fab-text" style="margin-left:74px">';
			$ts_fab_feed .= '<div class="ts-fab-header">';
				if( $author->user_url ) {
					$ts_fab_feed .= '<h4><a href="' . $author->user_url . '">' . $author->display_name . '</a></h4>';
				} else {
					$ts_fab_feed .= '<h4>' . $author->display_name . '</h4>';
				}
				
				if( get_user_meta( $author->ID, 'ts_fab_position', true) ) {
					$ts_fab_feed .= '<div class="ts-fab-description" style="margin-bottom:0.5em"><em><span>' . get_user_meta( $author->ID, 'ts_fab_position', true) . '</span>';
					
					if( get_user_meta( $author->ID, 'ts_fab_company', true) ) {
						if( get_user_meta( $author->ID, 'ts_fab_company_url', true) ) {
							$ts_fab_feed .= ' ' . __( 'at', 'ts-fab' ) . ' <a href="' . esc_url( get_user_meta( $author->ID, 'ts_fab_company_url', true) ) . '">';
								$ts_fab_feed .= '<span>' . get_user_meta( $author->ID, 'ts_fab_company', true) . '</span>';
							$ts_fab_feed .= '</a>';
						} else {
							$ts_fab_feed .= ' ' . __( 'at', 'ts-fab' ) . ' <span>' . get_user_meta( $author->ID, 'ts_fab_company', true) . '</span>';
						}
					}
					
					$ts_fab_feed .= '</em></div>';
				}
			$ts_fab_feed .= '</div><!-- /.ts-fab-header -->';
			
			$ts_fab_feed .= '<div class="ts-fab-content" style="margin-bottom:0.5em">' . wpautop( $author->user_description ) . '</div>';
		
			$ts_fab_feed .= '<div class="ts-fab-footer"> | ';
			// Twitter link
			if( get_user_meta( $author->ID, ts_fab_return_social_field( 'twitter' ), true) ) {
				$ts_fab_feed .= '<a href="http://twitter.com/' . get_user_meta( $author->ID, ts_fab_return_social_field( 'twitter' ), true) . '">Twitter</a> | ';
			}
			
			// Facebook link
			if( get_user_meta( $author->ID, ts_fab_return_social_field( 'facebook' ), true) ) {
				$ts_fab_feed .= '<a href="http://www.facebook.com/' . get_user_meta( $author->ID, ts_fab_return_social_field( 'facebook' ), true) . '">Facebook</a> | ';
			}
			
			// Google+ link
			if( get_user_meta( $author->ID, ts_fab_return_social_field( 'googleplus' ), true) ) {
				$ts_fab_feed .= '<a href="http://plus.google.com/' . get_user_meta( $author->ID, ts_fab_return_social_field( 'googleplus' ), true) . '">Google+</a> | ';
			}
			
			// LinkedIn link
			if( get_user_meta( $author->ID, ts_fab_return_social_field( 'linkedin' ), true) ) {
				if ( ts_fab_is_url( get_user_meta( $author->ID, ts_fab_return_social_field( 'linkedin' ), true ) ) ) {
					$ts_fab_linkedin_url = get_user_meta( $author->ID, ts_fab_return_social_field( 'linkedin' ), true );
				} else {
					$ts_fab_linkedin_url = 'http://www.linkedin.com/in/' . get_user_meta( $author->ID, ts_fab_return_social_field( 'linkedin' ), true );
				}

				$ts_fab_feed .= '<a href="' . $ts_fab_linkedin_url . '">LinkedIn</a> | ';
			}

			// YouTube link
			if( get_user_meta( $author->ID, ts_fab_return_social_field( 'youtube' ), true) ) {
				$ts_fab_feed .= '<a href="//www.youtube.com/user/' . get_user_meta( $author->ID, ts_fab_return_social_field( 'youtube' ), true) . '">YouTube</a> | ';
			}

			// Pinterest link
			if( get_user_meta( $author->ID, ts_fab_return_social_field( 'pinterest' ), true) ) {
				$ts_fab_feed .= '<a href="//pinterest.com/' . get_user_meta( $author->ID, ts_fab_return_social_field( 'pinterest' ), true) . '">Pinterest</a> | ';
			}

			$ts_fab_feed .= '</div><!-- /.ts-fab-footer -->';
		$ts_fab_feed .= '</div><!-- /.ts-fab-text -->';

	$ts_fab_feed .= '</div><!-- /.ts-fab-wrapper -->';
	
	return $ts_fab_feed;
	
}