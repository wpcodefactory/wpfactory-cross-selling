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
		 * $banner_content_transient_name.
		 *
		 * @since 1.0.7
		 *
		 * @var string
		 */
		protected $banner_content_transient_name = 'wpfcs_dashboard_banner_content';

		/**
		 * Initialized.
		 *
		 * @since   1.0.7
		 *
		 * @var bool
		 */
		protected static $initialized = false;

		/**
		 * $get_banner_ajax_action.
		 *
		 * @since   1.0.7
		 *
		 * @var string
		 */
		protected $get_banner_ajax_action = 'wpfcs_get_dashboard_banner';

		/**
		 * $close_banner_ajax_action.
		 *
		 * @since   1.0.7
		 *
		 * @var string
		 */
		protected $close_banner_ajax_action = 'wpfcs_close_dashboard_banner';

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

			if (
				$setup_args['dashboard_banner']['enable'] &&
				! self::$initialized
			) {
				self::$initialized = true;
				add_action( 'admin_notices', array( $this, 'display_dashboard_banner_wrapper' ) );
				add_action( 'wp_ajax_'.$this->get_banner_ajax_action, array( $this, 'get_banner_ajax_action' ) );
				add_action( 'wp_ajax_'.$this->close_banner_ajax_action, array( $this, 'close_banner_ajax_action' ) );
			}

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

			return null;
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
				if ( false !== $setup_args['dashboard_banner']['banner_cache_duration'] && $cached_content = get_transient( $this->banner_content_transient_name ) ) {
					return $cached_content;
				}
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
								if ( false !== $setup_args['dashboard_banner']['banner_cache_duration'] ) {
									set_transient( $this->banner_content_transient_name, $data['content'], $setup_args['dashboard_banner']['banner_cache_duration'] );
								}

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
		function display_dashboard_banner_wrapper() {
			if (
				! $this->can_display_banner_at_current_location() ||
				$this->banner_should_remain_closed()
			) {
				return;
			}

			$banner = '<div class="wpfcs-dashboard-banner-wrapper"></div>';
			echo wp_kses_post( $banner );
			echo $this->get_banner_style();
			echo $this->get_banner_js();
		}

		/**
		 * get_banner_ajax_action.
		 *
		 * @version 1.0.7
		 * @since   1.0.7
		 *
		 * @return void
		 */
		function get_banner_ajax_action() {
			if ( false !== check_ajax_referer( 'wpfcs-get-dashboard-banner', 'banner_nonce' ) ) {
				$banner = $this->banner_should_remain_closed() ? '' : $this->get_banner_content();
				$output = empty( $banner ) ? '' : '<div class="wpfcs-dashboard-banner"><div class="wpfcs-dashboard-banner-inner">' . $banner . '<button type="button" aria-label="' . __( 'Close', 'wpfactory-cross-selling' ) . '" class="wpfcs-dashboard-banner-close-btn"><div class="dashicons-before dashicons-no"></div></button></div></div>';
				wp_send_json_success( array(
					'banner_data' => wp_kses_post( $output ),
				) );
			}
		}

		/**
		 * banner_should_remain_closed.
		 *
		 * @version 1.0.7
		 * @since   1.0.7
		 *
		 * @return bool
		 */
		function banner_should_remain_closed(){
			$user_id = get_current_user_id();
			$setup_args = $this->get_wpfactory_cross_selling()->get_setup_args();

			if ( ! $user_id ) {
				return false;
			}

			$closed_at = (int) get_user_meta( $user_id, 'wpfcs_dashboard_banner_closed_time', true );

			if ( ! $closed_at ) {
				return false;
			}

			return ( time() - $closed_at ) < $setup_args['dashboard_banner']['banner_dismiss_duration'];
		}

		/**
		 * close_banner_ajax_action.
		 *
		 * @version 1.0.7
		 * @since   1.0.7
		 *
		 * @return void
		 */
		function close_banner_ajax_action() {
			if ( false !== check_ajax_referer( 'wpfcs-get-dashboard-banner', 'banner_nonce' ) ) {
				update_user_meta( get_current_user_id(), 'wpfcs_dashboard_banner_closed_time', time() );
				wp_send_json_success();
			}
		}

		/**
		 * can_display_banner_at_this_location.
		 *
		 * @version 1.0.7
		 * @since   1.0.7
		 *
		 * @return false|void
		 */
		function can_display_banner_at_current_location() {
			/*global $pagenow;

			if ( $pagenow == 'post.php' || $pagenow == 'profile.php' ) {
				return false;
			}*/
			return true;
		}

		/**
		 * get_banner_style.
		 *
		 * @version 1.0.7
		 * @since   1.0.7
		 *
		 * @return false|string
		 */
		function get_banner_style() {
			ob_start();
			$close_btn_right_or_left = true === is_rtl() ? 'left' : 'right';
			?>
			<style>
				.wpfcs-dashboard-banner {
					width: 100%;
					display: inline-block;
					margin: 25px 0;
					text-align: center;
				}

				.wpfcs-dashboard-banner-inner {
					display: inline-block;
					position: relative;
				}

				.wpfcs-dashboard-banner img {
					max-width: 100%;
					height: auto
				}

				.wpfcs-dashboard-banner-close-btn {
					position:absolute;
				<?php echo esc_attr($close_btn_right_or_left);?>: -12px;
					top:-12px;
					background: #2d2d2d;
					border-radius:27px;
					cursor: pointer;
					border:none;
				}

				.wpfcs-dashboard-banner-close-btn:hover {
					background:#2271b1;
				}

				.wpfcs-dashboard-banner-close-btn .dashicons-before{
					display:block;
					position:relative;
					left:-6px;
					top:-1px;
				}

				.wpfcs-dashboard-banner-close-btn .dashicons-before::before{
					font-size:25px;
					margin:1px 0 0 0.5px;
					color:#f0f0f1;
				}

				.wpfcs-dashboard-banner-close-btn,.wpfcs-dashboard-banner-close-btn .dashicons-before::before {
					width:27px;
					height:27px;
				}
			</style>
			<?php
			return ob_get_clean();
		}

		/**
		 * get_banner_js.
		 *
		 * @version 1.0.7
		 * @since   1.0.7
		 *
		 * @return false|string
		 */
		function get_banner_js() {
			ob_start();
			$php_to_js = array(
				'banner_nonce'            => wp_create_nonce( 'wpfcs-get-dashboard-banner' ),
				'get_banner_action'       => $this->get_banner_ajax_action,
				'close_banner_action'     => $this->close_banner_ajax_action,
				'banner_wrapper_selector' => '.wpfcs-dashboard-banner-wrapper',
				'close_button_selector'   => '.wpfcs-dashboard-banner-close-btn',
			);
			?>
			<script>
				// Gets banner.
				jQuery( function ( $ ) {
					let dataFromPHP = <?php echo wp_json_encode( $php_to_js );?>;
					dataFromPHP.action = dataFromPHP.get_banner_action;
					$.post( ajaxurl, dataFromPHP ).done( res => {
						if ( res.data.banner_data?.trim() ) {
							jQuery( dataFromPHP.banner_wrapper_selector ).html( res.data.banner_data )
						}
					} );
				} );

				// Closes banner.
				jQuery( function ( $ ) {
					let dataFromPHP = <?php echo wp_json_encode( $php_to_js );?>;
					dataFromPHP.action = dataFromPHP.close_banner_action;
					$( document ).on( 'click', dataFromPHP.close_button_selector, function () {
						$(dataFromPHP.banner_wrapper_selector).fadeOut(300, function () {
							$(this).remove();
						});
						$.post( ajaxurl, dataFromPHP );
					} );
				} );
			</script>
			<?php
			return ob_get_clean();
		}

	}
}