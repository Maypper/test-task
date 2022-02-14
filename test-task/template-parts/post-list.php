<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<?php test_task_post_thumbnail(); ?>
    <h2><a href="<?php echo get_the_permalink(); ?>"><?php the_title() ?></a></h2>
	<div class="entry-content">
		<?php
		the_content();

		wp_link_pages(
			array(
				'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'test-task' ),
				'after'  => '</div>',
			)
		);
		?>
	</div>
</article>
