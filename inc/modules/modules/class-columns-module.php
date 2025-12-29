<?php
/**
 * Columns Module - 多列布局
 *
 * @package Developer_Starter
 */

namespace Developer_Starter\Modules\Modules;

use Developer_Starter\Modules\Module_Base;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Columns_Module extends Module_Base {

    public function __construct() {
        $this->category = 'general';
        $this->icon = 'dashicons-columns';
        $this->description = '多列布局模块';
    }

    public function get_id() {
        return 'columns';
    }

    public function get_name() {
        return '多列布局';
    }

    public function render( $data = array() ) {
        $columns = isset( $data['columns_count'] ) ? $data['columns_count'] : '3';
        $items = isset( $data['columns_items'] ) ? $data['columns_items'] : array();
        
        if ( empty( $items ) ) {
            $items = array(
                array( 'title' => '第一列', 'content' => '内容描述', 'image' => '' ),
                array( 'title' => '第二列', 'content' => '内容描述', 'image' => '' ),
                array( 'title' => '第三列', 'content' => '内容描述', 'image' => '' ),
            );
        }
        ?>
        <section class="module module-columns section-padding">
            <div class="container">
                <div class="columns-grid grid-cols-<?php echo esc_attr( $columns ); ?>">
                    <?php foreach ( $items as $item ) : 
                        $item_title = isset( $item['title'] ) ? $item['title'] : '';
                        $item_content = isset( $item['content'] ) ? $item['content'] : '';
                        $item_image = isset( $item['image'] ) ? $item['image'] : '';
                    ?>
                        <div class="column-item" style="background: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
                            <?php if ( $item_image ) : ?>
                                <div class="column-image" style="margin-bottom: 20px;">
                                    <img src="<?php echo esc_url( $item_image ); ?>" alt="<?php echo esc_attr( $item_title ); ?>" style="width: 100%; border-radius: 8px;" />
                                </div>
                            <?php endif; ?>
                            <?php if ( $item_title ) : ?>
                                <h3 style="margin-bottom: 10px;"><?php echo esc_html( $item_title ); ?></h3>
                            <?php endif; ?>
                            <?php if ( $item_content ) : ?>
                                <div style="color: var(--color-gray-600);"><?php echo wp_kses_post( $item_content ); ?></div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
        <?php
    }
}
