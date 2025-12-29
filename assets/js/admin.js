/**
 * Qi Ling Theme - Admin JavaScript
 *
 * @package Developer_Starter
 * @since 1.0.0
 */

(function ($) {
    'use strict';

    $(document).ready(function () {
        initMediaUploader();
        initColorPicker();
        initModulesBuilder();
        initSortable();
    });

    /**
     * Media uploader for image fields
     */
    function initMediaUploader() {
        var mediaUploader;

        $(document).on('click', '.upload-image', function (e) {
            e.preventDefault();

            var button = $(this);
            var inputField = button.siblings('.image-url');

            if (mediaUploader) {
                mediaUploader.open();
                return;
            }

            mediaUploader = wp.media({
                title: developerStarterAdmin?.i18n?.selectImage || 'Select Image',
                button: {
                    text: developerStarterAdmin?.i18n?.useImage || 'Use this image'
                },
                multiple: false
            });

            mediaUploader.on('select', function () {
                var attachment = mediaUploader.state().get('selection').first().toJSON();
                inputField.val(attachment.url);

                var preview = button.siblings('img');
                if (preview.length) {
                    preview.attr('src', attachment.url);
                } else {
                    button.after('<img src="' + attachment.url + '" style="max-width:150px;margin-top:10px;display:block;" />');
                }
            });

            mediaUploader.open();
        });
    }

    /**
     * Color picker
     */
    function initColorPicker() {
        if ($.fn.wpColorPicker) {
            $('.color-picker').wpColorPicker();
        }
    }

    /**
     * Modules builder
     */
    function initModulesBuilder() {
        $(document).on('click', '.add-module', function () {
            var moduleType = $(this).data('module');
            var moduleName = $(this).text().replace('+ ', '');
            var modulesList = $('#modules-list');
            var index = modulesList.children().length;

            var moduleHtml = '<div class="module-item" data-module="' + moduleType + '">' +
                '<span class="module-handle">☰</span>' +
                '<span class="module-name">' + moduleName + '</span>' +
                '<button type="button" class="button-link remove-module">×</button>' +
                '<input type="hidden" name="modules[' + index + '][type]" value="' + moduleType + '" />' +
                '</div>';

            modulesList.append(moduleHtml);
        });

        $(document).on('click', '.remove-module', function () {
            $(this).closest('.module-item').remove();
            reindexModules();
        });
    }

    /**
     * Reindex modules after removal
     */
    function reindexModules() {
        $('#modules-list .module-item').each(function (index) {
            $(this).find('input').attr('name', 'modules[' + index + '][type]');
        });
    }

    /**
     * Sortable modules
     */
    function initSortable() {
        if ($.fn.sortable) {
            $('#modules-list').sortable({
                handle: '.module-handle',
                update: function () {
                    reindexModules();
                }
            });
        }
    }

})(jQuery);
