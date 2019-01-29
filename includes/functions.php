<?php
/**
 * Functions that can be used to easily get posts or categories.
 *
 * @package wpmc
 */
if ( ! function_exists( 'wpmc_get_posts_by_main_category' ) ) {
	/**
	 * Get Posts by Main Category
	 *
	 * @param $category string Term ID or category slug.
	 *
	 * @return false|array
	 */
	function wpmc_get_posts_by_main_category( $category ) {
		$wpmc_query = new WP_Main_Category_Query;
		$args       = array(
			'category' => $category,
		);
		return $wpmc_query->get_posts_by_main_category( $args );
	}
}
if ( ! function_exists( 'wpmc_get_post_ids_by_main_category' ) ) {
	/**
	 * Get Posts by Main Category
	 *
	 * @param $category string Term ID or category slug.
	 *
	 * @return false|array
	 */
	function wpmc_get_post_ids_by_main_category( $category ) {
		$wpmc_query = new WP_Main_Category_Query;
		$args       = array(
			'category' => $category,
			'return'   => 'ids',
		);
		return $wpmc_query->get_posts_by_main_category( $args );
	}
}
if ( ! function_exists( 'wpmc_get_main_category' ) ) {
	/**
	 * Get Main Category
	 *
	 * @return false|array
	 */
	function wpmc_get_main_category() {
		$post = get_post();
		if ( ! $post ) {
			return false;
		}

		$wpmc_query = new WP_Main_Category_Query;
		return $wpmc_query->get_main_category( $post->ID );
	}
}