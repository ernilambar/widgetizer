<?php
/**
 * Initialize
 *
 * @package Widgetizer
 */

if ( ! defined( 'WIDGETIZER_VERSION' ) ) {
	define( 'WIDGETIZER_VERSION', '1.0.0' );
}

if ( ! defined( 'WIDGETIZER_DIR' ) ) {
	define( 'WIDGETIZER_DIR', rtrim( plugin_dir_path( __FILE__ ), '/' ) );
}

if ( ! defined( 'WIDGETIZER_URL' ) ) {
	define( 'WIDGETIZER_URL', rtrim( plugin_dir_url( __FILE__ ), '/' ) );
}


if ( ! function_exists( 'widgetizer_init' ) ) {
	function widgetizer_init() {

		add_action(
			'admin_enqueue_scripts',
			function () {
				wp_enqueue_style( 'widgetizer-style', WIDGETIZER_URL . '/assets/widgetizer.css', [], WIDGETIZER_VERSION );
				wp_enqueue_script( 'widgetizer-script', WIDGETIZER_URL . '/assets/widgetizer.js', [ 'jquery', 'jquery-ui-sortable' ], WIDGETIZER_VERSION, true );
			}
		);
	}
}

add_action( 'init', 'widgetizer_init' );
