<?php
/**
 * WPFactory Cross-Selling
 *
 * @version 1.0.0
 * @since   1.0.0
 * @author  WPFactory
 */

namespace WPFactory\WPFactory_Cross_Selling;

use WPFactory\WPFactory_Admin_Menu\WPFactory_Admin_Menu;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'WPFactory\WPFactory_Cross_Selling\WPFactory_Cross_Selling' ) ) {

	/**
	 * WPF_Cross_Selling.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	class WPFactory_Cross_Selling {

		/**
		 * Setup args.
		 *
		 * @since 1.0.0
		 *
		 * @var array
		 */
		protected $setup_args = array();

		/**
		 * Admin page slug.
		 *
		 * @since   1.0.0
		 *
		 * @var string
		 */
		protected $submenu_page_slug = 'wpfactory-cross-selling';

		/**
		 * Products.
		 *
		 * @since   1.0.0
		 *
		 * @var array
		 */
		protected $products = array();

		/**
		 * Product categories.
		 *
		 * @since   1.0.0
		 *
		 * @var array
		 */
		protected $product_categories = array();

		/**
		 * Initialized.
		 *
		 * @since   1.0.0
		 *
		 * @var bool
		 */
		protected $initialized = false;

		/**
		 * Submenu initialized.
		 *
		 * @since   1.0.0
		 *
		 * @var bool
		 */
		protected static $submenu_initialized = false;

		/**
		 * Setups the class.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param $args
		 *
		 * @return void
		 */
		function setup( $args = null ) {
			$args = wp_parse_args( $args, array(
				'plugin_file_path'   => '',
				'plugin_action_link' => array(),
				'admin_page'         => array()
			) );

			// Plugin action link.
			$args['plugin_action_link'] = wp_parse_args( $args['plugin_action_link'], array(
				'enabled' => true,
				'label'   => __( 'Recommendations', 'wpfactory-cross-selling' ),
			) );

			// Menu page.
			$args['admin_page'] = wp_parse_args( $args['admin_page'], array(
				'page_title' => __( 'WPFactory Recommendations', 'wpfactory-cross-selling' ),
				'menu_title' => __( 'Recommendations', 'wpfactory-cross-selling' ),
				'capability' => 'manage_options',
				'position'   => 20
			) );

			$this->setup_args = $args;
		}

		/**
		 * Initializes the class.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return void
		 */
		function init() {
			if ( $this->initialized ) {
				return;
			}
			$this->initialized = true;

			// WPFactory admin menu.
			WPFactory_Admin_Menu::get_instance();

			// Action links.
			if ( $this->get_setup_args()['plugin_action_link']['enabled'] ) {
				add_filter( 'plugin_action_links_' . $this->get_plugin_basename(), array( $this, 'add_action_links' ) );
			}

			// Cross-selling admin page.
			add_action( 'admin_menu', array( $this, 'create_cross_selling_submenu' ) );
		}

		/**
		 * Creates cross-selling submenu.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return void
		 */
		function create_cross_selling_submenu() {
			if ( self::$submenu_initialized ) {
				return;
			}
			self::$submenu_initialized = true;

			// Gets params.
			$setup_args = $this->get_setup_args();
			$admin_page = $setup_args['admin_page'] ?? '';
			$page_title = $admin_page['page_title'] ?? '';
			$menu_title = $admin_page['menu_title'] ?? '';
			$capability = $admin_page['capability'] ?? '';
			$position   = $admin_page['position'] ?? '';

			// Creates the submenu page.
			\add_submenu_page(
				WPFactory_Admin_Menu::get_instance()->get_menu_slug(),
				$page_title,
				$menu_title,
				$capability,
				$this->submenu_page_slug,
				array( $this, 'render_cross_selling_page' ),
				$position
			);
		}

		/**
		 * Renders cross-selling page.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return void
		 */
		function render_cross_selling_page() {
			$setup_args = $this->get_setup_args();
			$admin_page = $setup_args['admin_page'] ?? '';
			$page_title = $admin_page['page_title'] ?? '';
			$categories = $this->get_product_categories();
			$products   = $this->get_products();
			?>
			<div class="wrap wpfcs">
				<h1><?php echo esc_html( $page_title ); ?></h1>
				<?php foreach ( $categories as $category_data ): ?>
					<h2 class="wpfcs-category"><?php echo esc_html( $category_data['name'] ); ?></h2>
					<?php foreach ( wp_list_filter( $products, array( 'category_slug' => $category_data['slug'] ) ) as $product_data ): ?>
						<?php echo $this->get_template( 'product.php', array(
							'product_data'            => $product_data,
							'free_version_installed'  => $this->is_plugin_installed( $product_data['free_plugin_path'] ),
							'pro_version_installed'   => $this->is_plugin_installed( $product_data['pro_plugin_path'] ),
							'free_plugin_install_url' => $this->generate_free_plugin_install_url( $product_data['free_plugin_slug'] ),
							'pro_plugin_url'          => $product_data['pro_plugin_url']
						) ); ?>
					<?php endforeach; ?>
				<?php endforeach; ?>
			</div>
			<?php
			$this->get_cross_selligs_page_style();
		}

		/**
		 * Generates plugin install url.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param $plugin_slug
		 *
		 * @return string
		 */
		function generate_free_plugin_install_url( $plugin_slug ) {
			$nonce       = wp_create_nonce( 'install-plugin_' . $plugin_slug );
			$install_url = add_query_arg(
				array(
					'action'   => 'install-plugin',
					'plugin'   => $plugin_slug,
					'_wpnonce' => $nonce
				),
				admin_url( 'update.php' )
			);

			return $install_url;
		}

		/**
		 * get_cross_selligs_page_style.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return void
		 */
		function get_cross_selligs_page_style() {
			?>
			<style>
				.wpfcs-product {
					background: #FFFFFF;
					padding: 18px 24px;
					border: 1px solid #F2F2F2;
					display: flex;
					justify-content: flex-start; /* Aligns items horizontally (in a row) */
					align-items: center; /* Centers items vertically */
					gap: 18px;
					flex-wrap: wrap;
				}

				@media (max-width: 800px) {
					.wpfcs-product {
						justify-content: center; /* Distribute items evenly on mobile */
					}
				}

				.wpfcs-product-title {
					margin: 0 0 7px;
				}

				.wpfcs-product-desc {
					margin: 0;
					color: #878787;
				}

				.wpfcs-product-actions {
					margin-left: auto;
					border-left: 1px solid #F2F2F2;
					padding: 10px 0 10px 24px;
					display: flex;
					justify-content: flex-start; /* Aligns items horizontally (in a row) */
					align-items: center;
					gap: 10px;
				}

				@media (max-width: 800px) {
					.wpfcs-product-actions {
						margin-left: unset;
						padding-left: 0;
						border-left: none;
					}
				}

				.wpfcs-category {
					margin: 33px 0 17px 0;
					font-size: 18px;
					font-weight: 700;
				}

				.wpfcs-product-img-wrapper {
					width: 60px;
					height: 60px;
					background: rgb(255, 255, 255);
					background: linear-gradient(180deg, rgba(255, 255, 255, 1) 0%, rgba(190, 233, 252, 1) 100%);
					display: flex;
					justify-content: center;
					align-items: center;
				}

				.wpfcs-product-img-wrapper img {
					max-width: 100%;
					max-height: 100%;
					height: auto;
					width: auto;
				}

				.wpfcs-button {
					border-radius: 100px;
					font-size: 14px;
					padding: 7px 18px;
					cursor: pointer;
					text-decoration: none;
					display: flex;
					align-items: center; /* Centers items vertically */
					justify-content: center;
					font-weight: 700;
				}

				.wpfcs-button.disabled, .wpfcs-product.disabled {
					pointer-events: none; /* Disables any mouse events (clicks, hovers, etc.) */
					opacity: 0.5;
				}

				.wpfcs-button-2 {
					color: #14243B;
					border: 1px solid #DCDCDE;
				}

				.wpfcs-button-2:hover {
					background: #f5f5f5;
					color: #14243B;
				}

				.wpfcs-button-2 i {
					color: #1A2DC9;
				}

				.wpfcs-button-1 {
					color: #fff;
					background: #14243B;
				}

				.wpfcs-button-1:hover {
					background: #204677;
					color: #fff;
				}

				.wpfcs-button-1 i {
					color: #02AAF2;
				}

				.wpfcs-button i {
					margin: 0 8px 0 -3px;
				}
			</style>
			<?php
		}

		/**
		 * get_template
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param $template_name
		 * @param $args
		 *
		 * @return false|string
		 */
		function get_template( $template_name, $args = array() ) {
			$template_path = plugin_dir_path( $this->get_library_file_path() ) . 'templates/' . $template_name;
			if ( file_exists( $template_path ) ) {
				ob_start();
				foreach ( $args as $key => $value ) {
					$$key = $value;
				}
				include $template_path;
				$content = ob_get_clean();

				return $content;
			} else {
				return '<p>Template not found.</p>';
			}
		}

		/**
		 * is_plugin_installed.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param $plugin_slug
		 *
		 * @return bool
		 */
		function is_plugin_installed( $plugin_slug ) {
			$all_plugins = get_plugins();

			return isset( $all_plugins[ $plugin_slug ] );
		}

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

		/**
		 * get_product_categories.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return array|array[]
		 */
		function get_product_categories() {
			$this->product_categories = array(
				array(
					'name' => 'Email & Marketing',
					'slug' => 'email-&-marketing',
				),
				array(
					'name' => 'Order & Quantity Management',
					'slug' => 'order-&-quantity-management',
				),
			);

			return $this->product_categories;
		}

		/**
		 * Adds action links.
		 *
		 * @param $links
		 *
		 * @return array
		 */
		function add_action_links( $links ) {
			$setup_args  = $this->get_setup_args();
			$action_link = $setup_args['plugin_action_link'] ?? '';
			$label       = $action_link['label'] ?? '';
			//$link           = admin_url( 'options-general.php?page=' . $this->admin_page_slug );
			$link           = admin_url( 'admin.php?page=' . $this->submenu_page_slug );
			$target         = '_self';
			$custom_links[] = sprintf( '<a href="%s" target="%s">%s</a>', esc_url( $link ), sanitize_text_field( $target ), sanitize_text_field( $label ) );
			$links          = array_merge( $links, $custom_links );

			return $links;
		}

		/**
		 * get_setup_args.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return array
		 */
		public function get_setup_args() {
			return $this->setup_args;
		}

		/**
		 * get_file_path.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return string
		 */
		function get_plugin_file_path() {
			$setup_args = $this->get_setup_args();

			return $setup_args['plugin_file_path'];
		}

		/**
		 * get_file_path.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return string
		 */
		function get_library_file_path() {
			return dirname( __FILE__, 2 );
		}

		/**
		 * get_basename.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return string
		 */
		function get_plugin_basename() {
			$file_path = $this->get_plugin_file_path();

			return plugin_basename( $file_path );
		}

	}
}