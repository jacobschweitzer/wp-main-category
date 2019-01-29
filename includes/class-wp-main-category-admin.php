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
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_scripts' ) );
		add_action( 'admin_footer', array( $this, 'add_input' ) );
	}

	/**
	 * Enqueue scripts to be used in the admin interface.
	 *
	 * @todo Add the non-Gutenberg version.
	 */
	public function enqueue_scripts() {
		if ( $this->using_gutenberg() ) {
			if ( WP_DEBUG ) {
				$script_url = plugins_url( 'js/src/wp-main-category-gutenberg.js', WPMC__FILE__ );
			} else {
				$script_url = plugins_url( 'js/dist/wp-main-category-gutenberg.js', WPMC__FILE__ );
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
}
