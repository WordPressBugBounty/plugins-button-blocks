<?php
/**
 * NR Blocks Main Loader
 *
 * @since 1.0.0
 * @package NRBlocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'NR_Blocks_Loader' ) ) {

	/**
	 * NR Blocks Loader Class
	 *
	 * @since 1.0.0
	 * @package NRBlocks
	 */
	class NR_Blocks_Loader {

		/**
		 * NR_Blocks_Loader Instance
		 *
		 * @since 1.0.0
		 * @var NR_Blocks_Loader
		 */
		private static $instance = null;

		/**
		 * Constructor
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function __construct() {
			$this->includes(); 
		}

		/**
		 * Include Files
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function includes() {
			require_once trailingslashit( NR_BUTTON_BLOCKS_PLUGIN_DIR ) . '/inc/classes/register-blocks.php';
			require_once trailingslashit( NR_BUTTON_BLOCKS_PLUGIN_DIR ) . '/inc/classes/register-category.php';
			require_once trailingslashit( NR_BUTTON_BLOCKS_PLUGIN_DIR ) . '/inc/classes/dynamic-style.php';
			require_once trailingslashit( NR_BUTTON_BLOCKS_PLUGIN_DIR ) . '/inc/classes/FontLoader.php';
		}

		/**
		 * NR_Blocks_Loader Instance
		 *
		 * @since 1.0.0
		 * @return NR_Blocks_Loader
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}
	}

}

// Initialize the loader class
NR_Blocks_Loader::get_instance();
