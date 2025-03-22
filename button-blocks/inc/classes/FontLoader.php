<?php
/**
 * NR Blocks Font Loader
 *
 * @since 1.0.0
 * @package NRBlocks
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('NR_Blocks_FontLoader')) {
    class NR_Blocks_FontLoader {
        private const FONT_WEIGHTS = '100,100italic,200,200italic,300,300italic,400,400italic,500,500italic,600,600italic,700,700italic,800,800italic,900,900italic';
        private const GOOGLE_FONTS_URL = '//fonts.googleapis.com/css';
        
        /**
         * @var null|self
         */
        private static $instance = null;

        /**
         * @var array<string>
         */
        private static $gfonts = [];

        public function __construct() {
            $this->init();
        }

        private function init() {
            add_filter('render_block', [$this, 'get_fonts_on_render_block'], 10, 2);
            add_action('wp_footer', [$this, 'fonts_loader'], 10);
        }

        public function get_fonts_on_render_block($block_content, $block) {
            if (!is_array($block) || empty($block['attrs'])) {
                return $block_content;
            }

            $fonts = self::get_fonts_family($block['attrs']);
            if (!empty($fonts)) {
                self::$gfonts = array_unique(array_merge(self::$gfonts, $fonts));
            }

            return $block_content;
        }

        /**
         * @param array<string, mixed> $attributes
         * @return array<string>
         */
        public static function get_fonts_family($attributes) {
            if (!is_array($attributes)) {
                return [];
            }

            $keys = preg_grep('/^(\w+)FontFamily$/i', array_keys($attributes));
            $googleFontFamily = [];
            
            foreach ($keys as $key) {
                if (isset($attributes[$key]) && is_string($attributes[$key])) {
                    $fontFamily = sanitize_text_field($attributes[$key]);
                    $googleFontFamily[$fontFamily] = $fontFamily;
                }
            }

            return $googleFontFamily;
        }

        /**
         * @param array<string> $fonts
         */
        private function load_google_font($fonts) {
            if (empty($fonts)) {
                return;
            }

            $gfonts = '';
            foreach ($fonts as $font) {
                $gfonts .= sprintf(
                    '%s:%s|',
                    str_replace(' ', '+', trim($font)),
                    self::FONT_WEIGHTS
                );
            }

            if (empty($gfonts)) {
                return;
            }

            $query_args = [
                'family' => rtrim($gfonts, '|'),
                'display' => 'swap'
            ];

            $font_url = add_query_arg($query_args, self::GOOGLE_FONTS_URL);

            wp_register_style(
                'button-blocks-fonts',
                esc_url($font_url),
                [],
                defined('NR_BUTTON_BLOCKS_VERSION') ? NR_BUTTON_BLOCKS_VERSION : '1.0.1'
            );

            wp_enqueue_style('button-blocks-fonts');
        }

        public function fonts_loader() {
            try {
                $fonts = array_filter(self::$gfonts);
                if (!empty($fonts)) {
                    $this->load_google_font($fonts);
                }
            } catch (Exception $e) {
                error_log('Font loading error: ' . $e->getMessage());
            } finally {
                self::$gfonts = [];
            }
        }

        public static function get_instance() {
            if (is_null(self::$instance)) {
                self::$instance = new self();
            }
            return self::$instance;
        }
    }
}

NR_Blocks_FontLoader::get_instance();