<?php
/**
 * Stats Module - 数据统计
 *
 * @package Developer_Starter
 */

namespace Developer_Starter\Modules\Modules;

use Developer_Starter\Modules\Module_Base;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Stats_Module extends Module_Base {

    public function __construct() {
        $this->category = 'homepage';
        $this->icon = 'dashicons-chart-bar';
        $this->description = '数据统计展示';
    }

    public function get_id() {
        return 'stats';
    }

    public function get_name() {
        return '数据统计';
    }

    public function get_fields() {
        return array(
            array(
                'id' => 'stats_bg_image',
                'label' => '背景图片',
                'type' => 'image',
            ),
            array(
                'id' => 'stats_text_align',
                'label' => '文字位置',
                'type' => 'select',
                'options' => array(
                    'left' => '左对齐',
                    'center' => '居中',
                    'right' => '右对齐',
                ),
                'default' => 'center',
            ),
            array(
                'id' => 'stats_items',
                'label' => '统计项',
                'type' => 'repeater',
                'fields' => array(
                    array( 'id' => 'number', 'label' => '数字', 'type' => 'text' ),
                    array( 'id' => 'label', 'label' => '标签', 'type' => 'text' ),
                ),
            ),
        );
    }

    public function render( $data = array() ) {
        $bg_image = isset( $data['stats_bg_image'] ) ? $data['stats_bg_image'] : '';
        $text_align = isset( $data['stats_text_align'] ) ? $data['stats_text_align'] : 'center';
        $items = isset( $data['stats_items'] ) ? $data['stats_items'] : array();
        
        if ( empty( $items ) ) {
            $items = array(
                array( 'number' => '500', 'label' => '服务客户' ),
                array( 'number' => '10', 'label' => '年行业经验' ),
                array( 'number' => '50', 'label' => '专业团队' ),
                array( 'number' => '99', 'label' => '客户满意度' ),
            );
        }
        
        // Grid alignment based on text align
        $justify = 'center';
        if ( $text_align === 'left' ) $justify = 'flex-start';
        if ( $text_align === 'right' ) $justify = 'flex-end';
        
        $bg_style = $bg_image 
            ? "background-image: url('" . esc_url( $bg_image ) . "'); background-size: cover; background-position: center; background-attachment: fixed;" 
            : "background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-dark) 100%);";
        ?>
        <section class="module module-stats" style="<?php echo $bg_style; ?> position: relative;">
            <div style="position: absolute; inset: 0; background: rgba(0,0,0,0.5);"></div>
            <div class="container" style="position: relative; z-index: 1; padding: 80px 0;">
                <div class="stats-grid" style="display: flex; flex-wrap: wrap; gap: 30px; justify-content: <?php echo esc_attr( $justify ); ?>; text-align: <?php echo esc_attr( $text_align ); ?>;">
                    <?php foreach ( $items as $item ) : 
                        $number = isset( $item['number'] ) ? $item['number'] : '0';
                        $label = isset( $item['label'] ) ? $item['label'] : '';
                    ?>
                        <div class="stat-item" style="min-width: 180px; padding: 20px;">
                            <div class="stat-number" style="font-size: 3.5rem; font-weight: 700; color: #fff;">
                                <?php echo esc_html( $number ); ?>+
                            </div>
                            <div class="stat-label" style="color: rgba(255,255,255,0.85); font-size: 1rem; margin-top: 10px;">
                                <?php echo esc_html( $label ); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
        <?php
    }
}
