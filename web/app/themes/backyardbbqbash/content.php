<?php if ( have_posts() ) : ?>

			<?php /* Start the Loop */ ?>
			<?php while ( have_posts() ) : the_post(); ?>
				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<header class="entry-header">

					<?php if ( is_single() ) : ?>
						<h1 class="entry-title"><?php the_title(); ?></h1>
					<?php else : ?>
						<h1 class="entry-title">
							<!-- <a href="<?php the_permalink(); ?>" rel="bookmark"> --><?php the_title(); ?><!--</a> -->
						</h1>
					<?php endif; // is_single() ?>

					<?php if ( comments_open() ) : ?>
						<div class="comments-link">
							<?php comments_popup_link( '<span class="leave-reply">' . __( 'Leave a reply', 'bbq' ) . '</span>', __( '1 Reply', 'bbq' ), __( '% Replies', 'bbq' ) ); ?>
						</div><!-- .comments-link -->
					<?php endif; // comments_open() ?>
					</header><!-- .entry-header -->
					<div class="entry-content">
						<?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'bbq' ) ); ?>
						<?php wp_link_pages( array( 'before' => '<div class="page-links">' . __( 'Pages:', 'bbq' ), 'after' => '</div>' ) ); ?>
					</div><!-- .entry-content -->
				</article> <!-- #post-id -->
				<?php comments_template( '', true ); ?>
			<?php endwhile; ?>

			

		<?php else : ?>

			<article id="post-0" class="post no-results not-found">
			<?php if ( current_user_can( 'edit_posts' ) ) :
				// Show a different message to a logged-in user who can add posts.
			?>
				<header class="entry-header">
					<h1 class="entry-title"><?php _e( 'No posts to display', 'bbq' ); ?></h1>
				</header>

				<div class="entry-content">
					<p><?php printf( __( 'Ready to publish your first post? <a href="%s">Get started here</a>.', 'bbq' ), admin_url( 'post-new.php' ) ); ?></p>
				</div><!-- .entry-content -->

			<?php else :
				// Show the default message to everyone else.
			?>
				<header class="entry-header">
					<h1 class="entry-title"><?php _e( 'Nothing Found', 'bbq' ); ?></h1>
				</header>

				<div class="entry-content">
					<p><?php _e( 'Apologies, but no results were found. Perhaps searching will help find a related post.', 'bbq' ); ?></p>
					<?php get_search_form(); ?>
				</div><!-- .entry-content -->
			<?php endif; // end current_user_can() check ?>

			</article><!-- #post-0 -->

		<?php endif; // end have_posts() check ?>