<?php
/*
Plugin Name: WPFactory Cross Selling
Description: WPFactory Cross Selling.
Version: 1.0.3
Author: WPFactory
Author URI: https://wpfactory.com
Text Domain: 'wpfactory-cross-selling'
Domain Path: /langs
Copyright: © 2025 WPFactory
WC tested up to: 9.9
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

/**
 * Testing.
 */
add_action( 'plugins_loaded', function () {
	if ( ! is_admin() ) {
		return;
	}
	require_once plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';

	//$fake_plugin_file_path = 'C:\laragon\www\dev\wp-content\plugins\cost-of-goods-for-woocommerce-pro\cost-of-goods-for-woocommerce-pro.php';
	$fake_plugin_file_path = __FILE__;

	// Cross-selling library.
	$cross_selling = new \WPFactory\WPFactory_Cross_Selling\WPFactory_Cross_Selling();
	$cross_selling->setup( array(
		'plugin_file_path'     => $fake_plugin_file_path,
		'wc_settings_tab_id'   => 'products',
		'recommendations_box'  => array(
			//'enable'=>false
		),
		'recommendations_page' => array(
			'action_link' => array(
				//'enable' => true,
			)
		)
	) );
	//$cross_selling->setup( array( 'plugin_file_path'   => $this->get_filesystem_path() ) );
	$cross_selling->init();
} );