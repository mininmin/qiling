<?php
/**
 * Clients Module - 合作客户
 *
 * @package Developer_Starter
 */

namespace Developer_Starter\Modules\Modules;

use Developer_Starter\Modules\Module_Base;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Clients_Module extends Module_Base {

    public function __construct() {
        $this->category = 'homepage';
        $this->icon = 'dashicons-groups';
        $this->description = '合作客户展示';
    }

    public function get_id() {
        return 'clients';
    }

    public function get_name() {
        return '合作客户';
    }

    public function render( $data = array() ) {
        $title = isset( $data['clients_title'] ) && $data['clients_title'] !== '' ? $data['clients_title'] : '合作客户';
        $items = isset( $data['clients_items'] ) ? $data['clients_items'] : array();
        
        if ( empty( $items ) ) {
            $items = array(
                array( 'name' => '客户A公司', 'logo' => '' ),
                array( 'name' => '客户B集团', 'logo' => '' ),
                array( 'name' => '客户C科技', 'logo' => '' ),
                array( 'name' => '客户D企业', 'logo' => '' ),
                array( 'name' => '客户E控股', 'logo' => '' ),
                array( 'name' => '客户F集团', 'logo' => '' ),
            );
        }
        ?>
        <section class="module module-clients section-padding bg-light">
            <div class="container">
                <div class="section-header text-center">
                    <h2 class="section-title"><?php echo esc_html( $title ); ?></h2>
                </div>
                
                <?php if ( ! empty( $items ) ) : ?>
                    <div class="clients-grid" style="display: grid; grid-template-columns: repeat(6, 1fr); gap: 30px; align-items: center;">
                        <?php foreach ( $items as $item ) : 
                            $logo = isset( $item['logo'] ) ? $item['logo'] : '';
                            $name = isset( $item['name'] ) ? $item['name'] : '';
                        ?>
                            <div class="client-item" style="display: flex; align-items: center; justify-content: center; padding: 20px; background: #fff; border-radius: 8px; min-height: 80px;">
                                <?php if ( $logo ) : ?>
                                    <img src="<?php echo esc_url( $logo ); ?>" alt="<?php echo esc_attr( $name ); ?>" style="max-width: 100%; max-height: 50px; object-fit: contain;" />
                                <?php else : ?>
                                    <span style="color: var(--color-gray-500); font-weight: 500;"><?php echo esc_html( $name ); ?></span>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>
        <?php
    }
}
