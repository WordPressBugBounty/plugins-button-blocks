<?php
/**
 * NR_Blocks - Generate and Manage Page-Specific Dynamic Styles with Minification
 *
 * @since 1.0.0
 * @package NR_Blocks
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('NR_Blocks_Dynamic_Style')) {
    /**
     * NR_Blocks Dynamic Style Class
     *
     * @since 1.0.0
     * @package NR_Blocks
     */
    class NR_Blocks_Dynamic_Style {
        /**
         * CSS subdirectory path
         *
         * @var string
         */
        private const CSS_SUBDIR = '/button-blocks/css';

        /**
         * File permissions for generated CSS
         *
         * @var int
         */
        private const FILE_PERMISSIONS = 0644;

        /**
         * Stores all dynamic styles
         *
         * @var array
         */
        private static $styles = array();

        /**
         * The directory for storing CSS files
         *
         * @var string
         */
        private $css_dir;

        /**
         * The URL for the CSS directory
         *
         * @var string
         */
        private $css_url;

        /**
         * Singleton instance
         *
         * @var null|self
         */
        private static $instance = null;

        /**
         * Constructor
         *
         * @since 1.0.0
         * @return void
         */
        private function __construct() {
            $this->setup_directories();
            $this->init();
        }

        /**
         * NR_Blocks_Registration Instance
         *
         * @since 1.0.0
         * @return self
         */
        public static function get_instance() {
            if (is_null(self::$instance)) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        /**
         * Setup directories
         *
         * @since 1.0.0
         * @return void
         */
        private function setup_directories() {
            $upload_dir = wp_upload_dir();
            $this->css_dir = $upload_dir['basedir'] . self::CSS_SUBDIR;
            $this->css_url = $upload_dir['baseurl'] . self::CSS_SUBDIR;

            if (!file_exists($this->css_dir)) {
                if (!wp_mkdir_p($this->css_dir)) {
                    error_log('Failed to create CSS directory at: ' . $this->css_dir);
                    return;
                }
            }
        }

        /**
         * Initialize the Class
         *
         * @since 1.0.0
         * @return void
         */
        private function init() {
            add_filter('render_block', [$this, 'collect_dynamic_styles'], 10, 2);
            add_action('wp_footer', [$this, 'generate_css_file'], 10);
            add_action('wp_enqueue_scripts', [$this, 'enqueue_dynamic_styles']);
        }

        /**
         * Collect Dynamic Styles
         *
         * @since 1.0.0
         * @param string $block_content Block Content.
         * @param array  $block Block Attributes.
         * @return string
         */
        public function collect_dynamic_styles($block_content, $block) {
            if (!is_array($block) || empty($block['blockName'])) {
                return $block_content;
            }

            if (isset($block['blockName']) && str_contains($block['blockName'], 'button-blocks/')) {
                if (isset($block['attrs']['blockStyle'])) {
                    $block_id = isset($block['attrs']['blockId']) ? 
                        sanitize_key($block['attrs']['blockId']) : 
                        'button-blocks-' . md5(serialize($block['attrs']));
                    self::$styles[$block_id] = $block['attrs']['blockStyle'];
                }
            }
            return $block_content;
        }

        /**
         * Generate CSS File
         *
         * @since 1.0.0
         * @return void
         */
        public function generate_css_file() {
            if (empty(self::$styles)) {
                return;
            }

            clearstatcache();

            $page_id = get_queried_object_id();
            $css_filename = 'button-blocks-dynamic-styles-' . $page_id . '.min.css';
            $css_file = $this->css_dir . '/' . $css_filename;

            $css_content = '';
            foreach (self::$styles as $block_id => $style) {
                $css_content .= $style;
            }

            $css_content = "/* Button Blocks Dynamic Styles - Page ID: $page_id - Generated on " . 
                current_time('mysql') . " */\n" . $this->minify_css($css_content);

            if (!function_exists('WP_Filesystem')) {
                require_once ABSPATH . 'wp-admin/includes/file.php';
            }

            global $wp_filesystem;
            if (empty($wp_filesystem)) {
                WP_Filesystem();
            }

            if (!is_dir($this->css_dir)) {
                if (!wp_mkdir_p($this->css_dir)) {
                    error_log('Failed to create CSS directory: ' . $this->css_dir);
                    return;
                }
            }

            if (file_exists($css_file)) {
                $existing_content = $wp_filesystem->get_contents($css_file);
                if ($existing_content === $css_content) {
                    return;
                }
            }

            if (!$wp_filesystem->put_contents($css_file, $css_content, self::FILE_PERMISSIONS)) {
                error_log('Failed to write CSS file: ' . $css_file);
                return;
            }

            self::$styles = array();
        }

        /**
         * Enqueue Dynamic Styles
         *
         * @since 1.0.0
         * @return void
         */
        public function enqueue_dynamic_styles() {
            clearstatcache();
        
            $page_id = get_queried_object_id();
            $css_filename = 'button-blocks-dynamic-styles-' . $page_id . '.min.css';
            $css_file_path = $this->css_dir . '/' . $css_filename;
            $css_file_url = $this->css_url . '/' . $css_filename;
        
            if (!file_exists($css_file_path)) {
                $this->generate_css_file();
            }
        
            if (file_exists($css_file_path)) {
                wp_enqueue_style(
                    'button-blocks-dynamic-styles-' . $page_id,
                    $css_file_url,
                    array(),
                    filemtime($css_file_path)
                );
            }
        }

        /**
         * Minify CSS
         *
         * @since 1.0.0
         * @param string $css The CSS to minify.
         * @return string Minified CSS.
         */
        private function minify_css($css) {
            if (empty($css)) {
                return '';
            }
            
            // Remove comments
            $css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
            // Remove space after colons and unnecessary whitespace
            $css = preg_replace('/\s*([:,;{}])\s*/', '$1', $css);
            // Remove last semicolon from last property
            $css = preg_replace('/;}/', '}', $css);
            
            return trim($css);
        }
    }
}

NR_Blocks_Dynamic_Style::get_instance();