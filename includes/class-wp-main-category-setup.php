<?php

/**
 * Class WP_Main_Category_Setup
 *
 * @package wpmc
 */
class WP_Main_Category_Setup {

	const TAXONOMY_SLUG = 'wpmc_main_category';

	public function __construct() {
		$this->actions();
	}

	/**
	 * Actions
	 */
	public function actions() {
		add_action( 'init', array( $this, 'register_taxonomy' ) );
		add_action( 'rest_api_init', array( $this, 'register_save_taxonomy_route' ) );
	}

	/**
	 * Register the taxonomy for all post types that have the category taxonomy.
	 */
	public function register_taxonomy() {
		$post_types        = get_post_types();
		$target_post_types = array();

		// Gather all the post types that use the category taxonomy.
		foreach ( $post_types as $post_type ) {
			if ( is_object_in_taxonomy( $post_type, 'category' ) ) {
				$target_post_types[] = $post_type;
			}
		}

		register_taxonomy(
			self::TAXONOMY_SLUG,
			$target_post_types,
			array(
				'label'        => __( 'Main Category' ),
				'public'       => false,
				'rewrite'      => false,
				'hierarchical' => false,
			)
		);
	}

	/**
	 * Register REST API route to save the taxonomy value.
	 */
	function register_save_taxonomy_route() {
		register_rest_route(
			'wpmc/v1', '/update-main-category/', array(
				'methods'  => 'POST',
				'callback' => array( $this, 'save_main_category' ),
			)
		);
	}

	/**
	 * Save Main Category REST API Callback for Gutenberg.
	 *
	 * @return array|false|WP_Error
	 */
	function save_main_category( $data ) {
		if ( ! wp_verify_nonce( $data['nonce'], 'wpmc' ) ) {
			return false;
		}
		return wp_set_post_terms( $data['post_id'], $data['value'], self::TAXONOMY_SLUG );
	}
}
