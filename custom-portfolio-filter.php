<?php
/**
 * Plugin Name: Custom Portfolio Filter
 * Description: A lightweight AJAX-powered portfolio filter with dynamic taxonomy filtering and pagination
 * Version: 1.0.0
 * Author: Donia Alhosin
 * Text Domain: custom-portfolio-filter
 * Domain Path: /languages
 * Requires at least: 5.0
 * Requires PHP: 7.4
 */


if (!defined('ABSPATH')) {
    exit;
}


define('CPF_VERSION', '1.0.0');
define('CPF_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('CPF_PLUGIN_URL', plugin_dir_url(__FILE__));

require_once CPF_PLUGIN_DIR . 'includes/class-portfolio-filter.php';

// Activation hook
register_activation_hook(__FILE__, 'cpf_activate');
function cpf_activate() {
    if (version_compare(PHP_VERSION, '7.4', '<')) {
        deactivate_plugins(plugin_basename(__FILE__));
        wp_die('This plugin requires PHP version 7.4 or higher.');
    }

    if (version_compare(get_bloginfo('version'), '5.0', '<')) {
        deactivate_plugins(plugin_basename(__FILE__));
        wp_die('This plugin requires WordPress version 5.0 or higher.');
    }

    flush_rewrite_rules();
}

// Deactivation hook
register_deactivation_hook(__FILE__, 'cpf_deactivate');
function cpf_deactivate() {
    flush_rewrite_rules();
    wp_clear_scheduled_hook('cpf_cleanup');
    delete_transient('cpf_portfolio_cache');
}

// Uninstall hook
register_uninstall_hook(__FILE__, 'cpf_uninstall');
function cpf_uninstall() {
    $portfolio_posts = get_posts(array(
        'post_type' => 'portfolio',
        'numberposts' => -1,
        'post_status' => 'any'
    ));

    foreach ($portfolio_posts as $post) {
        wp_delete_post($post->ID, true);
    }
    $taxonomies = array('project_category', 'service_type');
    foreach ($taxonomies as $taxonomy) {
        $terms = get_terms(array(
            'taxonomy' => $taxonomy,
            'hide_empty' => false
        ));
        
        foreach ($terms as $term) {
            wp_delete_term($term->term_id, $taxonomy);
        }
    }
    delete_option('cpf_settings');
    delete_transient('cpf_portfolio_cache');
}

function cpf_init() {
    $portfolio_filter = new Portfolio_Filter();
    $portfolio_filter->init();
    
    // usage instructions
    add_action('admin_notices', 'cpf_admin_notice');
}
add_action('plugins_loaded', 'cpf_init');


function cpf_admin_notice() {
    $screen = get_current_screen();
    if ($screen->post_type !== 'portfolio') {
        return;
    }
    $dismissed = get_user_meta(get_current_user_id(), 'cpf_notice_dismissed', true);
    if ($dismissed) {
        return;
    }

    ?>
    <div class="notice notice-info is-dismissible cpf-usage-notice">
        <div style="padding: 10px;">
            <h3 style="margin: 0 0 10px 0; color: #008236;">Portfolio Filter Usage Guide</h3>
            <p style="margin: 0 0 10px 0;">Here's how to use the Portfolio Filter plugin:</p>
            <div style="background: #f8f9fa; padding: 15px; border-radius: 5px; margin-bottom: 10px;">
                <h4 style="margin: 0 0 10px 0;">Available Shortcodes:</h4>
                <ul style="margin: 0; padding-left: 20px;">
                    <li><code>[portfolio_filter]</code> - Default display with 9 items per page and 3 columns</li>
                    <li><code>[portfolio_filter posts_per_page="12" columns="4"]</code> - Custom number of items and columns</li>
                </ul>
            </div>
            <div style="background: #f8f9fa; padding: 15px; border-radius: 5px;">
                <h4 style="margin: 0 0 10px 0;">Tips:</h4>
                <ul style="margin: 0; padding-left: 20px;">
                    <li>Add categories and services to organize your portfolio items</li>
                    <li>Use featured images for better visual presentation</li>
                    <li>Customize the appearance using the plugin's CSS classes</li>
                </ul>
            </div>
        </div>
    </div>
    <script>
    jQuery(document).ready(function($) {
        $('.cpf-usage-notice').on('click', '.notice-dismiss', function() {
            $.post(ajaxurl, {
                action: 'cpf_dismiss_notice',
                nonce: '<?php echo wp_create_nonce('cpf_dismiss_notice'); ?>'
            });
        });
    });
    </script>
    <?php
}

add_action('wp_ajax_cpf_dismiss_notice', 'cpf_dismiss_notice');
function cpf_dismiss_notice() {
    check_ajax_referer('cpf_dismiss_notice', 'nonce');
    update_user_meta(get_current_user_id(), 'cpf_notice_dismissed', true);
    wp_die();
}

// Add shortcode for portfolio filter
//  [portfolio_filter]
// [portfolio_filter posts_per_page="12" columns="4"]
