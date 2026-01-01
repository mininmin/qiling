/**
 * Qi Ling Theme - Main JavaScript
 */

document.addEventListener('DOMContentLoaded', function () {
    'use strict';

    // ===== User Auth Status Check (Cache-Safe) =====
    // 确保已登录用户不受页面缓存影响，始终显示正确的登录状态
    (function checkUserAuthStatus() {
        var authWrapper = document.getElementById('header-auth-wrapper');
        if (!authWrapper) return;

        var loginArea = document.getElementById('header-login-area');
        var userArea = document.getElementById('header-user-area');

        if (!loginArea || !userArea) return;

        // 通过AJAX获取真实的用户登录状态（不受页面缓存影响）
        var ajaxUrl = typeof developerStarterData !== 'undefined' ? developerStarterData.ajaxUrl : '/wp-admin/admin-ajax.php';

        // 添加时间戳防止CDN和浏览器缓存
        var timestamp = new Date().getTime();

        fetch(ajaxUrl + '?_=' + timestamp, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'Cache-Control': 'no-cache, no-store, must-revalidate',
                'Pragma': 'no-cache'
            },
            body: 'action=developer_starter_user_status&_nocache=' + timestamp,
            credentials: 'same-origin', // 确保发送cookies
            cache: 'no-store' // 强制不使用缓存
        })
            .then(function (response) { return response.json(); })
            .then(function (data) {
                if (!data.success) return;

                var userData = data.data;

                if (userData.logged_in) {
                    // 用户已登录：显示用户菜单，隐藏登录按钮
                    loginArea.style.display = 'none';
                    userArea.style.display = '';

                    // 更新用户信息（如果是从缓存页面加载的）
                    var userAvatar = document.getElementById('header-user-avatar');
                    var dropdownAvatar = document.getElementById('dropdown-user-avatar');
                    var userName = document.getElementById('dropdown-user-name');
                    var userEmail = document.getElementById('dropdown-user-email');
                    var accountLink = document.getElementById('dropdown-account-link');
                    var adminLink = document.getElementById('dropdown-admin-link');
                    var logoutLink = document.getElementById('dropdown-logout-link');
                    var userToggle = document.getElementById('header-user-toggle');

                    // 更新头像 - 查找所有可能的头像元素
                    if (userAvatar) {
                        userAvatar.src = userData.avatar_32;
                        userAvatar.alt = userData.display_name;
                    }
                    if (dropdownAvatar) {
                        dropdownAvatar.src = userData.avatar_48;
                        dropdownAvatar.alt = userData.display_name;
                    }

                    // 更新主按钮的头像（查找任何空src或没有正确src的img）
                    if (userToggle) {
                        var toggleImgs = userToggle.querySelectorAll('img');
                        toggleImgs.forEach(function (img) {
                            if (!img.src || img.src === '' || img.src === window.location.href) {
                                img.src = userData.avatar_32;
                                img.alt = userData.display_name;
                            }
                        });
                    }

                    // 更新下拉菜单中的头像
                    var dropdown = document.getElementById('user-dropdown');
                    if (dropdown) {
                        var dropdownImgs = dropdown.querySelectorAll('.dropdown-header img');
                        dropdownImgs.forEach(function (img) {
                            if (!img.src || img.src === '' || img.src === window.location.href) {
                                img.src = userData.avatar_48;
                                img.alt = userData.display_name;
                            }
                        });
                    }

                    // 更新用户名和邮箱（强制更新，不检查是否为空）
                    if (userName) userName.textContent = userData.display_name;
                    if (userEmail) userEmail.textContent = userData.email;

                    // 更新链接
                    if (accountLink && userData.account_url) accountLink.href = userData.account_url;
                    if (userToggle && userData.account_url) userToggle.href = userData.account_url;
                    if (adminLink) {
                        if (userData.can_access_admin && userData.admin_url) {
                            adminLink.href = userData.admin_url;
                            adminLink.style.display = '';
                        } else {
                            adminLink.style.display = 'none';
                        }
                    }
                    if (logoutLink && userData.logout_url) logoutLink.href = userData.logout_url;

                } else {
                    // 用户未登录：显示登录按钮，隐藏用户菜单
                    loginArea.style.display = '';
                    userArea.style.display = 'none';
                }

                // 标记状态检查完成
                authWrapper.setAttribute('data-auth-ready', 'true');
            })
            .catch(function (error) {
                // 网络错误时保持服务器端渲染的状态
                console.warn('User status check failed:', error);
                authWrapper.setAttribute('data-auth-ready', 'true');
            });
    })();


    // ===== Dark Mode Toggle =====
    var darkModeToggle = document.getElementById('darkmode-toggle');
    if (darkModeToggle) {
        var iconSun = darkModeToggle.querySelector('.icon-sun');
        var iconMoon = darkModeToggle.querySelector('.icon-moon');

        // 检查本地存储或系统偏好
        var savedTheme = localStorage.getItem('theme');
        var prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;

        function setDarkMode(isDark) {
            if (isDark) {
                document.documentElement.classList.add('dark-mode');
                if (iconSun) iconSun.style.display = 'none';
                if (iconMoon) iconMoon.style.display = 'block';
                localStorage.setItem('theme', 'dark');
            } else {
                document.documentElement.classList.remove('dark-mode');
                if (iconSun) iconSun.style.display = 'block';
                if (iconMoon) iconMoon.style.display = 'none';
                localStorage.setItem('theme', 'light');
            }
        }

        // 初始化
        if (savedTheme === 'dark' || (savedTheme === null && prefersDark)) {
            setDarkMode(true);
        }

        // 切换事件
        darkModeToggle.addEventListener('click', function () {
            var isDark = document.documentElement.classList.contains('dark-mode');
            setDarkMode(!isDark);
        });
    }

    // ===== Native Scroll Animation (替代 AOS 库) =====
    if ('IntersectionObserver' in window) {
        var aosElements = document.querySelectorAll('[data-aos]');
        if (aosElements.length > 0) {
            var aosObserver = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        var el = entry.target;
                        var delay = parseInt(el.getAttribute('data-aos-delay')) || 0;

                        setTimeout(function () {
                            el.classList.add('aos-animate');
                        }, delay);

                        aosObserver.unobserve(el);
                    }
                });
            }, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });

            aosElements.forEach(function (el) {
                el.classList.add('aos-init');
                aosObserver.observe(el);
            });
        }
    } else {
        // 不支持 IntersectionObserver 的浏览器，直接显示所有元素
        document.querySelectorAll('[data-aos]').forEach(function (el) {
            el.classList.add('aos-animate');
        });
    }

    // ===== Initialize Swiper =====
    if (typeof Swiper !== 'undefined') {
        var bannerSwipers = document.querySelectorAll('.banner-swiper');
        bannerSwipers.forEach(function (el) {
            new Swiper(el, {
                loop: true,
                autoplay: {
                    delay: 5000,
                    disableOnInteraction: false,
                },
                effect: 'fade',
                fadeEffect: { crossFade: true },
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true,
                },
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
            });
        });
    }

    // ===== Mobile Menu Toggle =====
    var menuToggle = document.querySelector('.mobile-menu-toggle');
    var mobileMenu = document.querySelector('.mobile-menu');

    if (menuToggle && mobileMenu) {
        menuToggle.addEventListener('click', function () {
            mobileMenu.classList.toggle('active');
            this.classList.toggle('is-active');
        });
    }

    // ===== Search Overlay =====
    var searchToggle = document.getElementById('search-toggle');
    var searchOverlay = document.getElementById('search-overlay');
    var searchClose = document.getElementById('search-close');

    if (searchToggle && searchOverlay) {
        searchToggle.addEventListener('click', function () {
            searchOverlay.classList.add('active');
            var input = searchOverlay.querySelector('input');
            if (input) input.focus();
        });
    }

    if (searchClose && searchOverlay) {
        searchClose.addEventListener('click', function () {
            searchOverlay.classList.remove('active');
        });

        searchOverlay.addEventListener('click', function (e) {
            if (e.target === searchOverlay) {
                searchOverlay.classList.remove('active');
            }
        });
    }

    // ESC key to close search
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && searchOverlay && searchOverlay.classList.contains('active')) {
            searchOverlay.classList.remove('active');
        }
    });

    // ===== Header Scroll Effect =====
    var header = document.querySelector('.site-header');
    if (header) {
        window.addEventListener('scroll', function () {
            if (window.scrollY > 50) {
                header.classList.add('is-scrolled');
            } else {
                header.classList.remove('is-scrolled');
            }
        });
    }

    // ===== FAQ Accordion =====
    var faqQuestions = document.querySelectorAll('.faq-question');
    faqQuestions.forEach(function (btn) {
        btn.addEventListener('click', function () {
            var item = this.closest('.faq-item');
            var answer = item.querySelector('.faq-answer');
            var icon = this.querySelector('.faq-icon');

            // Close all others
            document.querySelectorAll('.faq-item').forEach(function (other) {
                if (other !== item) {
                    var otherAnswer = other.querySelector('.faq-answer');
                    if (otherAnswer) otherAnswer.style.display = 'none';
                    var otherIcon = other.querySelector('.faq-icon');
                    if (otherIcon) otherIcon.textContent = '+';
                }
            });

            // Toggle current
            if (answer.style.display === 'block') {
                answer.style.display = 'none';
                if (icon) icon.textContent = '+';
            } else {
                answer.style.display = 'block';
                if (icon) icon.textContent = '-';
            }
        });
    });

    // ===== Stats Counter Animation =====
    var statNumbers = document.querySelectorAll('.stat-number');
    if (statNumbers.length > 0 && 'IntersectionObserver' in window) {
        var animateCounter = function (el) {
            var text = el.textContent.replace(/[^0-9]/g, '');
            var target = parseInt(text) || 0;
            if (target === 0) return;

            var duration = 2000;
            var startTime = null;

            function animate(timestamp) {
                if (!startTime) startTime = timestamp;
                var progress = Math.min((timestamp - startTime) / duration, 1);
                var current = Math.floor(progress * target);
                el.textContent = current + '+';

                if (progress < 1) {
                    requestAnimationFrame(animate);
                } else {
                    el.textContent = target + '+';
                }
            }

            requestAnimationFrame(animate);
        };

        var observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    animateCounter(entry.target);
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });

        statNumbers.forEach(function (el) {
            observer.observe(el);
        });
    }

    // ===== Back to Top =====
    var backToTop = document.getElementById('back-to-top');
    if (backToTop) {
        window.addEventListener('scroll', function () {
            if (window.scrollY > 300) {
                backToTop.style.display = 'flex';
            } else {
                backToTop.style.display = 'none';
            }
        });

        backToTop.addEventListener('click', function () {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    // Float widget hover is now handled by CSS

    // ===== Contact Form =====
    var contactForm = document.getElementById('contact-form');
    if (contactForm) {
        contactForm.addEventListener('submit', function (e) {
            e.preventDefault();

            var submitBtn = this.querySelector('button[type="submit"]');
            var originalText = submitBtn.textContent;

            submitBtn.textContent = '发送中...';
            submitBtn.disabled = true;

            // 实际AJAX请求可在此处添加
            setTimeout(function () {
                alert('感谢您的留言，我们会尽快与您联系！');
                contactForm.reset();
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            }, 1000);
        });
    }

    // ===== Smooth Scroll for Anchor Links =====
    document.querySelectorAll('a[href^="#"]').forEach(function (anchor) {
        anchor.addEventListener('click', function (e) {
            var href = this.getAttribute('href');
            if (href === '#' || href === '#top') {
                e.preventDefault();
                window.scrollTo({ top: 0, behavior: 'smooth' });
                return;
            }

            try {
                var target = document.querySelector(href);
                if (target) {
                    e.preventDefault();
                    var headerHeight = header ? header.offsetHeight : 0;
                    var targetPosition = target.offsetTop - headerHeight - 20;
                    window.scrollTo({ top: targetPosition, behavior: 'smooth' });
                }
            } catch (err) {
                // Invalid selector
            }
        });
    });

    // ===== Language Switcher Modal =====
    function openTranslateModal() {
        var modal = document.getElementById('translate-modal');
        var overlay = document.getElementById('translate-modal-overlay');
        if (modal && overlay) {
            modal.classList.add('show');
            overlay.classList.add('show');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeTranslateModal() {
        var modal = document.getElementById('translate-modal');
        var overlay = document.getElementById('translate-modal-overlay');
        if (modal && overlay) {
            modal.classList.remove('show');
            overlay.classList.remove('show');
            document.body.style.overflow = '';
        }
    }

    // Toggle button click - use event delegation
    document.addEventListener('click', function (e) {
        // Open modal
        if (e.target.closest('#translate-toggle')) {
            e.stopPropagation();
            openTranslateModal();
            return;
        }

        // Close button
        if (e.target.closest('#translate-modal-close')) {
            closeTranslateModal();
            return;
        }

        // Overlay click
        if (e.target.id === 'translate-modal-overlay') {
            closeTranslateModal();
            return;
        }

        // Language item click
        var langItem = e.target.closest('.translate-lang-item');
        if (langItem) {
            e.preventDefault();
            var lang = langItem.getAttribute('data-lang');

            // Call translate.js to change language
            if (typeof translate !== 'undefined' && translate.changeLanguage) {
                translate.changeLanguage(lang);
            }

            // Update active state
            document.querySelectorAll('.translate-lang-item').forEach(function (opt) {
                opt.classList.remove('active');
            });
            langItem.classList.add('active');

            // Close modal
            closeTranslateModal();
        }
    });

    // ESC key to close modal
    document.addEventListener('keydown', function (e) {
        var modal = document.getElementById('translate-modal');
        if (e.key === 'Escape' && modal && modal.classList.contains('show')) {
            closeTranslateModal();
        }
    });

});
