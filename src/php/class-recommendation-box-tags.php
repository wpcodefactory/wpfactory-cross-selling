<?php
/**
 * WPFactory Cross-Selling - Recommendation Box tags.
 *
 * @version 1.0.0
 * @since   1.0.0
 * @author  WPFactory
 */

namespace WPFactory\WPFactory_Cross_Selling;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'WPFactory\WPFactory_Cross_Selling\Recommendation_Box_Tags' ) ) {

	/**
	 * WPF_Cross_Selling.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	class Recommendation_Box_Tags {

		/**
		 * Tags.
		 *
		 * @since   1.0.0
		 *
		 * @var array
		 */
		protected $tags = array();

		/**
		 * get_product_categories.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return array|array[]
		 */
		function get_tags() {
			$this->tags = array(
				array(
					'name' => 'Top picks',
					'slug' => 'top-picks',
				),
				array(
					'name' => 'Must-have',
					'slug' => 'must-have',
				),
				array(
					'name' => 'Conversation Boosters',
					'slug' => 'conversation-boosters',
				),
				array(
					'name' => 'Marketing',
					'slug' => 'marketing',
				),
				array(
					'name' => 'Utilities',
					'slug' => 'utilities',
				),
			);

			return $this->tags;
		}
	}
}