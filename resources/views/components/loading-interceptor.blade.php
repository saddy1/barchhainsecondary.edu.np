{{-- Page Loading Interceptor for AJAX/Fetch Requests --}}
<script>
    /**
     * Show/Hide loader during AJAX/Fetch requests
     * This ensures the loader appears during any navigation or data loading
     */

    const pageLoader = {
        element: null,
        requestCount: 0,

        init() {
            this.element = document.getElementById('page-loader');
            this.setupFetchInterceptor();
            this.setupAjaxInterceptor();
            this.setupNavigationInterceptor();
        },

        show() {
            if (this.element && this.element.classList.contains('hidden')) {
                this.element.classList.remove('hidden');
            }
        },

        hide() {
            if (this.element && !this.element.classList.contains('hidden')) {
                this.element.classList.add('hidden');
            }
        },

        // Intercept fetch requests
        setupFetchInterceptor() {
            const originalFetch = window.fetch;
            window.fetch = function(...args) {
                pageLoader.requestCount++;
                pageLoader.show();
                
                return originalFetch.apply(this, args)
                    .finally(() => {
                        pageLoader.requestCount--;
                        if (pageLoader.requestCount === 0) {
                            setTimeout(() => pageLoader.hide(), 300);
                        }
                    });
            };
        },

        // Intercept jQuery AJAX requests (if jQuery is used)
        setupAjaxInterceptor() {
            if (window.jQuery) {
                jQuery(document).on('ajaxStart', () => {
                    pageLoader.requestCount++;
                    pageLoader.show();
                }).on('ajaxStop', () => {
                    pageLoader.requestCount--;
                    if (pageLoader.requestCount === 0) {
                        setTimeout(() => pageLoader.hide(), 300);
                    }
                });
            }
        },

        // Show loader on link clicks and form submissions
        setupNavigationInterceptor() {
            document.addEventListener('click', (e) => {
                const link = e.target.closest('a:not([target="_blank"])');
                if (link && link.href && !link.href.includes('javascript:')) {
                    const href = link.getAttribute('href');
                    // Don't show loader for anchor links or file downloads
                    if (!href.startsWith('#') && !link.classList.contains('no-loader')) {
                        pageLoader.show();
                    }
                }
            });

            document.addEventListener('submit', (e) => {
                const form = e.target.closest('form');
                if (form && !form.classList.contains('no-loader')) {
                    pageLoader.show();
                }
            });
        }
    };

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => pageLoader.init());
    } else {
        pageLoader.init();
    }

    // Hide loader when page becomes visible again (after navigation)
    document.addEventListener('visibilitychange', () => {
        if (!document.hidden && pageLoader.element?.classList.contains('hidden')) {
            // Page is visible and fully loaded
        }
    });
</script>
