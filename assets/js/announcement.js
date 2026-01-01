/**
 * 公告系统 JavaScript
 * 
 * 处理公告显示/隐藏、频率控制、用户偏好
 */

(function () {
    'use strict';

    // 等待 DOM 加载完成
    document.addEventListener('DOMContentLoaded', function () {
        var announcement = document.getElementById('ds-announcement');
        if (!announcement) return;

        var config = window.dsAnnouncement || {};
        var announcementId = config.announcementId || 'default';
        var frequency = config.frequency || 'always';
        var allowDismiss = config.allowDismiss !== false;

        // Cookie 工具函数
        var Cookie = {
            set: function (name, value, days) {
                var expires = '';
                if (days) {
                    var date = new Date();
                    date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
                    expires = '; expires=' + date.toUTCString();
                }
                document.cookie = name + '=' + encodeURIComponent(value) + expires + '; path=/';
            },
            get: function (name) {
                var nameEQ = name + '=';
                var ca = document.cookie.split(';');
                for (var i = 0; i < ca.length; i++) {
                    var c = ca[i].trim();
                    if (c.indexOf(nameEQ) === 0) {
                        return decodeURIComponent(c.substring(nameEQ.length));
                    }
                }
                return null;
            },
            delete: function (name) {
                this.set(name, '', -1);
            }
        };

        // 检查是否应该显示公告
        function shouldShow() {
            var cookieName = 'ds_ann_' + announcementId;
            var dismissed = Cookie.get(cookieName);

            if (frequency === 'once_day') {
                // 每天一次模式：检查今天是否已显示
                if (dismissed) {
                    var today = new Date().toDateString();
                    if (dismissed === today) {
                        return false;
                    }
                }
            } else if (frequency === 'always') {
                // 每次访问都显示，但检查用户是否选择了"今日不再显示"
                if (dismissed) {
                    var today = new Date().toDateString();
                    if (dismissed === today) {
                        return false;
                    }
                }
            }

            return true;
        }

        // 显示公告
        function showAnnouncement() {
            announcement.style.display = 'flex';
            document.body.style.overflow = 'hidden';

            // 记录显示（每天一次模式）
            if (frequency === 'once_day') {
                var cookieName = 'ds_ann_' + announcementId;
                var today = new Date().toDateString();
                Cookie.set(cookieName, today, 1);
            }
        }

        // 隐藏公告
        function hideAnnouncement() {
            announcement.style.display = 'none';
            document.body.style.overflow = '';
        }

        // 关闭公告
        function closeAnnouncement() {
            var todayDismiss = document.getElementById('announcement-today-dismiss');

            // 检查是否勾选了"今日不再显示"
            if (todayDismiss && todayDismiss.checked) {
                var cookieName = 'ds_ann_' + announcementId;
                var today = new Date().toDateString();
                Cookie.set(cookieName, today, 1);
            }

            // 动画关闭
            var modal = announcement.querySelector('.announcement-modal');
            if (modal) {
                modal.style.animation = 'announcementSlideOut 0.3s ease forwards';
            }
            announcement.querySelector('.announcement-overlay').style.opacity = '0';

            setTimeout(function () {
                hideAnnouncement();
            }, 300);
        }

        // 添加关闭动画
        var style = document.createElement('style');
        style.textContent = '@keyframes announcementSlideOut { to { opacity: 0; transform: scale(0.9) translateY(20px); } }';
        document.head.appendChild(style);

        // 绑定事件
        var closeBtn = announcement.querySelector('.announcement-close');
        if (closeBtn) {
            closeBtn.addEventListener('click', closeAnnouncement);
        }

        // 点击遮罩关闭
        var overlay = announcement.querySelector('.announcement-overlay');
        if (overlay) {
            overlay.addEventListener('click', closeAnnouncement);
        }

        // ESC 键关闭
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && announcement.style.display !== 'none') {
                closeAnnouncement();
            }
        });

        // 阻止模态框内点击冒泡
        var modal = announcement.querySelector('.announcement-modal');
        if (modal) {
            modal.addEventListener('click', function (e) {
                e.stopPropagation();
            });
        }

        // 初始化：检查并显示
        if (shouldShow()) {
            // 延迟显示，等待页面加载完成
            setTimeout(showAnnouncement, 500);
        }
    });
})();
