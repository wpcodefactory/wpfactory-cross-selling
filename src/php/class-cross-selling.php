<?php
/**
 * WPFactory Cross-Selling
 *
 * @version 1.0.0
 * @since   1.0.0
 * @author  WPFactory
 */

namespace WPFactory\WPF_Cross_Selling;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'WPFactory\WPF_Cross_Selling\WPF_Cross_Selling' ) ) {

	/**
	 * WPF_Cross_Selling.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	class WPF_Cross_Selling {

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
		protected $admin_page_slug = 'wpfactory';

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
				'file_path'          => '',
				'plugin_action_link' => array(),
				'admin_page'         => array()
			) );

			// Plugin action link.
			$args['plugin_action_link'] = wp_parse_args( $args['plugin_action_link'], array(
				'label' => __( 'Recommendations', 'wpfactory-cross-selling' ),
			) );

			// Menu page.
			$args['admin_page'] = wp_parse_args( $args['admin_page'], array(
				'page_title' => __( 'WPFactory Recommendations', 'wpfactory-cross-selling' ),
				'menu_title' => __( 'Recommendations', 'wpfactory-cross-selling' ),
				'capability' => 'manage_options',
				'icon_url'   => '',
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
			// Action links.
			add_filter( 'plugin_action_links_' . $this->get_plugin_basename(), array( $this, 'add_action_links' ) );

			// WPFactory admin page.
			add_action( 'admin_menu', array( $this, 'create_wpfactory_menu_page' ) );

			// Cross-selling admin page.
			add_action( 'admin_menu', array( $this, 'create_cross_selling_page' ) );
		}

		/**
		 * create_wpfactory_menu_page.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return void
		 */
		function create_wpfactory_menu_page() {
			$icon = 'data:image/svg+xml;base64,PHN2ZyBpZD0iU3ZnanNTdmcxMDAxIiB3aWR0aD0iMjg4IiBoZWlnaHQ9IjI4OCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB2ZXJzaW9uPSIxLjEiIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIiB4bWxuczpzdmdqcz0iaHR0cDovL3N2Z2pzLmNvbS9zdmdqcyI+PGRlZnMgaWQ9IlN2Z2pzRGVmczEwMDIiPjwvZGVmcz48ZyBpZD0iU3ZnanNHMTAwOCI+PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIGZpbGw9Im5vbmUiIHZpZXdCb3g9IjAgMCA2MyA0OCIgd2lkdGg9IjI4OCIgaGVpZ2h0PSIyODgiPjxwYXRoIGZpbGw9InVybCgjYSkiIGQ9Ik0wIDQwLjYwMjRDMCAzOC42ODEzIDMuMDc4NjMgMzYuOTMxIDguMTI2MjEgMzUuNjE1N1YzNy41MjA3QzguMTI2MjEgMzkuOTc5OSAxMC4wMzE3IDQwLjMzNTMgMTEuOTAyNSA0MC45MjU0QzIxLjcyOTcgNDQuMDI1OSAzOS4zNTU4IDQzLjc3OTUgNTEuNTkyNSA0MC45MjU0QzUyLjczODMgNDAuNjU4MyA1NC4wODE0IDQwLjM4NDkgNTQuMDgxNCAzOC43NlYzNS42MTZDNTkuMTI4NiAzNi45MzEgNjIuMjA2NyAzOC42ODE2IDYyLjIwNjcgNDAuNjAyN0M2Mi4yMDY3IDQ0LjY4ODIgNDguMjgxNCA0Ny45OTk5IDMxLjEwMzggNDcuOTk5OUMxMy45MjU1IDQ3Ljk5OTMgMCA0NC42ODc5IDAgNDAuNjAyNFoiPjwvcGF0aD48cGF0aCBmaWxsPSJ1cmwoI2IpIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiIGQ9Ik00NC44MDggMjUuNTMwOEwzMy4zOTQ0IDE5LjU1MzRMMzMuMjA5NCAxOS40NTU2TDMwLjEyNSA0My4xNTA4QzM1LjYxOTkgNDMuMTc3IDQxLjMzOCA0Mi43NjE2IDQ2LjUxNzIgNDEuOTQ1OUw0NC44MDggMjUuNTMwOFoiIGNsaXAtcnVsZT0iZXZlbm9kZCI+PC9wYXRoPjxwYXRoIGZpbGw9IiM5ODk4OTgiIGZpbGwtcnVsZT0iZXZlbm9kZCIgZD0iTTMzLjIxNzQgMTkuMDI2TDMzLjE5MjIgMTkuMjM3N0wyMy44NDI2IDE0LjI2MTRDMjMuMTU0MSAxMy44OTc3IDIwLjgzNjYgMTMuNzM1OCAyMC44MzY2IDE2LjE4OTVWMTkuNDI1OEwxMS45NzI3IDE0Ljc0NzJDMTAuNzk5NSAxNC4xODA2IDguMTI1IDE0LjU4NTMgOC4xMjUgMTcuMjAxNVYzNS42MTZDMTMuODEzMSAzNC4xMzQzIDIyLjAwMSAzMy4yMDQzIDMxLjEwMjYgMzMuMjA0M0MzMS4yMDQ3IDMzLjIwNDMgMzEuMzA2MiAzMy4yMDU1IDMxLjQwODMgMzMuMjA1NUwzMy4xNjcyIDE5LjU4MTRMMzMuMjE3NCAxOS4wMjZaIiBjbGlwLXJ1bGU9ImV2ZW5vZGQiIGNsYXNzPSJjb2xvcjhBNjNDRCBzdmdTaGFwZSI+PC9wYXRoPjxwYXRoIGZpbGw9InVybCgjYykiIGQ9Ik01Mi4xNzYyIDI5LjI2OThMNDQuNzkwOCAyNS4zNjQ0TDQ0Ljc4NzEgMjUuMzI4OUw0NC44MDg3IDI1LjUzMDNMNDUuNjk3OCAzNC4wNjg0QzQ4Ljg1NTcgMzQuNDY4MiA1MS42OSAzNC45OTI5IDU0LjA4MTkgMzUuNjE2VjMyLjI3ODVDNTQuMDgxOSAzMC4xMzYzIDUyLjM3MzQgMjkuMzc0MyA1Mi4xNzYyIDI5LjI2OThaIj48L3BhdGg+PHBhdGggZmlsbD0idXJsKCNkKSIgZD0iTTQ0LjgwNzkgMjUuNTMwNEw0NC43ODYzIDI1LjMyODlMNDIuMTQ3MSAwLjg3Mjg4OEM0Mi4xMDUyIDAuNDI5MzA0IDQxLjY3NjQgMCA0MS4yNzYzIDBIMzYuMjQxN0MzNS44NjU2IDAgMzUuNDI5MiAwLjQxOTI3OCAzNS4zNzUxIDAuODM0NjA2TDMzLjIxODEgMTkuMDI1OEwzMy4xNjggMTkuNTgxNVYxOS41ODNMNDQuODE0IDI1LjU4NzJMNDQuODA3OSAyNS41MzA0WiI+PC9wYXRoPjxwYXRoIGZpbGw9InVybCgjZSkiIGZpbGwtcnVsZT0iZXZlbm9kZCIgZD0iTTQ1LjY5NzMgMzQuMDY1N0w0Ni41MTQzIDQxLjkxMDdDNDguMjc5NyA0MS42Mjc2IDQ5Ljk4MjggNDEuMjk3NiA1MS41OTIzIDQwLjkyMjFDNTIuNzM4MSA0MC42NTUgNTQuMDgxMSA0MC4zODIyIDU0LjA4MTEgMzguNzU2N1YzNS42MTM0QzUxLjY4OTUgMzQuOTkwMiA0OC44NTUyIDM0LjQ2NDkgNDUuNjk3MyAzNC4wNjU3WiIgY2xpcC1ydWxlPSJldmVub2RkIj48L3BhdGg+PHBhdGggZmlsbD0idXJsKCNmKSIgZD0iTTMxLjQwODMgMzMuMjA2OEMzMS4zMDYyIDMzLjIwNjIgMzEuMjA0NyAzMy4yMDU2IDMxLjEwMjYgMzMuMjA1NkMyMi4wMDEgMzMuMjA1NiAxMy44MTMxIDM0LjEzNTYgOC4xMjUgMzUuNjE2N1YzNy41MjJDOC4xMjUgMzkuOTgxMiAxMC4wMzA1IDQwLjMzNjYgMTEuOTAxMyA0MC45MjY3QzE2LjY0OTYgNDIuNDI0NSAyMy4yMTk0IDQzLjE0MDYgMzAuMTIyNCA0My4xNTk1Ij48L3BhdGg+PGRlZnM+PGxpbmVhckdyYWRpZW50IGlkPSJhIiB4MT0iMCIgeDI9IjYyLjIwNyIgeTE9IjQxLjgwNyIgeTI9IjQxLjgwNyIgZ3JhZGllbnRVbml0cz0idXNlclNwYWNlT25Vc2UiPjxzdG9wIHN0b3AtY29sb3I9IiM5MTkxOTEiIGNsYXNzPSJzdG9wQ29sb3I0OERCRDcgc3ZnU2hhcGUiPjwvc3RvcD48c3RvcCBvZmZzZXQ9IjEiIHN0b3AtY29sb3I9IiM3YTdhN2EiIGNsYXNzPSJzdG9wQ29sb3IwMUY0QTAgc3ZnU2hhcGUiPjwvc3RvcD48L2xpbmVhckdyYWRpZW50PjxsaW5lYXJHcmFkaWVudCBpZD0iYiIgeDE9IjI0LjE4MSIgeDI9IjQyLjgxNCIgeTE9IjI5LjMyNiIgeTI9IjM0LjI0OSIgZ3JhZGllbnRVbml0cz0idXNlclNwYWNlT25Vc2UiPjxzdG9wIHN0b3AtY29sb3I9IiM3YTdhN2EiIGNsYXNzPSJzdG9wQ29sb3IyQjQyQzkgc3ZnU2hhcGUiPjwvc3RvcD48c3RvcCBvZmZzZXQ9Ii4xOTEiIHN0b3AtY29sb3I9IiM3OTc5NzkiIGNsYXNzPSJzdG9wQ29sb3IyOTQ3Q0Egc3ZnU2hhcGUiPjwvc3RvcD48c3RvcCBvZmZzZXQ9Ii40MTQiIHN0b3AtY29sb3I9IiM3ODc4NzgiIGNsYXNzPSJzdG9wQ29sb3IyMjU1Q0Ugc3ZnU2hhcGUiPjwvc3RvcD48c3RvcCBvZmZzZXQ9Ii42NTQiIHN0b3AtY29sb3I9IiM3Njc2NzYiIGNsYXNzPSJzdG9wQ29sb3IxNzZERDUgc3ZnU2hhcGUiPjwvc3RvcD48c3RvcCBvZmZzZXQ9Ii45MDMiIHN0b3AtY29sb3I9IiM3MjcyNzIiIGNsYXNzPSJzdG9wQ29sb3IwNzhEREUgc3ZnU2hhcGUiPjwvc3RvcD48c3RvcCBvZmZzZXQ9IjEiIHN0b3AtY29sb3I9IiM3MTcxNzEiIGNsYXNzPSJzdG9wQ29sb3IwMDlDRTIgc3ZnU2hhcGUiPjwvc3RvcD48L2xpbmVhckdyYWRpZW50PjxsaW5lYXJHcmFkaWVudCBpZD0iYyIgeDE9IjMyLjMxMyIgeDI9IjU0LjQxMSIgeTE9IjMwLjQ3MiIgeTI9IjMwLjQ3MiIgZ3JhZGllbnRVbml0cz0idXNlclNwYWNlT25Vc2UiPjxzdG9wIHN0b3AtY29sb3I9IiM5OTk5OTkiIGNsYXNzPSJzdG9wQ29sb3I4QzYzQ0Ygc3ZnU2hhcGUiPjwvc3RvcD48c3RvcCBvZmZzZXQ9IjEiIHN0b3AtY29sb3I9IiM4NTg1ODUiIGNsYXNzPSJzdG9wQ29sb3IzODQ3RDMgc3ZnU2hhcGUiPjwvc3RvcD48L2xpbmVhckdyYWRpZW50PjxsaW5lYXJHcmFkaWVudCBpZD0iZCIgeDE9IjMxLjgyNCIgeDI9IjUxLjc2NiIgeTE9IjEyLjc5NCIgeTI9IjEyLjc5NCIgZ3JhZGllbnRVbml0cz0idXNlclNwYWNlT25Vc2UiPjxzdG9wIG9mZnNldD0iLjA0NiIgc3RvcC1jb2xvcj0iIzk5OTk5OSIgY2xhc3M9InN0b3BDb2xvcjhDNjNDRiBzdmdTaGFwZSI+PC9zdG9wPjxzdG9wIG9mZnNldD0iMSIgc3RvcC1jb2xvcj0iIzg1ODU4NSIgY2xhc3M9InN0b3BDb2xvcjM4NDdEMyBzdmdTaGFwZSI+PC9zdG9wPjwvbGluZWFyR3JhZGllbnQ+PGxpbmVhckdyYWRpZW50IGlkPSJlIiB4MT0iNDUuNjk3IiB4Mj0iNTQuMDgxIiB5MT0iMzcuOTg4IiB5Mj0iMzcuOTg4IiBncmFkaWVudFVuaXRzPSJ1c2VyU3BhY2VPblVzZSI+PHN0b3Agc3RvcC1jb2xvcj0iIzc1NzU3NSIgY2xhc3M9InN0b3BDb2xvcjA1OUFFNSBzdmdTaGFwZSI+PC9zdG9wPjxzdG9wIG9mZnNldD0iMSIgc3RvcC1jb2xvcj0iIzc0NzQ3NCIgY2xhc3M9InN0b3BDb2xvcjAxQUNFNyBzdmdTaGFwZSI+PC9zdG9wPjwvbGluZWFyR3JhZGllbnQ+PGxpbmVhckdyYWRpZW50IGlkPSJmIiB4MT0iOC4xMjUiIHgyPSIzMS40MDgiIHkxPSIzOC4xODMiIHkyPSIzOC4xODMiIGdyYWRpZW50VW5pdHM9InVzZXJTcGFjZU9uVXNlIj48c3RvcCBzdG9wLWNvbG9yPSIjNzY3Njc2IiBjbGFzcz0ic3RvcENvbG9yMUMzN0QxIHN2Z1NoYXBlIj48L3N0b3A+PHN0b3Agb2Zmc2V0PSIxIiBzdG9wLWNvbG9yPSIjNzU3NTc1IiBjbGFzcz0ic3RvcENvbG9yMEU3MEREIHN2Z1NoYXBlIj48L3N0b3A+PC9saW5lYXJHcmFkaWVudD48L2RlZnM+PC9zdmc+PC9nPjwvc3ZnPg==';

			\add_menu_page(
				__( 'WPFactory', 'wpfactory-cross-selling' ),
				__( 'WPFactory', 'wpfactory-cross-selling' ),
				'manage_options',
				$this->admin_page_slug,
				array( $this, 'render_cross_selling_page' ),
				$icon,
				64
			);

			// Removes submenu page.
			add_action( 'admin_head', function () {
				remove_submenu_page( $this->admin_page_slug, $this->admin_page_slug );  // 'parent-slug', 'subpage-slug'
			} );
		}

		/**
		 * Creates admin menu.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return void
		 */
		function create_cross_selling_page() {
			$setup_args = $this->get_setup_args();
			$admin_page = $setup_args['admin_page'] ?? '';
			$page_title = $admin_page['page_title'] ?? '';
			$menu_title = $admin_page['menu_title'] ?? '';
			$capability = $admin_page['capability'] ?? '';
			$icon       = $admin_page['icon'] ?? '';

			\add_submenu_page(
				$this->admin_page_slug,
				$page_title,
				$menu_title,
				$capability,
				$this->submenu_page_slug,
				array( $this, 'render_cross_selling_page' ),
				20
			);
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
			$nonce = wp_create_nonce( 'install-plugin_' . $plugin_slug );
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
			$this->is_plugin_installed('woocommerce/woocommerce.php');

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
				.wpfcs-product{
					background:#FFFFFF;
					padding:18px 24px;
					border:1px solid #F2F2F2;
					display: flex;
					justify-content: flex-start; /* Aligns items horizontally (in a row) */
					align-items: center;     /* Centers items vertically */
					gap:18px;
					flex-wrap: wrap;
				}
				@media (max-width: 800px) {
					.wpfcs-product {
						justify-content: center;  /* Distribute items evenly on mobile */
					}
				}
				.wpfcs-product-title{
					margin:0 0 7px;
				}
				.wpfcs-product-desc{
					margin:0;
					color:#878787;
				}
				.wpfcs-product-actions{
					margin-left:auto;
					border-left:1px solid #F2F2F2;
					padding:10px 0 10px 24px;
					display: flex;
					justify-content: flex-start; /* Aligns items horizontally (in a row) */
					align-items: center;
					gap:10px;
				}
				@media (max-width: 800px) {
					.wpfcs-product-actions {
						margin-left:unset;
						padding-left:0;
						border-left:none;
					}
				}
				.wpfcs-category{
					margin:33px 0 17px 0;
					font-size:18px;
					font-weight:700;
				}
				.wpfcs-product-img-wrapper{
					width:60px;
					height:60px;
					background: rgb(255,255,255);
					background: linear-gradient(180deg, rgba(255,255,255,1) 0%, rgba(190,233,252,1) 100%);
					display: flex;
					justify-content: center;
					align-items: center;
				}
				.wpfcs-product-img-wrapper img{
					max-width: 100%;
					max-height: 100%;
					height: auto;
					width: auto;
				}
				.wpfcs-button{
					border-radius:100px;
					font-size:14px;
					padding:7px 18px;
					cursor:pointer;
					text-decoration:none;
					display: flex;
					align-items: center;  /* Centers items vertically */
					justify-content: center;
					font-weight:700;
				}
				.wpfcs-button.disabled,.wpfcs-product.disabled{
					pointer-events: none; /* Disables any mouse events (clicks, hovers, etc.) */
					opacity: 0.5;
				}
				.wpfcs-button-2{
					color:#14243B;
					border:1px solid #DCDCDE;
				}
				.wpfcs-button-2:hover{
					background: #f5f5f5;
					color:#14243B;
				}
				.wpfcs-button-2 i{
					color:#1A2DC9;
				}
				.wpfcs-button-1{
					color:#fff;
					background: #14243B;
				}
				.wpfcs-button-1:hover{
					background: #204677;
					color:#fff;
				}
				.wpfcs-button-1 i{
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
					'pro_plugin_url'  => 'http://uol.com.br',
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
			$links          = array_merge( $custom_links, $links );

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

			return $setup_args['file_path'];
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