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
					'category_slug'    => 'marketing-&-promotion',
					'icon_url'         => 'https://ps.w.org/wish-list-for-woocommerce/assets/icon.svg?rev=3078494',
					'free_plugin_path' => 'wish-list-for-woocommerce/wish-list-for-woocommerce.php',
					'free_plugin_slug' => 'wish-list-for-woocommerce',
					'pro_plugin_path'  => 'wish-list-for-woocommerce-pro/wish-list-for-woocommerce-pro.php',
					'pro_plugin_url'   => 'https://wpfactory.com/item/wish-list-woocommerce/?utm_source=plugin&utm_medium=cross-selling&utm_campaign=wpfactory',
				),
				array(
					'name'             => 'Min Max Default Quantity for WooCommerce',
					'desc'             => 'description for additional custom email for woocommerce',
					'category_slug'    => 'orders-restrictions',
					'icon_url'         => 'https://ps.w.org/product-quantity-for-woocommerce/assets/icon.svg?rev=2970983',
					'free_plugin_path' => 'product-quantity-for-woocommerce/product-quantity-for-woocommerce.php',
					'free_plugin_slug' => 'product-quantity-for-woocommerce',
					'pro_plugin_path'  => 'product-quantity-for-woocommerce-pro/product-quantity-for-woocommerce-pro.php',
					'pro_plugin_url'   => 'https://wpfactory.com/item/product-quantity-for-woocommerce/?utm_source=plugin&utm_medium=cross-selling&utm_campaign=wpfactory'
				),
				array(
					'name'             => 'Cost of Goods Sold (COGS): Cost & Profit Calculator for WooCommerce',
					'desc'             => 'description for Payment Gateways by Shipping for WooCommerce',
					'category_slug'    => 'admin-&-reporting',
					'icon_url'         => 'https://ps.w.org/cost-of-goods-for-woocommerce/assets/icon.svg',
					'free_plugin_path' => 'cost-of-goods-for-woocommerce/cost-of-goods-for-woocommerce.php',
					'free_plugin_slug' => 'cost-of-goods-for-woocommerce',
					'pro_plugin_path'  => 'cost-of-goods-for-woocommerce-pro/cost-of-goods-for-woocommerce-pro.php',
					'pro_plugin_url'   => 'https://wpfactory.com/item/cost-of-goods-for-woocommerce/?utm_source=plugin&utm_medium=cross-selling&utm_campaign=wpfactory'
				),
				array(
					'name'             => 'Maximum Products per User for WooCommerce',
					'desc'             => 'description for Payment Gateways by Shipping for WooCommerce',
					'category_slug'    => 'orders-restrictions',
					'icon_url'         => 'https://ps.w.org/maximum-products-per-user-for-woocommerce/assets/icon.svg',
					'free_plugin_path' => 'maximum-products-per-user-for-woocommerce/maximum-products-per-user-for-woocommerce.php',
					'free_plugin_slug' => 'maximum-products-per-user-for-woocommerce',
					'pro_plugin_path'  => 'maximum-products-per-user-for-woocommerce-pro/maximum-products-per-user-for-woocommerce-pro.php',
					'pro_plugin_url'   => 'https://wpfactory.com/item/maximum-products-per-user-for-woocommerce/?utm_source=plugin&utm_medium=cross-selling&utm_campaign=wpfactory'
				),
				array(
					'name'             => 'Order Minimum/Maximum Amount for WooCommerce',
					'desc'             => 'description for Payment Gateways by Shipping for WooCommerce',
					'category_slug'    => 'orders-restrictions',
					'icon_url'         => 'https://ps.w.org/order-minimum-amount-for-woocommerce/assets/icon.svg',
					'free_plugin_path' => 'order-minimum-amount-for-woocommerce/order-minimum-amount-for-woocommerce.php',
					'free_plugin_slug' => 'order-minimum-amount-for-woocommerce',
					'pro_plugin_path'  => 'order-minimum-amount-for-woocommerce-pro/order-minimum-amount-for-woocommerce-pro.php',
					'pro_plugin_url'   => 'https://wpfactory.com/item/order-minimum-maximum-amount-for-woocommerce/?utm_source=plugin&utm_medium=cross-selling&utm_campaign=wpfactory'
				),
				array(
					'name'             => 'EU VAT Manager for WooCommerce',
					'desc'             => 'description for Payment Gateways by Shipping for WooCommerce',
					'category_slug'    => 'admin-&-reporting',
					'icon_url'         => 'https://ps.w.org/eu-vat-for-woocommerce/assets/icon.svg',
					'free_plugin_path' => 'eu-vat-for-woocommerce/eu-vat-for-woocommerce.php',
					'free_plugin_slug' => 'eu-vat-for-woocommerce',
					'pro_plugin_path'  => 'eu-vat-for-woocommerce-pro/eu-vat-for-woocommerce-pro.php',
					'pro_plugin_url'   => 'https://wpfactory.com/item/eu-vat-for-woocommerce/?utm_source=plugin&utm_medium=cross-selling&utm_campaign=wpfactory'
				),
				array(
					'name'             => 'Email Verification for WooCommerce',
					'desc'             => 'description for Payment Gateways by Shipping for WooCommerce',
					'category_slug'    => 'admin-&-reporting',
					'icon_url'         => 'https://ps.w.org/emails-verification-for-woocommerce/assets/icon.svg',
					'free_plugin_path' => 'emails-verification-for-woocommerce/email-verification-for-woocommerce.php',
					'free_plugin_slug' => 'emails-verification-for-woocommerce',
					'pro_plugin_path'  => 'email-verification-for-woocommerce-pro/email-verification-for-woocommerce-pro.php',
					'pro_plugin_url'   => 'https://wpfactory.com/item/email-verification-for-woocommerce/?utm_source=plugin&utm_medium=cross-selling&utm_campaign=wpfactory'
				),
				array(
					'name'             => 'Free Shipping Over Amount: Amount Left Tracker for WooCommerce',
					'desc'             => 'description for Payment Gateways by Shipping for WooCommerce',
					'category_slug'    => 'marketing-&-promotion',
					'icon_url'         => 'https://ps.w.org/amount-left-free-shipping-woocommerce/assets/icon.svg',
					'free_plugin_path' => 'amount-left-free-shipping-woocommerce/left-to-free-shipping-for-woocommerce.php',
					'free_plugin_slug' => 'amount-left-free-shipping-woocommerce',
					'pro_plugin_path'  => 'left-to-free-shipping-for-woocommerce-pro/left-to-free-shipping-for-woocommerce-pro.php',
					'pro_plugin_url'   => 'https://wpfactory.com/item/amount-left-free-shipping-woocommerce/?utm_source=plugin&utm_medium=cross-selling&utm_campaign=wpfactory'
				),
				array(
					'name'             => 'Payment Methods by Product & Country for WooCommerce',
					'desc'             => 'description for Payment Gateways by Shipping for WooCommerce',
					'category_slug'    => 'orders-restrictions',
					'icon_url'         => 'https://ps.w.org/payment-gateways-per-product-categories-for-woocommerce/assets/icon.svg',
					'free_plugin_path' => 'payment-gateways-per-product-categories-for-woocommerce/payment-gateways-per-product-for-woocommerce.php',
					'free_plugin_slug' => 'payment-gateways-per-product-categories-for-woocommerce',
					'pro_plugin_path'  => 'payment-gateways-per-product-for-woocommerce-pro/payment-gateways-per-product-for-woocommerce-pro.php',
					'pro_plugin_url'   => 'https://wpfactory.com/item/payment-gateways-per-product-for-woocommerce/?utm_source=plugin&utm_medium=cross-selling&utm_campaign=wpfactory'
				),
				array(
					'name'             => 'Product XML Feeds for WooCommerce',
					'desc'             => 'description for Payment Gateways by Shipping for WooCommerce',
					'category_slug'    => 'admin-&-reporting',
					'icon_url'         => 'https://ps.w.org/product-xml-feeds-for-woocommerce/assets/icon.svg',
					'free_plugin_path' => 'product-xml-feeds-for-woocommerce/product-xml-feeds-for-woocommerce.php.php',
					'free_plugin_slug' => 'product-xml-feeds-for-woocommerce',
					'pro_plugin_path'  => 'product-xml-feeds-for-woocommerce-pro/product-xml-feeds-for-woocommerce-pro.php',
					'pro_plugin_url'   => 'https://wpfactory.com/item/product-xml-feeds-woocommerce/?utm_source=plugin&utm_medium=cross-selling&utm_campaign=wpfactory'
				),
				array(
					'name'             => 'Popup Notices: Added to Cart, Checkout Popups & More',
					'desc'             => 'description for Payment Gateways by Shipping for WooCommerce',
					'category_slug'    => 'marketing-&-promotion',
					'icon_url'         => 'https://ps.w.org/popup-notices-for-woocommerce/assets/icon.svg',
					'free_plugin_path' => 'popup-notices-for-woocommerce/popup-notices-for-woocommerce.php',
					'free_plugin_slug' => 'popup-notices-for-woocommerce',
					'pro_plugin_path'  => 'popup-notices-for-woocommerce-pro/popup-notices-for-woocommerce-pro.php',
					'pro_plugin_url'   => 'https://wpfactory.com/item/popup-notices-for-woocommerce/?utm_source=plugin&utm_medium=cross-selling&utm_campaign=wpfactory'
				),
				array(
					'name'             => 'EAN and Barcodes for WooCommerce',
					'desc'             => 'description for Payment Gateways by Shipping for WooCommerce',
					'category_slug'    => 'admin-&-reporting',
					'icon_url'         => 'https://ps.w.org/ean-for-woocommerce/assets/icon.svg',
					'free_plugin_path' => 'ean-for-woocommerce/ean-for-woocommerce.php',
					'free_plugin_slug' => 'ean-for-woocommerce',
					'pro_plugin_path'  => 'ean-for-woocommerce-pro/ean-for-woocommerce-pro.php',
					'pro_plugin_url'   => 'https://wpfactory.com/item/ean-barcodes-woocommerce/?utm_source=plugin&utm_medium=cross-selling&utm_campaign=wpfactory'
				),
				array(
					'name'             => 'MSRP (RRP) Pricing for WooCommerce',
					'desc'             => 'description for Payment Gateways by Shipping for WooCommerce',
					'category_slug'    => 'marketing-&-promotion',
					'icon_url'         => 'https://ps.w.org/msrp-for-woocommerce/assets/icon.svg',
					'free_plugin_path' => 'msrp-for-woocommerce/msrp-for-woocommerce.php',
					'free_plugin_slug' => 'msrp-for-woocommerce',
					'pro_plugin_path'  => 'msrp-for-woocommerce-pro/msrp-for-woocommerce-pro.php',
					'pro_plugin_url'   => 'https://wpfactory.com/item/msrp-for-woocommerce/?utm_source=plugin&utm_medium=cross-selling&utm_campaign=wpfactory'
				),
				array(
					'name'             => 'File Renaming on Upload – WordPress Plugin',
					'desc'             => 'description for Payment Gateways by Shipping for WooCommerce',
					'category_slug'    => 'wordpress-utilities',
					'icon_url'         => 'https://ps.w.org/file-renaming-on-upload/assets/icon.svg',
					'free_plugin_path' => 'file-renaming-on-upload/file-renaming-on-upload.php',
					'free_plugin_slug' => 'file-renaming-on-upload',
					'pro_plugin_path'  => 'file-renaming-on-upload-pro/file-renaming-on-upload-pro.php',
					'pro_plugin_url'   => 'https://wpfactory.com/item/file-renaming-on-upload-wordpress-plugin/?utm_source=plugin&utm_medium=cross-selling&utm_campaign=wpfactory'
				),
				array(
					'name'             => 'Coupons & Add to Cart by URL for WooCommerce',
					'desc'             => 'description for Payment Gateways by Shipping for WooCommerce',
					'category_slug'    => 'marketing-&-promotion',
					'icon_url'         => 'https://ps.w.org/url-coupons-for-woocommerce-by-algoritmika/assets/icon.svg',
					'free_plugin_path' => 'url-coupons-for-woocommerce-by-algoritmika/url-coupons-woocommerce.php',
					'free_plugin_slug' => 'url-coupons-for-woocommerce-by-algoritmika',
					'pro_plugin_path'  => 'url-coupons-woocommerce-pro/url-coupons-woocommerce-pro.php',
					'pro_plugin_url'   => 'https://wpfactory.com/item/url-coupons-woocommerce/?utm_source=plugin&utm_medium=cross-selling&utm_campaign=wpfactory'
				),
				array(
					'name'             => 'Dynamic Pricing & Bulk Quantity Discounts',
					'desc'             => 'description for Payment Gateways by Shipping for WooCommerce',
					'category_slug'    => 'marketing-&-promotion',
					'icon_url'         => 'https://ps.w.org/wholesale-pricing-woocommerce/assets/icon.svg',
					'free_plugin_path' => 'wholesale-pricing-woocommerce/wholesale-pricing-woocommerce.php',
					'free_plugin_slug' => 'wholesale-pricing-woocommerce',
					'pro_plugin_path'  => 'wholesale-pricing-woocommerce-pro/wholesale-pricing-woocommerce-pro.php',
					'pro_plugin_url'   => 'https://wpfactory.com/item/product-price-by-quantity-for-woocommerce/?utm_source=plugin&utm_medium=cross-selling&utm_campaign=wpfactory'
				),
				array(
					'name'             => 'Download Plugins and Themes from Dashboard',
					'desc'             => 'description for Payment Gateways by Shipping for WooCommerce',
					'category_slug'    => 'wordpress-utilities',
					'icon_url'         => 'https://ps.w.org/download-plugins-dashboard/assets/icon.svg',
					'free_plugin_path' => 'download-plugins-dashboard/download-plugins-from-dashboard.php',
					'free_plugin_slug' => 'download-plugins-dashboard',
					'pro_plugin_path'  => 'download-plugins-from-dashboard-pro/download-plugins-from-dashboard-pro.php',
					'pro_plugin_url'   => 'https://wpfactory.com/item/download-plugins-and-themes-from-dashboard/?utm_source=plugin&utm_medium=cross-selling&utm_campaign=wpfactory'
				),
				array(
					'name'             => 'Back Button Widget - WordPress Plugin',
					'desc'             => 'description for Payment Gateways by Shipping for WooCommerce',
					'category_slug'    => 'wordpress-utilities',
					'icon_url'         => 'https://ps.w.org/back-button-widget/assets/icon.svg',
					'free_plugin_path' => 'back-button-widget/back-button-widget.php',
					'free_plugin_slug' => 'back-button-widget',
					'pro_plugin_path'  => 'back-button-widget-pro/back-button-widget-pro.php',
					'pro_plugin_url'   => 'https://wpfactory.com/item/back-button-widget-wordpress-plugin/?utm_source=plugin&utm_medium=cross-selling&utm_campaign=wpfactory'
				),
				array(
					'name'             => 'Slugs Manager: Delete Old Permalinks ',
					'desc'             => 'description for Payment Gateways by Shipping for WooCommerce',
					'category_slug'    => 'wordpress-utilities',
					'icon_url'         => 'https://ps.w.org/remove-old-slugspermalinks/assets/icon.svg',
					'free_plugin_path' => 'remove-old-slugspermalinks/remove-old-slugs.php',
					'free_plugin_slug' => 'remove-old-slugspermalinks',
					'pro_plugin_path'  => 'remove-old-slugs-pro/remove-old-slugs-pro.php',
					'pro_plugin_url'   => 'https://wpfactory.com/item/slugs-manager-wordpress-plugin/?utm_source=plugin&utm_medium=cross-selling&utm_campaign=wpfactory'
				),
				array(
					'name'             => 'Name Your Price: Make a Price Offer for WooCommerce',
					'desc'             => 'description for Payment Gateways by Shipping for WooCommerce',
					'category_slug'    => 'marketing-&-promotion',
					'icon_url'         => 'https://ps.w.org/price-offerings-for-woocommerce/assets/icon.svg',
					'free_plugin_path' => 'price-offerings-for-woocommerce/price-offerings-for-woocommerce.php',
					'free_plugin_slug' => 'price-offerings-for-woocommerce',
					'pro_plugin_path'  => 'price-offerings-for-woocommerce-pro/price-offerings-for-woocommerce-pro.php',
					'pro_plugin_url'   => 'https://wpfactory.com/item/price-offers-for-woocommerce/?utm_source=plugin&utm_medium=cross-selling&utm_campaign=wpfactory'
				),
			);

			return $this->products;
		}
	}
}