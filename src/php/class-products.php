<?php
/**
 * WPFactory Cross-Selling - Products
 *
 * @version 1.0.0
 * @since   1.0.0
 * @author  WPFactory
 */

namespace WPFactory\WPFactory_Cross_Selling;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'WPFactory\WPFactory_Cross_Selling\Products' ) ) {

	/**
	 * WPF_Cross_Selling.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	class Products {

		/**
		 * Products.
		 *
		 * @since   1.0.0
		 *
		 * @var array
		 */
		protected $products = array();

		/**
		 * get_products.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return array[]
		 */
		function get_products() {
			$this->products = array(
				array(
					'name'             => 'Wishlist for WooCommerce',
					'desc'             => 'description for wish list',
					'category_slug'    => 'email-&-marketing',
					'icon_url'         => 'https://ps.w.org/wish-list-for-woocommerce/assets/icon.svg?rev=3078494',
					'free_plugin_path' => 'wish-list-for-woocommerce/wish-list-for-woocommerce.php',
					'free_plugin_slug' => 'wish-list-for-woocommerce',
					'pro_plugin_path'  => 'wish-list-for-woocommerce-pro/wish-list-for-woocommerce-pro.php',
					'pro_plugin_url'   => 'http://uol.com.br',
				),
				array(
					'name'             => 'Additional Custom Email for WooCommerce',
					'desc'             => 'description for additional custom email for woocommerce',
					'category_slug'    => 'email-&-marketing',
					'icon_url'         => 'https://ps.w.org/custom-emails-for-woocommerce/assets/icon.svg?rev=2970983',
					'free_plugin_path' => 'a/a.php',
					'free_plugin_slug' => 'custom-emails-for-woocommerce',
					'pro_plugin_path'  => 'a-pro/a-pro.php',
					'pro_plugin_url'   => 'http://g1.com.br'
				),
				array(
					'name'             => 'Payment Gateways by Shipping for WooCommerce',
					'desc'             => 'description for Payment Gateways by Shipping for WooCommerce',
					'category_slug'    => 'order-&-quantity-management',
					'icon_url'         => 'https://ps.w.org/payment-gateways-per-product-categories-for-woocommerce/assets/icon.svg',
					'free_plugin_path' => 'payment-gateways-per-product-categories-for-woocommerce/payment-gateways-per-product-for-woocommerce.php',
					'free_plugin_slug' => 'payment-gateways-per-product-categories-for-woocommerce',
					'pro_plugin_path'  => 'a-pro/a-pro.php',
					'pro_plugin_url'   => 'https://wpfactory.com/item/payment-gateways-per-product-for-woocommerce/?utm_source=plugin&utm_medium=cross-selling&utm_campaign=wpfactory'
				)
			);

			return $this->products;
		}
	}
}