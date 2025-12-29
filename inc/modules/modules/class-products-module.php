<?php
/**
 * Products Module - 产品中心模块
 *
 * @package Developer_Starter
 * @since 1.0.0
 */

namespace Developer_Starter\Modules\Modules;

use Developer_Starter\Modules\Module_Base;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Products_Module extends Module_Base {

    public function __construct() {
        $this->category = 'content';
        $this->icon = 'dashicons-products';
        $this->description = __( '产品列表展示', 'developer-starter' );
    }

    public function get_id() {
        return 'products';
    }

    public function get_name() {
        return __( '产品列表', 'developer-starter' );
    }

    public function render( $data = array() ) {
        $title = isset( $data['products_title'] ) && $data['products_title'] !== '' ? $data['products_title'] : __( '产品中心', 'developer-starter' );
        $count = isset( $data['products_count'] ) && $data['products_count'] !== '' ? intval( $data['products_count'] ) : 8;
        $columns = isset( $data['products_columns'] ) && $data['products_columns'] !== '' ? $data['products_columns'] : '4';
        $categories = isset( $data['products_categories'] ) ? $data['products_categories'] : '';
        
        // 显示开关 - 默认显示图片，只有明确设置为0时才隐藏
        $show_image = ! isset( $data['products_show_image'] ) || $data['products_show_image'] !== '0';
        $image_height = isset( $data['products_image_height'] ) && $data['products_image_height'] !== '' ? $data['products_image_height'] : '200px';
        
        // 解析分类
        $cat_ids = array();
        if ( ! empty( $categories ) ) {
            $cat_ids = array_map( 'intval', array_filter( explode( ',', $categories ) ) );
        }
        
        // 获取分类信息
        $category_list = array();
        if ( ! empty( $cat_ids ) ) {
            foreach ( $cat_ids as $cat_id ) {
                $cat = get_category( $cat_id );
                if ( $cat && ! is_wp_error( $cat ) ) {
                    $category_list[] = array(
                        'id'   => $cat_id,
                        'name' => $cat->name,
                    );
                }
            }
        }
        
        // 检测 WooCommerce
        $post_type = class_exists( 'WooCommerce' ) ? 'product' : 'post';
        
        $args = array(
            'post_type'      => $post_type,
            'posts_per_page' => $count,
            'post_status'    => 'publish',
        );
        
        if ( ! empty( $cat_ids ) ) {
            $args['cat'] = $cat_ids[0];
        }
        
        $query = new \WP_Query( $args );
        $module_id = 'products-module-' . uniqid();
        ?>
        <section class="module module-products section-padding" id="<?php echo esc_attr( $module_id ); ?>">
            <div class="container">
                <div class="section-header text-center">
                    <h2 class="section-title"><?php echo esc_html( $title ); ?></h2>
                </div>
                
                <?php if ( count( $category_list ) > 1 ) : ?>
                    <div class="category-tabs" style="text-align: center; margin-bottom: 30px;">
                        <?php foreach ( $category_list as $index => $cat ) : ?>
                            <button type="button" 
                                    class="tab-btn <?php echo $index === 0 ? 'active' : ''; ?>" 
                                    data-category="<?php echo esc_attr( $cat['id'] ); ?>"
                                    style="padding: 8px 20px; margin: 5px; border: 1px solid var(--color-primary); background: <?php echo $index === 0 ? 'var(--color-primary)' : 'transparent'; ?>; color: <?php echo $index === 0 ? '#fff' : 'var(--color-primary)'; ?>; border-radius: 20px; cursor: pointer;">
                                <?php echo esc_html( $cat['name'] ); ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php if ( $query->have_posts() ) : ?>
                    <div class="products-grid grid-cols-<?php echo esc_attr( $columns ); ?>">
                        <?php while ( $query->have_posts() ) : $query->the_post(); 
                            // 获取封面图片 - 优先特色图片，其次文章第一张图片
                            $image_url = '';
                            if ( has_post_thumbnail() ) {
                                $image_url = get_the_post_thumbnail_url( get_the_ID(), 'large' );
                            }
                            if ( empty( $image_url ) ) {
                                // 从文章内容中获取第一张图片
                                $post_content = get_the_content();
                                if ( preg_match( '/<img[^>]+src=[\'"]([^\'"]+)[\'"][^>]*>/i', $post_content, $matches ) ) {
                                    $image_url = $matches[1];
                                }
                            }
                            if ( empty( $image_url ) && function_exists( 'developer_starter_get_first_image' ) ) {
                                $image_url = developer_starter_get_first_image( get_the_ID() );
                            }
                        ?>
                            <div class="product-card" style="background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
                                <?php if ( $show_image ) : ?>
                                    <a href="<?php the_permalink(); ?>" class="product-thumb" style="display: block; height: <?php echo esc_attr( $image_height ); ?>; overflow: hidden;">
                                        <?php if ( $image_url ) : ?>
                                            <img src="<?php echo esc_url( $image_url ); ?>" alt="<?php the_title_attribute(); ?>" style="width: 100%; height: 100%; object-fit: cover;" />
                                        <?php else : ?>
                                            <div style="width: 100%; height: 100%; background: linear-gradient(135deg, #f5f7fa 0%, #e4e8eb 100%); display: flex; align-items: center; justify-content: center; color: #999;">
                                                <span class="dashicons dashicons-format-image" style="font-size: 3rem;"></span>
                                            </div>
                                        <?php endif; ?>
                                    </a>
                                <?php endif; ?>
                                
                                <div class="product-info" style="padding: 15px;">
                                    <h3 class="product-title" style="margin: 0; font-size: 1rem;">
                                        <a href="<?php the_permalink(); ?>" style="color: #333; text-decoration: none;"><?php the_title(); ?></a>
                                    </h3>
                                </div>
                            </div>
                        <?php endwhile; wp_reset_postdata(); ?>
                    </div>
                <?php else : ?>
                    <p class="text-center"><?php _e( '暂无产品', 'developer-starter' ); ?></p>
                <?php endif; ?>
            </div>
        </section>
        <?php
    }
}
