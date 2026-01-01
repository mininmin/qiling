<?php
/**
 * Team Module - 团队成员
 *
 * @package Developer_Starter
 */

namespace Developer_Starter\Modules\Modules;

use Developer_Starter\Modules\Module_Base;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Team_Module extends Module_Base {

    public function __construct() {
        $this->category = 'homepage';
        $this->icon = 'dashicons-groups';
        $this->description = '展示团队成员/核心人员';
    }

    public function get_id() {
        return 'team';
    }

    public function get_name() {
        return '团队成员';
    }

    public function get_fields() {
        return array(
            array( 'id' => 'team_title', 'label' => '标题', 'type' => 'text', 'default' => '核心团队' ),
            array( 'id' => 'team_subtitle', 'label' => '副标题', 'type' => 'text', 'default' => '专业团队，值得信赖' ),
            array( 'id' => 'team_bg_color', 'label' => '背景颜色', 'type' => 'text', 'description' => '支持渐变色' ),
            array( 'id' => 'team_title_color', 'label' => '标题颜色', 'type' => 'color' ),
            array( 'id' => 'team_subtitle_color', 'label' => '副标题颜色', 'type' => 'color' ),
            array( 'id' => 'team_columns', 'label' => '每行列数', 'type' => 'select', 'options' => array( '2' => '2列', '3' => '3列', '4' => '4列' ), 'default' => '4' ),
            array(
                'id' => 'team_members',
                'label' => '团队成员',
                'type' => 'repeater',
                'fields' => array(
                    array( 'id' => 'avatar', 'label' => '头像', 'type' => 'text' ),
                    array( 'id' => 'name', 'label' => '姓名', 'type' => 'text' ),
                    array( 'id' => 'position', 'label' => '职位', 'type' => 'text' ),
                    array( 'id' => 'desc', 'label' => '简介', 'type' => 'textarea' ),
                    array( 'id' => 'wechat', 'label' => '微信二维码', 'type' => 'text' ),
                    array( 'id' => 'email', 'label' => '邮箱', 'type' => 'text' ),
                    array( 'id' => 'phone', 'label' => '电话', 'type' => 'text' ),
                ),
            ),
        );
    }

    public function render( $data = array() ) {
        $title = isset( $data['team_title'] ) && $data['team_title'] !== '' ? $data['team_title'] : '核心团队';
        $subtitle = isset( $data['team_subtitle'] ) ? $data['team_subtitle'] : '专业团队，值得信赖';
        $bg_color = isset( $data['team_bg_color'] ) && ! empty( $data['team_bg_color'] ) ? $data['team_bg_color'] : '';
        $title_color = isset( $data['team_title_color'] ) && ! empty( $data['team_title_color'] ) ? $data['team_title_color'] : '';
        $subtitle_color = isset( $data['team_subtitle_color'] ) && ! empty( $data['team_subtitle_color'] ) ? $data['team_subtitle_color'] : '';
        $columns = isset( $data['team_columns'] ) && ! empty( $data['team_columns'] ) ? intval( $data['team_columns'] ) : 4;
        $members = isset( $data['team_members'] ) ? $data['team_members'] : array();
        
        // 默认示例数据
        if ( empty( $members ) ) {
            $members = array(
                array( 'avatar' => '', 'name' => '张明', 'position' => '首席执行官', 'desc' => '20年行业经验，曾任多家知名企业高管。' ),
                array( 'avatar' => '', 'name' => '李华', 'position' => '技术总监', 'desc' => '资深技术专家，主导多个大型项目研发。' ),
                array( 'avatar' => '', 'name' => '王芳', 'position' => '市场总监', 'desc' => '深耕市场营销领域15年，擅长品牌策略。' ),
                array( 'avatar' => '', 'name' => '刘强', 'position' => '运营总监', 'desc' => '精细化运营专家，打造高效团队管理体系。' ),
            );
        }
        
        // 背景样式
        $bg_style = '';
        if ( ! empty( $bg_color ) ) {
            $bg_style = strpos( $bg_color, 'gradient' ) !== false ? "background: {$bg_color};" : "background-color: {$bg_color};";
        }
        
        $title_style = ! empty( $title_color ) ? "color: {$title_color};" : '';
        $subtitle_style = ! empty( $subtitle_color ) ? "color: {$subtitle_color};" : '';
        $grid_class = 'grid-cols-' . $columns;
        
        $avatar_colors = array(
            'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
            'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)',
            'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)',
            'linear-gradient(135deg, #43e97b 0%, #38f9d7 100%)',
            'linear-gradient(135deg, #fa709a 0%, #fee140 100%)',
            'linear-gradient(135deg, #a8edea 0%, #fed6e3 100%)',
        );
        ?>
        <section class="module module-team section-padding" style="<?php echo esc_attr( $bg_style ); ?>">
            <div class="container">
                <div class="section-header text-center">
                    <h2 class="section-title" style="<?php echo esc_attr( $title_style ); ?>"><?php echo esc_html( $title ); ?></h2>
                    <?php if ( $subtitle ) : ?>
                        <p class="section-subtitle" style="<?php echo esc_attr( $subtitle_style ); ?>"><?php echo esc_html( $subtitle ); ?></p>
                    <?php endif; ?>
                </div>
                
                <?php if ( ! empty( $members ) ) : ?>
                    <div class="team-grid <?php echo esc_attr( $grid_class ); ?>">
                        <?php foreach ( $members as $index => $member ) : 
                            $avatar = isset( $member['avatar'] ) ? $member['avatar'] : '';
                            $name = isset( $member['name'] ) ? $member['name'] : '';
                            $position = isset( $member['position'] ) ? $member['position'] : '';
                            $desc = isset( $member['desc'] ) ? $member['desc'] : '';
                            $wechat = isset( $member['wechat'] ) ? $member['wechat'] : '';
                            $email = isset( $member['email'] ) ? $member['email'] : '';
                            $phone = isset( $member['phone'] ) ? $member['phone'] : '';
                            $default_avatar_bg = $avatar_colors[ $index % count( $avatar_colors ) ];
                        ?>
                            <div class="team-card" style="
                                background: #fff;
                                border-radius: 20px;
                                padding: 35px 25px;
                                text-align: center;
                                box-shadow: 0 10px 40px rgba(0,0,0,0.08);
                                transition: transform 0.3s, box-shadow 0.3s;
                            ">
                                <!-- 头像 -->
                                <div class="team-avatar" style="
                                    width: 120px;
                                    height: 120px;
                                    border-radius: 50%;
                                    margin: 0 auto 20px;
                                    overflow: hidden;
                                    <?php echo empty( $avatar ) ? "background: {$default_avatar_bg};" : ''; ?>
                                    display: flex;
                                    align-items: center;
                                    justify-content: center;
                                    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
                                ">
                                    <?php if ( $avatar ) : ?>
                                        <img src="<?php echo esc_url( $avatar ); ?>" alt="<?php echo esc_attr( $name ); ?>" style="width: 100%; height: 100%; object-fit: cover;" />
                                    <?php else : ?>
                                        <span style="color: #fff; font-size: 2.5rem; font-weight: 600;"><?php echo esc_html( mb_substr( $name, 0, 1 ) ); ?></span>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- 姓名 -->
                                <h3 style="margin: 0 0 5px; font-size: 1.25rem; font-weight: 700; color: #1e293b;"><?php echo esc_html( $name ); ?></h3>
                                
                                <!-- 职位 -->
                                <?php if ( $position ) : ?>
                                    <p style="margin: 0 0 15px; color: var(--color-primary); font-weight: 500; font-size: 0.9rem;"><?php echo esc_html( $position ); ?></p>
                                <?php endif; ?>
                                
                                <!-- 简介 -->
                                <?php if ( $desc ) : ?>
                                    <p style="margin: 0 0 20px; color: #64748b; font-size: 0.9rem; line-height: 1.6;"><?php echo esc_html( $desc ); ?></p>
                                <?php endif; ?>
                                
                                <!-- 联系方式 -->
                                <?php if ( $wechat || $email || $phone ) : ?>
                                    <div class="team-social" style="display: flex; justify-content: center; gap: 12px;">
                                        <?php if ( $phone ) : ?>
                                            <a href="tel:<?php echo esc_attr( $phone ); ?>" style="width: 38px; height: 38px; background: #f1f5f9; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #64748b; transition: all 0.3s;" title="<?php echo esc_attr( $phone ); ?>">
                                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72c.127.96.362 1.903.7 2.81a2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.907.338 1.85.573 2.81.7A2 2 0 0122 16.92z"/></svg>
                                            </a>
                                        <?php endif; ?>
                                        <?php if ( $email ) : ?>
                                            <a href="mailto:<?php echo esc_attr( $email ); ?>" style="width: 38px; height: 38px; background: #f1f5f9; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #64748b; transition: all 0.3s;" title="<?php echo esc_attr( $email ); ?>">
                                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                                            </a>
                                        <?php endif; ?>
                                        <?php if ( $wechat ) : ?>
                                            <div class="team-wechat-wrap" style="position: relative;">
                                                <span style="width: 38px; height: 38px; background: #f1f5f9; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #64748b; cursor: pointer; transition: all 0.3s;">
                                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M8.691 2.188C3.891 2.188 0 5.476 0 9.53c0 2.212 1.17 4.203 3.002 5.55a.59.59 0 01.213.665l-.39 1.48c-.019.07-.048.141-.048.213 0 .163.13.295.29.295a.326.326 0 00.167-.054l1.903-1.114a.864.864 0 01.717-.098 10.16 10.16 0 002.837.403c.276 0 .543-.027.811-.05-.857-2.578.157-4.972 1.932-6.446 1.703-1.415 3.882-1.98 5.853-1.838-.576-3.583-4.196-6.348-8.596-6.348z"/></svg>
                                                </span>
                                                <div class="team-wechat-qr" style="display: none; position: absolute; bottom: 50px; left: 50%; transform: translateX(-50%); background: #fff; padding: 10px; border-radius: 8px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); z-index: 10;">
                                                    <img src="<?php echo esc_url( $wechat ); ?>" alt="微信" style="width: 120px; display: block;" />
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>
        
        <style>
        .team-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 25px 60px rgba(0,0,0,0.12);
        }
        .team-avatar {
            transition: transform 0.3s;
        }
        .team-card:hover .team-avatar {
            transform: scale(1.05);
        }
        .team-social a:hover,
        .team-social span:hover {
            background: linear-gradient(135deg, #2563eb 0%, #7c3aed 100%) !important;
            color: #fff !important;
        }
        .team-wechat-wrap:hover .team-wechat-qr {
            display: block !important;
        }
        @media (max-width: 768px) {
            .team-grid {
                gap: 20px !important;
            }
            .team-card {
                padding: 25px 20px !important;
            }
            .team-avatar {
                width: 100px !important;
                height: 100px !important;
            }
        }
        </style>
        <?php
    }
}
