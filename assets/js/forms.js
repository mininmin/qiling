/**
 * 表单前端交互
 *
 * @package Developer_Starter
 * @since 1.0.2
 */

(function ($) {
    'use strict';

    $(document).ready(function () {
        // 表单提交处理
        $('.developer-form').on('submit', function (e) {
            e.preventDefault();

            var $form = $(this);
            var $wrap = $form.closest('.developer-form-wrap');
            var $submitBtn = $form.find('.btn-submit');
            var $btnText = $submitBtn.find('.btn-text');
            var $btnLoading = $submitBtn.find('.btn-loading');
            var $message = $form.find('.form-message');

            // 防止重复提交
            if ($submitBtn.prop('disabled')) {
                return;
            }

            // 收集表单数据
            var formData = new FormData($form[0]);
            formData.append('action', 'developer_submit_form');
            formData.append('nonce', developerForms.nonce);

            // 显示加载状态
            $submitBtn.prop('disabled', true);
            $btnText.hide();
            $btnLoading.show();
            $message.hide().removeClass('success error');

            $.ajax({
                url: developerForms.ajaxUrl,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    if (response.success) {
                        $message.addClass('success').html(response.data.message).slideDown();
                        $form[0].reset();

                        // 滚动到消息
                        $('html, body').animate({
                            scrollTop: $wrap.offset().top - 100
                        }, 500);
                    } else {
                        $message.addClass('error').html(response.data.message).slideDown();
                    }
                },
                error: function () {
                    $message.addClass('error').html('网络错误，请稍后重试').slideDown();
                },
                complete: function () {
                    $submitBtn.prop('disabled', false);
                    $btnText.show();
                    $btnLoading.hide();
                }
            });
        });

        // 输入验证反馈
        $('.developer-form input, .developer-form textarea, .developer-form select').on('blur', function () {
            var $input = $(this);
            if ($input.prop('required') && !$input.val().trim()) {
                $input.addClass('error');
            } else {
                $input.removeClass('error');
            }
        });

        // 邮箱格式验证
        $('.developer-form input[type="email"]').on('blur', function () {
            var $input = $(this);
            var value = $input.val().trim();
            var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if (value && !emailRegex.test(value)) {
                $input.addClass('error');
            }
        });

        // 电话格式验证
        $('.developer-form input[type="tel"]').on('blur', function () {
            var $input = $(this);
            var value = $input.val().trim();
            var phoneRegex = /^1[3-9]\d{9}$/;

            if (value && !phoneRegex.test(value)) {
                $input.addClass('error');
            }
        });
    });
})(jQuery);
