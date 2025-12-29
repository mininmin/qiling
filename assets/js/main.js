/**
 * Qi Ling Theme - Main JavaScript
 */

document.addEventListener('DOMContentLoaded', function () {
    'use strict';

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

    // ===== Float Widget Hover =====
    document.querySelectorAll('.float-widget').forEach(function (widget) {
        widget.addEventListener('mouseenter', function () {
            var popup = this.querySelector('.widget-popup');
            if (popup) popup.style.display = 'block';
        });
        widget.addEventListener('mouseleave', function () {
            var popup = this.querySelector('.widget-popup');
            if (popup) popup.style.display = 'none';
        });
    });

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

});
