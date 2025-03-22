<?php
/**
 * NR Blocks Category
 *
 * @since 1.0.0
 * @package NRBlocks
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('NR_Blocks_Category')) {
    class NR_Blocks_Category {
        private const CATEGORY_SLUG = 'button-blocks';
        private const TEXT_DOMAIN = 'button-blocks';

        /**
         * @var null|self
         */
        private static $instance = null;

        public function __construct() {
            $this->init();
        }

        private function init() {
            add_filter('block_categories_all', [$this, 'register_category'], 10, 2);
        }

        /**
         * @param array $categories
         * @return array
         */
        public function register_category($categories): array {
            if (!is_array($categories)) {
                return $categories;
            }

            $new_category = [
                'slug' => self::CATEGORY_SLUG,
                'title' => esc_html__('Button Blocks', self::TEXT_DOMAIN),
                'icon' => null
            ];

            array_unshift($categories, $new_category);
            
            return $categories;
        }

        public static function get_instance(): self {
            if (is_null(self::$instance)) {
                self::$instance = new self();
            }
            return self::$instance;
        }
    }
}

NR_Blocks_Category::get_instance();