<?php
/**
 * NR Blocks Registration
 *
 * @since 1.0.0
 * @package NRBlocks
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('NR_Blocks_Registration')) {
    class NR_Blocks_Registration {
        private const BLOCKS = [
            'button',
        ];

        /**
         * @var null|self
         */
        private static $instance = null;

        public function __construct() {
            $this->init();
        }

        private function init() {
            add_action('init', [$this, 'register_blocks']);
        }

        public function register_blocks() {
            if (!defined('NR_BUTTON_BLOCKS_PLUGIN_DIR')) {
                error_log('NR Blocks: Plugin directory constant not defined.');
                return;
            }

            $build_dir = trailingslashit(NR_BUTTON_BLOCKS_PLUGIN_DIR) . 'build/blocks/';
            
            if (!is_readable($build_dir)) {
                error_log('NR Blocks: Main build directory is not readable: ' . $build_dir);
                return;
            }

            foreach (self::BLOCKS as $block) {
                try {
                    $this->register_single_block($build_dir, $block);
                } catch (Exception $e) {
                    error_log('NR Blocks: Error registering block ' . $block . ': ' . $e->getMessage());
                }
            }
        }

        private function register_single_block(string $build_dir, string $block): void {
            $block_dir = trailingslashit($build_dir . sanitize_file_name($block));
            
            if (!is_readable($block_dir)) {
                throw new Exception("Build directory not readable: {$block_dir}");
            }

            if (!file_exists($block_dir . 'block.json')) {
                throw new Exception("block.json not found for {$block}");
            }

            $registration_result = register_block_type($block_dir);
            
            if (false === $registration_result) {
                throw new Exception("Block registration failed for {$block}");
            }
        }

        public static function get_instance(): self {
            if (is_null(self::$instance)) {
                self::$instance = new self();
            }
            return self::$instance;
        }
    }
}

NR_Blocks_Registration::get_instance();