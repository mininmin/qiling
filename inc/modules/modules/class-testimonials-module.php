<?php
/**
 * Testimonials Module - 客户评价
 *
 * @package Developer_Starter
 */

namespace Developer_Starter\Modules\Modules;

use Developer_Starter\Modules\Module_Base;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Testimonials_Module extends Module_Base {

    public function __construct() {
        $this->category = 'homepage';
        $this->icon = 'dashicons-format-quote';
        $this->description = '展示客户评价和推荐';
    }

    public function get_id() {
        return 'testimonials';
    }

    public function get_name() {
        return '客户评价';
    }

    public function render( $data = array() ) {
        $title = isset( $data['testimonials_title'] ) && $data['testimonials_title'] !== '' ? $data['testimonials_title'] : '客户评价';
        $subtitle = isset( $data['testimonials_subtitle'] ) ? $data['testimonials_subtitle'] : '听听客户怎么说';
        $bg_color = isset( $data['testimonials_bg_color'] ) && ! empty( $data['testimonials_bg_color'] ) ? $data['testimonials_bg_color'] : '';
        $title_color = isset( $data['testimonials_title_color'] ) && ! empty( $data['testimonials_title_color'] ) ? $data['testimonials_title_color'] : '';
        $subtitle_color = isset( $data['testimonials_subtitle_color'] ) && ! empty( $data['testimonials_subtitle_color'] ) ? $data['testimonials_subtitle_color'] : '';
        $columns = isset( $data['testimonials_columns'] ) && ! empty( $data['testimonials_columns'] ) ? intval( $data['testimonials_columns'] ) : 3;
        $items = isset( $data['testimonials_items'] ) ? $data['testimonials_items'] : array();
        
        // 默认示例数据
        if ( empty( $items ) ) {
            $items = array(
                array( 
                    'avatar' => '',
                    'name' => '张先生',
                    'position' => 'CEO · 某科技公司',
                    'content' => '非常专业的团队，项目交付准时，质量超出预期。推荐给所有需要高品质服务的企业！',
                    'rating' => '5',
                    'card_bg' => '#ffffff',
                    'name_color' => '',
                    'content_color' => '',
                ),
                array( 
                    'avatar' => '',
                    'name' => '李女士',
                    'position' => '市场总监 · 某传媒集团',
                    'content' => '合作非常愉快，沟通顺畅，设计方案很有创意，完美达成了我们的需求目标。',
                    'rating' => '5',
                    'card_bg' => '#ffffff',
                    'name_color' => '',
                    'content_color' => '',
                ),
                array( 
                    'avatar' => '',
                    'name' => '王总',
                    'position' => '创始人 · 某电商平台',
                    'content' => '从需求分析到最终交付，每个环节都很用心。技术实力强，值得长期合作！',
                    'rating' => '5',
                    'card_bg' => '#ffffff',
                    'name_color' => '',
                    'content_color' => '',
                ),
            );
        }
        
        // 背景样式
        $bg_style = '';
        if ( ! empty( $bg_color ) ) {
            $bg_style = strpos( $bg_color, 'gradient' ) !== false ? "background: {$bg_color};" : "background-color: {$bg_color};";
        }
        
        // 标题颜色样式
        $title_style = ! empty( $title_color ) ? "color: {$title_color};" : '';
        $subtitle_style = ! empty( $subtitle_color ) ? "color: {$subtitle_color};" : '';
        
        // 网格类
        $grid_class = 'grid-cols-' . $columns;
        ?>
        <section class="module module-testimonials section-padding" style="<?php echo esc_attr( $bg_style ); ?>">
            <div class="container">
                <div class="section-header text-center">
                    <h2 class="section-title" style="<?php echo esc_attr( $title_style ); ?>"><?php echo esc_html( $title ); ?></h2>
                    <?php if ( $subtitle ) : ?>
                        <p class="section-subtitle" style="<?php echo esc_attr( $subtitle_style ); ?>"><?php echo esc_html( $subtitle ); ?></p>
                    <?php endif; ?>
                </div>
                
                <?php if ( ! empty( $items ) ) : ?>
                    <div class="testimonials-grid <?php echo esc_attr( $grid_class ); ?>" style="align-items: stretch;">
                        <?php foreach ( $items as $index => $item ) : 
                            $avatar = isset( $item['avatar'] ) ? $item['avatar'] : '';
                            $name = isset( $item['name'] ) ? $item['name'] : '';
                            $name_color = isset( $item['name_color'] ) && ! empty( $item['name_color'] ) ? $item['name_color'] : '';
                            $position = isset( $item['position'] ) ? $item['position'] : '';
                            $content = isset( $item['content'] ) ? $item['content'] : '';
                            $content_color = isset( $item['content_color'] ) && ! empty( $item['content_color'] ) ? $item['content_color'] : '';
                            $rating = isset( $item['rating'] ) ? intval( $item['rating'] ) : 5;
                            $card_bg = isset( $item['card_bg'] ) && ! empty( $item['card_bg'] ) ? $item['card_bg'] : '#ffffff';
                            
                            // 卡片背景样式
                            $card_bg_style = strpos( $card_bg, 'gradient' ) !== false ? "background: {$card_bg};" : "background-color: {$card_bg};";
                            
                            // 名称颜色
                            $name_style = ! empty( $name_color ) ? "color: {$name_color};" : 'color: var(--color-dark);';
                            
                            // 内容颜色
                            $content_style = ! empty( $content_color ) ? "color: {$content_color};" : 'color: var(--color-gray-600);';
                            
                            // 默认头像颜色（使用渐变）
                            $avatar_colors = array(
                                'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
                                'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)',
                                'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)',
                                'linear-gradient(135deg, #43e97b 0%, #38f9d7 100%)',
                                'linear-gradient(135deg, #fa709a 0%, #fee140 100%)',
                            );
                            $default_avatar_bg = $avatar_colors[ $index % count( $avatar_colors ) ];
                        ?>
                            <div class="testimonial-card" style="
                                <?php echo esc_attr( $card_bg_style ); ?>
                                border-radius: 20px;
                                padding: 30px;
                                box-shadow: 0 10px 40px rgba(0,0,0,0.08);
                                transition: transform 0.3s, box-shadow 0.3s;
                                position: relative;
                                display: flex;
                                flex-direction: column;
                            ">
                                <!-- 引号装饰 -->
                                <div style="
                                    position: absolute;
                                    top: 20px;
                                    right: 25px;
                                    font-size: 4rem;
                                    line-height: 1;
                                    background: linear-gradient(135deg, var(--color-primary) 0%, #7c3aed 100%);
                                    -webkit-background-clip: text;
                                    -webkit-text-fill-color: transparent;
                                    background-clip: text;
                                    opacity: 0.2;
                                    font-family: Georgia, serif;
                                ">"</div>
                                
                                <!-- 评价内容 -->
                                <div class="testimonial-content" style="
                                    <?php echo esc_attr( $content_style ); ?>
                                    font-size: 1rem;
                                    line-height: 1.8;
                                    margin-bottom: 25px;
                                    flex-grow: 1;
                                    position: relative;
                                    z-index: 1;
                                ">
                                    <?php echo esc_html( $content ); ?>
                                </div>
                                
                                <!-- 星级评分 -->
                                <?php if ( $rating > 0 ) : ?>
                                    <div class="testimonial-rating" style="margin-bottom: 20px;">
                                        <?php for ( $i = 1; $i <= 5; $i++ ) : ?>
                                            <span style="
                                                color: <?php echo $i <= $rating ? '#fbbf24' : '#e5e7eb'; ?>;
                                                font-size: 1.1rem;
                                            ">★</span>
                                        <?php endfor; ?>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- 用户信息 -->
                                <div class="testimonial-author" style="
                                    display: flex;
                                    align-items: center;
                                    gap: 15px;
                                    padding-top: 20px;
                                    border-top: 1px solid rgba(0,0,0,0.06);
                                ">
                                    <!-- 头像 -->
                                    <div class="testimonial-avatar" style="
                                        width: 55px;
                                        height: 55px;
                                        border-radius: 50%;
                                        overflow: hidden;
                                        flex-shrink: 0;
                                        <?php echo empty( $avatar ) ? "background: {$default_avatar_bg};" : ''; ?>
                                        display: flex;
                                        align-items: center;
                                        justify-content: center;
                                        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
                                    ">
                                        <?php if ( $avatar ) : ?>
                                            <img src="<?php echo esc_url( $avatar ); ?>" alt="<?php echo esc_attr( $name ); ?>" style="width: 100%; height: 100%; object-fit: cover;" />
                                        <?php else : ?>
                                            <span style="color: #fff; font-size: 1.3rem; font-weight: 600;"><?php echo esc_html( mb_substr( $name, 0, 1 ) ); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <!-- 名称和职位 -->
                                    <div class="testimonial-info">
                                        <h4 class="testimonial-name" style="
                                            <?php echo esc_attr( $name_style ); ?>
                                            font-size: 1.1rem;
                                            font-weight: 600;
                                            margin: 0 0 4px 0;
                                        "><?php echo esc_html( $name ); ?></h4>
                                        <?php if ( $position ) : ?>
                                            <p class="testimonial-position" style="
                                                color: var(--color-gray-500);
                                                font-size: 0.85rem;
                                                margin: 0;
                                            "><?php echo esc_html( $position ); ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>
        
        <style>
        .testimonial-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 50px rgba(0,0,0,0.12);
        }
        .testimonial-avatar {
            transition: transform 0.3s;
        }
        .testimonial-card:hover .testimonial-avatar {
            transform: scale(1.1);
        }
        @media (max-width: 768px) {
            .testimonials-grid {
                gap: 20px !important;
            }
            .testimonial-card {
                padding: 25px !important;
            }
        }
        </style>
        <?php
    }
}
