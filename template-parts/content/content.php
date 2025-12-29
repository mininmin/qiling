<?php
/**
 * Template part for displaying posts
 *
 * @package Developer_Starter
 * @since 1.0.0
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'post-card' ); ?>>
    <?php if ( has_post_thumbnail() ) : ?>
        <a href="<?php the_permalink(); ?>" class="post-thumb">
            <?php the_post_thumbnail( 'developer-starter-card' ); ?>
        </a>
    <?php endif; ?>
    
    <div class="post-content">
        <div class="post-meta">
            <span class="post-date"><?php the_date(); ?></span>
            <?php developer_starter_entry_categories(); ?>
        </div>
        
        <h2 class="post-title">
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        </h2>
        
        <p class="post-excerpt"><?php echo wp_trim_words( get_the_excerpt(), 20 ); ?></p>
        
        <a href="<?php the_permalink(); ?>" class="post-read-more">
            <?php _e( '阅读更多 →', 'developer-starter' ); ?>
        </a>
    </div>
</article>
