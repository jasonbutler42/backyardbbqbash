<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * For example, it puts together the home page when no home.php file exists.
 *
 * @link http://codex.wordpress.org/Template_Hierarchy
 *
 */

get_header(); ?>
<div class="row">
	<div class="col-md-8 main_content">
		<?php get_template_part( 'content'); ?>
	</div>
	<div class="col-md-4">
		<?php get_sidebar(); ?>
	</div>
</div>
<?php get_footer(); ?>