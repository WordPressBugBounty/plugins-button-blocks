<?php
/**
 * Plugin Name: Button Blocks
 * Description: Enhance your site with dynamic, customizable, and multi-functional buttons to create a more engaging and functional experience.
 * Author: 		Noruzzaman
 * Plugin URI: 	https://wordpress.org/plugins/button-blocks
 * Author URI: 	https://github.com/noruzzamans/
 * Version: 	1.0.2
* Text Domain: 	button-blocks
 * Domain Path: /languages
 * License: 	GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 *
 * @package ButtonBlocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'NR_ButtonBlocks' ) ) {

	/**
	 * NR_ButtonBlocks Final Class
	 *
	 * @since 1.0.0
	 * @package ButtonBlocks
	 */
	final class NR_ButtonBlocks {

		/**
		 * NR_ButtonBlocks Instance
		 *
		 * @since 1.0.0
		 */
		private static $instance;

		/**
		 * NR_ButtonBlocks Constructor
		 *
		 * @since 1.0.0
		 * @return void
		 */
		private function __construct() {
			$this->define_constants();
			$this->init();
			$this->includes();
		}

		/**
		 * NR_ButtonBlocks Define Constants
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function define_constants() {
			if ( ! defined( 'NR_BUTTON_BLOCKS_VERSION' ) ) {
				define( 'NR_BUTTON_BLOCKS_VERSION', '1.0.2' );
			}

			if ( ! defined( 'NR_BUTTON_BLOCKS__FILE__' ) ) {
				define( 'NR_BUTTON_BLOCKS__FILE__', __FILE__ );
			}

			if ( ! defined( 'NR_BUTTON_BLOCKS_URL_FILE' ) ) {
				define( 'NR_BUTTON_BLOCKS_URL_FILE', plugin_dir_url( NR_BUTTON_BLOCKS__FILE__ ) );
			}

			if ( ! defined( 'NR_BUTTON_BLOCKS_PLUGIN_DIR' ) ) {
				define( 'NR_BUTTON_BLOCKS_PLUGIN_DIR', plugin_dir_path( NR_BUTTON_BLOCKS__FILE__ ) );
			}

			if ( ! defined( 'NR_BUTTON_BLOCKS_URL' ) ) {
				define( 'NR_BUTTON_BLOCKS_URL', plugins_url( '/', NR_BUTTON_BLOCKS_PLUGIN_DIR ) );
			}
		}

		/**
		 * NR_ButtonBlocks Init
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function init() {
			add_action( 'init', array( $this, 'load_textdomain' ) );
		}

		/**
		 * NR_ButtonBlocks Load Text Domain
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function load_textdomain() {
			load_plugin_textdomain( 'button-blocks', false, basename( NR_BUTTON_BLOCKS_PLUGIN_DIR ) . '/languages' );
		}

		/**
		 * NR_ButtonBlocks Instance
		 *
		 * @since 1.0.0
		 * @return NR_ButtonBlocks
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * NR_ButtonBlocks Includes Files
		 *
		 * @since 1.0.0
		 * @return void
		 */
		private function includes() {
			require_once trailingslashit( NR_BUTTON_BLOCKS_PLUGIN_DIR ) . 'inc/button-blocks-loader.php';
		}
	}

}

/**
 * NR_ButtonBlocks
 *
 * @since 1.0.0
 * @return NR_ButtonBlocks
 */
function nr_button_blocks() {
	return NR_ButtonBlocks::get_instance();
}
nr_button_blocks(); // Initialize the NR_ButtonBlocks class
