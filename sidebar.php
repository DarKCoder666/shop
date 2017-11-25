<?php
/**
 * The sidebar containing the main widget area.
 *
 * @package storefront
 */

if ( ! is_active_sidebar( 'sidebar-1' ) ) {
	return;
}
?>

<div class="right_block cat_right_block" role="complementary">
	<?php dynamic_sidebar( 'sidebar-right' ); ?>
</div><!-- #secondary -->
