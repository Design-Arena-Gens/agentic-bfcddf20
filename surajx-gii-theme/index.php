<?php
/**
 * The main template file
 *
 * @package Surajx_GII_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>

<div class="container" style="padding: 2rem 0;">
    <?php if (have_posts()) : ?>
        <?php while (have_posts()) : the_post(); ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?> style="background: white; padding: 2rem; margin-bottom: 2rem; border-radius: 0.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <h2 style="margin-bottom: 1rem;">
                    <a href="<?php the_permalink(); ?>" style="color: #2563eb; text-decoration: none;">
                        <?php the_title(); ?>
                    </a>
                </h2>

                <div class="entry-meta" style="color: #6b7280; font-size: 0.875rem; margin-bottom: 1rem;">
                    <?php printf(
                        __('Posted on %s by %s', 'surajx-gii-theme'),
                        '<time datetime="' . esc_attr(get_the_date('c')) . '">' . esc_html(get_the_date()) . '</time>',
                        '<span class="author">' . esc_html(get_the_author()) . '</span>'
                    ); ?>
                </div>

                <div class="entry-content">
                    <?php the_excerpt(); ?>
                    <a href="<?php the_permalink(); ?>" class="btn btn-primary">
                        <?php _e('Read More', 'surajx-gii-theme'); ?>
                    </a>
                </div>
            </article>
        <?php endwhile; ?>

        <div class="pagination" style="margin-top: 2rem;">
            <?php
            the_posts_pagination(array(
                'mid_size'  => 2,
                'prev_text' => __('&larr; Previous', 'surajx-gii-theme'),
                'next_text' => __('Next &rarr;', 'surajx-gii-theme'),
            ));
            ?>
        </div>

    <?php else : ?>
        <div style="text-align: center; padding: 4rem 0;">
            <h2><?php _e('Nothing Found', 'surajx-gii-theme'); ?></h2>
            <p><?php _e('Sorry, no posts matched your criteria.', 'surajx-gii-theme'); ?></p>
        </div>
    <?php endif; ?>
</div>

<?php
get_footer();
