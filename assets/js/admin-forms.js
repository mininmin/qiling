/**
 * 表单后台管理交互
 *
 * @package Developer_Starter
 * @since 1.0.2
 */

(function ($) {
    'use strict';

    $(document).ready(function () {
        var fieldIndex = $('.field-item').length;

        // 字段类型标签
        var typeLabels = {
            'text': '文本',
            'email': '邮箱',
            'tel': '电话',
            'textarea': '多行',
            'select': '下拉',
            'radio': '单选',
            'checkbox': '多选',
            'date': '日期'
        };

        // 需要选项的字段类型
        var optionTypes = ['select', 'radio', 'checkbox'];

        // ========================================
        // 字段拖拽排序
        // ========================================
        $('#fields-list').sortable({
            handle: '.field-drag',
            placeholder: 'field-placeholder',
            update: function () {
                updateFieldIndexes();
            }
        });

        // ========================================
        // 添加字段
        // ========================================
        $('.add-field').on('click', function () {
            var type = $(this).data('type');
            var label = typeLabels[type] || type;
            var hasOptions = optionTypes.indexOf(type) > -1;

            var fieldHtml = '<div class="field-item" data-index="' + fieldIndex + '">' +
                '<div class="field-header">' +
                '<span class="field-drag">☰</span>' +
                '<span class="field-type-badge">' + label + '</span>' +
                '<span class="field-label">新字段</span>' +
                '<span class="field-actions">' +
                '<button type="button" class="edit-field" title="编辑">✏️</button>' +
                '<button type="button" class="delete-field" title="删除">🗑️</button>' +
                '</span>' +
                '</div>' +
                '<div class="field-editor" style="display: none;">' +
                '<table class="field-settings">' +
                '<tr>' +
                '<td width="80"><label>字段名</label></td>' +
                '<td><input type="text" class="field-name" value="field_' + fieldIndex + '" /></td>' +
                '</tr>' +
                '<tr>' +
                '<td><label>标签</label></td>' +
                '<td><input type="text" class="field-label-input" value="新字段" /></td>' +
                '</tr>' +
                '<tr>' +
                '<td><label>占位符</label></td>' +
                '<td><input type="text" class="field-placeholder" value="" /></td>' +
                '</tr>' +
                '<tr class="options-row" style="' + (hasOptions ? '' : 'display:none') + '">' +
                '<td><label>选项</label></td>' +
                '<td><textarea class="field-options" rows="3" placeholder="每行一个选项"></textarea></td>' +
                '</tr>' +
                '<tr>' +
                '<td><label>宽度</label></td>' +
                '<td>' +
                '<select class="field-width">' +
                '<option value="100">100%</option>' +
                '<option value="50">50%</option>' +
                '<option value="33">33%</option>' +
                '</select>' +
                '</td>' +
                '</tr>' +
                '<tr>' +
                '<td><label>必填</label></td>' +
                '<td><input type="checkbox" class="field-required-input" /></td>' +
                '</tr>' +
                '</table>' +
                '<input type="hidden" class="field-type" value="' + type + '" />' +
                '</div>' +
                '</div>';

            $('#fields-list').append(fieldHtml);
            fieldIndex++;

            // 展开编辑
            $('#fields-list .field-item:last .field-editor').slideDown();
        });

        // ========================================
        // 编辑字段
        // ========================================
        $(document).on('click', '.edit-field', function (e) {
            e.stopPropagation();
            var $item = $(this).closest('.field-item');
            var $editor = $item.find('.field-editor');

            if ($editor.is(':visible')) {
                $editor.slideUp();
            } else {
                $('.field-editor').slideUp();
                $editor.slideDown();
            }
        });

        // ========================================
        // 更新字段标签显示
        // ========================================
        $(document).on('input', '.field-label-input', function () {
            var $item = $(this).closest('.field-item');
            var label = $(this).val() || '未命名字段';
            $item.find('.field-label').text(label);
        });

        // ========================================
        // 更新必填星号
        // ========================================
        $(document).on('change', '.field-required-input', function () {
            var $item = $(this).closest('.field-item');
            var isRequired = $(this).is(':checked');

            if (isRequired) {
                if (!$item.find('.field-required-star').length) {
                    $item.find('.field-label').after('<span class="field-required-star">*</span>');
                }
            } else {
                $item.find('.field-required-star').remove();
            }
        });

        // ========================================
        // 删除字段
        // ========================================
        $(document).on('click', '.delete-field', function (e) {
            e.stopPropagation();
            if (confirm('确定删除此字段？')) {
                $(this).closest('.field-item').fadeOut(300, function () {
                    $(this).remove();
                    updateFieldIndexes();
                });
            }
        });

        // ========================================
        // 保存表单
        // ========================================
        $('#form-editor').on('submit', function (e) {
            e.preventDefault();

            var $form = $(this);
            var formId = $form.data('form-id') || 0;

            // 收集字段数据
            var fields = [];
            $('.field-item').each(function () {
                var $item = $(this);
                var type = $item.find('.field-type').val();
                var optionsText = $item.find('.field-options').val().trim();
                var options = optionsText ? optionsText.split('\n').filter(function (o) { return o.trim(); }) : [];

                fields.push({
                    type: type,
                    name: $item.find('.field-name').val(),
                    label: $item.find('.field-label-input').val(),
                    placeholder: $item.find('.field-placeholder').val(),
                    required: $item.find('.field-required-input').is(':checked'),
                    width: $item.find('.field-width').val(),
                    options: options
                });
            });

            var data = {
                action: 'developer_save_form',
                nonce: developerFormsData.nonce,
                form_id: formId,
                title: $('#form-title').val(),
                slug: $('#form-slug').val(),
                fields: JSON.stringify(fields),
                notify_emails: $('#notify-emails').val(),
                submit_button: $('#submit-button').val(),
                success_message: $('#success-message').val(),
                limit_per_ip: $('#limit-per-ip').val(),
                limit_interval: $('#limit-interval').val(),
                status: $('#form-status').val()
            };

            $('#save-form').prop('disabled', true).text('保存中...');

            $.post(developerFormsData.ajaxUrl, data, function (response) {
                if (response.success) {
                    if (formId === 0 && response.data.redirect) {
                        window.location.href = response.data.redirect;
                    } else {
                        alert('保存成功！');
                        // 更新短代码显示
                        $('#shortcode-preview').text('[developer_form id="' + response.data.form_id + '"]');
                    }
                } else {
                    alert('保存失败：' + response.data.message);
                }
            }).always(function () {
                $('#save-form').prop('disabled', false).text('保存表单');
            });
        });

        // ========================================
        // 删除表单
        // ========================================
        $(document).on('click', '.delete-form', function (e) {
            e.preventDefault();

            if (!confirm('确定删除此表单？所有提交数据也将被删除！')) {
                return;
            }

            var formId = $(this).data('id');
            var $row = $(this).closest('tr');

            $.post(developerFormsData.ajaxUrl, {
                action: 'developer_delete_form',
                nonce: developerFormsData.nonce,
                form_id: formId
            }, function (response) {
                if (response.success) {
                    $row.fadeOut(300, function () {
                        $(this).remove();
                    });
                } else {
                    alert('删除失败：' + response.data.message);
                }
            });
        });

        // ========================================
        // 查看提交详情
        // ========================================
        $(document).on('click', '.view-entry', function (e) {
            e.preventDefault();

            var entryId = $(this).data('id');
            var content = $(this).data('content');
            var data = typeof content === 'string' ? JSON.parse(content) : content;

            var html = '<table>';
            for (var key in data) {
                var value = data[key];
                if (Array.isArray(value)) {
                    value = value.join(', ');
                }
                html += '<tr><th>' + key + '</th><td>' + escapeHtml(value) + '</td></tr>';
            }
            html += '</table>';

            $('#entry-detail').html(html);
            $('#entry-modal').fadeIn(200);

            // 标记已读
            var $row = $(this).closest('tr');
            if ($row.hasClass('unread')) {
                $row.removeClass('unread');
                $row.find('td:eq(-2)').text('已读');
            }
        });

        // 关闭弹窗
        $(document).on('click', '.entry-modal-close, #entry-modal', function (e) {
            if (e.target === this) {
                $('#entry-modal').fadeOut(200);
            }
        });

        // ========================================
        // 删除提交
        // ========================================
        $(document).on('click', '.delete-entry', function (e) {
            e.preventDefault();

            if (!confirm('确定删除此条数据？')) {
                return;
            }

            var entryId = $(this).data('id');
            var $row = $(this).closest('tr');

            $.post(developerFormsData.ajaxUrl, {
                action: 'developer_delete_entry',
                nonce: developerFormsData.nonce,
                entry_id: entryId
            }, function (response) {
                if (response.success) {
                    $row.fadeOut(300, function () {
                        $(this).remove();
                    });
                } else {
                    alert('删除失败：' + response.data.message);
                }
            });
        });

        // ========================================
        // 导出数据
        // ========================================
        $(document).on('click', '.export-entries', function (e) {
            e.preventDefault();
            var formId = $(this).data('form-id');
            window.location.href = developerFormsData.ajaxUrl + '?action=developer_export_entries&form_id=' + formId + '&nonce=' + developerFormsData.nonce;
        });

        // ========================================
        // 辅助函数
        // ========================================
        function updateFieldIndexes() {
            $('.field-item').each(function (index) {
                $(this).attr('data-index', index);
            });
        }

        function escapeHtml(text) {
            if (!text) return '';
            var div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    });
})(jQuery);
