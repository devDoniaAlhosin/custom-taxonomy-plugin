<?php
/**
 * Main Portfolio Filter Class
 */
class Portfolio_Filter {
  
    public function init() {
        add_action('init', array($this, 'register_post_type_and_taxonomies'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_shortcode('portfolio_filter', array($this, 'portfolio_filter_shortcode'));
        add_action('wp_ajax_portfolio_filter', array($this, 'ajax_portfolio_filter'));
        add_action('wp_ajax_nopriv_portfolio_filter', array($this, 'ajax_portfolio_filter'));
        
        // Add dummy data generation
        add_action('admin_init', array($this, 'generate_dummy_data'));

        // Add template loader
        add_filter('single_template', array($this, 'load_portfolio_template'));
    }

    /**
     * Register Portfolio Post Type and Taxonomies
     */
    public function register_post_type_and_taxonomies() {
        // Register Project Category Taxonomy
        register_taxonomy('project_category', 'portfolio', array(
            'hierarchical' => true,
            'labels' => array(
                'name' => __('Project Categories', 'custom-portfolio-filter'),
                'singular_name' => __('Project Category', 'custom-portfolio-filter'),
                'menu_name' => __('Categories', 'custom-portfolio-filter'),
                'all_items' => __('All Categories', 'custom-portfolio-filter'),
                'edit_item' => __('Edit Category', 'custom-portfolio-filter'),
                'view_item' => __('View Category', 'custom-portfolio-filter'),
                'update_item' => __('Update Category', 'custom-portfolio-filter'),
                'add_new_item' => __('Add New Category', 'custom-portfolio-filter'),
                'new_item_name' => __('New Category Name', 'custom-portfolio-filter'),
                'search_items' => __('Search Categories', 'custom-portfolio-filter'),
            ),
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'project-category'),
        ));

        // Register Service Type Taxonomy
        register_taxonomy('service_type', 'portfolio', array(
            'hierarchical' => true,
            'labels' => array(
                'name' => __('Service Types', 'custom-portfolio-filter'),
                'singular_name' => __('Service Type', 'custom-portfolio-filter'),
                'menu_name' => __('Services', 'custom-portfolio-filter'),
                'all_items' => __('All Services', 'custom-portfolio-filter'),
                'edit_item' => __('Edit Service', 'custom-portfolio-filter'),
                'view_item' => __('View Service', 'custom-portfolio-filter'),
                'update_item' => __('Update Service', 'custom-portfolio-filter'),
                'add_new_item' => __('Add New Service', 'custom-portfolio-filter'),
                'new_item_name' => __('New Service Name', 'custom-portfolio-filter'),
                'search_items' => __('Search Services', 'custom-portfolio-filter'),
            ),
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'service-type'),
        ));

        // Register Portfolio Post Type
        register_post_type('portfolio', array(
            'labels' => array(
                'name' => __('Portfolio', 'custom-portfolio-filter'),
                'singular_name' => __('Portfolio Item', 'custom-portfolio-filter'),
                'menu_name' => __('Portfolio', 'custom-portfolio-filter'),
                'add_new' => __('Add New', 'custom-portfolio-filter'),
                'add_new_item' => __('Add New Portfolio Item', 'custom-portfolio-filter'),
                'edit_item' => __('Edit Portfolio Item', 'custom-portfolio-filter'),
                'new_item' => __('New Portfolio Item', 'custom-portfolio-filter'),
                'view_item' => __('View Portfolio Item', 'custom-portfolio-filter'),
                'search_items' => __('Search Portfolio', 'custom-portfolio-filter'),
                'not_found' => __('No portfolio items found', 'custom-portfolio-filter'),
                'not_found_in_trash' => __('No portfolio items found in Trash', 'custom-portfolio-filter'),
            ),
            'public' => true,
            'has_archive' => true,
            'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'),
            'menu_icon' => 'dashicons-portfolio',
            'rewrite' => array('slug' => 'portfolio'),
            'show_in_rest' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'query_var' => true,
            'capability_type' => 'post',
            'hierarchical' => false,
            'menu_position' => 5,
        ));
    }

    /**
     * Enqueue necessary scripts and styles
     */
    public function enqueue_scripts() {
        wp_enqueue_style(
            'cpf-styles',
            CPF_PLUGIN_URL . 'assets/css/portfolio-filter.css',
            array(),
            CPF_VERSION
        );

        wp_enqueue_script(
            'cpf-scripts',
            CPF_PLUGIN_URL . 'assets/js/portfolio-filter.js',
            array('jquery'),
            CPF_VERSION,
            true
        );

        wp_localize_script('cpf-scripts', 'cpfAjax', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('cpf-nonce')
        ));
    }

    /**
     * Portfolio Filter Shortcode
     */
    public function portfolio_filter_shortcode($atts) {
        $atts = shortcode_atts(array(
            'posts_per_page' => 9,
            'columns' => 3
        ), $atts);

        ob_start();
        
        // Get taxonomy terms
        $project_categories = get_terms(array(
            'taxonomy' => 'project_category',
            'hide_empty' => true
        ));

        $service_types = get_terms(array(
            'taxonomy' => 'service_type',
            'hide_empty' => true
        ));


        include CPF_PLUGIN_DIR . 'templates/portfolio-filter.php';
        return ob_get_clean();
    }

    /**
     * AJAX Portfolio Filter Handler
     */
    public function ajax_portfolio_filter() {
        check_ajax_referer('cpf-nonce', 'nonce');

        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $project_category = isset($_POST['project_category']) ? sanitize_text_field($_POST['project_category']) : '';
        $service_type = isset($_POST['service_type']) ? sanitize_text_field($_POST['service_type']) : '';
        $posts_per_page = isset($_POST['posts_per_page']) ? intval($_POST['posts_per_page']) : 9;

        $args = array(
            'post_type' => 'portfolio',
            'posts_per_page' => $posts_per_page,
            'paged' => $page,
            'post_status' => 'publish'
        );

        // Add taxonomy queries if filters are set
        if (!empty($project_category) || !empty($service_type)) {
            $args['tax_query'] = array('relation' => 'AND');

            if (!empty($project_category)) {
                $args['tax_query'][] = array(
                    'taxonomy' => 'project_category',
                    'field' => 'slug',
                    'terms' => $project_category
                );
            }

            if (!empty($service_type)) {
                $args['tax_query'][] = array(
                    'taxonomy' => 'service_type',
                    'field' => 'slug',
                    'terms' => $service_type
                );
            }
        }

        $query = new WP_Query($args);
        $response = array(
            'html' => '',
            'max_pages' => $query->max_num_pages
        );

        if ($query->have_posts()) {
            ob_start();
            while ($query->have_posts()) {
                $query->the_post();
                include CPF_PLUGIN_DIR . 'templates/portfolio-item.php';
            }
            $response['html'] = ob_get_clean();
        }

        wp_reset_postdata();
        wp_send_json_success($response);
    }

    /**
     * Generate dummy portfolio data
     */
    public function generate_dummy_data() {
        // Check if dummy data already exists
        $existing_posts = get_posts(array(
            'post_type' => 'portfolio',
            'posts_per_page' => 1
        ));

        if (!empty($existing_posts)) {
            return; // Dummy data already exists
        }

        // Sample categories
        $categories = array(
            'Web Design',
            'Mobile Apps',
            'Branding',
            'UI/UX Design',
            'Print Design'
        );

        // Sample services
        $services = array(
            'Website Development',
            'Mobile Development',
            'Brand Strategy',
            'User Interface Design',
            'Print Materials'
        );

        // Sample project titles
        $project_titles = array(
            'Modern E-commerce Website',
            'Mobile Banking App',
            'Corporate Brand Identity',
            'Restaurant Website Redesign',
            'Product Packaging Design',
            'Social Media Dashboard',
            'Fitness Tracking App',
            'Hotel Booking System',
            'Food Delivery Platform',
            'Real Estate Website',
            'Travel Blog Design',
            'Educational Platform',
            'Healthcare App',
            'Fashion E-commerce',
            'Portfolio Website',
            'Event Management System',
            'Music Streaming App',
            'Recipe Sharing Platform',
            'Job Board Website',
            'Online Learning Platform'
        );

        // Create categories
        foreach ($categories as $category) {
            wp_insert_term($category, 'project_category');
        }

        // Create services
        foreach ($services as $service) {
            wp_insert_term($service, 'service_type');
        }

        // Create portfolio items
        foreach ($project_titles as $title) {
            $post_data = array(
                'post_title'    => $title,
                'post_content'  => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.',
                'post_status'   => 'publish',
                'post_type'     => 'portfolio'
            );

            $post_id = wp_insert_post($post_data);

            if ($post_id) {
                // Assign random categories
                $random_categories = array_rand($categories, rand(1, 3));
                if (!is_array($random_categories)) {
                    $random_categories = array($random_categories);
                }
                $category_terms = array();
                foreach ($random_categories as $index) {
                    $category_terms[] = $categories[$index];
                }
                wp_set_object_terms($post_id, $category_terms, 'project_category');

                // Assign random services
                $random_services = array_rand($services, rand(1, 3));
                if (!is_array($random_services)) {
                    $random_services = array($random_services);
                }
                $service_terms = array();
                foreach ($random_services as $index) {
                    $service_terms[] = $services[$index];
                }
                wp_set_object_terms($post_id, $service_terms, 'service_type');

                // Generate random featured image
                $image_url = 'https://picsum.photos/800/600?random=' . $post_id;
                $upload_dir = wp_upload_dir();
                $image_data = file_get_contents($image_url);
                $filename = 'portfolio-' . $post_id . '.jpg';

                if ($image_data) {
                    $file = $upload_dir['path'] . '/' . $filename;
                    file_put_contents($file, $image_data);

                    $wp_filetype = wp_check_filetype($filename, null);
                    $attachment = array(
                        'post_mime_type' => $wp_filetype['type'],
                        'post_title' => sanitize_file_name($filename),
                        'post_content' => '',
                        'post_status' => 'inherit'
                    );

                    $attach_id = wp_insert_attachment($attachment, $file, $post_id);
                    require_once(ABSPATH . 'wp-admin/includes/image.php');
                    $attach_data = wp_generate_attachment_metadata($attach_id, $file);
                    wp_update_attachment_metadata($attach_id, $attach_data);
                    set_post_thumbnail($post_id, $attach_id);
                }
            }
        }
    }

    /**
     * Load portfolio template
     */
    public function load_portfolio_template($template) {
        global $post;

        if ($post->post_type === 'portfolio') {
            $plugin_template = CPF_PLUGIN_DIR . 'templates/single-portfolio.php';
            if (file_exists($plugin_template)) {
                return $plugin_template;
            }
        }

        return $template;
    }
} 