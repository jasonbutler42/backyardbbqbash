<?php /*
Plugin Name: Remove Image Links
Plugin URI: http://stevehardsoft.com/remove-image-links-wordpress-plugin/
Description: Custom plugin that  makes "no link" the default when adding images in the editor and removes the link tag from images in posts and pages when they link directly to image files.
Version: 1.2
Author: SteveHardSoft
Author URI: http://stevehardsoft.com/
License: GPLv2
Licence URI: http://www.gnu.org/licenses/gpl-2.0.html
Copyright 2012 SteveHardSoft


/* =SET NO LINK ON IMAGE INSERT AS DEFAULT
-------------------------------------------------------------- */
$options = get_option( 'remove-image-links-options' );
update_option( 'image_default_link_type', $options['default_link'] );


/* =REMOVE LINK FROM IMAGES IN POSTS
-------------------------------------------------------------- */
function shs_remove_links( $content ) {
	// get options
	$options = get_option( 'remove-image-links-options' );
	
	// make sure they exist
	if( !$options ) {
		$options = array(
			'default_link' => 'none',
			'remove_jpg' => 1,
			'remove_jpeg' => 1,
			'remove_gif' => 1,
			'remove_png' => 1,
			'remove_bmp' => 1
		);
		
		// add default options if they don't
		add_option( 'remove-image-links-options', $options, '', 'no' );
	}
	
	// regex pattern of links that should be removed
	$pattern = array();
	if( $options['remove_jpg'] ) array_push( $pattern, '/<a(.*?)href="(.*?).jpg"(.*?)>(.*?)<img(.*?)>(.*?)<\/a>/' );
	if( $options['remove_jpeg'] ) array_push( $pattern, '/<a(.*?)href="(.*?).jpeg"(.*?)>(.*?)<img(.*?)>(.*?)<\/a>/' );
	if( $options['remove_gif'] ) array_push( $pattern, 	'/<a(.*?)href="(.*?).gif"(.*?)>(.*?)<img(.*?)>(.*?)<\/a>/' );
	if( $options['remove_png'] ) array_push( $pattern, 	'/<a(.*?)href="(.*?).png"(.*?)>(.*?)<img(.*?)>(.*?)<\/a>/' );
	if( $options['remove_bmp'] ) array_push( $pattern, 	'/<a(.*?)href="(.*?).bmp"(.*?)>(.*?)<img(.*?)>(.*?)<\/a>/' );
	
	// parse content through regex and return
	$result = preg_replace( $pattern, '$4<img$5>$6', $content );
    return $result;
}


/* =ADD SETTINGS LINK
-------------------------------------------------------------- */
function shs_set_plugin_meta( $links, $file ) { 
	$plugin = plugin_basename( __FILE__ ); // get plugin's file name
    if ( $file == $plugin ) { // if called for THIS plugin then:
		$newlinks = array( '<a href="options-general.php?page=remove-image-links">' . __( 'Settings' ) . '</a>'	); // array of links to add
		return array_merge( $links, $newlinks ); // merge new links into existing $links
	}
return $links; // return the $links (merged or otherwise)
}


/* =WHITELIST PLUGIN'S OPTIONS PAGE
-------------------------------------------------------------- */
function shs_options_init() { 
	register_setting( 'remove-image-links-group', 'remove-image-links-options', 'shs_sanatize_input' );
}


/* =ADD LINK TO OPTIONS UNDER SETTINGS MENU
-------------------------------------------------------------- */
function shs_add_settings_link() { 
	add_options_page( 'Remove Image Links Settings', 'Image Links', 'manage_options', 'remove-image-links', 'shs_draw_options_page' );
}


/* =ADD LINK TO OPTIONS UNDER SETTINGS MENU
-------------------------------------------------------------- */
function shs_sanatize_input( $input ) { 
	// default linking behavior
	if( $input['default_link'] != 'file' && $input['default_link'] != 'post' )
		$input['default_link'] = 'none';
	
	// extensions to remove
	$input['remove_jpg'] = ( $input['remove_jpg'] ? 1 : 0 ); // (checkbox) if TRUE then 1, else 0
	$input['remove_jpeg'] = ( $input['remove_jpeg'] ? 1 : 0 ); 
	$input['remove_gif'] = ( $input['remove_gif'] ? 1 : 0 );
	$input['remove_png'] = ( $input['remove_png'] ? 1 : 0 );
	$input['remove_bmp'] = ( $input['remove_bmp'] ? 1 : 0 );
	return $input;
}


/* =DRAW OPTIONS PAGE
-------------------------------------------------------------- */
function shs_draw_options_page() { 
	?>

	<div class="wrap">
    <div class="icon32" id="icon-options-general"><br /></div>
		<h2>Remove Image Links Settings</h2>
		<p><small>Please visit this <a href="http://stevehardsoft.com/remove-image-links-wordpress-plugin/" rel="help">plugin's homepage</a> for more information.</small></p>
		
		<form name="form1" id="form1" method="post" action="options.php">
			<?php settings_fields( 'remove-image-links-group' ); // nonce settings page ?>
			<?php $options = get_option( 'remove-image-links-options' ); // populate $options array from database ?>
			
			<table class="form-table">

            	 <!-- Default Linking Behavior -->
				<tr valign="top">
					<th scope="row"><label for="remove-image-links-options[default_link]">Default link for post images:</label></th>					<td>
						<label for="remove-image-links-options[default_link]">
						<input name="remove-image-links-options[default_link]" type="radio" value="none" <?php checked( $options['default_link'], 'none', TRUE ); ?>/>
						no link &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
						
						<label for="remove-image-links-options[default_link]">
						<input name="remove-image-links-options[default_link]" type="radio" value="post" <?php checked( $options['default_link'], 'post', TRUE ); ?>/>
						attachment page &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
						
						<label for="remove-image-links-options[default_link]">
						<input name="remove-image-links-options[default_link]" type="radio" value="file" <?php checked( $options['default_link'], 'file', TRUE ); ?>/>
						image file &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
					</td>
                </tr>
				 
				<!-- Extensions -->
				<tr valign="top">
					<th scope="row"><label>Remove links to image types:</label></th>
					<td>
						<label for="remove-image-links-options[remove_jpg]">
						<input name="remove-image-links-options[remove_jpg]" type="checkbox" value="1" <?php echo ( $options['remove_jpg'] ? 'checked="checked" ' : '' ); ?>/>
						.jpg &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
						
						<label for="remove-image-links-options[remove_jpeg]">
						<input name="remove-image-links-options[remove_jpeg]" type="checkbox" value="1" <?php echo ( $options['remove_jpeg'] ? 'checked="checked" ' : '' ); ?>/>
						.jpeg &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
						
						<label for="remove-image-links-options[remove_gif]">
						<input name="remove-image-links-options[remove_gif]" type="checkbox" value="1" <?php echo ( $options['remove_gif'] ? 'checked="checked" ' : '' ); ?>/>
						.gif &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
						
						<label for="remove-image-links-options[remove_png]">
						<input name="remove-image-links-options[remove_png]" type="checkbox" value="1" <?php echo ( $options['remove_png'] ? 'checked="checked" ' : '' ); ?>/>
						.png &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
						
						<label for="remove-image-links-options[remove_bmp]">
						<input name="remove-image-links-options[remove_bmp]" type="checkbox" value="1" <?php echo ( $options['remove_bmp'] ? 'checked="checked" ' : '' ); ?>/>
						.bmp &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
                </td>				
			</table>
			<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e( 'Save Changes' ) ?>" />
			</p>
		</form>
	</div>
	
	<h3>The problem this plugin solves</h3>
	<p>Right now, all over the internet there are people clicking on images on WordPress sites for no purpose. Why?</p>

<p>As a mouse cursor passes over an image, they see a hand cursor, indicating that the image is a link to something. On clicking, to their surprise, all they see is an image of the image... They then need to use the browser’s back button to return to the web page.</p>

<p>This is NOT a good user experience because it undermines the convention that clickable images are real links. There may also be negative implications for search engine optimization (SEO).</p>

<h3>Why does this happen?</h3>
<p>This occurs because, by default, when an image is added to a post or a page from the media library, WordPress adds a link to an image itself if the writer forgets (or does not know about) the need to click the ‘No Link’ button at the time of insertion.</p>

<ol>When this plugin is activated:
<li>The link field for new images being inserted is blank by default.</li>
<li>The link code in existing posts and pages is not deleted but it is not outputted, meaning that readers can no longer click on the images.</li>
<li>Links to web pages or documents from images are not affected.</li>
</ol>

<p>The plugin was coded by <a href="http://shinraholdings.com/" rel="author" target="_blank">Shinra Web Holdings</a> from an idea by Steve Hards, of <a href="http://stevehardsoft.com" rel="author" target="_blank">SteveHardSoft</a>.</p>

<p>It is licensed to users under the terms of <a href="http://www.gnu.org/licenses/gpl-2.0.html" target="_blank">GNU GPL, version 2</a>.</p>

<h3>Additional Resources</h3>
<p>Links to other resources from Steve Hards (including free resources for PowerPoint users) can be found here: <a href="http://stevehardsoft.com" rel="author" target="_blank">http://stevehardsoft.com</a></p>

<p>Please <a href="http://shinraholdings.com/contact">contact</a> Shinra Web Holdings for technical issues.</p>
	<?php
}


/* =SET DEFAULT OPTIONS ON FIRST RUN
-------------------------------------------------------------- */
function shs_set_defaults() {
	// check for options
	$options = get_option( 'remove-image-links-options' ); 

	// if none, use default
	if( !$options ) {
		$defaults = array(
			'default_link' => 'none',
			'remove_jpg' => 1,
			'remove_jpeg' => 1,
			'remove_gif' => 1,
			'remove_png' => 1,
			'remove_bmp' => 1
		);
		
		// add default options
		add_option( 'remove-image-links-options', $defaults, '', 'no' );
	}
}

/* =HOOKS AND FILTERS
-------------------------------------------------------------- */
add_filter( 'the_content', 'shs_remove_links' ); // cull image links
add_filter( 'plugin_row_meta', 'shs_set_plugin_meta', 10, 2 ); // add plugin page meta links
add_action( 'admin_init', 'shs_options_init' ); // whitelist options page
add_action( 'admin_init', 'shs_set_defaults' ); // set default values on first run
add_action( 'admin_menu', 'shs_add_settings_link' ); // add link to plugin's settings page in 'settings'
