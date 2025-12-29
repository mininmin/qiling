<?php
/**
 * Template Name: 案例展示
 *
 * @package Developer_Starter
 */

get_header();

// 获取设置
$category = developer_starter_get_option( 'cases_category', '' );
$per_page = developer_starter_get_option( 'cases_per_page', 9 );
$columns = developer_starter_get_option( 'cases_columns', '3' );
$thumb_height = developer_starter_get_option( 'cases_thumb_height', 220 );
$show_title = developer_starter_get_option( 'cases_show_title', '1' );
$show_date = developer_starter_get_option( 'cases_show_date', '' );

$paged = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;

// 查询参数
$args = array(
    'post_type'      => 'post',
    'posts_per_page' => intval( $per_page ),
    'paged'          => $paged,
);
if ( $category ) {
    $args['category_name'] = $category;
}

$cases_query = new WP_Query( $args );

// 列数样式
$grid_columns = intval( $columns );
$min_width = $grid_columns == 4 ? '250px' : ( $grid_columns == 2 ? '450px' : '350px' );
?>

<div class="page-header" style="background: linear-gradient(135deg, var(--color-primary) 0%, #7c3aed 100%); padding: 100px 0 60px;">
    <div class="container">
        <h1 class="page-title" style="color: #fff; text-align: center; font-size: 2.5rem; margin: 0;">
            <?php the_title(); ?>
        </h1>
        <p style="text-align: center; color: rgba(255,255,255,0.8); margin-top: 15px; font-size: 1.1rem;">
            精选客户成功案例
        </p>
    </div>
</div>

<div class="page-content section-padding">
    <div class="container">
        
        <?php if ( $cases_query->have_posts() ) : ?>
            
            <div class="cases-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(<?php echo $min_width; ?>, 1fr)); gap: 30px;">
                
                <?php while ( $cases_query->have_posts() ) : $cases_query->the_post(); ?>
                    <article class="case-card" style="position: relative; border-radius: 20px; overflow: hidden; box-shadow: 0 20px 50px rgba(0,0,0,0.1);">
                        
                        <?php if ( has_post_thumbnail() ) : ?>
                            <div class="case-image" style="height: <?php echo intval( $thumb_height ); ?>px; overflow: hidden;">
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_post_thumbnail( 'large', array( 'style' => 'width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s;' ) ); ?>
                                </a>
                            </div>
                        <?php else : ?>
                            <div style="height: <?php echo intval( $thumb_height ); ?>px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);"></div>
                        <?php endif; ?>
                        
                        <div class="case-overlay" style="position: absolute; bottom: 0; left: 0; right: 0; padding: 30px; background: linear-gradient(to top, rgba(0,0,0,0.9) 0%, transparent 100%);">
                            <?php if ( $show_title ) : ?>
                                <h3 style="font-size: 1.25rem; color: #fff; margin-bottom: 10px;">
                                    <a href="<?php the_permalink(); ?>" style="color: #fff;"><?php the_title(); ?></a>
                                </h3>
                            <?php endif; ?>
                            
                            <?php if ( $show_date ) : ?>
                                <p style="color: rgba(255,255,255,0.7); font-size: 0.85rem; margin-bottom: 10px;"><?php echo get_the_date(); ?></p>
                            <?php endif; ?>
                            
                            <a href="<?php the_permalink(); ?>" style="color: #fff; font-weight: 500; font-size: 0.9rem; display: inline-flex; align-items: center; gap: 5px;">
                                查看案例 <span>→</span>
                            </a>
                        </div>
                        
                    </article>
                <?php endwhile; ?>
                
            </div>
            
            <div class="pagination" style="margin-top: 50px; text-align: center;">
                <?php
                echo paginate_links( array(
                    'total'   => $cases_query->max_num_pages,
                    'current' => $paged,
                    'prev_text' => '&laquo; 上一页',
                    'next_text' => '下一页 &raquo;',
                ) );
                ?>
            </div>
            
            <?php wp_reset_postdata(); ?>
            
        <?php else : ?>
            
            <div style="text-align: center; padding: 80px 20px;">
                <div style="font-size: 4rem; margin-bottom: 20px;">🏆</div>
                <h2 style="color: #64748b; font-weight: 400;">暂无案例</h2>
                <p style="color: #94a3b8;">请先在后台添加案例内容<?php echo $category ? '（分类：' . esc_html( $category ) . '）' : ''; ?></p>
            </div>
            
        <?php endif; ?>
        
    </div>
</div>

<?php get_footer(); ?>
