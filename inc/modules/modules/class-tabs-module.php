<?php
/**
 * Tabs Module - 标签页切换
 *
 * @package Developer_Starter
 */

namespace Developer_Starter\Modules\Modules;

use Developer_Starter\Modules\Module_Base;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Tabs_Module extends Module_Base {

    public function __construct() {
        $this->category = 'general';
        $this->icon = 'dashicons-index-card';
        $this->description = '多标签页内容切换';
    }

    public function get_id() {
        return 'tabs';
    }

    public function get_name() {
        return '标签切换';
    }

    public function get_fields() {
        return array(
            array( 'id' => 'tabs_title', 'label' => '标题', 'type' => 'text', 'default' => '' ),
            array( 'id' => 'tabs_subtitle', 'label' => '副标题', 'type' => 'text', 'default' => '' ),
            array( 'id' => 'tabs_bg_color', 'label' => '背景颜色', 'type' => 'text', 'description' => '支持渐变色' ),
            array( 'id' => 'tabs_title_color', 'label' => '标题颜色', 'type' => 'color' ),
            array( 'id' => 'tabs_style', 'label' => '标签样式', 'type' => 'select', 'options' => array( 
                'default' => '默认样式', 
                'pills' => '胶囊样式', 
                'underline' => '下划线样式',
                'boxed' => '卡片样式',
            ), 'default' => 'default' ),
            array( 'id' => 'tabs_align', 'label' => '标签对齐', 'type' => 'select', 'options' => array( 
                'left' => '左对齐', 
                'center' => '居中', 
                'right' => '右对齐',
            ), 'default' => 'center' ),
            array(
                'id' => 'tabs_items',
                'label' => '标签页',
                'type' => 'repeater',
                'description' => '添加标签页，内容支持HTML',
                'fields' => array(
                    array( 'id' => 'title', 'label' => '标签标题', 'type' => 'text' ),
                    array( 'id' => 'icon', 'label' => '图标(emoji或留空)', 'type' => 'text' ),
                    array( 'id' => 'content', 'label' => '标签内容(支持HTML)', 'type' => 'textarea' ),
                ),
            ),
        );
    }

    public function render( $data = array() ) {
        $title = isset( $data['tabs_title'] ) ? $data['tabs_title'] : '';
        $subtitle = isset( $data['tabs_subtitle'] ) ? $data['tabs_subtitle'] : '';
        $bg_color = isset( $data['tabs_bg_color'] ) && ! empty( $data['tabs_bg_color'] ) ? $data['tabs_bg_color'] : '';
        $title_color = isset( $data['tabs_title_color'] ) && ! empty( $data['tabs_title_color'] ) ? $data['tabs_title_color'] : '';
        $style = isset( $data['tabs_style'] ) ? $data['tabs_style'] : 'default';
        $align = isset( $data['tabs_align'] ) ? $data['tabs_align'] : 'center';
        $items = isset( $data['tabs_items'] ) ? $data['tabs_items'] : array();
        
        // 默认示例数据
        if ( empty( $items ) ) {
            $items = array(
                array( 
                    'title' => '产品介绍', 
                    'icon' => '📦',
                    'content' => '<p>这里是产品介绍的详细内容。您可以在这里添加产品的特点、优势、使用方法等信息。</p><ul><li>特点一：高效稳定</li><li>特点二：易于使用</li><li>特点三：安全可靠</li></ul>',
                ),
                array( 
                    'title' => '技术规格', 
                    'icon' => '⚙️',
                    'content' => '<p>产品的技术参数和规格说明。</p><table style="width:100%;border-collapse:collapse;"><tr><td style="padding:10px;border:1px solid #e2e8f0;">尺寸</td><td style="padding:10px;border:1px solid #e2e8f0;">100 x 50 x 30 mm</td></tr><tr><td style="padding:10px;border:1px solid #e2e8f0;">重量</td><td style="padding:10px;border:1px solid #e2e8f0;">500g</td></tr></table>',
                ),
                array( 
                    'title' => '使用说明', 
                    'icon' => '📖',
                    'content' => '<p>产品的使用步骤和注意事项。</p><ol><li>第一步：打开包装</li><li>第二步：阅读说明书</li><li>第三步：按照指引操作</li></ol>',
                ),
            );
        }
        
        // 背景样式
        $bg_style = '';
        if ( ! empty( $bg_color ) ) {
            $bg_style = strpos( $bg_color, 'gradient' ) !== false ? "background: {$bg_color};" : "background-color: {$bg_color};";
        }
        
        $title_style_attr = ! empty( $title_color ) ? "color: {$title_color};" : '';
        $tabs_id = 'tabs-' . uniqid();
        
        // 对齐样式
        $align_style = 'justify-content: center;';
        if ( $align === 'left' ) $align_style = 'justify-content: flex-start;';
        if ( $align === 'right' ) $align_style = 'justify-content: flex-end;';
        ?>
        <section class="module module-tabs section-padding" style="<?php echo esc_attr( $bg_style ); ?>">
            <div class="container">
                <?php if ( $title ) : ?>
                    <div class="section-header text-center">
                        <h2 class="section-title" style="<?php echo esc_attr( $title_style_attr ); ?>"><?php echo esc_html( $title ); ?></h2>
                        <?php if ( $subtitle ) : ?>
                            <p class="section-subtitle"><?php echo esc_html( $subtitle ); ?></p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                
                <?php if ( ! empty( $items ) ) : ?>
                    <div id="<?php echo esc_attr( $tabs_id ); ?>" class="tabs-wrapper tabs-style-<?php echo esc_attr( $style ); ?>">
                        <!-- 标签导航 -->
                        <div class="tabs-nav" style="display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 30px; <?php echo esc_attr( $align_style ); ?>">
                            <?php foreach ( $items as $index => $item ) : 
                                $tab_title = isset( $item['title'] ) ? $item['title'] : '标签';
                                $icon = isset( $item['icon'] ) ? $item['icon'] : '';
                            ?>
                                <button type="button" class="tab-btn <?php echo $index === 0 ? 'active' : ''; ?>" data-tab="<?php echo esc_attr( $index ); ?>" style="
                                    display: inline-flex;
                                    align-items: center;
                                    gap: 8px;
                                    padding: 12px 24px;
                                    border: none;
                                    cursor: pointer;
                                    font-size: 0.95rem;
                                    font-weight: 500;
                                    transition: all 0.3s;
                                    <?php if ( $style === 'default' ) : ?>
                                        background: <?php echo $index === 0 ? 'linear-gradient(135deg, #2563eb 0%, #7c3aed 100%)' : '#f1f5f9'; ?>;
                                        color: <?php echo $index === 0 ? '#fff' : '#64748b'; ?>;
                                        border-radius: 10px;
                                    <?php elseif ( $style === 'pills' ) : ?>
                                        background: <?php echo $index === 0 ? 'var(--color-primary)' : 'transparent'; ?>;
                                        color: <?php echo $index === 0 ? '#fff' : '#64748b'; ?>;
                                        border-radius: 50px;
                                        border: 2px solid <?php echo $index === 0 ? 'var(--color-primary)' : '#e2e8f0'; ?>;
                                    <?php elseif ( $style === 'underline' ) : ?>
                                        background: transparent;
                                        color: <?php echo $index === 0 ? 'var(--color-primary)' : '#64748b'; ?>;
                                        border-radius: 0;
                                        border-bottom: 3px solid <?php echo $index === 0 ? 'var(--color-primary)' : 'transparent'; ?>;
                                        padding-bottom: 10px;
                                    <?php elseif ( $style === 'boxed' ) : ?>
                                        background: <?php echo $index === 0 ? '#fff' : '#f8fafc'; ?>;
                                        color: <?php echo $index === 0 ? 'var(--color-primary)' : '#64748b'; ?>;
                                        border-radius: 10px 10px 0 0;
                                        box-shadow: <?php echo $index === 0 ? '0 -5px 20px rgba(0,0,0,0.05)' : 'none'; ?>;
                                        border: 1px solid <?php echo $index === 0 ? '#e2e8f0' : 'transparent'; ?>;
                                        border-bottom: <?php echo $index === 0 ? 'none' : '1px solid #e2e8f0'; ?>;
                                        margin-bottom: -1px;
                                    <?php endif; ?>
                                ">
                                    <?php if ( $icon ) : ?>
                                        <span style="font-size: 1.1em;"><?php echo esc_html( $icon ); ?></span>
                                    <?php endif; ?>
                                    <?php echo esc_html( $tab_title ); ?>
                                </button>
                            <?php endforeach; ?>
                        </div>
                        
                        <!-- 标签内容 -->
                        <div class="tabs-content" style="
                            background: #fff;
                            padding: 35px;
                            border-radius: <?php echo $style === 'boxed' ? '0 10px 10px 10px' : '16px'; ?>;
                            box-shadow: 0 10px 40px rgba(0,0,0,0.08);
                            <?php echo $style === 'boxed' ? 'border: 1px solid #e2e8f0;' : ''; ?>
                        ">
                            <?php foreach ( $items as $index => $item ) : 
                                $content = isset( $item['content'] ) ? $item['content'] : '';
                            ?>
                                <div class="tab-pane" data-tab="<?php echo esc_attr( $index ); ?>" style="display: <?php echo $index === 0 ? 'block' : 'none'; ?>;">
                                    <div class="tab-content-inner" style="color: #475569; line-height: 1.8;">
                                        <?php echo wp_kses_post( $content ); ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </section>
        
        <style>
        #<?php echo esc_attr( $tabs_id ); ?> .tab-btn:hover {
            opacity: 0.85;
        }
        #<?php echo esc_attr( $tabs_id ); ?> .tab-content-inner table {
            margin: 15px 0;
        }
        #<?php echo esc_attr( $tabs_id ); ?> .tab-content-inner ul,
        #<?php echo esc_attr( $tabs_id ); ?> .tab-content-inner ol {
            padding-left: 20px;
            margin: 15px 0;
        }
        #<?php echo esc_attr( $tabs_id ); ?> .tab-content-inner li {
            margin-bottom: 8px;
        }
        @media (max-width: 768px) {
            #<?php echo esc_attr( $tabs_id ); ?> .tabs-nav {
                justify-content: center !important;
            }
            #<?php echo esc_attr( $tabs_id ); ?> .tab-btn {
                padding: 10px 16px !important;
                font-size: 0.85rem !important;
            }
            #<?php echo esc_attr( $tabs_id ); ?> .tabs-content {
                padding: 25px !important;
            }
        }
        </style>
        
        <script>
        (function() {
            var tabsId = '<?php echo esc_js( $tabs_id ); ?>';
            var wrapper = document.getElementById(tabsId);
            if (!wrapper) return;
            
            var btns = wrapper.querySelectorAll('.tab-btn');
            var panes = wrapper.querySelectorAll('.tab-pane');
            var style = '<?php echo esc_js( $style ); ?>';
            
            btns.forEach(function(btn) {
                btn.addEventListener('click', function() {
                    var tabIndex = this.getAttribute('data-tab');
                    
                    // 更新按钮状态
                    btns.forEach(function(b) {
                        b.classList.remove('active');
                        // 重置样式
                        if (style === 'default') {
                            b.style.background = '#f1f5f9';
                            b.style.color = '#64748b';
                        } else if (style === 'pills') {
                            b.style.background = 'transparent';
                            b.style.color = '#64748b';
                            b.style.borderColor = '#e2e8f0';
                        } else if (style === 'underline') {
                            b.style.color = '#64748b';
                            b.style.borderBottomColor = 'transparent';
                        } else if (style === 'boxed') {
                            b.style.background = '#f8fafc';
                            b.style.color = '#64748b';
                            b.style.boxShadow = 'none';
                        }
                    });
                    
                    this.classList.add('active');
                    // 激活样式
                    if (style === 'default') {
                        this.style.background = 'linear-gradient(135deg, #2563eb 0%, #7c3aed 100%)';
                        this.style.color = '#fff';
                    } else if (style === 'pills') {
                        this.style.background = 'var(--color-primary)';
                        this.style.color = '#fff';
                        this.style.borderColor = 'var(--color-primary)';
                    } else if (style === 'underline') {
                        this.style.color = 'var(--color-primary)';
                        this.style.borderBottomColor = 'var(--color-primary)';
                    } else if (style === 'boxed') {
                        this.style.background = '#fff';
                        this.style.color = 'var(--color-primary)';
                        this.style.boxShadow = '0 -5px 20px rgba(0,0,0,0.05)';
                    }
                    
                    // 显示对应内容
                    panes.forEach(function(pane) {
                        pane.style.display = pane.getAttribute('data-tab') === tabIndex ? 'block' : 'none';
                    });
                });
            });
        })();
        </script>
        <?php
    }
}
