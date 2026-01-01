<?php
/**
 * Comparison Module - 比较表格
 *
 * @package Developer_Starter
 */

namespace Developer_Starter\Modules\Modules;

use Developer_Starter\Modules\Module_Base;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Comparison_Module extends Module_Base {

    public function __construct() {
        $this->category = 'general';
        $this->icon = 'dashicons-editor-table';
        $this->description = '产品/服务对比表格';
    }

    public function get_id() {
        return 'comparison';
    }

    public function get_name() {
        return '比较表格';
    }

    public function render( $data = array() ) {
        $title = isset( $data['comparison_title'] ) ? $data['comparison_title'] : '产品对比';
        $subtitle = isset( $data['comparison_subtitle'] ) ? $data['comparison_subtitle'] : '';
        $bg_color = isset( $data['comparison_bg_color'] ) && ! empty( $data['comparison_bg_color'] ) ? $data['comparison_bg_color'] : '';
        $title_color = isset( $data['comparison_title_color'] ) && ! empty( $data['comparison_title_color'] ) ? $data['comparison_title_color'] : '';
        $highlight_col = isset( $data['comparison_highlight'] ) && ! empty( $data['comparison_highlight'] ) ? intval( $data['comparison_highlight'] ) : 0;
        $features = isset( $data['comparison_features'] ) ? $data['comparison_features'] : '';
        $products = isset( $data['comparison_products'] ) ? $data['comparison_products'] : array();
        
        // 解析特性列表
        $feature_list = array();
        if ( ! empty( $features ) ) {
            $feature_list = array_filter( array_map( 'trim', explode( "\n", $features ) ) );
        } else {
            $feature_list = array( '基础功能', '高级功能', '技术支持', 'API接口', '数据导出', '自定义域名' );
        }
        
        // 默认产品数据
        if ( empty( $products ) ) {
            $products = array(
                array( 'name' => '基础版', 'values' => "✓\n✗\n邮件支持\n✗\n✗\n✗" ),
                array( 'name' => '专业版', 'values' => "✓\n✓\n在线客服\n✓\n✓\n✗" ),
                array( 'name' => '企业版', 'values' => "✓\n✓\n7×24专属\n✓\n✓\n✓" ),
            );
        }
        
        // 背景样式
        $bg_style = '';
        if ( ! empty( $bg_color ) ) {
            $bg_style = strpos( $bg_color, 'gradient' ) !== false ? "background: {$bg_color};" : "background-color: {$bg_color};";
        }
        
        $title_style_attr = ! empty( $title_color ) ? "color: {$title_color};" : '';
        $table_id = 'comparison-' . uniqid();
        ?>
        <section class="module module-comparison section-padding" style="<?php echo esc_attr( $bg_style ); ?>">
            <div class="container">
                <?php if ( $title ) : ?>
                    <div class="section-header text-center">
                        <h2 class="section-title" style="<?php echo esc_attr( $title_style_attr ); ?>"><?php echo esc_html( $title ); ?></h2>
                        <?php if ( $subtitle ) : ?>
                            <p class="section-subtitle"><?php echo esc_html( $subtitle ); ?></p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                
                <div id="<?php echo esc_attr( $table_id ); ?>" class="comparison-table-wrapper" style="overflow-x: auto;">
                    <table style="
                        width: 100%;
                        border-collapse: separate;
                        border-spacing: 0;
                        background: #fff;
                        border-radius: 16px;
                        overflow: hidden;
                        box-shadow: 0 10px 40px rgba(0,0,0,0.08);
                    ">
                        <!-- 表头：产品名称 -->
                        <thead>
                            <tr>
                                <th style="
                                    padding: 25px 30px;
                                    text-align: left;
                                    font-weight: 600;
                                    color: #64748b;
                                    background: #f8fafc;
                                    border-bottom: 2px solid #e2e8f0;
                                    min-width: 180px;
                                ">功能特性</th>
                                <?php foreach ( $products as $col_index => $product ) : 
                                    $is_highlight = ( $highlight_col > 0 && $col_index + 1 === $highlight_col );
                                ?>
                                    <th style="
                                        padding: 25px 20px;
                                        text-align: center;
                                        font-weight: 700;
                                        font-size: 1.1rem;
                                        min-width: 150px;
                                        <?php if ( $is_highlight ) : ?>
                                            background: linear-gradient(135deg, #2563eb 0%, #7c3aed 100%);
                                            color: #fff;
                                        <?php else : ?>
                                            background: #f8fafc;
                                            color: #1e293b;
                                            border-bottom: 2px solid #e2e8f0;
                                        <?php endif; ?>
                                    ">
                                        <?php echo esc_html( $product['name'] ); ?>
                                        <?php if ( $is_highlight ) : ?>
                                            <div style="font-size: 0.75rem; font-weight: 500; opacity: 0.9; margin-top: 4px;">推荐</div>
                                        <?php endif; ?>
                                    </th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ( $feature_list as $row_index => $feature ) : ?>
                                <tr style="<?php echo $row_index % 2 === 1 ? 'background: #fafafa;' : ''; ?>">
                                    <td style="
                                        padding: 18px 30px;
                                        font-weight: 500;
                                        color: #334155;
                                        border-bottom: 1px solid #e2e8f0;
                                    "><?php echo esc_html( $feature ); ?></td>
                                    <?php foreach ( $products as $col_index => $product ) : 
                                        $values_str = isset( $product['values'] ) ? $product['values'] : '';
                                        $values_arr = array_map( 'trim', explode( "\n", $values_str ) );
                                        $cell_value = isset( $values_arr[ $row_index ] ) ? $values_arr[ $row_index ] : '';
                                        $is_highlight = ( $highlight_col > 0 && $col_index + 1 === $highlight_col );
                                        
                                        // 格式化显示
                                        $display_value = $cell_value;
                                        $cell_style = 'color: #64748b;';
                                        if ( $cell_value === '✓' || strtolower( $cell_value ) === 'yes' || $cell_value === '是' ) {
                                            $display_value = '✓';
                                            $cell_style = 'color: #10b981; font-size: 1.3rem;';
                                        } elseif ( $cell_value === '✗' || strtolower( $cell_value ) === 'no' || $cell_value === '否' ) {
                                            $display_value = '✗';
                                            $cell_style = 'color: #ef4444; font-size: 1.3rem;';
                                        }
                                    ?>
                                        <td style="
                                            padding: 18px 20px;
                                            text-align: center;
                                            border-bottom: 1px solid #e2e8f0;
                                            <?php echo esc_attr( $cell_style ); ?>
                                            <?php if ( $is_highlight ) : ?>
                                                background: rgba(37, 99, 235, 0.05);
                                            <?php endif; ?>
                                        "><?php echo esc_html( $display_value ); ?></td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
        
        <style>
        #<?php echo esc_attr( $table_id ); ?> table tr:hover td {
            background: rgba(37, 99, 235, 0.03);
        }
        @media (max-width: 768px) {
            #<?php echo esc_attr( $table_id ); ?> th,
            #<?php echo esc_attr( $table_id ); ?> td {
                padding: 12px 15px !important;
                font-size: 0.9rem;
            }
        }
        </style>
        <?php
    }
}
