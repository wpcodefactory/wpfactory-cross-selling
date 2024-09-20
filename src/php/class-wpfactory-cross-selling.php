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
		 * @var Products
		 */
		protected $products;

		/**
		 * Product categories.
		 *
		 * @since   1.0.0
		 *
		 * @var Product_Categories
		 */
		protected $product_categories;

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

			// Products.
			$this->products = new Products();

			// Product Categories.
			$this->product_categories = new Product_Categories();

			// WPFactory admin menu.
			WPFactory_Admin_Menu::get_instance();

			// Action links.
			if ( $this->get_setup_args()['plugin_action_link']['enabled'] ) {
				add_filter( 'plugin_action_links_' . $this->get_plugin_basename(), array( $this, 'add_action_links' ) );
			}

			// Cross-selling admin page.
			add_action( 'admin_menu', array( $this, 'create_cross_selling_submenu' ) );

			// Hook into admin_enqueue_scripts
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
			//$test = plugin_dir_url( $this->get_library_file_path() );
			//error_log($test);

			/*add_action('admin_head', function(){
				*/?><!--
					<script type="text/javascript">
						__webpack_public_path__ = <?php /*echo untrailingslashit( plugin_dir_url( $this->get_library_file_path() ) )*/?>
					</script>
				--><?php
/*			},1);*/
		}

		function enqueue_admin_styles() {
			if ( ! isset( $_GET['page'] ) || $_GET['page'] !== $this->submenu_page_slug ) {
				return;
			}

			wp_enqueue_style( 'wpfactory-cross-selling', plugin_dir_url( $this->get_library_file_path() ) . 'assets/css/admin.css', array(), '1.0.0' );

			wp_enqueue_script(
				'wpfactory-cross-selling',
				plugin_dir_url( $this->get_library_file_path() ) . 'assets/js/admin.js',
				array(), // No dependencies
				'1.0.0',
				true // Load in the footer
			);
			wp_localize_script('wpfactory-cross-selling', 'myPluginUrl', plugin_dir_url( $this->get_library_file_path() ));


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
			$categories = $this->product_categories->get_product_categories();
			$products   = $this->products->get_products();
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
			//$this->get_cross_selligs_page_style();
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