<?php
/**
 * Gallery Module - 画廊/相册
 *
 * @package Developer_Starter
 */

namespace Developer_Starter\Modules\Modules;

use Developer_Starter\Modules\Module_Base;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Gallery_Module extends Module_Base {

    public function __construct() {
        $this->category = 'general';
        $this->icon = 'dashicons-format-gallery';
        $this->description = '图片画廊/相册展示';
    }

    public function get_id() {
        return 'gallery';
    }

    public function get_name() {
        return '画廊相册';
    }

    public function get_fields() {
        return array(
            array( 'id' => 'gallery_title', 'label' => '标题', 'type' => 'text', 'default' => '图片展示' ),
            array( 'id' => 'gallery_subtitle', 'label' => '副标题', 'type' => 'text', 'default' => '' ),
            array( 'id' => 'gallery_bg_color', 'label' => '背景颜色', 'type' => 'text', 'description' => '支持渐变色' ),
            array( 'id' => 'gallery_title_color', 'label' => '标题颜色', 'type' => 'color' ),
            array( 'id' => 'gallery_columns', 'label' => '每行列数', 'type' => 'select', 'options' => array( '2' => '2列', '3' => '3列', '4' => '4列', '5' => '5列' ), 'default' => '4' ),
            array( 'id' => 'gallery_style', 'label' => '展示样式', 'type' => 'select', 'options' => array( 'grid' => '网格布局', 'masonry' => '瀑布流' ), 'default' => 'grid' ),
            array( 'id' => 'gallery_gap', 'label' => '图片间距(px)', 'type' => 'number', 'default' => '15' ),
            array( 'id' => 'gallery_lightbox', 'label' => '点击放大', 'type' => 'checkbox', 'default' => '1' ),
            array(
                'id' => 'gallery_images',
                'label' => '图片列表',
                'type' => 'repeater',
                'description' => '添加需要展示的图片',
                'fields' => array(
                    array( 'id' => 'image', 'label' => '图片', 'type' => 'text' ),
                    array( 'id' => 'title', 'label' => '标题', 'type' => 'text' ),
                    array( 'id' => 'desc', 'label' => '描述', 'type' => 'text' ),
                ),
            ),
        );
    }

    public function render( $data = array() ) {
        $title = isset( $data['gallery_title'] ) && $data['gallery_title'] !== '' ? $data['gallery_title'] : '图片展示';
        $subtitle = isset( $data['gallery_subtitle'] ) ? $data['gallery_subtitle'] : '';
        $bg_color = isset( $data['gallery_bg_color'] ) && ! empty( $data['gallery_bg_color'] ) ? $data['gallery_bg_color'] : '';
        $title_color = isset( $data['gallery_title_color'] ) && ! empty( $data['gallery_title_color'] ) ? $data['gallery_title_color'] : '';
        $columns = isset( $data['gallery_columns'] ) && ! empty( $data['gallery_columns'] ) ? intval( $data['gallery_columns'] ) : 4;
        $style = isset( $data['gallery_style'] ) ? $data['gallery_style'] : 'grid';
        $gap = isset( $data['gallery_gap'] ) && $data['gallery_gap'] !== '' ? intval( $data['gallery_gap'] ) : 15;
        $lightbox = isset( $data['gallery_lightbox'] ) ? $data['gallery_lightbox'] : '1';
        $images = isset( $data['gallery_images'] ) ? $data['gallery_images'] : array();
        
        // 默认示例数据
        if ( empty( $images ) ) {
            $images = array(
                array( 'image' => '', 'title' => '产品展示', 'desc' => '' ),
                array( 'image' => '', 'title' => '办公环境', 'desc' => '' ),
                array( 'image' => '', 'title' => '团队活动', 'desc' => '' ),
                array( 'image' => '', 'title' => '荣誉资质', 'desc' => '' ),
            );
        }
        
        // 背景样式
        $bg_style = '';
        if ( ! empty( $bg_color ) ) {
            $bg_style = strpos( $bg_color, 'gradient' ) !== false ? "background: {$bg_color};" : "background-color: {$bg_color};";
        }
        
        $title_style = ! empty( $title_color ) ? "color: {$title_color};" : '';
        $gallery_id = 'gallery-' . uniqid();
        ?>
        <section class="module module-gallery section-padding" style="<?php echo esc_attr( $bg_style ); ?>">
            <div class="container">
                <?php if ( $title ) : ?>
                    <div class="section-header text-center">
                        <h2 class="section-title" style="<?php echo esc_attr( $title_style ); ?>"><?php echo esc_html( $title ); ?></h2>
                        <?php if ( $subtitle ) : ?>
                            <p class="section-subtitle"><?php echo esc_html( $subtitle ); ?></p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                
                <?php if ( ! empty( $images ) ) : ?>
                    <div id="<?php echo esc_attr( $gallery_id ); ?>" class="gallery-grid gallery-<?php echo esc_attr( $style ); ?>" style="
                        display: grid;
                        grid-template-columns: repeat(<?php echo esc_attr( $columns ); ?>, 1fr);
                        gap: <?php echo esc_attr( $gap ); ?>px;
                    ">
                        <?php foreach ( $images as $index => $item ) : 
                            $image = isset( $item['image'] ) ? $item['image'] : '';
                            $img_title = isset( $item['title'] ) ? $item['title'] : '';
                            $desc = isset( $item['desc'] ) ? $item['desc'] : '';
                            
                            // 默认占位背景
                            $placeholder_colors = array(
                                'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
                                'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)',
                                'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)',
                                'linear-gradient(135deg, #43e97b 0%, #38f9d7 100%)',
                            );
                            $placeholder_bg = $placeholder_colors[ $index % count( $placeholder_colors ) ];
                        ?>
                            <div class="gallery-item" style="
                                position: relative;
                                overflow: hidden;
                                border-radius: 12px;
                                <?php echo $style === 'grid' ? 'aspect-ratio: 1;' : ''; ?>
                                cursor: <?php echo $lightbox === '1' ? 'zoom-in' : 'default'; ?>;
                            " <?php echo $lightbox === '1' ? 'data-lightbox="' . esc_attr( $image ) . '"' : ''; ?>>
                                <?php if ( $image ) : ?>
                                    <img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $img_title ); ?>" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s;" loading="lazy" />
                                <?php else : ?>
                                    <div style="width: 100%; height: 100%; min-height: 200px; background: <?php echo esc_attr( $placeholder_bg ); ?>; display: flex; align-items: center; justify-content: center;">
                                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,0.5)" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- 悬停遮罩 -->
                                <?php if ( $img_title || $desc ) : ?>
                                    <div class="gallery-overlay" style="
                                        position: absolute;
                                        inset: 0;
                                        background: linear-gradient(to top, rgba(0,0,0,0.7) 0%, transparent 100%);
                                        display: flex;
                                        flex-direction: column;
                                        justify-content: flex-end;
                                        padding: 20px;
                                        opacity: 0;
                                        transition: opacity 0.3s;
                                    ">
                                        <?php if ( $img_title ) : ?>
                                            <h4 style="color: #fff; margin: 0 0 5px; font-size: 1rem; font-weight: 600;"><?php echo esc_html( $img_title ); ?></h4>
                                        <?php endif; ?>
                                        <?php if ( $desc ) : ?>
                                            <p style="color: rgba(255,255,255,0.8); margin: 0; font-size: 0.85rem;"><?php echo esc_html( $desc ); ?></p>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>
        
        <!-- Lightbox Modal -->
        <?php if ( $lightbox === '1' ) : ?>
        <div id="<?php echo esc_attr( $gallery_id ); ?>-lightbox" class="gallery-lightbox" style="
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.95);
            z-index: 99999;
            align-items: center;
            justify-content: center;
            cursor: zoom-out;
        ">
            <img src="" alt="" style="max-width: 90%; max-height: 90%; object-fit: contain;" />
            <button type="button" style="position: absolute; top: 20px; right: 20px; background: none; border: none; color: #fff; font-size: 2rem; cursor: pointer;">&times;</button>
        </div>
        <?php endif; ?>
        
        <style>
        .gallery-item:hover img {
            transform: scale(1.1);
        }
        .gallery-item:hover .gallery-overlay {
            opacity: 1 !important;
        }
        @media (max-width: 991px) {
            #<?php echo esc_attr( $gallery_id ); ?> {
                grid-template-columns: repeat(3, 1fr) !important;
            }
        }
        @media (max-width: 768px) {
            #<?php echo esc_attr( $gallery_id ); ?> {
                grid-template-columns: repeat(2, 1fr) !important;
            }
        }
        </style>
        
        <?php if ( $lightbox === '1' ) : ?>
        <script>
        (function() {
            var galleryId = '<?php echo esc_js( $gallery_id ); ?>';
            var gallery = document.getElementById(galleryId);
            var lightbox = document.getElementById(galleryId + '-lightbox');
            if (!gallery || !lightbox) return;
            
            gallery.querySelectorAll('[data-lightbox]').forEach(function(item) {
                item.addEventListener('click', function() {
                    var src = this.getAttribute('data-lightbox');
                    if (src) {
                        lightbox.querySelector('img').src = src;
                        lightbox.style.display = 'flex';
                        document.body.style.overflow = 'hidden';
                    }
                });
            });
            
            lightbox.addEventListener('click', function() {
                this.style.display = 'none';
                document.body.style.overflow = '';
            });
        })();
        </script>
        <?php endif; ?>
        <?php
    }
}
