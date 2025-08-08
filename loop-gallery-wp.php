<?php
/**
 * Plugin Name: Loop Gallery GSAP
 * Description: GSAP loop gallery with shortcode [loop_gallery_gsap], image uploads, drag-drop ordering, and background color option.
 * Version: 2.2
 * Author: Hassan
 */

defined('ABSPATH') || exit;



class Loop_Gallery_GSAP {



    public function __construct() {
        add_shortcode('loop_gallery_gsap', [$this, 'render_gallery']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_init', [$this, 'register_settings']);
        add_action('admin_enqueue_scripts', [$this, 'admin_assets']);
        add_action('wp_head', [$this, 'output_custom_bg_css']);
    }

    public function enqueue_assets() {
        $plugin_url = plugin_dir_url(__FILE__);

        wp_enqueue_style('loop-gallery-style', $plugin_url . 'style.css', [], '1.0');
        wp_enqueue_script('gsap', 'https://unpkg.com/gsap@3/dist/gsap.min.js', [], null, true);
        wp_enqueue_script('gsap-scrolltrigger', 'https://unpkg.com/gsap@3/dist/ScrollTrigger.min.js', ['gsap'], null, true);
        wp_enqueue_script('loop-gallery-script', $plugin_url . 'script.js', ['gsap', 'gsap-scrolltrigger'], '1.1', true);
    }

    public function admin_assets($hook) {
        if ($hook !== 'settings_page_loop_gallery') return;

        wp_enqueue_media();
        wp_enqueue_script('jquery-ui-sortable');
        wp_enqueue_script('loop-gallery-admin', plugin_dir_url(__FILE__) . 'admin.js', ['jquery', 'jquery-ui-sortable'], '1.1', true);
        wp_enqueue_style('loop-gallery-admin-style', plugin_dir_url(__FILE__) . 'admin.css', [], '1.0');

        echo '<style>
            .wrap.loop-gallery-admin {
                background: #f9f9f9;
                padding: 20px;
                border-radius: 10px;
            }
            .loop-gallery-admin h1 {
                color: #333;
                margin-bottom: 20px;
            }
            .loop-gallery-setting {
                margin-bottom: 20px;
            }
            .loop-gallery-setting label {
                font-weight: bold;
                margin-right: 10px;
            }
            #loop-gallery-images {
                display: flex;
                flex-wrap: wrap;
                gap: 10px;
            }
            .loop-gallery-item {
                position: relative;
                cursor: move;
                border: 2px dashed #ccc;
                padding: 5px;
                background: #fff;
            }
            .loop-gallery-item img {
                width: 100px;
                height: 100px;
                object-fit: cover;
                display: block;
            }
            .loop-gallery-item .close-icon {
                position: absolute;
                top: -8px;
                right: -8px;
                background: #ff4d4d;
                color: #fff;
                border-radius: 50%;
                width: 18px;
                height: 18px;
                text-align: center;
                line-height: 18px;
                font-weight: bold;
                cursor: pointer;
            }
        </style>';
    }

    public function add_admin_menu() {
        add_options_page('Loop Gallery', 'Loop Gallery', 'manage_options', 'loop_gallery', [$this, 'settings_page']);
    }

    public function register_settings() {
        register_setting('loop_gallery_settings', 'loop_gallery_images', [
            'type' => 'array',
            'sanitize_callback' => function ($images) {
                return array_filter($images, function ($img) {
                    return filter_var($img, FILTER_VALIDATE_URL);
                });
            }
        ]);

        register_setting('loop_gallery_settings', 'loop_gallery_bg_color', [
            'type' => 'string',
            'default' => '#ffffff',
            'sanitize_callback' => 'sanitize_hex_color'
        ]);
    }

    public function settings_page() {
        $images = get_option('loop_gallery_images', []);
        $bg_color = get_option('loop_gallery_bg_color', '#ffffff');
        ?>
        <div class="wrap loop-gallery-admin">
            <h1>Loop Gallery Settings</h1>
            <form method="post" action="options.php">
                <?php settings_fields('loop_gallery_settings'); ?>
                <?php do_settings_sections('loop_gallery_settings'); ?>

                <div class="loop-gallery-setting">
                    <label for="loop_gallery_bg_color">Background Color:</label>
                    <input type="color" name="loop_gallery_bg_color" value="<?php echo esc_attr($bg_color); ?>">
                </div>

                <div id="loop-gallery-images">
                    <?php if (!empty($images)): ?>
                        <?php foreach ($images as $image): ?>
                            <div class="loop-gallery-item">
                                <img src="<?php echo esc_url($image); ?>" alt="">
                                <input type="hidden" name="loop_gallery_images[]" value="<?php echo esc_url($image); ?>">
                                <span class="close-icon" title="Remove">&times;</span>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <button type="button" class="button button-primary" id="add-loop-gallery-images">Add Images</button>
                <br><br>
                <?php submit_button('Save Settings'); ?>
            </form>
        </div>
        <?php
    }

    public function render_gallery() {
        if (is_admin()) {
            return '';
        }
        $images = get_option('loop_gallery_images', []);
        if (empty($images)) {
            return '<p>No images added to Loop Gallery yet.</p>';
        }

        $columns = array_chunk($images, ceil(count($images) / 3));
        ob_start();
        ?>
        <div class="gril">
        <section>



</section>
            <div class="gallery">
                <?php foreach ($columns as $col): ?>
                    <div class="col">
                        <?php foreach ($col as $img): ?>
                            <div class="image"><img src="<?php echo esc_url($img); ?>" alt=""></div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    public function output_custom_bg_css() {
        $bg_color = get_option('loop_gallery_bg_color', '#ffffff');
        echo "<style>
            .gril .gallery {
                z-index: 1;
                display: flex;
                flex-direction: row;
                justify-content: center;
                width: 100%;
                height: 100%;
                position: fixed;
                top: 0;
                left: 50%;
                transform: translateX(-50%);
                overflow: visible;
                background: {$bg_color} !important;
            }
        </style>";
    }
}

new Loop_Gallery_GSAP();
