<?php
/**
 * Timeline Module - 时间轴
 *
 * @package Developer_Starter
 */

namespace Developer_Starter\Modules\Modules;

use Developer_Starter\Modules\Module_Base;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Timeline_Module extends Module_Base {

    public function __construct() {
        $this->category = 'homepage';
        $this->icon = 'dashicons-backup';
        $this->description = '发展历程展示';
    }

    public function get_id() {
        return 'timeline';
    }

    public function get_name() {
        return '时间轴';
    }

    public function render( $data = array() ) {
        $title = isset( $data['timeline_title'] ) && $data['timeline_title'] !== '' ? $data['timeline_title'] : '发展历程';
        $items = isset( $data['timeline_items'] ) ? $data['timeline_items'] : array();
        
        if ( empty( $items ) ) {
            $items = array(
                array( 'year' => '2020', 'title' => '公司成立', 'desc' => '正式成立，开始创业之旅' ),
                array( 'year' => '2021', 'title' => '业务扩展', 'desc' => '团队规模扩大至50人' ),
                array( 'year' => '2022', 'title' => '产品升级', 'desc' => '发布2.0版本产品' ),
                array( 'year' => '2023', 'title' => '全国布局', 'desc' => '业务覆盖全国20个省市' ),
            );
        }
        ?>
        <section class="module module-timeline section-padding">
            <div class="container">
                <div class="section-header text-center">
                    <h2 class="section-title"><?php echo esc_html( $title ); ?></h2>
                </div>
                
                <div class="timeline" style="position: relative; max-width: 800px; margin: 0 auto;">
                    <div style="position: absolute; left: 50%; top: 0; bottom: 0; width: 2px; background: var(--color-primary); transform: translateX(-50%);"></div>
                    <?php foreach ( $items as $i => $item ) : 
                        $year = isset( $item['year'] ) ? $item['year'] : '';
                        $item_title = isset( $item['title'] ) ? $item['title'] : '';
                        $desc = isset( $item['desc'] ) ? $item['desc'] : '';
                        $is_left = $i % 2 === 0;
                    ?>
                        <div class="timeline-item" style="display: flex; margin-bottom: 40px; <?php echo $is_left ? '' : 'flex-direction: row-reverse;'; ?>">
                            <div style="flex: 1; text-align: <?php echo $is_left ? 'right' : 'left'; ?>; padding: <?php echo $is_left ? '0 30px 0 0' : '0 0 0 30px'; ?>;">
                                <div style="font-size: 1.5rem; font-weight: 700; color: var(--color-primary);"><?php echo esc_html( $year ); ?></div>
                                <h3 style="margin: 5px 0;"><?php echo esc_html( $item_title ); ?></h3>
                                <p style="color: var(--color-gray-600); margin: 0;"><?php echo esc_html( $desc ); ?></p>
                            </div>
                            <div style="width: 20px; height: 20px; background: var(--color-primary); border-radius: 50%; flex-shrink: 0; position: relative; z-index: 1;"></div>
                            <div style="flex: 1;"></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
        <?php
    }
}
