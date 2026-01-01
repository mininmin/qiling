<?php
/**
 * Template Name: 个人中心
 *
 * @package Developer_Starter
 */

// 未登录用户跳转到登录页或首页
if ( ! is_user_logged_in() ) {
    $login_page = developer_starter_get_option( 'login_page_id', '' );
    if ( $login_page ) {
        wp_redirect( get_permalink( $login_page ) );
    } else {
        wp_redirect( wp_login_url( get_permalink() ) );
    }
    exit;
}

// 加载个人中心专用样式
add_action( 'wp_enqueue_scripts', function() {
    wp_enqueue_style(
        'developer-starter-account',
        DEVELOPER_STARTER_ASSETS . '/css/account.css',
        array( 'developer-starter-main' ),
        developer_starter_get_assets_version()
    );
}, 20 );

get_header();

$current_user = wp_get_current_user();
$user_id = $current_user->ID;

// 当前激活的标签
$active_tab = isset( $_GET['tab'] ) ? sanitize_key( $_GET['tab'] ) : 'profile';

// 检查 WooCommerce 是否激活
$woo_active = class_exists( 'WooCommerce' );

// 定义可用标签
$tabs = array(
    'profile' => array( 'icon' => 'user', 'label' => '我的资料' ),
    'social' => array( 'icon' => 'share', 'label' => '社交媒体' ),
    'security' => array( 'icon' => 'lock', 'label' => '账户安全' ),
);

// WooCommerce 标签
if ( $woo_active ) {
    $tabs['orders'] = array( 'icon' => 'package', 'label' => '我的订单' );
    $tabs['address'] = array( 'icon' => 'map', 'label' => '收货地址' );
}

// 处理表单提交
$message = '';
$message_type = '';

if ( $_SERVER['REQUEST_METHOD'] === 'POST' && isset( $_POST['account_nonce'] ) ) {
    if ( wp_verify_nonce( $_POST['account_nonce'], 'developer_starter_account' ) ) {
        $action = isset( $_POST['account_action'] ) ? $_POST['account_action'] : '';
        
        if ( $action === 'update_profile' ) {
            $display_name = sanitize_text_field( $_POST['display_name'] ?? '' );
            $user_email = sanitize_email( $_POST['user_email'] ?? '' );
            $user_url = esc_url_raw( $_POST['user_url'] ?? '' );
            $description = sanitize_textarea_field( $_POST['description'] ?? '' );
            
            $userdata = array(
                'ID' => $user_id,
                'display_name' => $display_name,
                'user_email' => $user_email,
                'user_url' => $user_url,
                'description' => $description,
            );
            
            $result = wp_update_user( $userdata );
            if ( is_wp_error( $result ) ) {
                $message = $result->get_error_message();
                $message_type = 'error';
            } else {
                $message = '资料更新成功！';
                $message_type = 'success';
                $current_user = get_userdata( $user_id ); // 刷新用户数据
            }
        }
        
        if ( $action === 'update_password' ) {
            $current_pass = $_POST['current_password'] ?? '';
            $new_pass = $_POST['new_password'] ?? '';
            $confirm_pass = $_POST['confirm_password'] ?? '';
            
            if ( empty( $current_pass ) || empty( $new_pass ) || empty( $confirm_pass ) ) {
                $message = '请填写所有密码字段';
                $message_type = 'error';
            } elseif ( $new_pass !== $confirm_pass ) {
                $message = '新密码与确认密码不一致';
                $message_type = 'error';
            } elseif ( strlen( $new_pass ) < 6 ) {
                $message = '新密码长度至少6位';
                $message_type = 'error';
            } elseif ( ! wp_check_password( $current_pass, $current_user->user_pass, $user_id ) ) {
                $message = '当前密码不正确';
                $message_type = 'error';
            } else {
                wp_set_password( $new_pass, $user_id );
                wp_set_current_user( $user_id );
                wp_set_auth_cookie( $user_id );
                $message = '密码修改成功！';
                $message_type = 'success';
            }
        }
        
        if ( $action === 'update_social' ) {
            // 保存社交媒体链接
            $social_keys = array( 'user_weibo', 'user_twitter', 'user_wechat', 'user_github', 'user_bilibili', 'user_zhihu', 'user_website' );
            foreach ( $social_keys as $key ) {
                if ( isset( $_POST[ $key ] ) ) {
                    $value = esc_url_raw( $_POST[ $key ] );
                    update_user_meta( $user_id, $key, $value );
                }
            }
            // 使用 PRG 模式重定向，避免表单重复提交
            wp_safe_redirect( add_query_arg( array( 'tab' => 'social', 'saved' => '1' ), get_permalink() ) );
            exit;
        }
    }
}

// 检查是否刚保存成功
if ( isset( $_GET['saved'] ) && $_GET['saved'] === '1' ) {
    $message = '保存成功！';
    $message_type = 'success';
}

// 获取头像
$avatar_url = get_avatar_url( $user_id, array( 'size' => 150 ) );
?>

<div class="account-page">
    <div class="account-header">
        <div class="container">
            <div class="account-user-card" data-aos="fade-up">
                <div class="user-avatar">
                    <?php echo get_avatar( $user_id, 100 ); ?>
                </div>
                <div class="user-info">
                    <h2 class="user-name"><?php echo esc_html( $current_user->display_name ); ?></h2>
                    <p class="user-email"><?php echo esc_html( $current_user->user_email ); ?></p>
                    <p class="user-meta">
                        <span>注册时间：<?php echo date_i18n( 'Y-m-d', strtotime( $current_user->user_registered ) ); ?></span>
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="account-content section-padding">
        <div class="container">
            <div class="account-layout">
                <!-- 侧边栏 -->
                <aside class="account-sidebar" data-aos="fade-right">
                    <nav class="account-nav">
                        <?php foreach ( $tabs as $tab_key => $tab_data ) : 
                            $is_active = $active_tab === $tab_key;
                        ?>
                            <a href="?tab=<?php echo $tab_key; ?>" class="account-nav-item <?php echo $is_active ? 'active' : ''; ?>">
                                <span class="nav-icon"><?php echo developer_starter_account_icon( $tab_data['icon'] ); ?></span>
                                <span class="nav-label"><?php echo esc_html( $tab_data['label'] ); ?></span>
                            </a>
                        <?php endforeach; ?>
                        
                        <div class="nav-divider"></div>
                        
                        <a href="<?php echo esc_url( wp_logout_url( home_url() ) ); ?>" class="account-nav-item logout">
                            <span class="nav-icon"><?php echo developer_starter_account_icon( 'logout' ); ?></span>
                            <span class="nav-label">退出登录</span>
                        </a>
                    </nav>
                </aside>
                
                <!-- 主内容区 -->
                <main class="account-main" data-aos="fade-left">
                    <?php if ( $message ) : ?>
                        <div class="account-message <?php echo $message_type; ?>">
                            <?php echo esc_html( $message ); ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ( $active_tab === 'profile' ) : 
                        $avatar_upload_enable = developer_starter_get_option( 'user_avatar_upload_enable', '' );
                    ?>
                    <!-- 我的资料 -->
                    <div class="account-section">
                        <?php if ( $avatar_upload_enable ) : ?>
                        <h3 class="section-title">我的头像</h3>
                        <div class="avatar-upload-section" style="margin-bottom: 30px;">
                            <div class="avatar-upload-container" id="avatar-upload-container" style="
                                display: flex;
                                align-items: center;
                                gap: 24px;
                                padding: 24px;
                                background: linear-gradient(135deg, rgba(37, 99, 235, 0.03) 0%, rgba(124, 58, 237, 0.03) 100%);
                                border-radius: 12px;
                                border: 2px dashed var(--color-gray-200);
                                transition: all 0.3s ease;
                            ">
                                <div class="current-avatar" style="flex-shrink: 0;">
                                    <img src="<?php echo esc_url( $avatar_url ); ?>" alt="当前头像" id="current-avatar-img" style="
                                        width: 100px;
                                        height: 100px;
                                        border-radius: 50%;
                                        object-fit: cover;
                                        border: 3px solid #fff;
                                        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
                                    " />
                                </div>
                                <div class="avatar-upload-info" style="flex: 1;">
                                    <h4 style="margin: 0 0 8px; font-size: 1rem; font-weight: 600;">上传新头像</h4>
                                    <p style="margin: 0 0 12px; color: var(--color-gray-500); font-size: 0.875rem;">
                                        支持 JPG、PNG、GIF、WebP 格式，最大 2MB
                                    </p>
                                    <div class="avatar-upload-actions">
                                        <label for="avatar-file-input" class="btn-secondary" style="
                                            display: inline-flex;
                                            align-items: center;
                                            gap: 6px;
                                            padding: 10px 20px;
                                            font-size: 0.875rem;
                                            border-radius: 8px;
                                            cursor: pointer;
                                            background: linear-gradient(135deg, var(--color-primary) 0%, #7c3aed 100%);
                                            color: #fff;
                                            border: none;
                                            transition: all 0.3s;
                                        ">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                                            选择图片
                                        </label>
                                        <input type="file" id="avatar-file-input" accept="image/jpeg,image/png,image/gif,image/webp" style="display: none;" />
                                    </div>
                                    <div id="avatar-upload-status" style="margin-top: 10px; font-size: 0.875rem;"></div>
                                </div>
                            </div>
                        </div>
                        <script>
                        (function() {
                            var container = document.getElementById('avatar-upload-container');
                            var fileInput = document.getElementById('avatar-file-input');
                            var avatarImg = document.getElementById('current-avatar-img');
                            var statusDiv = document.getElementById('avatar-upload-status');
                            var ajaxUrl = '<?php echo admin_url('admin-ajax.php'); ?>';
                            var nonce = '<?php echo wp_create_nonce('developer_starter_avatar_upload'); ?>';
                            
                            // 拖拽效果
                            container.addEventListener('dragover', function(e) {
                                e.preventDefault();
                                container.style.borderColor = 'var(--color-primary)';
                                container.style.background = 'rgba(37, 99, 235, 0.08)';
                            });
                            container.addEventListener('dragleave', function(e) {
                                e.preventDefault();
                                container.style.borderColor = 'var(--color-gray-200)';
                                container.style.background = '';
                            });
                            container.addEventListener('drop', function(e) {
                                e.preventDefault();
                                container.style.borderColor = 'var(--color-gray-200)';
                                container.style.background = '';
                                if (e.dataTransfer.files.length) {
                                    handleFile(e.dataTransfer.files[0]);
                                }
                            });
                            
                            // 文件选择
                            fileInput.addEventListener('change', function() {
                                if (this.files.length) {
                                    handleFile(this.files[0]);
                                }
                            });
                            
                            function handleFile(file) {
                                // 验证文件类型
                                var allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                                if (allowedTypes.indexOf(file.type) === -1) {
                                    statusDiv.innerHTML = '<span style="color: #ef4444;">只允许上传 JPG、PNG、GIF、WebP 格式</span>';
                                    return;
                                }
                                
                                // 验证文件大小
                                if (file.size > 2 * 1024 * 1024) {
                                    statusDiv.innerHTML = '<span style="color: #ef4444;">图片大小不能超过 2MB</span>';
                                    return;
                                }
                                
                                // 显示预览
                                var reader = new FileReader();
                                reader.onload = function(e) {
                                    avatarImg.src = e.target.result;
                                };
                                reader.readAsDataURL(file);
                                
                                // 上传
                                statusDiv.innerHTML = '<span style="color: var(--color-primary);">上传中...</span>';
                                
                                var formData = new FormData();
                                formData.append('action', 'developer_starter_upload_avatar');
                                formData.append('nonce', nonce);
                                formData.append('avatar', file);
                                
                                fetch(ajaxUrl, {
                                    method: 'POST',
                                    body: formData,
                                    credentials: 'same-origin'
                                })
                                .then(function(r) { return r.json(); })
                                .then(function(data) {
                                    if (data.success) {
                                        statusDiv.innerHTML = '<span style="color: #10b981;">✓ ' + data.data.message + '</span>';
                                        avatarImg.src = data.data.avatar_url;
                                        // 更新页面其他头像
                                        document.querySelectorAll('.user-avatar img, .avatar').forEach(function(img) {
                                            if (img.id !== 'current-avatar-img') {
                                                img.src = data.data.avatar_url;
                                            }
                                        });
                                    } else {
                                        statusDiv.innerHTML = '<span style="color: #ef4444;">' + data.data.message + '</span>';
                                    }
                                })
                                .catch(function() {
                                    statusDiv.innerHTML = '<span style="color: #ef4444;">上传失败，请重试</span>';
                                });
                            }
                        })();
                        </script>
                        <?php endif; ?>
                        
                        <h3 class="section-title">基本资料</h3>
                        <form method="post" class="account-form">
                            <?php wp_nonce_field( 'developer_starter_account', 'account_nonce' ); ?>
                            <input type="hidden" name="account_action" value="update_profile" />
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label>用户名</label>
                                    <input type="text" value="<?php echo esc_attr( $current_user->user_login ); ?>" disabled />
                                    <p class="form-hint">用户名不可修改</p>
                                </div>
                                <div class="form-group">
                                    <label>显示名称</label>
                                    <input type="text" name="display_name" value="<?php echo esc_attr( $current_user->display_name ); ?>" required />
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label>电子邮箱</label>
                                    <input type="email" name="user_email" value="<?php echo esc_attr( $current_user->user_email ); ?>" required />
                                </div>
                                <div class="form-group">
                                    <label>个人网站</label>
                                    <input type="url" name="user_url" value="<?php echo esc_attr( $current_user->user_url ); ?>" placeholder="https://" />
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label>个人简介</label>
                                <textarea name="description" rows="4" placeholder="介绍一下自己..."><?php echo esc_textarea( $current_user->description ); ?></textarea>
                            </div>
                            
                            <div class="form-actions">
                                <button type="submit" class="btn-primary">保存修改</button>
                            </div>
                        </form>
                    </div>
                    
                    <?php elseif ( $active_tab === 'security' ) : ?>
                    <!-- 账户安全 -->
                    <div class="account-section">
                        <h3 class="section-title">修改密码</h3>
                        <form method="post" class="account-form">
                            <?php wp_nonce_field( 'developer_starter_account', 'account_nonce' ); ?>
                            <input type="hidden" name="account_action" value="update_password" />
                            
                            <div class="form-group">
                                <label>当前密码</label>
                                <input type="password" name="current_password" required autocomplete="current-password" />
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label>新密码</label>
                                    <input type="password" name="new_password" required minlength="6" autocomplete="new-password" />
                                </div>
                                <div class="form-group">
                                    <label>确认新密码</label>
                                    <input type="password" name="confirm_password" required minlength="6" autocomplete="new-password" />
                                </div>
                            </div>
                            
                            <div class="form-actions">
                                <button type="submit" class="btn-primary">更新密码</button>
                            </div>
                        </form>
                    </div>
                    
                    <?php elseif ( $active_tab === 'social' ) : ?>
                    <!-- 社交媒体设置 -->
                    <div class="account-section">
                        <h3 class="section-title">社交媒体链接</h3>
                        <p class="section-desc" style="color: var(--color-gray-500); margin-bottom: 24px;">设置您的社交媒体链接，这些信息将显示在您的作者信息卡片中。</p>
                        <form method="post" class="account-form" enctype="multipart/form-data">
                            <?php wp_nonce_field( 'developer_starter_account', 'account_nonce' ); ?>
                            <input type="hidden" name="account_action" value="update_social" />
                            
                            <?php 
                            // 获取后台启用的社交链接字段
                            $social_fields = array(
                                'user_social_weibo' => array( 'key' => 'user_weibo', 'label' => '微博', 'type' => 'url', 'placeholder' => 'https://weibo.com/u/...' ),
                                'user_social_twitter' => array( 'key' => 'user_twitter', 'label' => 'X (Twitter)', 'type' => 'url', 'placeholder' => 'https://x.com/...' ),
                                'user_social_wechat' => array( 'key' => 'user_wechat', 'label' => '微信二维码', 'type' => 'image', 'placeholder' => '上传微信二维码图片' ),
                                'user_social_github' => array( 'key' => 'user_github', 'label' => 'GitHub', 'type' => 'url', 'placeholder' => 'https://github.com/...' ),
                                'user_social_bilibili' => array( 'key' => 'user_bilibili', 'label' => 'B站', 'type' => 'url', 'placeholder' => 'https://space.bilibili.com/...' ),
                                'user_social_zhihu' => array( 'key' => 'user_zhihu', 'label' => '知乎', 'type' => 'url', 'placeholder' => 'https://www.zhihu.com/people/...' ),
                                'user_social_website' => array( 'key' => 'user_website', 'label' => '个人网站', 'type' => 'url', 'placeholder' => 'https://...' ),
                            );
                            
                            $has_fields = false;
                            foreach ( $social_fields as $option_key => $field ) :
                                if ( ! developer_starter_get_option( $option_key, '' ) ) continue;
                                $has_fields = true;
                                $current_value = get_user_meta( $user_id, $field['key'], true );
                            ?>
                                <div class="form-group">
                                    <label><?php echo esc_html( $field['label'] ); ?></label>
                                    <?php if ( $field['type'] === 'image' ) : ?>
                                        <div class="wechat-qr-upload">
                                            <?php if ( $current_value ) : ?>
                                                <div class="current-qr" style="margin-bottom: 10px;">
                                                    <img src="<?php echo esc_url( $current_value ); ?>" alt="微信二维码" style="max-width: 120px; border-radius: 8px;" />
                                                </div>
                                            <?php endif; ?>
                                            <input type="url" name="<?php echo esc_attr( $field['key'] ); ?>" value="<?php echo esc_attr( $current_value ); ?>" placeholder="输入二维码图片URL" />
                                            <p class="form-hint">请输入微信二维码图片的URL地址，鼠标悬停时会显示此二维码</p>
                                        </div>
                                    <?php else : ?>
                                        <input type="url" name="<?php echo esc_attr( $field['key'] ); ?>" value="<?php echo esc_attr( $current_value ); ?>" placeholder="<?php echo esc_attr( $field['placeholder'] ); ?>" />
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                            
                            <?php if ( ! $has_fields ) : ?>
                                <div class="notice-info" style="padding: 20px; background: #f8fafc; border-radius: 8px; text-align: center; color: var(--color-gray-500);">
                                    <p>管理员尚未启用任何社交链接字段。</p>
                                </div>
                            <?php else : ?>
                                <div class="form-actions">
                                    <button type="submit" class="btn-primary">保存社交信息</button>
                                </div>
                            <?php endif; ?>
                        </form>
                    </div>
                    
                    <?php elseif ( $active_tab === 'orders' && $woo_active ) : ?>
                    <!-- 我的订单 (WooCommerce) -->
                    <div class="account-section">
                        <h3 class="section-title">我的订单</h3>
                        <?php 
                        // 使用 WooCommerce 的订单列表
                        echo do_shortcode( '[woocommerce_my_account]' );
                        ?>
                    </div>
                    
                    <?php elseif ( $active_tab === 'address' && $woo_active ) : ?>
                    <!-- 收货地址 (WooCommerce) -->
                    <div class="account-section">
                        <h3 class="section-title">收货地址</h3>
                        <?php 
                        // 使用 WooCommerce 的地址管理
                        $addresses = wc_get_account_menu_items();
                        wc_get_template( 'myaccount/my-address.php' );
                        ?>
                    </div>
                    
                    <?php endif; ?>
                    
                </main>
            </div>
        </div>
    </div>
</div>

<?php
// 辅助函数：输出图标
function developer_starter_account_icon( $icon ) {
    $icons = array(
        'user' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>',
        'share' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg>',
        'lock' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>',
        'package' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16.5 9.4l-9-5.19M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>',
        'map' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>',
        'logout' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>',
    );
    return isset( $icons[ $icon ] ) ? $icons[ $icon ] : '';
}

get_footer();
?>
