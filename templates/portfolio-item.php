<?php
/**
 * Template for displaying individual portfolio items in the grid
 */
?>
<article id="post-<?php the_ID(); ?>" <?php post_class('portfolio-item'); ?>>
    <div class="portfolio-item-inner">
        <?php if (has_post_thumbnail()) : ?>
            <div class="portfolio-thumbnail">
                <a href="<?php the_permalink(); ?>" class="portfolio-link">
                    <?php the_post_thumbnail('medium_large'); ?>
                </a>
            </div>
        <?php endif; ?>

        <div class="portfolio-content">
            <h2 class="portfolio-title">
                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
            </h2>

            <?php
            // Display project categories
            $project_categories = get_the_terms(get_the_ID(), 'project_category');
            if ($project_categories && !is_wp_error($project_categories)) : ?>
                <div class="portfolio-categories">
                    <?php foreach ($project_categories as $category) : ?>
                        <span class="category-tag"><?php echo esc_html($category->name); ?></span>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div class="portfolio-excerpt">
                <?php the_excerpt(); ?>
            </div>

            <a href="<?php the_permalink(); ?>" class="read-more-link">
                <?php _e('View Project', 'custom-portfolio-filter'); ?>
            </a>
        </div>
    </div>
</article> 