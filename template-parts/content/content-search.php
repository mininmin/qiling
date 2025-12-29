<?php
/**
 * Template part for displaying search results
 *
 * @package Developer_Starter
 * @since 1.0.0
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'search-result-item' ); ?>>
    <div class="search-result-content">
        <h2 class="search-result-title">
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        </h2>
        
        <div class="search-result-meta">
            <span class="result-type"><?php echo get_post_type_object( get_post_type() )->labels->singular_name; ?></span>
            <span class="result-date"><?php the_date(); ?></span>
        </div>
        
        <p class="search-result-excerpt">
            <?php echo wp_trim_words( get_the_excerpt(), 30 ); ?>
        </p>
    </div>
    
    <?php if ( has_post_thumbnail() ) : ?>
        <a href="<?php the_permalink(); ?>" class="search-result-thumb">
            <?php the_post_thumbnail( 'thumbnail' ); ?>
        </a>
    <?php endif; ?>
</article>
