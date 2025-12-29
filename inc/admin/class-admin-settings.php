<?php
/**
 * Admin Settings Class - 完整版
 *
 * @package Developer_Starter
 */

namespace Developer_Starter\Admin;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Admin_Settings {

    private $option_name = 'developer_starter_options';

    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_menu_page' ), 10 );
        add_action( 'admin_init', array( $this, 'register_settings' ) );
        add_action( 'admin_init', array( $this, 'handle_reset' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
    }

    public function enqueue_admin_scripts( $hook ) {
        if ( strpos( $hook, 'developer-starter' ) === false ) {
            return;
        }
        wp_enqueue_media();
        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_script( 'wp-color-picker' );
        
        add_action( 'admin_footer', array( $this, 'admin_footer_js' ) );
    }

    public function admin_footer_js() {
        ?>
        <script>
        jQuery(document).ready(function($) {
            $('.ds-color-picker').wpColorPicker();
            
            $('.ds-upload-image-btn').on('click', function(e) {
                e.preventDefault();
                var button = $(this);
                var input = button.siblings('.ds-image-url');
                var preview = button.siblings('.ds-image-preview');
                
                var frame = wp.media({ title: '选择图片', multiple: false });
                frame.on('select', function() {
                    var attachment = frame.state().get('selection').first().toJSON();
                    input.val(attachment.url);
                    if (preview.length) {
                        preview.attr('src', attachment.url).show();
                    } else {
                        button.after('<img src="' + attachment.url + '" class="ds-image-preview" style="display:block;max-width:200px;margin-top:10px;"/>');
                    }
                });
                frame.open();
            });
            
            $('.ds-remove-image-btn').on('click', function(e) {
                e.preventDefault();
                $(this).siblings('.ds-image-url').val('');
                $(this).siblings('.ds-image-preview').attr('src', '').hide();
            });

            $(document).on('click', '.ds-repeater-add', function() {
                var $wrap = $(this).closest('.ds-repeater-wrap');
                var $list = $wrap.find('.ds-repeater-list');
                var $tpl = $wrap.find('.ds-repeater-tpl');
                var tpl = $tpl.attr('data-template');
                var idx = $list.children().length;
                tpl = tpl.replace(/__IDX__/g, idx);
                $list.append(tpl);
            });

            $(document).on('click', '.ds-repeater-remove', function(e) {
                e.preventDefault();
                $(this).closest('.ds-repeater-item').remove();
            });
        });
        </script>
        <?php
    }

    public function add_menu_page() {
        add_menu_page( '企业主题设置', '企业主题设置', 'manage_options', 'developer-starter-settings',
            array( $this, 'render_settings_page' ), 'dashicons-building', 60 );
    }

    private function get_tabs() {
        return array(
            'basic'     => '基础设置',
            'header'    => '顶部导航',
            'footer'    => '页脚设置',
            'article'   => '文章设置',
            'pages'     => '页面模板',
            'content'   => '内容设置',
            'smtp'      => '邮件设置',
            'advanced'  => '高级设置',
        );
    }

    public function register_settings() {
        register_setting( 'developer_starter_settings', $this->option_name, array(
            'sanitize_callback' => array( $this, 'sanitize_options' ),
        ) );
    }

    public function handle_reset() {
        if ( isset( $_POST['ds_reset_settings'] ) && isset( $_POST['ds_reset_nonce'] ) ) {
            if ( wp_verify_nonce( $_POST['ds_reset_nonce'], 'ds_reset_action' ) && current_user_can( 'manage_options' ) ) {
                delete_option( $this->option_name );
                add_settings_error( 'developer_starter_settings', 'reset', '主题设置已恢复默认！', 'updated' );
            }
        }
    }

    public function render_settings_page() {
        $tabs = $this->get_tabs();
        $current_tab = isset( $_GET['tab'] ) ? sanitize_key( $_GET['tab'] ) : 'basic';
        $options = get_option( $this->option_name, array() );
        ?>
        <div class="wrap">
            <h1>企业主题设置</h1>
            <?php settings_errors(); ?>
            
            <nav class="nav-tab-wrapper">
                <?php foreach ( $tabs as $tab_id => $tab_name ) : ?>
                    <a href="?page=developer-starter-settings&tab=<?php echo $tab_id; ?>" 
                       class="nav-tab <?php echo $current_tab === $tab_id ? 'nav-tab-active' : ''; ?>">
                        <?php echo esc_html( $tab_name ); ?>
                    </a>
                <?php endforeach; ?>
            </nav>
            
            <form method="post" action="options.php" style="margin-top: 20px;">
                <?php settings_fields( 'developer_starter_settings' ); ?>
                
                <table class="form-table" role="presentation">
                    <?php $this->render_tab_fields( $current_tab, $options ); ?>
                </table>
                
                <?php submit_button( '保存设置' ); ?>
            </form>
            
            <hr style="margin: 40px 0 20px;" />
            <h2>恢复默认设置</h2>
            <p class="description">如果设置出现问题，可以一键恢复所有主题设置为默认值。</p>
            <form method="post" style="margin-top: 15px;">
                <?php wp_nonce_field( 'ds_reset_action', 'ds_reset_nonce' ); ?>
                <button type="submit" name="ds_reset_settings" class="button button-secondary" 
                        onclick="return confirm('确定要恢复所有主题设置为默认值吗？此操作不可撤销！');">
                    恢复默认设置
                </button>
            </form>
        </div>
        <?php
    }

    private function render_tab_fields( $tab, $options ) {
        switch ( $tab ) {
            case 'basic': $this->render_basic_tab( $options ); break;
            case 'header': $this->render_header_tab( $options ); break;
            case 'footer': $this->render_footer_tab( $options ); break;
            case 'article': $this->render_article_tab( $options ); break;
            case 'pages': $this->render_pages_tab( $options ); break;
            case 'content': $this->render_content_tab( $options ); break;
            case 'smtp': $this->render_smtp_tab( $options ); break;
            case 'advanced': $this->render_advanced_tab( $options ); break;
        }
    }

    private function render_basic_tab( $options ) {
        echo '<tr><th colspan="2"><h2>网站信息</h2></th></tr>';
        $this->field_image( 'site_logo', '网站 Logo', $options, '推荐尺寸: 200x60 像素' );
        $this->field_text( 'company_name', '企业名称', $options );
        $this->field_text( 'company_phone', '联系电话', $options );
        $this->field_text( 'company_email', '联系邮箱', $options );
        $this->field_textarea( 'company_address', '企业地址', $options );
        $this->field_textarea( 'company_brief', '公司简介', $options, '显示在页脚' );
        
        echo '<tr><th colspan="2"><h2>语言设置</h2></th></tr>';
        $this->field_select( 'theme_language', '前台显示语言', $options, array(
            'zh_CN' => '简体中文',
            'en_US' => 'English',
        ), '独立于WordPress后台语言' );
        
        echo '<tr><th colspan="2"><h2>备案信息</h2></th></tr>';
        $this->field_text( 'icp_number', 'ICP 备案号', $options );
        $this->field_text( 'police_number', '公安备案号', $options );
        $this->field_image( 'police_icon', '公安备案图标', $options );
        
        echo '<tr><th colspan="2"><h2>社交媒体</h2></th></tr>';
        $this->field_image( 'wechat_qrcode', '微信公众号二维码', $options );
        $this->field_text( 'weibo_url', '微博链接', $options );
    }

    private function render_header_tab( $options ) {
        echo '<tr><th colspan="2"><h2>顶部导航设置</h2></th></tr>';
        $this->field_text( 'header_bg_color', '顶部背景色', $options, '支持渐变色，留空使用默认白色' );
        $this->field_color( 'header_text_color', '顶部文字颜色', $options, '#333333' );
        $this->field_checkbox( 'header_transparent_home', '首页顶部透明', $options, '首页首屏时顶部透明，滚动后显示背景色' );
        $this->field_checkbox( 'hide_search_button', '隐藏搜索按钮', $options, '取消勾选将在顶部导航显示搜索按钮' );
        $this->field_checkbox( 'hide_phone_header', '隐藏电话号码', $options, '取消勾选将在顶部导航显示联系电话' );
        
        echo '<tr><th colspan="2"><h2>菜单样式</h2><p class="description">自定义导航菜单的悬停和激活效果</p></th></tr>';
        $this->field_text( 'nav_hover_bg', '菜单Hover背景色', $options, '支持渐变色，如: linear-gradient(135deg, #2563eb 0%, #7c3aed 100%)' );
        $this->field_color( 'nav_hover_text', '菜单Hover文字颜色', $options, '#ffffff' );
        
        echo '<tr><th colspan="2"><h2>电话按钮样式</h2><p class="description">自定义顶部导航电话按钮的颜色</p></th></tr>';
        $this->field_text( 'phone_bg_transparent', '透明模式-背景色', $options, '首页透明头部时的背景，如: rgba(255,255,255,0.2) 或渐变色' );
        $this->field_color( 'phone_text_transparent', '透明模式-文字颜色', $options, '#ffffff' );
        $this->field_text( 'phone_bg_normal', '常规模式-背景色', $options, '滚动后或普通页面的背景，支持渐变色' );
        $this->field_color( 'phone_text_normal', '常规模式-文字颜色', $options, '#ffffff' );
    }

    private function render_footer_tab( $options ) {
        echo '<tr><th colspan="2"><h2>页脚文字设置</h2></th></tr>';
        $this->field_text( 'footer_about_title', '关于我们标题', $options, '默认: 关于我们' );
        $this->field_text( 'footer_links_title', '快速链接标题', $options, '默认: 快速链接' );
        $this->field_text( 'footer_contact_title', '联系方式标题', $options, '默认: 联系方式' );
        $this->field_text( 'footer_follow_title', '关注我们标题', $options, '默认: 关注我们' );
        $this->field_textarea( 'footer_copyright', '版权信息（支持HTML）', $options );
        
        echo '<tr><th colspan="2"><h2>快速链接（友情链接）</h2></th></tr>';
        $this->field_repeater( 'footer_quick_links', '链接列表', $options, array(
            array( 'id' => 'text', 'label' => '链接文字', 'type' => 'text' ),
            array( 'id' => 'url', 'label' => '链接地址', 'type' => 'text' ),
        ) );
        
        echo '<tr><th colspan="2"><h2>页脚颜色设置</h2></th></tr>';
        $this->field_text( 'footer_widgets_bg', '页脚顶部背景', $options, '支持渐变色，默认: #1e293b' );
        $this->field_text( 'footer_bottom_bg', '页脚底部背景', $options, '支持渐变色，默认: #0f172a' );
        $this->field_color( 'footer_text_color', '页脚文字颜色', $options, '#ffffff' );
        
        echo '<tr><th colspan="2"><h2>页脚动画特效</h2></th></tr>';
        $this->field_checkbox( 'footer_effect_enable', '启用背景特效', $options, '在页脚显示动态背景效果' );
        $this->field_select( 'footer_effect_type', '特效类型', $options, array(
            'particles' => '粒子飘动',
            'lines' => '线条流动',
            'waves' => '波浪效果',
            'stars' => '星空闪烁',
        ), '选择动画效果类型' );
    }

    private function render_article_tab( $options ) {
        echo '<tr><th colspan="2"><h2>文章列表设置</h2></th></tr>';
        $this->field_number( 'article_thumb_height', '缩略图高度(px)', $options, '默认: 180' );
        $this->field_checkbox( 'hide_article_thumb', '隐藏缩略图', $options, '勾选后文章列表不显示缩略图' );
        $this->field_checkbox( 'hide_article_excerpt', '隐藏摘要', $options, '勾选后文章列表不显示摘要' );
        $this->field_checkbox( 'hide_article_date', '隐藏日期', $options, '勾选后文章列表不显示发布日期' );
        $this->field_checkbox( 'hide_article_category', '隐藏分类', $options, '勾选后文章列表不显示所属分类' );
        $this->field_checkbox( 'hide_article_author', '隐藏作者', $options, '勾选后文章列表不显示文章作者' );
        $this->field_number( 'article_excerpt_length', '摘要字数', $options, '默认: 80' );
    }

    private function render_pages_tab( $options ) {
        $categories = get_categories( array( 'hide_empty' => false ) );
        $cat_options = array( '' => '全部分类' );
        foreach ( $categories as $cat ) {
            $cat_options[ $cat->slug ] = $cat->name;
        }
        
        echo '<tr><th colspan="2"><h2>产品中心设置</h2></th></tr>';
        $this->field_select( 'products_category', '调用分类', $options, $cat_options, '选择要显示的文章分类' );
        $this->field_number( 'products_per_page', '每页显示数量', $options, '默认: 12' );
        $this->field_select( 'products_layout', '布局样式', $options, array( 'grid' => '网格布局', 'list' => '列表布局' ) );
        $this->field_select( 'products_columns', '每行列数', $options, array( '2' => '2列', '3' => '3列', '4' => '4列' ) );
        $this->field_number( 'products_thumb_height', '缩略图高度(px)', $options, '默认: 200' );
        $this->field_checkbox( 'hide_products_title', '隐藏标题', $options );
        $this->field_checkbox( 'hide_products_date', '隐藏日期', $options );
        $this->field_checkbox( 'hide_products_excerpt', '隐藏摘要', $options );
        
        echo '<tr><th colspan="2"><h2>新闻中心设置</h2></th></tr>';
        $this->field_select( 'news_category', '调用分类', $options, $cat_options );
        $this->field_number( 'news_per_page', '每页显示数量', $options, '默认: 10' );
        $this->field_number( 'news_thumb_height', '缩略图高度(px)', $options, '默认: 150' );
        $this->field_checkbox( 'hide_news_title', '隐藏标题', $options );
        $this->field_checkbox( 'hide_news_date', '隐藏日期', $options );
        $this->field_checkbox( 'hide_news_excerpt', '隐藏摘要', $options );
        $this->field_checkbox( 'hide_news_thumb', '隐藏缩略图', $options );
        
        echo '<tr><th colspan="2"><h2>案例展示设置</h2></th></tr>';
        $this->field_select( 'cases_category', '调用分类', $options, $cat_options );
        $this->field_number( 'cases_per_page', '每页显示数量', $options, '默认: 9' );
        $this->field_select( 'cases_columns', '每行列数', $options, array( '2' => '2列', '3' => '3列', '4' => '4列' ) );
        $this->field_number( 'cases_thumb_height', '缩略图高度(px)', $options, '默认: 220' );
        $this->field_checkbox( 'hide_cases_title', '隐藏标题', $options );
        $this->field_checkbox( 'hide_cases_date', '隐藏日期', $options );
        
        echo '<tr><th colspan="2"><h2>关于我们设置</h2></th></tr>';
        $this->field_checkbox( 'about_show_timeline', '显示发展历程', $options );
        $this->field_checkbox( 'about_show_team', '显示团队成员', $options );
        
        echo '<tr><th colspan="2"><h2>联系我们设置</h2></th></tr>';
        $this->field_checkbox( 'contact_show_form', '显示留言表单', $options, '在联系我们页面显示在线留言表单' );
        $this->field_checkbox( 'contact_show_info', '显示基础信息', $options, '显示企业名称、电话、邮箱、地址' );
        $this->field_image( 'contact_image', '右侧图片', $options, '留言表单关闭时显示的图片' );
    }

    private function render_content_tab( $options ) {
        echo '<tr><th colspan="2"><h2>发展历程</h2><p class="description">在"关于我们"页面显示（需开启显示发展历程）</p></th></tr>';
        $this->field_repeater( 'timeline_items', '时间节点', $options, array(
            array( 'id' => 'year', 'label' => '年份', 'type' => 'text' ),
            array( 'id' => 'title', 'label' => '标题', 'type' => 'text' ),
            array( 'id' => 'desc', 'label' => '描述', 'type' => 'textarea' ),
        ) );
        
        echo '<tr><th colspan="2"><h2>团队成员</h2><p class="description">在"关于我们"页面显示（需开启显示团队成员）</p></th></tr>';
        $this->field_repeater( 'team_members', '成员', $options, array(
            array( 'id' => 'name', 'label' => '姓名', 'type' => 'text' ),
            array( 'id' => 'position', 'label' => '职位', 'type' => 'text' ),
            array( 'id' => 'avatar', 'label' => '头像URL', 'type' => 'text' ),
            array( 'id' => 'desc', 'label' => '简介', 'type' => 'textarea' ),
        ) );
        
        echo '<tr><th colspan="2"><h2>右侧浮动栏</h2></th></tr>';
        $this->field_checkbox( 'float_widget_enable', '启用浮动栏', $options, '开启后在前台显示右侧浮动栏' );
        $this->field_text( 'float_phone', '悬浮电话', $options );
        $this->field_text( 'float_qq', '悬浮QQ', $options );
        $this->field_image( 'float_wechat_qrcode', '悬浮微信二维码', $options );
        
        echo '<tr><th colspan="2"><h2>浮动栏自定义项目</h2><p class="description">添加自定义链接到浮动栏（如在线客服）</p></th></tr>';
        $this->field_repeater( 'float_custom_items', '自定义项目', $options, array(
            array( 'id' => 'title', 'label' => '标题', 'type' => 'text' ),
            array( 'id' => 'url', 'label' => '链接地址', 'type' => 'text' ),
            array( 'id' => 'icon', 'label' => '图标(emoji或iconfont类名，如: iconfont icon-weibo)', 'type' => 'text' ),
            array( 'id' => 'color', 'label' => '背景颜色', 'type' => 'text' ),
        ) );
    }

    private function render_smtp_tab( $options ) {
        echo '<tr><th colspan="2"><h2>SMTP 邮件设置</h2><p class="description">配置SMTP后可实现邮件发送功能</p></th></tr>';
        $this->field_text( 'smtp_host', 'SMTP 服务器', $options, '如: smtp.qq.com, smtp.163.com' );
        $this->field_number( 'smtp_port', 'SMTP 端口', $options, '常用: 465(SSL), 587(TLS), 25' );
        $this->field_select( 'smtp_secure', '加密协议', $options, array(
            'ssl' => 'SSL',
            'tls' => 'TLS',
            '' => '无加密',
        ) );
        $this->field_text( 'smtp_username', '邮箱账号', $options, '发件人邮箱地址' );
        $this->field_text( 'smtp_password', '邮箱密码/授权码', $options, 'QQ邮箱需使用授权码' );
        $this->field_text( 'smtp_sender_name', '发送者名称', $options, '邮件显示的发件人名称' );
        
        echo '<tr><th colspan="2"><h2>留言通知</h2></th></tr>';
        $this->field_checkbox( 'smtp_send_to_admin', '留言发送到邮箱', $options, '用户提交留言时发送邮件通知到管理员邮箱' );
    }

    private function render_advanced_tab( $options ) {
        echo '<tr><th colspan="2"><h2>主题样式</h2></th></tr>';
        $this->field_color( 'primary_color', '主色调', $options, '#2563eb' );
        
        echo '<tr><th colspan="2"><h2>SEO 设置</h2></th></tr>';
        $this->field_text( 'default_title', '默认标题', $options );
        $this->field_textarea( 'default_description', '默认描述', $options );
        $this->field_text( 'default_keywords', '默认关键词', $options );
        
        echo '<tr><th colspan="2"><h2>第三方资源</h2><p class="description">自定义CDN地址，留空使用默认CDN</p></th></tr>';
        $this->field_text( 'swiper_css_url', 'Swiper CSS 地址', $options, '默认: cdn.jsdelivr.net' );
        $this->field_text( 'swiper_js_url', 'Swiper JS 地址', $options, '默认: cdn.jsdelivr.net' );
        
        echo '<tr><th colspan="2"><h2>图标库</h2><p class="description">支持iconfont图标库（CSS方式），在浮动栏自定义项目中输入类名如 <code>iconfont icon-xxx</code></p></th></tr>';
        $this->field_text( 'iconfont_css_url', 'Iconfont CSS 地址', $options, '如: //at.alicdn.com/t/c/font_xxx.css' );
        
        echo '<tr><th colspan="2"><h2>代码设置</h2></th></tr>';
        $this->field_textarea( 'baidu_analytics', '百度统计代码/ID', $options );
        $this->field_textarea( 'custom_css', '自定义 CSS', $options );
        $this->field_textarea( 'custom_js', '自定义 JS', $options );
    }

    // ===== Field Renderers =====
    private function field_text( $id, $label, $options, $desc = '' ) {
        $value = isset( $options[ $id ] ) ? $options[ $id ] : '';
        echo '<tr><th scope="row"><label for="' . $id . '">' . esc_html( $label ) . '</label></th>';
        echo '<td><input type="text" id="' . $id . '" name="' . $this->option_name . '[' . $id . ']" value="' . esc_attr( $value ) . '" class="regular-text" />';
        if ( $desc ) echo '<p class="description">' . esc_html( $desc ) . '</p>';
        echo '</td></tr>';
    }

    private function field_number( $id, $label, $options, $desc = '' ) {
        $value = isset( $options[ $id ] ) ? $options[ $id ] : '';
        echo '<tr><th scope="row"><label for="' . $id . '">' . esc_html( $label ) . '</label></th>';
        echo '<td><input type="number" id="' . $id . '" name="' . $this->option_name . '[' . $id . ']" value="' . esc_attr( $value ) . '" class="small-text" />';
        if ( $desc ) echo '<p class="description">' . esc_html( $desc ) . '</p>';
        echo '</td></tr>';
    }

    private function field_textarea( $id, $label, $options, $desc = '' ) {
        $value = isset( $options[ $id ] ) ? $options[ $id ] : '';
        echo '<tr><th scope="row"><label for="' . $id . '">' . esc_html( $label ) . '</label></th>';
        echo '<td><textarea id="' . $id . '" name="' . $this->option_name . '[' . $id . ']" rows="4" class="large-text">' . esc_textarea( $value ) . '</textarea>';
        if ( $desc ) echo '<p class="description">' . esc_html( $desc ) . '</p>';
        echo '</td></tr>';
    }

    private function field_image( $id, $label, $options, $desc = '' ) {
        $value = isset( $options[ $id ] ) ? $options[ $id ] : '';
        echo '<tr><th scope="row"><label>' . esc_html( $label ) . '</label></th><td>';
        echo '<div class="ds-image-field">';
        echo '<input type="text" name="' . $this->option_name . '[' . $id . ']" value="' . esc_attr( $value ) . '" class="ds-image-url regular-text" placeholder="输入图片URL或点击选择" />';
        echo '<button type="button" class="button ds-upload-image-btn">选择图片</button> ';
        echo '<button type="button" class="button ds-remove-image-btn">移除</button>';
        echo $value ? '<img src="' . esc_url( $value ) . '" class="ds-image-preview" style="display:block;max-width:200px;margin-top:10px;" />' : '<img class="ds-image-preview" style="display:none;max-width:200px;margin-top:10px;" />';
        echo '</div>';
        if ( $desc ) echo '<p class="description">' . esc_html( $desc ) . '</p>';
        echo '</td></tr>';
    }

    private function field_color( $id, $label, $options, $default = '#2563eb' ) {
        $value = isset( $options[ $id ] ) ? $options[ $id ] : $default;
        echo '<tr><th scope="row"><label for="' . $id . '">' . esc_html( $label ) . '</label></th>';
        echo '<td><input type="text" id="' . $id . '" name="' . $this->option_name . '[' . $id . ']" value="' . esc_attr( $value ) . '" class="ds-color-picker" data-default-color="' . esc_attr( $default ) . '" /></td></tr>';
    }

    private function field_checkbox( $id, $label, $options, $desc = '' ) {
        $value = isset( $options[ $id ] ) ? $options[ $id ] : '';
        echo '<tr><th scope="row">' . esc_html( $label ) . '</th>';
        echo '<td><label><input type="checkbox" name="' . $this->option_name . '[' . $id . ']" value="1"' . checked( $value, '1', false ) . ' /> ';
        if ( $desc ) echo esc_html( $desc );
        echo '</label></td></tr>';
    }

    private function field_select( $id, $label, $options, $choices, $desc = '' ) {
        $value = isset( $options[ $id ] ) ? $options[ $id ] : '';
        echo '<tr><th scope="row"><label for="' . $id . '">' . esc_html( $label ) . '</label></th><td>';
        echo '<select id="' . $id . '" name="' . $this->option_name . '[' . $id . ']">';
        foreach ( $choices as $k => $v ) {
            echo '<option value="' . esc_attr( $k ) . '"' . selected( $value, $k, false ) . '>' . esc_html( $v ) . '</option>';
        }
        echo '</select>';
        if ( $desc ) echo '<p class="description">' . esc_html( $desc ) . '</p>';
        echo '</td></tr>';
    }

    private function field_repeater( $id, $label, $options, $fields ) {
        $items = isset( $options[ $id ] ) && is_array( $options[ $id ] ) ? $options[ $id ] : array();
        echo '<tr><th scope="row">' . esc_html( $label ) . '</th><td>';
        echo '<div class="ds-repeater-wrap">';
        echo '<div class="ds-repeater-list" style="margin-bottom: 10px;">';
        
        foreach ( $items as $idx => $item ) {
            echo '<div class="ds-repeater-item" style="background: #f9f9f9; padding: 15px; margin-bottom: 10px; border-radius: 5px; position: relative; border: 1px solid #ddd;">';
            echo '<a href="#" class="ds-repeater-remove" style="position: absolute; top: 5px; right: 10px; color: #a00; text-decoration: none;">删除</a>';
            foreach ( $fields as $f ) {
                $fval = isset( $item[ $f['id'] ] ) ? $item[ $f['id'] ] : '';
                $fname = $this->option_name . '[' . $id . '][' . $idx . '][' . $f['id'] . ']';
                echo '<div style="margin-bottom: 8px;"><label><strong>' . esc_html( $f['label'] ) . '</strong></label><br>';
                if ( $f['type'] === 'textarea' ) {
                    echo '<textarea name="' . esc_attr( $fname ) . '" rows="2" style="width:100%;">' . esc_textarea( $fval ) . '</textarea>';
                } else {
                    echo '<input type="text" name="' . esc_attr( $fname ) . '" value="' . esc_attr( $fval ) . '" style="width:100%;" />';
                }
                echo '</div>';
            }
            echo '</div>';
        }
        
        echo '</div>';
        
        $tpl = '<div class="ds-repeater-item" style="background: #f9f9f9; padding: 15px; margin-bottom: 10px; border-radius: 5px; position: relative; border: 1px solid #ddd;">';
        $tpl .= '<a href="#" class="ds-repeater-remove" style="position: absolute; top: 5px; right: 10px; color: #a00; text-decoration: none;">删除</a>';
        foreach ( $fields as $f ) {
            $fname = $this->option_name . '[' . $id . '][__IDX__][' . $f['id'] . ']';
            $tpl .= '<div style="margin-bottom: 8px;"><label><strong>' . esc_html( $f['label'] ) . '</strong></label><br>';
            if ( $f['type'] === 'textarea' ) {
                $tpl .= '<textarea name="' . esc_attr( $fname ) . '" rows="2" style="width:100%;"></textarea>';
            } else {
                $tpl .= '<input type="text" name="' . esc_attr( $fname ) . '" value="" style="width:100%;" />';
            }
            $tpl .= '</div>';
        }
        $tpl .= '</div>';
        
        echo '<div class="ds-repeater-tpl" data-template="' . esc_attr( $tpl ) . '" style="display:none;"></div>';
        echo '<button type="button" class="button ds-repeater-add">+ 添加</button>';
        echo '</div></td></tr>';
    }

    public function sanitize_options( $input ) {
        if ( ! is_array( $input ) ) return array();
        
        // 获取现有选项，确保其他选项卡的设置不会被清空
        $existing_options = get_option( $this->option_name, array() );
        if ( ! is_array( $existing_options ) ) {
            $existing_options = array();
        }
        
        // 清理新提交的数据
        $sanitized = array();
        foreach ( $input as $key => $value ) {
            if ( is_array( $value ) ) {
                $sanitized[ $key ] = $this->sanitize_array_recursive( $value );
            } else {
                $sanitized[ $key ] = wp_kses_post( $value );
            }
        }
        
        // 合并：用新数据覆盖现有数据
        $merged = array_merge( $existing_options, $sanitized );
        
        return $merged;
    }

    private function sanitize_array_recursive( $arr ) {
        $result = array();
        foreach ( $arr as $k => $v ) {
            if ( is_array( $v ) ) {
                $result[ $k ] = $this->sanitize_array_recursive( $v );
            } else {
                // icon 字段允许 HTML 标签（如 <i class="iconfont icon-xxx"></i>）
                if ( $k === 'icon' ) {
                    $result[ $k ] = wp_kses_post( $v );
                } else {
                    $result[ $k ] = sanitize_text_field( $v );
                }
            }
        }
        return $result;
    }
}
