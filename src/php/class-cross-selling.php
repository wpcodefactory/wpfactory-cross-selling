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
		 * $admin_page_created.
		 *
		 * @since 1.0.0
		 *
		 * @var bool
		 */
		protected static $admin_page_created = false;

		/**
		 * Admin page slug.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @var string
		 */
		protected $admin_page_slug = 'wpcodefactory';

		/**
		 * Admin page slug.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @var string
		 */
		protected $submenu_page_slug = 'wpcodefactory-cross-selling';

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
				'page_title' => __( 'More by WPFactory', 'wpfactory-cross-selling' ),
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
			add_filter( 'plugin_action_links_' . $this->get_plugin_basename(), array( $this, 'add_action_links' ) );

			// Hook to add the admin menu
			add_action( 'admin_menu', array( $this, 'create_menu_pages' ) );
		}

		/**
		 * Creates admin menu.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return void
		 */
		function create_menu_pages() {
			if ( self::$admin_page_created ) {
				return;
			}
			$setup_args = $this->get_setup_args();
			$admin_page = $setup_args['admin_page'] ?? '';
			$page_title = $admin_page['page_title'] ?? '';
			$menu_title = $admin_page['menu_title'] ?? '';
			$capability = $admin_page['capability'] ?? '';
			$icon = $admin_page['icon'] ?? '';

			$test = plugins_url('assets/img/wpfactory.png', $this->get_plugin_file_path());
			error_log(print_r(__FILE__,true));


			\add_menu_page(
				__( 'WPFactory', 'wpfactory-cross-selling' ),
				__( 'WPFactory', 'wpfactory-cross-selling' ),
				'manage_options',
				$this->admin_page_slug,
				array( $this, 'render_admin_page' ),
				plugins_url('assets/img/wpfactory.png', $this->get_plugin_file_path()),
				//'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNDUiIHZpZXdCb3g9IjAgMCA2MCA0NSIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHBhdGggZD0iTTAuODE0NDUzIDM4LjA2NDlDMC44MTQ0NTMgMzYuMjYzOSAzLjcwMDY4IDM0LjYyMjkgOC40MzI4MSAzMy4zODk5VjM1LjE3NThDOC40MzI4MSAzNy40ODEzIDEwLjIxOTIgMzcuODE0NSAxMS45NzMxIDM4LjM2NzdDMjEuMTg2MSA0MS4yNzQ1IDM3LjcxMDcgNDEuMDQzNSA0OS4xODI2IDM4LjM2NzdDNTAuMjU2OCAzOC4xMTczIDUxLjUxNTkgMzcuODYxIDUxLjUxNTkgMzYuMzM3N1YzMy4zOTAyQzU2LjI0NzggMzQuNjIyOSA1OS4xMzM0IDM2LjI2NDIgNTkuMTMzNCAzOC4wNjUyQzU5LjEzMzQgNDEuODk1NCA0Ni4wNzg1IDQ1LjAwMDEgMjkuOTc0NCA0NS4wMDAxQzEzLjg2OTcgNDQuOTk5NSAwLjgxNDQ1MyA0MS44OTUxIDAuODE0NDUzIDM4LjA2NDlaIiBmaWxsPSJ1cmwoI3BhaW50MF9saW5lYXJfMzQwM18xMTUpIi8+CjxwYXRoIGZpbGwtcnVsZT0iZXZlbm9kZCIgY2xpcC1ydWxlPSJldmVub2RkIiBkPSJNNDIuODI0IDIzLjkzNTFMMzIuMTIzNyAxOC4zMzEyTDMxLjk1MDIgMTguMjM5NUwyOS4wNTg2IDQwLjQ1MzlDMzQuMjEgNDAuNDc4NCAzOS41NzA5IDQwLjA4OSA0NC40MjYzIDM5LjMyNDJMNDIuODI0IDIzLjkzNTFaIiBmaWxsPSJ1cmwoI3BhaW50MV9saW5lYXJfMzQwM18xMTUpIi8+CjxwYXRoIGZpbGwtcnVsZT0iZXZlbm9kZCIgY2xpcC1ydWxlPSJldmVub2RkIiBkPSJNMzEuOTU1OCAxNy44MzY4TDMxLjkzMjIgMTguMDM1M0wyMy4xNjcgMTMuMzdDMjIuNTIxNSAxMy4wMjkgMjAuMzQ4OSAxMi44NzcyIDIwLjM0ODkgMTUuMTc3NVYxOC4yMTE2TDEyLjAzODkgMTMuODI1NEMxMC45MzkgMTMuMjk0MiA4LjQzMTY0IDEzLjY3MzYgOC40MzE2NCAxNi4xMjYzVjMzLjM5QzEzLjc2NDMgMzIuMDAwOCAyMS40NDA0IDMxLjEyOSAyOS45NzMyIDMxLjEyOUMzMC4wNjg5IDMxLjEyOSAzMC4xNjQxIDMxLjEzMDEgMzAuMjU5OCAzMS4xMzAxTDMxLjkwODggMTguMzU3NUwzMS45NTU4IDE3LjgzNjhaIiBmaWxsPSIjOEE2M0NEIi8+CjxwYXRoIGQ9Ik00OS43MzIgMjcuNDQwNUw0Mi44MDgxIDIzLjc3OTJMNDIuODA0NyAyMy43NDU4TDQyLjgyNDkgMjMuOTM0N0w0My42NTg0IDMxLjkzOTJDNDYuNjE5IDMyLjMxNCA0OS4yNzYyIDMyLjgwNTkgNTEuNTE4NiAzMy4zOTAxVjMwLjI2MTJDNTEuNTE4NiAyOC4yNTI4IDQ5LjkxNjggMjcuNTM4NCA0OS43MzIgMjcuNDQwNVoiIGZpbGw9InVybCgjcGFpbnQyX2xpbmVhcl8zNDAzXzExNSkiLz4KPHBhdGggZD0iTTQyLjgyNDYgMjMuOTM0OEw0Mi44MDQ0IDIzLjc0Nkw0MC4zMzAxIDAuODE4MzM1QzQwLjI5MDggMC40MDI0NzQgMzkuODg4OCAwIDM5LjUxMzcgMEgzNC43OTM4QzM0LjQ0MTEgMCAzNC4wMzIxIDAuMzkzMDc1IDMzLjk4MTMgMC43ODI0NDZMMzEuOTU5MSAxNy44MzY3TDMxLjkxMjEgMTguMzU3N1YxOC4zNTkxTDQyLjgzMDMgMjMuOTg4MUw0Mi44MjQ2IDIzLjkzNDhaIiBmaWxsPSJ1cmwoI3BhaW50M19saW5lYXJfMzQwM18xMTUpIi8+CjxwYXRoIGZpbGwtcnVsZT0iZXZlbm9kZCIgY2xpcC1ydWxlPSJldmVub2RkIiBkPSJNNDMuNjU2MiAzMS45MzY4TDQ0LjQyMjIgMzkuMjkxNUM0Ni4wNzczIDM5LjAyNjEgNDcuNjczOSAzOC43MTY3IDQ5LjE4MjkgMzguMzY0N0M1MC4yNTcxIDM4LjExNDMgNTEuNTE2MiAzNy44NTg1IDUxLjUxNjIgMzYuMzM0NlYzMy4zODc3QzQ5LjI3NCAzMi44MDM1IDQ2LjYxNjggMzIuMzExIDQzLjY1NjIgMzEuOTM2OFoiIGZpbGw9InVybCgjcGFpbnQ0X2xpbmVhcl8zNDAzXzExNSkiLz4KPHBhdGggZD0iTTMwLjI1OTggMzEuMTMxNUMzMC4xNjQxIDMxLjEzMDkgMzAuMDY4OSAzMS4xMzA0IDI5Ljk3MzIgMzEuMTMwNEMyMS40NDA0IDMxLjEzMDQgMTMuNzY0MyAzMi4wMDIzIDguNDMxNjQgMzMuMzkwOFYzNS4xNzdDOC40MzE2NCAzNy40ODI1IDEwLjIxOCAzNy44MTU4IDExLjk3MTkgMzguMzY4OUMxNi40MjM1IDM5Ljc3MzIgMjIuNTgyNyA0MC40NDQ1IDI5LjA1NDIgNDAuNDYyMiIgZmlsbD0idXJsKCNwYWludDVfbGluZWFyXzM0MDNfMTE1KSIvPgo8ZGVmcz4KPGxpbmVhckdyYWRpZW50IGlkPSJwYWludDBfbGluZWFyXzM0MDNfMTE1IiB4MT0iMC44MTQzOTYiIHkxPSIzOS4xOTQ3IiB4Mj0iNTkuMTMzNiIgeTI9IjM5LjE5NDciIGdyYWRpZW50VW5pdHM9InVzZXJTcGFjZU9uVXNlIj4KPHN0b3Agc3RvcC1jb2xvcj0iIzQ4REJENyIvPgo8c3RvcCBvZmZzZXQ9IjEiIHN0b3AtY29sb3I9IiMwMUY0QTAiLz4KPC9saW5lYXJHcmFkaWVudD4KPGxpbmVhckdyYWRpZW50IGlkPSJwYWludDFfbGluZWFyXzM0MDNfMTE1IiB4MT0iMjMuNDg2MyIgeTE9IjI3LjQ5MjgiIHgyPSI0MC45NTQ0IiB5Mj0iMzIuMTA4MyIgZ3JhZGllbnRVbml0cz0idXNlclNwYWNlT25Vc2UiPgo8c3RvcCBzdG9wLWNvbG9yPSIjMkI0MkM5Ii8+CjxzdG9wIG9mZnNldD0iMC4xOTA4IiBzdG9wLWNvbG9yPSIjMjk0N0NBIi8+CjxzdG9wIG9mZnNldD0iMC40MTQxIiBzdG9wLWNvbG9yPSIjMjI1NUNFIi8+CjxzdG9wIG9mZnNldD0iMC42NTM3IiBzdG9wLWNvbG9yPSIjMTc2REQ1Ii8+CjxzdG9wIG9mZnNldD0iMC45MDMxIiBzdG9wLWNvbG9yPSIjMDc4RERFIi8+CjxzdG9wIG9mZnNldD0iMSIgc3RvcC1jb2xvcj0iIzAwOUNFMiIvPgo8L2xpbmVhckdyYWRpZW50Pgo8bGluZWFyR3JhZGllbnQgaWQ9InBhaW50Ml9saW5lYXJfMzQwM18xMTUiIHgxPSIzMS4xMTA3IiB5MT0iMjguNTY3OCIgeDI9IjUxLjgyNzIiIHkyPSIyOC41Njc4IiBncmFkaWVudFVuaXRzPSJ1c2VyU3BhY2VPblVzZSI+CjxzdG9wIHN0b3AtY29sb3I9IiM4QzYzQ0YiLz4KPHN0b3Agb2Zmc2V0PSIxIiBzdG9wLWNvbG9yPSIjMzg0N0QzIi8+CjwvbGluZWFyR3JhZGllbnQ+CjxsaW5lYXJHcmFkaWVudCBpZD0icGFpbnQzX2xpbmVhcl8zNDAzXzExNSIgeDE9IjMwLjY1MjUiIHkxPSIxMS45OTQiIHgyPSI0OS4zNDc1IiB5Mj0iMTEuOTk0IiBncmFkaWVudFVuaXRzPSJ1c2VyU3BhY2VPblVzZSI+CjxzdG9wIG9mZnNldD0iMC4wNDU2IiBzdG9wLWNvbG9yPSIjOEM2M0NGIi8+CjxzdG9wIG9mZnNldD0iMSIgc3RvcC1jb2xvcj0iIzM4NDdEMyIvPgo8L2xpbmVhckdyYWRpZW50Pgo8bGluZWFyR3JhZGllbnQgaWQ9InBhaW50NF9saW5lYXJfMzQwM18xMTUiIHgxPSI0My42NTY0IiB5MT0iMzUuNjE0MSIgeDI9IjUxLjUxNjUiIHkyPSIzNS42MTQxIiBncmFkaWVudFVuaXRzPSJ1c2VyU3BhY2VPblVzZSI+CjxzdG9wIHN0b3AtY29sb3I9IiMwNTlBRTUiLz4KPHN0b3Agb2Zmc2V0PSIxIiBzdG9wLWNvbG9yPSIjMDFBQ0U3Ii8+CjwvbGluZWFyR3JhZGllbnQ+CjxsaW5lYXJHcmFkaWVudCBpZD0icGFpbnQ1X2xpbmVhcl8zNDAzXzExNSIgeDE9IjguNDMxNjQiIHkxPSIzNS43OTY0IiB4Mj0iMzAuMjU5NyIgeTI9IjM1Ljc5NjQiIGdyYWRpZW50VW5pdHM9InVzZXJTcGFjZU9uVXNlIj4KPHN0b3Agc3RvcC1jb2xvcj0iIzFDMzdEMSIvPgo8c3RvcCBvZmZzZXQ9IjEiIHN0b3AtY29sb3I9IiMwRTcwREQiLz4KPC9saW5lYXJHcmFkaWVudD4KPC9kZWZzPgo8L3N2Zz4K',
				20
			);

			// Remove the default submenu
			//remove_submenu_page($this->admin_page_slug, 'wpcodefactory');
			add_action( 'admin_head', function () {
				error_log( 'admin head' );
				remove_submenu_page( $this->admin_page_slug, $this->admin_page_slug );  // 'parent-slug', 'subpage-slug'
			} );

			\add_submenu_page(
				$this->admin_page_slug,
				$page_title,
				$menu_title,
				$capability,
				$this->submenu_page_slug,
				array( $this, 'render_admin_page' ),
				//'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNDUiIHZpZXdCb3g9IjAgMCA2MCA0NSIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHBhdGggZD0iTTAuODE0NDUzIDM4LjA2NDlDMC44MTQ0NTMgMzYuMjYzOSAzLjcwMDY4IDM0LjYyMjkgOC40MzI4MSAzMy4zODk5VjM1LjE3NThDOC40MzI4MSAzNy40ODEzIDEwLjIxOTIgMzcuODE0NSAxMS45NzMxIDM4LjM2NzdDMjEuMTg2MSA0MS4yNzQ1IDM3LjcxMDcgNDEuMDQzNSA0OS4xODI2IDM4LjM2NzdDNTAuMjU2OCAzOC4xMTczIDUxLjUxNTkgMzcuODYxIDUxLjUxNTkgMzYuMzM3N1YzMy4zOTAyQzU2LjI0NzggMzQuNjIyOSA1OS4xMzM0IDM2LjI2NDIgNTkuMTMzNCAzOC4wNjUyQzU5LjEzMzQgNDEuODk1NCA0Ni4wNzg1IDQ1LjAwMDEgMjkuOTc0NCA0NS4wMDAxQzEzLjg2OTcgNDQuOTk5NSAwLjgxNDQ1MyA0MS44OTUxIDAuODE0NDUzIDM4LjA2NDlaIiBmaWxsPSJ1cmwoI3BhaW50MF9saW5lYXJfMzQwM18xMTUpIi8+CjxwYXRoIGZpbGwtcnVsZT0iZXZlbm9kZCIgY2xpcC1ydWxlPSJldmVub2RkIiBkPSJNNDIuODI0IDIzLjkzNTFMMzIuMTIzNyAxOC4zMzEyTDMxLjk1MDIgMTguMjM5NUwyOS4wNTg2IDQwLjQ1MzlDMzQuMjEgNDAuNDc4NCAzOS41NzA5IDQwLjA4OSA0NC40MjYzIDM5LjMyNDJMNDIuODI0IDIzLjkzNTFaIiBmaWxsPSJ1cmwoI3BhaW50MV9saW5lYXJfMzQwM18xMTUpIi8+CjxwYXRoIGZpbGwtcnVsZT0iZXZlbm9kZCIgY2xpcC1ydWxlPSJldmVub2RkIiBkPSJNMzEuOTU1OCAxNy44MzY4TDMxLjkzMjIgMTguMDM1M0wyMy4xNjcgMTMuMzdDMjIuNTIxNSAxMy4wMjkgMjAuMzQ4OSAxMi44NzcyIDIwLjM0ODkgMTUuMTc3NVYxOC4yMTE2TDEyLjAzODkgMTMuODI1NEMxMC45MzkgMTMuMjk0MiA4LjQzMTY0IDEzLjY3MzYgOC40MzE2NCAxNi4xMjYzVjMzLjM5QzEzLjc2NDMgMzIuMDAwOCAyMS40NDA0IDMxLjEyOSAyOS45NzMyIDMxLjEyOUMzMC4wNjg5IDMxLjEyOSAzMC4xNjQxIDMxLjEzMDEgMzAuMjU5OCAzMS4xMzAxTDMxLjkwODggMTguMzU3NUwzMS45NTU4IDE3LjgzNjhaIiBmaWxsPSIjOEE2M0NEIi8+CjxwYXRoIGQ9Ik00OS43MzIgMjcuNDQwNUw0Mi44MDgxIDIzLjc3OTJMNDIuODA0NyAyMy43NDU4TDQyLjgyNDkgMjMuOTM0N0w0My42NTg0IDMxLjkzOTJDNDYuNjE5IDMyLjMxNCA0OS4yNzYyIDMyLjgwNTkgNTEuNTE4NiAzMy4zOTAxVjMwLjI2MTJDNTEuNTE4NiAyOC4yNTI4IDQ5LjkxNjggMjcuNTM4NCA0OS43MzIgMjcuNDQwNVoiIGZpbGw9InVybCgjcGFpbnQyX2xpbmVhcl8zNDAzXzExNSkiLz4KPHBhdGggZD0iTTQyLjgyNDYgMjMuOTM0OEw0Mi44MDQ0IDIzLjc0Nkw0MC4zMzAxIDAuODE4MzM1QzQwLjI5MDggMC40MDI0NzQgMzkuODg4OCAwIDM5LjUxMzcgMEgzNC43OTM4QzM0LjQ0MTEgMCAzNC4wMzIxIDAuMzkzMDc1IDMzLjk4MTMgMC43ODI0NDZMMzEuOTU5MSAxNy44MzY3TDMxLjkxMjEgMTguMzU3N1YxOC4zNTkxTDQyLjgzMDMgMjMuOTg4MUw0Mi44MjQ2IDIzLjkzNDhaIiBmaWxsPSJ1cmwoI3BhaW50M19saW5lYXJfMzQwM18xMTUpIi8+CjxwYXRoIGZpbGwtcnVsZT0iZXZlbm9kZCIgY2xpcC1ydWxlPSJldmVub2RkIiBkPSJNNDMuNjU2MiAzMS45MzY4TDQ0LjQyMjIgMzkuMjkxNUM0Ni4wNzczIDM5LjAyNjEgNDcuNjczOSAzOC43MTY3IDQ5LjE4MjkgMzguMzY0N0M1MC4yNTcxIDM4LjExNDMgNTEuNTE2MiAzNy44NTg1IDUxLjUxNjIgMzYuMzM0NlYzMy4zODc3QzQ5LjI3NCAzMi44MDM1IDQ2LjYxNjggMzIuMzExIDQzLjY1NjIgMzEuOTM2OFoiIGZpbGw9InVybCgjcGFpbnQ0X2xpbmVhcl8zNDAzXzExNSkiLz4KPHBhdGggZD0iTTMwLjI1OTggMzEuMTMxNUMzMC4xNjQxIDMxLjEzMDkgMzAuMDY4OSAzMS4xMzA0IDI5Ljk3MzIgMzEuMTMwNEMyMS40NDA0IDMxLjEzMDQgMTMuNzY0MyAzMi4wMDIzIDguNDMxNjQgMzMuMzkwOFYzNS4xNzdDOC40MzE2NCAzNy40ODI1IDEwLjIxOCAzNy44MTU4IDExLjk3MTkgMzguMzY4OUMxNi40MjM1IDM5Ljc3MzIgMjIuNTgyNyA0MC40NDQ1IDI5LjA1NDIgNDAuNDYyMiIgZmlsbD0idXJsKCNwYWludDVfbGluZWFyXzM0MDNfMTE1KSIvPgo8ZGVmcz4KPGxpbmVhckdyYWRpZW50IGlkPSJwYWludDBfbGluZWFyXzM0MDNfMTE1IiB4MT0iMC44MTQzOTYiIHkxPSIzOS4xOTQ3IiB4Mj0iNTkuMTMzNiIgeTI9IjM5LjE5NDciIGdyYWRpZW50VW5pdHM9InVzZXJTcGFjZU9uVXNlIj4KPHN0b3Agc3RvcC1jb2xvcj0iIzQ4REJENyIvPgo8c3RvcCBvZmZzZXQ9IjEiIHN0b3AtY29sb3I9IiMwMUY0QTAiLz4KPC9saW5lYXJHcmFkaWVudD4KPGxpbmVhckdyYWRpZW50IGlkPSJwYWludDFfbGluZWFyXzM0MDNfMTE1IiB4MT0iMjMuNDg2MyIgeTE9IjI3LjQ5MjgiIHgyPSI0MC45NTQ0IiB5Mj0iMzIuMTA4MyIgZ3JhZGllbnRVbml0cz0idXNlclNwYWNlT25Vc2UiPgo8c3RvcCBzdG9wLWNvbG9yPSIjMkI0MkM5Ii8+CjxzdG9wIG9mZnNldD0iMC4xOTA4IiBzdG9wLWNvbG9yPSIjMjk0N0NBIi8+CjxzdG9wIG9mZnNldD0iMC40MTQxIiBzdG9wLWNvbG9yPSIjMjI1NUNFIi8+CjxzdG9wIG9mZnNldD0iMC42NTM3IiBzdG9wLWNvbG9yPSIjMTc2REQ1Ii8+CjxzdG9wIG9mZnNldD0iMC45MDMxIiBzdG9wLWNvbG9yPSIjMDc4RERFIi8+CjxzdG9wIG9mZnNldD0iMSIgc3RvcC1jb2xvcj0iIzAwOUNFMiIvPgo8L2xpbmVhckdyYWRpZW50Pgo8bGluZWFyR3JhZGllbnQgaWQ9InBhaW50Ml9saW5lYXJfMzQwM18xMTUiIHgxPSIzMS4xMTA3IiB5MT0iMjguNTY3OCIgeDI9IjUxLjgyNzIiIHkyPSIyOC41Njc4IiBncmFkaWVudFVuaXRzPSJ1c2VyU3BhY2VPblVzZSI+CjxzdG9wIHN0b3AtY29sb3I9IiM4QzYzQ0YiLz4KPHN0b3Agb2Zmc2V0PSIxIiBzdG9wLWNvbG9yPSIjMzg0N0QzIi8+CjwvbGluZWFyR3JhZGllbnQ+CjxsaW5lYXJHcmFkaWVudCBpZD0icGFpbnQzX2xpbmVhcl8zNDAzXzExNSIgeDE9IjMwLjY1MjUiIHkxPSIxMS45OTQiIHgyPSI0OS4zNDc1IiB5Mj0iMTEuOTk0IiBncmFkaWVudFVuaXRzPSJ1c2VyU3BhY2VPblVzZSI+CjxzdG9wIG9mZnNldD0iMC4wNDU2IiBzdG9wLWNvbG9yPSIjOEM2M0NGIi8+CjxzdG9wIG9mZnNldD0iMSIgc3RvcC1jb2xvcj0iIzM4NDdEMyIvPgo8L2xpbmVhckdyYWRpZW50Pgo8bGluZWFyR3JhZGllbnQgaWQ9InBhaW50NF9saW5lYXJfMzQwM18xMTUiIHgxPSI0My42NTY0IiB5MT0iMzUuNjE0MSIgeDI9IjUxLjUxNjUiIHkyPSIzNS42MTQxIiBncmFkaWVudFVuaXRzPSJ1c2VyU3BhY2VPblVzZSI+CjxzdG9wIHN0b3AtY29sb3I9IiMwNTlBRTUiLz4KPHN0b3Agb2Zmc2V0PSIxIiBzdG9wLWNvbG9yPSIjMDFBQ0U3Ii8+CjwvbGluZWFyR3JhZGllbnQ+CjxsaW5lYXJHcmFkaWVudCBpZD0icGFpbnQ1X2xpbmVhcl8zNDAzXzExNSIgeDE9IjguNDMxNjQiIHkxPSIzNS43OTY0IiB4Mj0iMzAuMjU5NyIgeTI9IjM1Ljc5NjQiIGdyYWRpZW50VW5pdHM9InVzZXJTcGFjZU9uVXNlIj4KPHN0b3Agc3RvcC1jb2xvcj0iIzFDMzdEMSIvPgo8c3RvcCBvZmZzZXQ9IjEiIHN0b3AtY29sb3I9IiMwRTcwREQiLz4KPC9saW5lYXJHcmFkaWVudD4KPC9kZWZzPgo8L3N2Zz4K',
				20
			);
			self::$admin_page_created = true;
		}

		function render_admin_page() {
			echo '<h1>TEST</h1>';
		}

		/**
		 * Adds action links.
		 *
		 * @param $links
		 *
		 * @return array
		 */
		function add_action_links( $links ) {
			$setup_args     = $this->get_setup_args();
			$action_link    = $setup_args['plugin_action_link'] ?? '';
			$label          = $action_link['label'] ?? '';
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