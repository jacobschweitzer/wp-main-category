<?php
/**
 * Plugin Name: WP Main Category
 * Description: Allow selection of one category to be the main category for a post.
 * Plugin URI: https://jacobschweitzer.com
 * Author: Jacob Schweitzer
 * Author URI: https://github.com/jacobschweitzer/wp-main-category
 * Version: 0.2
 * Text Domain: wp-main-category
 * Domain Path: /languages/
 * License: GPLv2 or later
 *
 * @package wpmc
 */

define( 'WPMC__FILE__', __FILE__ );
define( 'WPMC__DIR__', dirname( __FILE__ ) );
define( 'WPMC__VERSION', '0.2' );

require_once WPMC__DIR__ . '/includes/class-wp-main-category-setup.php';
$wpmc_setup = new WP_Main_Category_Setup();
$wpmc_setup->actions();

if ( is_admin() ) {
	require_once WPMC__DIR__ . '/includes/class-wp-main-category-admin.php';
	$admin = new WP_Main_Category_Admin();
} else {
	require_once WPMC__DIR__ . '/includes/class-wp-main-category-query.php';
	require_once WPMC__DIR__ . '/includes/functions.php';
}

