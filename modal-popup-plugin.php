<?php
/**
 * Plugin Name: Modal Popup Plugin
 * Description: Creates a customizable modal popup using ACF fields.
 * Version: 3.2
 * Author: P York
 */

if (!defined('ABSPATH')) exit;

// Enqueue assets
add_action('wp_enqueue_scripts', function() {
    wp_enqueue_style('modal-popup-style', plugin_dir_url(__FILE__) . 'assets/modal.css');
    wp_enqueue_script('modal-popup-script', plugin_dir_url(__FILE__) . 'assets/modal.js', ['jquery'], null, true);
 if (function_exists('get_field')) {
    // Send ACF fields to JS
    $trigger_type = get_field('popup_trigger', 'option');
$trigger_delay = get_field('popup_delay', 'option');
$scroll_percent = get_field('popup_scroll_percentage', 'option');

// Try per-post modal trigger override
if (is_singular()) {
    global $post;
    $modals = get_posts([
        'post_type' => 'pop-modal',
        'posts_per_page' => -1,
        'post_status' => 'publish',
    ]);

    foreach ($modals as $modal_post) {
        $target_pages = get_field('target_modal_page', $modal_post->ID);

        if ($target_pages) {
            $page_ids = is_array($target_pages) ? wp_list_pluck($target_pages, 'ID') : [$target_pages->ID];
            if (in_array($post->ID, $page_ids)) {
                $trigger_type = get_field('pop_trigger_type', $modal_post->ID) ?: $trigger_type;
                $trigger_delay = get_field('popup_delay', $modal_post->ID) ?: $trigger_delay;
                $scroll_percent = get_field('popup_scroll_percentage', $modal_post->ID) ?: $scroll_percent;
                break;
            }
        }
    }
}

$popup_data = [
    'enabled' => get_field('enable_modal', 'option'),
    'trigger' => $trigger_type,
    'delay'   => $trigger_delay,
    'scroll'  => $scroll_percent,
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
    if (!is_singular() || !$post instanceof WP_Post) return;
    
     
 
     $modal = null;
    $modal_content = '';
    $modal_img_content = '';
    $modal_button = '';
    $mm_image_fit = '';
    $bg_color = '#ffffff';
    $overlay_color = 'rgba(0,0,0,0.6)';
    $modal_width = 'md';
    $show = false;

    // STEP 1: Check for matching page_modal post
    $modals = get_posts([
        'post_type' => 'pop-modal',
        'posts_per_page' => -1,
        'post_status' => 'publish',
    ]);
   

  
   
    
    
    
    if ($modals) {
        foreach ($modals as $modal_post) {
            $target_pages = get_field('target_modal_page', $modal_post->ID); // assuming it's a post object field (can be multiple)

            if ($target_pages) {
                $page_ids = is_array($target_pages) ? wp_list_pluck($target_pages, 'ID') : [$target_pages->ID];

                if (in_array($post->ID, $page_ids)) {
                    $modal = $modal_post;
                    break; // found one, stop loop
                }
            }
        }
    }

    if ($modal) {
        $modal_content = get_field('the_modal_content', $modal->ID);
         $modal_add_image = get_field('modal_add_image', $modal->ID);
         $mm_image_fit = get_field('modal_mobile_image_fit', $modal->ID);
         $modal_image = get_field('modal_image', $modal->ID);
         $modal_button = get_field('modal_button', $modal->ID);
         $modal_button_color = get_field('modal_button_color', $modal->ID);
        $bg_color = get_field('modal_bg_color', $modal->ID) ?: $bg_color;
        $overlay_color = get_field('modal_overlay_color', $modal->ID) ?: $overlay_color;
        $modal_width = get_field('modal_width', $modal->ID) ?: $modal_width;
           if ($modal_add_image && $modal_image) {
        $modal_img_content .= '<img src="' . esc_url($modal_image['url']) . '" class="modal-img" alt="' . esc_attr($modal_image['alt']) . '">';
    }
        $show = true;
    }
    
     
    // STEP 2: Fallback to global settings
    if (!$show) {
        $global_enabled = get_field('enable_modal', 'option');
        $global_pages = get_field('show_on_pages', 'option');
        
              if ($global_enabled && is_array($global_pages)) {
            $global_ids = wp_list_pluck($global_pages, 'ID');
            if (in_array($post->ID, $global_ids)) {
                $modal_content = get_field('popup_content', 'option');
                $modal_add_image = get_field('modal_add_image', 'option');
                $modal_image = get_field('modal_image', 'option');
                $modal_button = get_field('modal_button', 'option');
                $modal_button_color = get_field('modal_button_color', 'option');
                $mm_image_fit = get_field('modal_mobile_image_fit', 'option');
                $bg_color = get_field('modal_bg_color', 'option') ?: $bg_color;
                $overlay_color = get_field('modal_overlay_color', 'option') ?: $overlay_color;
                $modal_width = get_field('pop_modal_width', 'option') ?: $modal_width;
                   if ($modal_add_image && $modal_image) {
        $modal_img_content .= '<img src="' . esc_url($modal_image['url']) . '" class="modal-img" alt="' . esc_attr($modal_image['alt']) . '">';
    }
                $show = true;
            }
        }
    }

    if (!$show || empty($modal_content)) return;

 
    ?>
     <style>
        .custom-modal { background-color: <?php echo $overlay_color; ?>; }
        .custom-modal-content {
            background-color: <?php echo $bg_color; ?>;
        }
        @media screen and (max-width: 890px) {
            .custom-modal-content.with-image .modal-image img.modal-img {
               object-fit: <?php echo $mm_image_fit ; ?>; 
            }
             
        }
      
    </style>
    <div id="custom-modal" class="custom-modal">
        <div class="custom-modal-content modal-width-<?php echo $modal_width ; ?> <?php echo $modal_add_image ? 'with-image' : ''; ?>">
            <span class="close-button"><span class="close-x">&times;</span></span>
            <?php if ($modal_add_image && $modal_image) : ?>
                <div class="modal-image">
                    <?php echo $modal_img_content; ?>
                </div>
            <?php endif; ?>
            <div class="modal-text">
                <?php echo $modal_content; ?>

                <?php if ($modal_button) : ?>
                    <a href="<?php echo esc_url($modal_button['url']); ?>" class="modal-button btn btn-<?php echo $modal_button_color ; ?>" target="<?php echo esc_attr($modal_button['target']); ?>">
                        <?php echo esc_html($modal_button['title']); ?>
                    </a>
                <?php endif; ?> 
            </div>
        </div>
    </div>
    <?php
});

