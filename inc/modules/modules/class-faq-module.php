<?php
/**
 * FAQ Module - 常见问题
 *
 * @package Developer_Starter
 */

namespace Developer_Starter\Modules\Modules;

use Developer_Starter\Modules\Module_Base;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class FAQ_Module extends Module_Base {

    public function __construct() {
        $this->category = 'homepage';
        $this->icon = 'dashicons-editor-help';
        $this->description = '常见问题解答';
    }

    public function get_id() {
        return 'faq';
    }

    public function get_name() {
        return '常见问题';
    }

    public function render( $data = array() ) {
        $title = isset( $data['faq_title'] ) && $data['faq_title'] !== '' ? $data['faq_title'] : '常见问题';
        $items = isset( $data['faq_items'] ) ? $data['faq_items'] : array();
        
        if ( empty( $items ) ) {
            $items = array(
                array( 'question' => '你们的服务范围是什么？', 'answer' => '我们提供全国范围内的服务，包括产品研发、技术咨询、解决方案定制等。' ),
                array( 'question' => '如何与你们取得联系？', 'answer' => '您可以通过页面底部的联系方式与我们取得联系，或直接拨打客服热线。' ),
                array( 'question' => '付款方式有哪些？', 'answer' => '我们支持对公转账、支付宝、微信等多种付款方式。' ),
                array( 'question' => '售后服务如何保障？', 'answer' => '我们提供7x24小时技术支持，并有完善的售后服务体系。' ),
            );
        }
        ?>
        <section class="module module-faq section-padding">
            <div class="container">
                <div class="section-header text-center">
                    <h2 class="section-title"><?php echo esc_html( $title ); ?></h2>
                </div>
                
                <div class="faq-list" style="max-width: 800px; margin: 0 auto;">
                    <?php foreach ( $items as $item ) : 
                        $question = isset( $item['question'] ) ? $item['question'] : '';
                        $answer = isset( $item['answer'] ) ? $item['answer'] : '';
                    ?>
                        <div class="faq-item" style="border: 1px solid #e5e7eb; border-radius: 8px; margin-bottom: 10px; overflow: hidden;">
                            <button type="button" class="faq-question" style="width: 100%; display: flex; justify-content: space-between; align-items: center; padding: 15px 20px; background: #fff; border: none; cursor: pointer; text-align: left; font-size: 1rem; font-weight: 600;">
                                <span><?php echo esc_html( $question ); ?></span>
                                <span class="faq-icon">+</span>
                            </button>
                            <div class="faq-answer" style="display: none; padding: 0 20px 20px; background: #fff;">
                                <p style="margin: 0; color: var(--color-gray-600);"><?php echo wp_kses_post( $answer ); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
        <?php
    }
}
