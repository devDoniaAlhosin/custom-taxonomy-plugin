<?php
/**
 * Portfolio Filter Template
 */
?>
<div class="portfolio-filter-container">
    <!-- Filter Controls -->
    <div class="portfolio-filters">
        <div class="filter-group">
            <label for="project-category"><?php esc_html_e('Project Category', 'custom-portfolio-filter'); ?></label>
            <select id="project-category" class="portfolio-filter">
                <option value=""><?php esc_html_e('All Categories', 'custom-portfolio-filter'); ?></option>
                <?php foreach ($project_categories as $category) : ?>
                    <option value="<?php echo esc_attr($category->slug); ?>">
                        <?php echo esc_html($category->name); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="filter-group">
            <label for="service-type"><?php esc_html_e('Service Type', 'custom-portfolio-filter'); ?></label>
            <select id="service-type" class="portfolio-filter">
                <option value=""><?php esc_html_e('All Services', 'custom-portfolio-filter'); ?></option>
                <?php foreach ($service_types as $service) : ?>
                    <option value="<?php echo esc_attr($service->slug); ?>">
                        <?php echo esc_html($service->name); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <!-- Portfolio Grid -->
    <div class="portfolio-grid" data-columns="<?php echo esc_attr($atts['columns']); ?>">
        <?php
        $args = array(
            'post_type' => 'portfolio',
            'posts_per_page' => $atts['posts_per_page'],
            'paged' => 1
        );

        $query = new WP_Query($args);

        if ($query->have_posts()) :
            while ($query->have_posts()) : $query->the_post();
                include CPF_PLUGIN_DIR . 'templates/portfolio-item.php';
            endwhile;
        endif;
        wp_reset_postdata();
        ?>
    </div>

    <!-- Pagination -->
    <div class="portfolio-pagination" data-max-pages="<?php echo esc_attr($query->max_num_pages); ?>">
        <?php if ($query->max_num_pages > 1) : ?>
            <button class="load-more" data-page="1">
                <?php esc_html_e('Load More', 'custom-portfolio-filter'); ?>
            </button>
        <?php endif; ?>
    </div>

    <!-- Loading Indicator -->
    <div class="portfolio-loading" style="display: none;">
        <span class="spinner"></span>
    </div>
</div> 