<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package test-task
 */

get_header();
global $wp_query;
global $post;
?>

	<main id="primary" class="site-main">

		<?php
		if ( have_posts() ) :

			if ( is_home() && ! is_front_page() ) :
				?>
				<header>
					<h1 class="page-title screen-reader-text"><?php single_post_title(); ?></h1>
				</header>
				<?php
			endif;
            ?>
            <form method="get" action="<?php echo home_url('/'); ?>">
                <select name="sort_by">
                    <option value="ASC">A-Z</option>
                    <option value="DESC" <?php if (array_key_exists('sort_by', $_GET) && $_GET['sort_by'] == 'DESC') { echo 'selected'; } ?>>Z-A</option>
	                <?php if (get_option( 'sort_possibility' )): ?>
                        <option value="ASC-post_length" <?php if (array_key_exists('sort_by', $_GET) && $_GET['sort_by'] == 'ASC-post_length') { echo 'selected'; } ?>>1-∞</option>
                        <option value="DESC-post_length" <?php if (array_key_exists('sort_by', $_GET) && $_GET['sort_by'] == 'DESC-post_length') { echo 'selected'; } ?>>∞-1</option>
	                <?php endif; ?>
                </select>
                <button type="submit">Sort</button>
            </form>
            <?php
            $order = 'ASC';
            $orderby = 'title';
            if (array_key_exists('sort_by', $_GET)) {
	            $order = $_GET['sort_by'];
	            if (strpos($order, '-')) {
                    $sort_data = explode('-', $order);
                    $order = $sort_data[0];
		            $orderby = 'meta_value_num';
		            $meta_key = $sort_data[1];
                }
            }
            if ($meta_key) {
	            $args = array(
		            'post_type' => 'post',
		            'order' => $order,
		            'orderby' => $orderby,
		            'meta_key' => $meta_key,
	            );
            } else {
	            $args = array(
		            'post_type' => 'post',
		            'order' => $order,
		            'orderby' => $orderby,
	            );
            }

			$my_query = new WP_Query( $args );

			if ( $my_query->have_posts() ) {
				while ( $my_query->have_posts() ) {
					$my_query->the_post();
                    get_template_part('template-parts/post', 'list');
					$post_size = get_post_meta($post->ID, 'post_length', true);
				}
				$paged = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
				$max_pages = $wp_query->max_num_pages;

				if ( $paged < $max_pages ) {
					echo '<div id="loadmore" style="text-align:center;"><a href="#" data-max_pages="' . $max_pages . '" data-paged="' . $paged . '" class="button">Load More</a></div>';
				}
			}
			wp_reset_postdata();
		else:

			get_template_part( 'template-parts/content', 'none' );

		endif;
		?>

	</main><!-- #main -->

<?php
get_footer();
