<?php
/**
 * Plugin Name: Modal Popup Plugin
 * Description: Creates a customizable modal popup using ACF fields.
 * Version: 2.9
 * Author: P York
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
   

    global $post;
     $global_enabled = get_field('enable_modal', 'option');
    $global_pages = get_field('show_on_pages', 'option');
    $modals = get_field('page_per_modals', 'option');
 $global_content = get_field('popup_content', 'option');
     $modal_content = '';
    $bg_color = '#ffffff';
    $overlay_color = 'rgba(0,0,0,0.6)';
    $show = false;

     if ($modals) {
        foreach ($modals as $modal) {
            if ($modal['target_page']['ID'] == $post->ID) {
                $modal_content = $modal['modal_content'];
                $bg_color = $modal['modal_bg_color'] ?: $bg_color;
                $overlay_color = $modal['modal_overlay_color'] ?: $overlay_color;
                $pop_modal_width = $modal['pop_modal_width'];
                $modal_image = $modal['modal_image'];
                $modal_add_image = $modal['modal_add_image'];
                 if ($modal_add_image && $modal_image) {
        $modal_img_content .= '<img src="' . esc_url($modal_image['url']) . '" class="modal-img" alt="' . esc_attr($modal_image['alt']) . '">';
    }
                $show = true;
                break;
            }
        }
    }

      if (!$show && $global_enabled && is_array($global_pages)) {
        if (in_array($post->ID, array_column($global_pages, 'ID'))) {
            $modal_content = $global_content;
            $modal_add_image = get_field('modal_add_image', 'option');
               $modal_image = get_field('modal_image', 'option');
    $modal_button = get_field('modal_button', 'option');
      $modal_mobile_image_fit = get_field('modal_mobile_image_fit', 'option');
            $bg_color = get_field('modal_bg_color', 'option') ?: $bg_color;
            $overlay_color = get_field('modal_overlay_color', 'option') ?: $overlay_color;
             $pop_modal_width = get_field('pop_modal_width', 'option');
            if ($modal_add_image && $modal_image) {
        $modal_img_content .= '<img src="' . esc_url($modal_image['url']) . '" class="modal-img" alt="' . esc_attr($modal_image['alt']) . '">';
    }
            $show = true;
        }
    }

    if (!$show || !$modal_content) return;

 
    ?>
     <style>
        .custom-modal { background-color: <?php echo $overlay_color; ?>; }
        .custom-modal-content {
            background-color: <?php echo $bg_color; ?>;
        }
        @media screen and (max-width: 768px) {
            .custom-modal-content.with-image .modal-image img.modal-img {
               object-fit: <?php echo $modal_mobile_image_fit; ?> !important;
            }
            
        }
    </style>
    <div id="custom-modal" class="custom-modal" style="display:none;">
        <div class="custom-modal-content modal-width-<?php echo $pop_modal_width ; ?> <?php echo $modal_add_image ? 'with-image' : ''; ?>">
            <span class="close-button">&times;</span>
            <?php if ($modal_add_image && $modal_image) : ?>
                <div class="modal-image">
                    <?php echo $modal_img_content; ?>
                </div>
            <?php endif; ?>
            <div class="modal-text">
                <?php echo $modal_content; ?>

                <?php if ($modal_button) : ?>
                    <a href="<?php echo esc_url($modal_button['url']); ?>" class="modal-button" target="<?php echo esc_attr($modal_button['target']); ?>">
                        <?php echo esc_html($modal_button['title']); ?>
                    </a>
                <?php endif; ?> 
            </div>
        </div>
    </div>
    <?php
});
