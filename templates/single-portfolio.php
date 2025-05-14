<?php
/**
 * Template Name: Single Portfolio
 * Template Post Type: portfolio
 */

get_header();
?>

<div class="portfolio-single-container">
    <div class="portfolio-single-content">
        <?php while (have_posts()) : the_post(); ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class('portfolio-single'); ?>>
                <div class="portfolio-single-main">
                    <h1 class="portfolio-title"><?php the_title(); ?></h1>
                    <?php if (has_post_thumbnail()) : ?>
                        <div class="portfolio-featured-image">
                            <?php the_post_thumbnail('full'); ?>
                        </div>
                    <?php endif; ?>
                    <div class="portfolio-meta">
                        <?php
                        // Display project categories
                        $project_categories = get_the_terms(get_the_ID(), 'project_category');
                        if ($project_categories && !is_wp_error($project_categories)) : ?>
                            <div class="portfolio-categories">
                                <span class="meta-label"><?php _e('Categories:', 'custom-portfolio-filter'); ?></span>
                                <div class="meta-content">
                                    <?php foreach ($project_categories as $category) : ?>
                                        <a href="<?php echo esc_url(get_term_link($category)); ?>" class="category-link">
                                            <?php echo esc_html($category->name); ?>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php
                        // Display service types
                        $service_types = get_the_terms(get_the_ID(), 'service_type');
                        if ($service_types && !is_wp_error($service_types)) : ?>
                            <div class="portfolio-services">
                                <span class="meta-label"><?php _e('Services:', 'custom-portfolio-filter'); ?></span>
                                <div class="meta-content">
                                    <?php foreach ($service_types as $service) : ?>
                                        <a href="<?php echo esc_url(get_term_link($service)); ?>" class="service-link">
                                            <?php echo esc_html($service->name); ?>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="portfolio-content">
                        <?php the_content(); ?>
                    </div>
                </div>
            </article>
        <?php endwhile; ?>
    </div>
</div>

<?php get_footer(); ?> 