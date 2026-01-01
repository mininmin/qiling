/**
 * Article Enhancement Script
 * 文章增强脚本 - TOC交互、平滑滚动等
 *
 * @package Developer_Starter
 * @since 1.0.0
 */

(function ($) {
    'use strict';

    // 配置
    const config = window.articleEnhanceConfig || {};

    /**
     * TOC 功能
     */
    const TOC = {
        init: function () {
            const $toc = $('#article-toc');
            if (!$toc.length) return;

            this.$toc = $toc;
            this.$links = $toc.find('.toc-link');
            this.$toggle = $toc.find('.toc-toggle');

            this.bindEvents();
            this.initScrollSpy();
        },

        bindEvents: function () {
            const self = this;

            // 平滑滚动
            this.$links.on('click', function (e) {
                e.preventDefault();
                const targetId = $(this).attr('href');
                const $target = $(targetId);

                if ($target.length) {
                    const offset = 80; // 顶部固定导航高度
                    $('html, body').animate({
                        scrollTop: $target.offset().top - offset
                    }, 500, 'swing');

                    // 更新活动状态
                    self.$links.removeClass('active');
                    $(this).addClass('active');
                }
            });

            // 折叠/展开
            if (this.$toggle.length) {
                this.$toggle.on('click', function () {
                    $(this).toggleClass('collapsed');
                    self.$toc.toggleClass('collapsed');
                });
            }
        },

        initScrollSpy: function () {
            const self = this;
            const headings = [];

            // 收集所有标题
            this.$links.each(function () {
                const targetId = $(this).attr('href');
                const $target = $(targetId);
                if ($target.length) {
                    headings.push({
                        id: targetId,
                        top: $target.offset().top,
                        $link: $(this)
                    });
                }
            });

            if (!headings.length) return;

            // 滚动监听
            let ticking = false;
            $(window).on('scroll', function () {
                if (!ticking) {
                    window.requestAnimationFrame(function () {
                        self.updateActiveLink(headings);
                        ticking = false;
                    });
                    ticking = true;
                }
            });

            // 初始检查
            this.updateActiveLink(headings);
        },

        updateActiveLink: function (headings) {
            const scrollTop = $(window).scrollTop();
            const offset = 100;

            let currentId = null;

            for (let i = headings.length - 1; i >= 0; i--) {
                if (scrollTop >= headings[i].top - offset) {
                    currentId = headings[i].id;
                    break;
                }
            }

            this.$links.removeClass('active');
            if (currentId) {
                this.$links.filter('[href="' + currentId + '"]').addClass('active');
            }
        }
    };

    /**
     * 代码块增强
     */
    const CodeBlock = {
        init: function () {
            // 移除 PrismJS 自带的 copy 按钮
            this.removePrismCopyButtons();

            // 添加自定义复制按钮
            this.addCopyButtons();

            // 监听 DOM 变化，确保动态添加的代码块也被处理
            this.observeDOM();
        },

        removePrismCopyButtons: function () {
            // 物理删除所有 PrismJS toolbar 和 copy 按钮
            document.querySelectorAll('.toolbar, .copy-to-clipboard-button, [data-copy-state]').forEach(function (el) {
                el.remove();
            });
        },

        observeDOM: function () {
            const self = this;
            // 使用 MutationObserver 监听 DOM 变化
            const observer = new MutationObserver(function (mutations) {
                mutations.forEach(function (mutation) {
                    if (mutation.addedNodes.length) {
                        self.removePrismCopyButtons();
                    }
                });
            });

            observer.observe(document.body, {
                childList: true,
                subtree: true
            });
        },

        addCopyButtons: function () {
            $('pre[class*="language-"], .wp-block-code pre').each(function () {
                const $pre = $(this);

                // 避免重复添加
                if ($pre.find('.code-copy-btn').length) return;

                const $btn = $('<button class="code-copy-btn" title="复制代码"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg></button>');

                $pre.css('position', 'relative').append($btn);

                $btn.on('click', function () {
                    const code = $pre.find('code').text();

                    if (navigator.clipboard) {
                        navigator.clipboard.writeText(code).then(function () {
                            $btn.addClass('copied');
                            $btn.html('<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>');

                            setTimeout(function () {
                                $btn.removeClass('copied');
                                $btn.html('<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>');
                            }, 2000);
                        });
                    }
                });
            });
        }
    };

    /**
     * 初始化
     */
    $(document).ready(function () {
        TOC.init();
        CodeBlock.init();
    });

    // 代码复制按钮样式
    const style = document.createElement('style');
    style.textContent = `
        /* 完全隐藏 PrismJS 自带的所有 toolbar 和 copy 按钮 */
        .code-toolbar > .toolbar,
        div.code-toolbar > .toolbar,
        pre[class*="language-"] > .toolbar,
        .toolbar,
        .toolbar-item,
        button.copy-to-clipboard-button,
        [data-copy-state],
        .prism-toolbar,
        .copy-to-clipboard {
            display: none !important;
            opacity: 0 !important;
            visibility: hidden !important;
            pointer-events: none !important;
        }
        
        /* 自定义复制按钮 */
        .code-copy-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            padding: 8px 10px;
            background: rgba(255, 255, 255, 0.15);
            border: none;
            border-radius: 8px;
            cursor: pointer;
            color: rgba(255, 255, 255, 0.7);
            transition: all 0.2s;
            z-index: 100;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .code-copy-btn:hover {
            background: rgba(255, 255, 255, 0.25);
            color: #fff;
        }
        .code-copy-btn.copied {
            background: #10b981;
            color: #fff;
        }
        
        /* 确保 pre 有定位 */
        pre[class*="language-"],
        .wp-block-code pre {
            position: relative !important;
        }
    `;
    document.head.appendChild(style);

})(jQuery);
