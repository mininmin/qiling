<?php
/**
 * CTA Module - 行动号召
 *
 * @package Developer_Starter
 */

namespace Developer_Starter\Modules\Modules;

use Developer_Starter\Modules\Module_Base;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class CTA_Module extends Module_Base {

    public function __construct() {
        $this->category = 'homepage';
        $this->icon = 'dashicons-megaphone';
        $this->description = '行动号召模块';
    }

    public function get_id() {
        return 'cta';
    }

    public function get_name() {
        return 'CTA按钮';
    }

    public function render( $data = array() ) {
        $title = isset( $data['cta_title'] ) && $data['cta_title'] !== '' ? $data['cta_title'] : '准备好开始了吗？';
        $subtitle = isset( $data['cta_subtitle'] ) ? $data['cta_subtitle'] : '立即联系我们，获取专业方案和报价';
        $btn_text = isset( $data['cta_button_text'] ) && $data['cta_button_text'] !== '' ? $data['cta_button_text'] : '免费咨询';
        $btn_url = isset( $data['cta_button_url'] ) ? $data['cta_button_url'] : '#';
        ?>
        <section class="module module-cta" style="background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-dark) 100%); padding: 80px 0;">
            <div class="container text-center">
                <h2 style="color: #fff; font-size: 2.5rem; margin-bottom: 15px;"><?php echo esc_html( $title ); ?></h2>
                <?php if ( $subtitle ) : ?>
                    <p style="color: rgba(255,255,255,0.85); font-size: 1.25rem; margin-bottom: 30px;"><?php echo esc_html( $subtitle ); ?></p>
                <?php endif; ?>
                <a href="<?php echo esc_url( $btn_url ?: '#' ); ?>" class="btn btn-light btn-lg" style="background: #fff; color: var(--color-primary); padding: 14px 40px; font-size: 1.125rem;">
                    <?php echo esc_html( $btn_text ); ?>
                </a>
            </div>
        </section>
        <?php
    }
}
