<?php

/**
 * Class WP_Main_Category_Admin
 *
 * @package wpmc
 */
class WP_Main_Category_Admin {

	public function __construct() {
		$this->actions();
	}

	/**
	 * Actions
	 */
	public function actions() {
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_gutenberg_script' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_classic_script' ) );
		add_action( 'admin_footer', array( $this, 'add_input' ) );
		add_action( 'save_post', array( $this, 'save_main_category_classic' ) );
	}

	/**
	 * Enqueue scripts to be used in the admin interface.
	 */
	public function enqueue_gutenberg_script() {
		if ( WP_DEBUG ) {
			$script_url = plugins_url( 'js/src/wp-main-category-gutenberg.js', WPMC__FILE__ );
		} else {
			$script_url = plugins_url( 'js/dist/wp-main-category-gutenberg.min.js', WPMC__FILE__ );
		}
		wp_enqueue_script(
			'wpmc_admin',
			$script_url,
			array(
				'underscore',
				'wp-hooks',
				'wp-components',
				'react',
				'wp-api',
				'wp-api-fetch',
			),
			WPMC__VERSION
		);

		wp_localize_script( 'wpmc_admin',
			'wpmc',
			array(
				'nonce' => wp_create_nonce( 'wpmc' ),
			)
		);
	}

	/**
	 * Enqueue Classic Script (Non-Gutenberg version)
	 */
	public function enqueue_classic_script() {
		if ( $this->using_gutenberg() ) {
			return;
		}

		if ( WP_DEBUG ) {
			$script_url = plugins_url( 'js/src/wp-main-category-classic.js', WPMC__FILE__ );
		} else {
			$script_url = plugins_url( 'js/dist/wp-main-category-classic.min.js', WPMC__FILE__ );
		}
		wp_enqueue_script(
			'wpmc_admin_classic',
			$script_url,
			array(),
			WPMC__VERSION
		);

		$main_cat = wp_get_post_terms( get_the_ID(), 'wpmc_main_category' );
		$main_category_id = -1;
		if ( $main_cat ) {
			$main_category_id = intval( $main_cat[0]->name );
		}
		wp_localize_script( 'wpmc_admin_classic',
			'wpmc',
			array(
				'nonce'        => wp_create_nonce( 'wpmc' ),
				'mainCategory' => $main_category_id,
			)
		);
	}

	/**
	 * Checks if we are using Gutenberg on this page.
	 *
	 * @return bool
	 */
	public function using_gutenberg() {
		$post = get_post();
		if ( ! $post ) {
			return false;
		}

		if ( function_exists( 'use_block_editor_for_post' ) ) {
			return ! empty( $post->ID ) && use_block_editor_for_post( $post->ID );
		}

		if ( function_exists( 'is_gutenberg_page' ) ) {
			return is_gutenberg_page();
		}

		return false;
	}

	/**
	 * Add Input
	 */
	public function add_input() {
		$screen = get_current_screen();
		if ( 'edit' !== $screen->parent_base ) {
			return;
		}

		$main_cat = wp_get_post_terms( get_the_ID(), 'wpmc_main_category' );
		printf(
			'<input type="hidden" id="wpmc[_primary_category]" name="wpmc[_primary_category]" value="%d">',
			! is_wp_error( $main_cat ) && ! empty( $main_cat ) ? esc_attr( $main_cat[0]->name ) : ''
		);
	}

	/**
	 * Save Main Category Classic
	 *
	 * @param int $post_id Post ID.
	 */
	public function save_main_category_classic( $post_id ) {
		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}

		$args = array(
			'wpmc' => array(
				'filter' => FILTER_VALIDATE_INT,
				'flags'  => FILTER_REQUIRE_ARRAY,
			),
		);

		$main_category_array = filter_input_array( INPUT_POST, $args );
		if ( ! isset( $main_category_array['wpmc'][0] ) ) {
			return;
		}

		$main_category = $main_category_array['wpmc'][0];
		wp_set_post_terms( $post_id, $main_category, 'wpmc_main_category' );
	}
}
