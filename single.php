<?php
/**
 * The template for displaying all single posts.
 *
 * @package storefront
 */

get_header(); ?>
<div class="wrap_block">
	<?php woocommerce_breadcrumb(); ?>
	<div class="left_block">
		<main id="main" class="site-main" role="main">

		<?php while ( have_posts() ) : the_post();

			do_action( 'storefront_single_post_before' );

			get_template_part( 'content', 'single' );

			do_action( 'storefront_single_post_after' );

		endwhile; // End of the loop. ?>

		</main><!-- #main -->
	</div><!-- #primary -->

?>
<div class="clear"></div>
</div>
<?php
get_footer();

