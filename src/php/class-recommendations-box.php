<?php
/**
 * WPFactory Cross-Selling - Recommendations Box.
 *
 * @version 1.0.0
 * @since   1.0.0
 * @author  WPFactory
 */

namespace WPFactory\WPFactory_Cross_Selling;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'WPFactory\WPFactory_Cross_Selling\Recommendations_Box' ) ) {

	/**
	 * WPF_Cross_Selling.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	class Recommendations_Box {

		/**
		 * WPFactory_Cross_Selling_Injector.
		 *
		 * @since 1.0.4
		 */
		use WPFactory_Cross_Selling_Injector;

		/**
		 * Initializes the class.
		 *
		 * @version 1.0.4
		 * @since   1.0.4
		 *
		 * @return void
		 */
		function init() {
			$setup_args = $this->get_wpfactory_cross_selling()->get_setup_args();

			// Enqueue admin css.
			add_filter( 'wpfcs_enqueue_admin_css', array( $this, 'enqueue_wcfcs_css_on_recommendations_box' ) );

			// Wrap WC settings.
			add_action( 'woocommerce_settings_' . $setup_args['wc_settings_tab_id'], array( $this, 'wrap_wc_settings_start' ), 9 );
			add_action( 'woocommerce_settings_' . $setup_args['wc_settings_tab_id'], array( $this, 'wrap_wc_settings_end' ), 11 );

			// Render Recommendations box.
			add_action( 'woocommerce_settings_' . $setup_args['wc_settings_tab_id'], array( $this, 'render_recommendations_box' ), 15 );
		}

		/**
		 * wrap_wc_settings_end.
		 *
		 * @version 1.0.4
		 * @since   1.0.4
		 *
		 * @return void
		 */
		function wrap_wc_settings_end() {
			$setup_args = $this->get_wpfactory_cross_selling()->get_setup_args();
			if ( ! $setup_args['recommendations_box']['enable'] ) {
				return;
			}
			echo '</div>';
		}

		/**
		 * wrap_wc_settings_start.
		 *
		 * @version 1.0.4
		 * @since   1.0.4
		 *
		 * @return void
		 */
		function wrap_wc_settings_start() {
			$setup_args = $this->get_wpfactory_cross_selling()->get_setup_args();
			if ( ! $setup_args['recommendations_box']['enable'] ) {
				return;
			}
			echo '<div class="wpfcs-wc-settings-wrapper">';
		}

		/**
		 * render_recommendations_box.
		 *
		 * @version 1.0.4
		 * @since   1.0.4
		 *
		 * @return void
		 */
		function render_recommendations_box() {
			$setup_args = $this->get_wpfactory_cross_selling()->get_setup_args();
			if ( ! $setup_args['recommendations_box']['enable'] ) {
				return;
			}

			// Products.
			$products = $this->get_wpfactory_cross_selling()->products->get_products();

			// Tags.
			$box_tags_class = new Recommendation_Box_Tags();
			$box_tags       = $box_tags_class->get_tags();

			?>
			<div class="wpfcs-recommendations-box">
				<h3 class="wpfcs-recommendation-box-title">
					Recommended Plugins
				</h3>
				<div class="tab-links">
					<?php $i = 0; ?>
					<?php foreach ( $box_tags as $tag ): ?>
						<a href="#wpfcs-recommendation-box-tab-<?php echo esc_attr( $i ) ?>"><?php echo esc_html( $tag['name'] ) ?></a>
						<?php $i ++; ?>
					<?php endforeach; ?>
				</div>
				<div id="wpfcs-recommendation-box-tab-0" class="tab-content">
					Content for Tab 1
				</div>
				<div id="wpfcs-recommendation-box-tab-1" class="tab-content">
					Content for Tab 2
				</div>
			</div>
			<?php
		}

		/**
		 * enqueue_wcfcs_css_on_recommendations_box.
		 *
		 * @version 1.0.4
		 * @since   1.0.4
		 *
		 * @param $enqueue
		 *
		 * @return mixed|true
		 */
		function enqueue_wcfcs_css_on_recommendations_box( $enqueue ) {
			$setup_args = $this->get_wpfactory_cross_selling()->get_setup_args();

			if (
				$setup_args['recommendations_box']['enable'] &&
				in_array( 'wc_settings_tab', $setup_args['recommendations_box']['position'] ) &&
				isset( $_GET['page'] ) &&
				'wc-settings' === $_GET['page'] &&
				isset( $_GET['tab'] ) &&
				! empty( $setup_args['wc_settings_tab_id'] ) &&
				$setup_args['wc_settings_tab_id'] === $_GET['tab']
			) {
				$enqueue = true;
			}

			return $enqueue;
		}
	}
}