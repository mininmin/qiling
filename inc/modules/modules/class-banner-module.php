<?php
/**
 * Banner Module - 首屏Banner
 *
 * @package Developer_Starter
 */

namespace Developer_Starter\Modules\Modules;

use Developer_Starter\Modules\Module_Base;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Banner_Module extends Module_Base {

    public function __construct() {
        $this->category = 'homepage';
        $this->icon = 'dashicons-format-image';
        $this->description = '首屏Banner模块';
    }

    public function get_id() {
        return 'banner';
    }

    public function get_name() {
        return '首屏Banner';
    }

    public function render( $data = array() ) {
        $layout = isset( $data['banner_layout'] ) ? $data['banner_layout'] : 'slider';
        $height = isset( $data['banner_height'] ) ? $data['banner_height'] : 'full';
        $slides = isset( $data['banner_slides'] ) ? $data['banner_slides'] : array();
        $image_position = isset( $data['banner_image_position'] ) ? $data['banner_image_position'] : 'right';
        
        if ( empty( $slides ) ) {
            $slides = array(
                array(
                    'image'    => '',
                    'title'    => __( '专业企业解决方案', 'developer-starter' ),
                    'subtitle' => __( '助力企业数字化转型，提供一站式服务', 'developer-starter' ),
                    'btn_text' => __( '了解更多', 'developer-starter' ),
                    'btn_url'  => '#',
                ),
            );
        }
        
        $height_class = 'banner-height-' . $height;
        
        if ( $layout === 'image_text' ) {
            $this->render_image_text_layout( $slides, $image_position, $height_class );
        } else {
            $this->render_slider_layout( $slides, $height_class );
        }
    }
    
    private function render_slider_layout( $slides, $height_class ) {
        ?>
        <section class="module module-banner banner-slider <?php echo esc_attr( $height_class ); ?>">
            <?php if ( count( $slides ) > 1 ) : ?>
                <div class="swiper banner-swiper">
                    <div class="swiper-wrapper">
                        <?php foreach ( $slides as $slide ) : ?>
                            <div class="swiper-slide" style="background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-dark) 100%); <?php echo ! empty( $slide['image'] ) ? 'background-image: url(' . esc_url( $slide['image'] ) . '); background-size: cover; background-position: center;' : ''; ?>">
                                <div class="banner-overlay"></div>
                                <div class="container">
                                    <div class="banner-content">
                                        <?php if ( ! empty( $slide['title'] ) ) : ?>
                                            <h1 class="banner-title"><?php echo esc_html( $slide['title'] ); ?></h1>
                                        <?php endif; ?>
                                        <?php if ( ! empty( $slide['subtitle'] ) ) : ?>
                                            <p class="banner-subtitle"><?php echo esc_html( $slide['subtitle'] ); ?></p>
                                        <?php endif; ?>
                                        <?php if ( ! empty( $slide['btn_text'] ) ) : ?>
                                            <div class="banner-buttons">
                                                <a href="<?php echo esc_url( $slide['btn_url'] ?: '#' ); ?>" class="btn btn-primary btn-lg">
                                                    <?php echo esc_html( $slide['btn_text'] ); ?>
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="swiper-pagination"></div>
                    <div class="swiper-button-prev"></div>
                    <div class="swiper-button-next"></div>
                </div>
            <?php else : 
                $slide = $slides[0];
            ?>
                <div class="banner-single" style="background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-dark) 100%); <?php echo ! empty( $slide['image'] ) ? 'background-image: url(' . esc_url( $slide['image'] ) . '); background-size: cover; background-position: center;' : ''; ?>">
                    <div class="banner-overlay"></div>
                    <div class="container">
                        <div class="banner-content">
                            <?php if ( ! empty( $slide['title'] ) ) : ?>
                                <h1 class="banner-title"><?php echo esc_html( $slide['title'] ); ?></h1>
                            <?php endif; ?>
                            <?php if ( ! empty( $slide['subtitle'] ) ) : ?>
                                <p class="banner-subtitle"><?php echo esc_html( $slide['subtitle'] ); ?></p>
                            <?php endif; ?>
                            <?php if ( ! empty( $slide['btn_text'] ) ) : ?>
                                <div class="banner-buttons">
                                    <a href="<?php echo esc_url( $slide['btn_url'] ?: '#' ); ?>" class="btn btn-primary btn-lg">
                                        <?php echo esc_html( $slide['btn_text'] ); ?>
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </section>
        <?php
    }
    
    private function render_image_text_layout( $slides, $image_position, $height_class ) {
        $slide = is_array( $slides ) && ! empty( $slides ) ? $slides[0] : array();
        $title = isset( $slide['title'] ) ? $slide['title'] : __( '专业企业解决方案', 'developer-starter' );
        $subtitle = isset( $slide['subtitle'] ) ? $slide['subtitle'] : __( '助力企业数字化转型', 'developer-starter' );
        $btn_text = isset( $slide['btn_text'] ) ? $slide['btn_text'] : __( '了解更多', 'developer-starter' );
        $btn_url = isset( $slide['btn_url'] ) ? $slide['btn_url'] : '#';
        $image = isset( $slide['image'] ) ? $slide['image'] : '';
        ?>
        <section class="module module-banner banner-image-text <?php echo esc_attr( $height_class ); ?>" style="background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-dark) 100%);">
            <div class="container">
                <div class="banner-flex" style="display: flex; align-items: center; min-height: 120vh; gap: 60px; flex-direction: <?php echo $image_position === 'left' ? 'row-reverse' : 'row'; ?>;">
                    <div class="banner-text" style="flex: 1;">
                        <h1 class="banner-title" style="color: #fff; font-size: 3rem; font-weight: 700; line-height: 1.2; margin-bottom: 20px;">
                            <?php echo esc_html( $title ); ?>
                        </h1>
                        <p class="banner-subtitle" style="color: rgba(255,255,255,0.85); font-size: 1.25rem; margin-bottom: 30px;">
                            <?php echo esc_html( $subtitle ); ?>
                        </p>
                        <?php if ( $btn_text ) : ?>
                            <a href="<?php echo esc_url( $btn_url ); ?>" class="btn btn-light btn-lg" style="background: #fff; color: var(--color-primary); padding: 14px 32px;">
                                <?php echo esc_html( $btn_text ); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                    <div class="banner-image" style="flex: 1;">
                        <?php if ( $image ) : ?>
                            <img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $title ); ?>" style="max-width: 100%; border-radius: 12px; box-shadow: 0 20px 40px rgba(0,0,0,0.2);" />
                        <?php else : ?>
                            <div style="aspect-ratio: 4/3; background: rgba(255,255,255,0.1); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: rgba(255,255,255,0.5);">
                                <?php esc_html_e( '请上传图片', 'developer-starter' ); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>
        <?php
    }
}
