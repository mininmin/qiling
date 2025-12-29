<?php
/**
 * The template for displaying search results
 *
 * @package Developer_Starter
 */

get_header();
?>

<div class="page-header" style="background: var(--color-gray-900); padding: 100px 0 60px;">
    <div class="container">
        <h1 class="page-title" style="color: #fff; text-align: center; font-size: 2rem; margin: 0;">
            搜索结果：<?php echo esc_html( get_search_query() ); ?>
        </h1>
        <p style="color: rgba(255,255,255,0.7); text-align: center; margin-top: 15px;">
            共找到 <?php echo $wp_query->found_posts; ?> 条结果
        </p>
    </div>
</div>

<section class="search-results section-padding">
    <div class="container">
        <!-- Search Form -->
        <div style="max-width: 600px; margin: 0 auto 50px;">
            <form role="search" method="get" action="<?php echo home_url( '/' ); ?>">
                <div style="display: flex; gap: 10px;">
                    <input type="search" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" 
                           placeholder="继续搜索..." 
                           style="flex: 1; padding: 12px 16px; border: 1px solid var(--color-gray-300); border-radius: 8px; font-size: 1rem;" />
                    <button type="submit" class="btn btn-primary">搜索</button>
                </div>
            </form>
        </div>
        
        <?php if ( have_posts() ) : ?>
            <div class="search-results-list">
                <?php while ( have_posts() ) : the_post(); ?>
                    <article class="search-result-item" style="padding: 30px; background: #fff; border-radius: 12px; margin-bottom: 20px; box-shadow: var(--shadow-sm);">
                        <div style="display: flex; gap: 20px; align-items: flex-start;">
                            <?php if ( has_post_thumbnail() ) : ?>
                                <a href="<?php the_permalink(); ?>" style="flex-shrink: 0; width: 150px;">
                                    <?php the_post_thumbnail( 'thumbnail', array( 'style' => 'border-radius: 8px; width: 100%; height: auto;' ) ); ?>
                                </a>
                            <?php endif; ?>
                            
                            <div style="flex: 1;">
                                <span style="color: var(--color-gray-500); font-size: 0.875rem;">
                                    <?php echo get_post_type_object( get_post_type() )->labels->singular_name; ?>
                                    · <?php echo get_the_date(); ?>
                                </span>
                                
                                <h2 style="font-size: 1.25rem; margin: 10px 0;">
                                    <a href="<?php the_permalink(); ?>" style="color: var(--color-dark);">
                                        <?php the_title(); ?>
                                    </a>
                                </h2>
                                
                                <p style="color: var(--color-gray-600); margin: 0;">
                                    <?php echo wp_trim_words( get_the_excerpt(), 40 ); ?>
                                </p>
                            </div>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>
            
            <!-- Pagination -->
            <nav style="margin-top: 40px; text-align: center;">
                <?php
                the_posts_pagination( array(
                    'mid_size'  => 2,
                    'prev_text' => '&laquo; 上一页',
                    'next_text' => '下一页 &raquo;',
                ) );
                ?>
            </nav>
            
        <?php else : ?>
            <div class="text-center" style="padding: 60px 0;">
                <p style="font-size: 1.25rem; color: var(--color-gray-600);">
                    未找到与 "<?php echo esc_html( get_search_query() ); ?>" 相关的内容
                </p>
                <p style="color: var(--color-gray-500); margin-top: 15px;">
                    请尝试使用其他关键词搜索
                </p>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php get_footer(); ?>
