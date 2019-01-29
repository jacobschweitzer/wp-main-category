<?php

/**
 * Class WP_Main_Category_Query
 *
 * @package wpmc
 */
class WP_Main_Category_Query {

	/**
	 * Get Posts by Main Category
	 *
	 * @param $args
	 *
	 * @return array|bool
	 */
	public function get_posts_by_main_category( $args ) {
		if ( ! isset( $args['category'] ) ) {
			return false;
		}

		$term_id = self::get_term_id( $args['category'] );
		if ( ! $term_id ) {
			return false;
		}

		$query_args = self::get_posts_by_main_category_query_args( $args, $term_id );

		$query = new WP_Query( $query_args );
		if ( empty( $query->posts ) ) {
			return array();
		}
		return $query->posts;
	}

	/**
	 * Get Term Id
	 *
	 * @param $category string Category term ID or slug.
	 *
	 * @return int|null|string
	 */
	protected function get_term_id( $category ) {
		$term_id = null;
		if ( ! is_numeric( $category ) ) {
			$term = get_term_by( 'slug', $category, 'category' );
			if ( $term ) {
				$term_id = $term->term_id;
			}
		} else {
			$term_id = $category;
		}
		return $term_id;
	}

	/**
	 * Get Posts by Main Category Query Args
	 *
	 * @param $args     array Arguments passed.
	 * @param $term_id  int   Term ID.
	 *
	 * @return array
	 */
	protected function get_posts_by_main_category_query_args( $args, $term_id ) {
		$query_args = array(
			'post_type'              => 'any',
			'tax_query'              => array(
				array(
					'taxonomy' => WP_Main_Category_Setup::TAXONOMY_SLUG,
					'field'    => 'slug',
					'terms'    => $term_id,
				),
			),
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		);
		if ( isset( $args['return'] ) && 'ids' === $args['return'] ) {
			$query_args['fields'] = 'ids';
		}
		return apply_filters( 'wpmc_get_posts_by_main_category_query_args', $query_args );
	}

	/**
	 * Get Main Category
	 *
	 * @param $post_id
	 *
	 * @return bool|array|WP_Error
	 */
	public function get_main_category( $post_id ) {
		if ( empty( $post_id ) ) {
			$post = get_post();
			if ( ! $post ) {
				return null;
			}
			$post_id = $post->ID;
		}
		if ( empty( $post_id ) ) {
			return null;
		}
		return wp_get_post_terms( $post_id, WP_Main_Category_Setup::TAXONOMY_SLUG );
	}
}
