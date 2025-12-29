<?php
/**
 * Image Text Module - 图文模块
 *
 * @package Developer_Starter
 */

namespace Developer_Starter\Modules\Modules;

use Developer_Starter\Modules\Module_Base;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Image_Text_Module extends Module_Base {

    public function __construct() {
        $this->category = 'general';
        $this->icon = 'dashicons-align-left';
        $this->description = '图文组合布局';
    }

    public function get_id() {
        return 'image_text';
    }

    public function get_name() {
        return '图文模块';
    }

    public function render( $data = array() ) {
        $layout = isset( $data['image_text_layout'] ) ? $data['image_text_layout'] : 'left';
        $image = isset( $data['image_text_image'] ) ? $data['image_text_image'] : '';
        $title = isset( $data['image_text_title'] ) ? $data['image_text_title'] : '关于我们';
        $content = isset( $data['image_text_content'] ) ? $data['image_text_content'] : '';
        $button = isset( $data['image_text_button'] ) ? $data['image_text_button'] : '';
        $url = isset( $data['image_text_url'] ) ? $data['image_text_url'] : '';
        ?>
        <section class="module module-image-text section-padding">
            <div class="container">
                <div class="image-text-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 60px; align-items: center; <?php echo $layout === 'right' ? 'direction: rtl;' : ''; ?>">
                    <div class="image-text-image" style="<?php echo $layout === 'right' ? 'direction: ltr;' : ''; ?>">
                        <?php if ( $image ) : ?>
                            <img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $title ); ?>" style="width: 100%; border-radius: 12px;" />
                        <?php else : ?>
                            <div style="aspect-ratio: 4/3; background: var(--color-gray-100); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: var(--color-gray-500);">请上传图片</div>
                        <?php endif; ?>
                    </div>
                    <div class="image-text-content" style="<?php echo $layout === 'right' ? 'direction: ltr;' : ''; ?>">
                        <?php if ( $title ) : ?>
                            <h2 style="font-size: 2rem; margin-bottom: 20px;"><?php echo esc_html( $title ); ?></h2>
                        <?php endif; ?>
                        <?php if ( $content ) : ?>
                            <div style="color: var(--color-gray-600); line-height: 1.8; margin-bottom: 30px;"><?php echo wp_kses_post( $content ); ?></div>
                        <?php endif; ?>
                        <?php if ( $button && $url && $url !== '#' ) : ?>
                            <a href="<?php echo esc_attr( $url ); ?>" class="btn btn-primary"><?php echo esc_html( $button ); ?></a>
                        <?php elseif ( $button ) : ?>
                            <a href="#" class="btn btn-primary"><?php echo esc_html( $button ); ?></a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>
        <?php
    }
}
