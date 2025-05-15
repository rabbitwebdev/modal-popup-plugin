<?php
/**
 * Plugin Name: Modal Popup Plugin
 * Description: Creates a customizable modal popup using ACF fields.
 * Version: 1.2
 * Author: Your Name
 */

if (!defined('ABSPATH')) exit;

// Enqueue assets
add_action('wp_enqueue_scripts', function() {
    wp_enqueue_style('modal-popup-style', plugin_dir_url(__FILE__) . 'assets/modal.css');
    wp_enqueue_script('modal-popup-script', plugin_dir_url(__FILE__) . 'assets/modal.js', ['jquery'], null, true);

    // Send ACF fields to JS
    if (function_exists('get_field')) {
        $popup_data = [
            'enabled' => get_field('enable_modal', 'option'),
            'trigger' => get_field('popup_trigger', 'option'),
            'delay'   => get_field('popup_delay', 'option'),
            'scroll'  => get_field('popup_scroll_percentage', 'option'),
        ];
        wp_localize_script('modal-popup-script', 'popupOptions', $popup_data);
    }
});

// ACF Options Page
if (function_exists('acf_add_options_page')) {
    acf_add_options_page([
        'page_title' => 'Modal Settings',
        'menu_title' => 'Modal Popup',
        'menu_slug'  => 'modal-popup-settings',
        'capability' => 'edit_posts',
        'redirect'   => false
    ]);
}

// Display Modal
add_action('wp_footer', function() {
    if (!function_exists('get_field')) return;
    if (!get_field('enable_modal', 'option')) return;

    global $post;
    $selected_pages = get_field('show_on_pages', 'option');

    if ($selected_pages && !in_array($post->ID, array_column($selected_pages, 'ID'))) return;
    $modal_add_image = get_field('modal_add_image', 'option');
    $modal_content = get_field('popup_content', 'option');
    $modal_image = get_field('modal_image', 'option');
    if ($modal_add_image && $modal_image) {
        $modal_img_content .= '<img src="' . esc_url($modal_image['url']) . '" alt="' . esc_attr($modal_image['alt']) . '">';
    }
    ?>
    <div id="custom-modal" class="custom-modal" style="display:none;">
        <div class="custom-modal-content">
            <span class="close-button">&times;</span>
            <?php if ($modal_add_image && $modal_image) : ?>
                <div class="modal-image">
                    <?php echo $modal_img_content; ?>
                </div>
            <?php endif; ?>
            <div class="modal-text">
                <?php echo $modal_content; ?>
            </div>
        </div>
    </div>
    <?php
});
