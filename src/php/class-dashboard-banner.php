<?php
/**
 * WPFactory Cross-Selling - Dashboard Banner
 *
 * @version 1.0.7
 * @since   1.0.7
 * @author  WPFactory
 */

namespace WPFactory\WPFactory_Cross_Selling;

use WPFactory\WPFactory_Admin_Menu\WPFactory_Admin_Menu;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'WPFactory\WPFactory_Cross_Selling\Dashboard_Banner' ) ) {

	/**
	 * WPF_Cross_Selling.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	class Dashboard_Banner {

		/**
		 * WPFactory_Cross_Selling_Injector.
		 *
		 * @since 1.0.7
		 */
		use WPFactory_Cross_Selling_Injector;

		/**
		 * Initializes the class.
		 *
		 * @version 1.0.7
		 * @since   1.0.7
		 *
		 * @return void
		 */
		function init() {
			$setup_args = $this->get_wpfactory_cross_selling()->get_setup_args();

			if ( ! $setup_args['dashboard_banner']['enable'] ) {
				remove_action( 'admin_notices', array( $this, 'display_dashboard_banner' ) );
				return;
			}

			remove_action( 'admin_notices', array( $this, 'display_dashboard_banner' ) );
			add_action( 'admin_notices', array( $this, 'display_dashboard_banner' ) );
		}

		/**
		 * get_advanced_ads_group.
		 *
		 * @version 1.0.7
		 * @since   1.0.7
		 *
		 * @return mixed|void|null
		 */
		function get_advanced_ads_group() {
			$setup_args = $this->get_wpfactory_cross_selling()->get_setup_args();
			$response   = wp_remote_get( 'https://wpfactory.com/wp-json/advanced-ads/v1/groups' );
			if ( ! is_wp_error( $response ) ) {
				$body   = wp_remote_retrieve_body( $response );
				$data   = json_decode( $body, true );
				$result = wp_list_filter( $data, [
					'name' => $setup_args['dashboard_banner']['advanced_ads_setup']['group_name']
				] );
				if ( empty( $result ) ) {
					return null;
				}

				return array_shift( $result );
			}
		}

		/**
		 * get_banner_content.
		 *
		 * @version 1.0.7
		 * @since   1.0.7
		 *
		 * @return mixed|void
		 */
		function get_banner_content() {
			$setup_args = $this->get_wpfactory_cross_selling()->get_setup_args();
			if ( 'advanced_ads' === $setup_args['dashboard_banner']['method'] ) {
				$group = $this->get_advanced_ads_group();
				if ( ! is_null( $group ) && isset( $group['ads'] ) && ! empty( $ads = $group['ads'] ) && is_array( $ads ) ) {
					foreach ( $ads as $ad_id ) {
						$response = wp_remote_get( 'https://wpfactory.com/wp-json/advanced-ads/v1/ads/' . $ad_id );
						if ( ! is_wp_error( $response ) ) {
							$body = wp_remote_retrieve_body( $response );
							$data = json_decode( $body, true );
							if ( isset( $data['expiration_date'] ) && ! empty( $expiration_date = $data['expiration_date'] ) ) {
								if ( $expiration_date < current_time( 'timestamp' ) ) {
									continue;
								}
							}
							if ( isset( $data['content'] ) && ! empty( $data['content'] ) ) {
								return $data['content'];
								break;
							}
						}
					}
				}
			}

			return null;
		}

		/**
		 * display_dashboard_banner.
		 *
		 * @version 1.0.7
		 * @since   1.0.7
		 *
		 * @return void
		 */
		function display_dashboard_banner() {
			$banner = $this->get_banner_content();
			if ( ! is_null( $banner ) ) {
				$banner = '<div class="wpfcs-dashboard-banner"><div>' . $banner . '</div></div>';
				echo wp_kses_post( $banner );
				echo $this->get_style();
			}
		}

		/**
		 * get_style.
		 *
		 * @version 1.0.7
		 * @since   1.0.7
		 *
		 * @return false|string
		 */
		function get_style(){
			ob_start();
			?>
			<style>
				.wpfcs-dashboard-banner{
					width:100%;
					display: inline-block;
				}
				.wpfcs-dashboard-banner > div{
					text-align: center;
					margin: 25px;
				}
                .wpfcs-dashboard-banner img {
                    max-width:100%;
                    height:auto
                }
			</style>
			<?php
			return ob_get_clean();
		}

	}
}