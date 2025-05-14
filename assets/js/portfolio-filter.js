(function($) {
    'use strict';

    class PortfolioFilter {
        constructor() {
            this.container = $('.portfolio-filter-container');
            this.grid = this.container.find('.portfolio-grid');
            this.filters = this.container.find('.portfolio-filter');
            this.pagination = this.container.find('.portfolio-pagination');
            this.loadMoreBtn = this.container.find('.load-more');
            this.loading = this.container.find('.portfolio-loading');
            this.currentPage = 1;
            this.isLoading = false;

            this.init();
        }

        init() {
            this.bindEvents();
        }

        bindEvents() {
            // Filter change event
            this.filters.on('change', () => {
                this.currentPage = 1;
                this.filterPortfolio();
            });
            this.loadMoreBtn.on('click', () => {
                this.currentPage++;
                this.loadMore();
            });
        }

        filterPortfolio() {
            if (this.isLoading) return;

            this.isLoading = true;
            this.showLoading();

            const data = {
                action: 'portfolio_filter',
                nonce: cpfAjax.nonce,
                page: this.currentPage,
                project_category: $('#project-category').val(),
                service_type: $('#service-type').val(),
                posts_per_page: this.grid.data('posts-per-page') || 9
            };

            $.post(cpfAjax.ajaxurl, data, (response) => {
                if (response.success) {
                    this.grid.html(response.data.html);
                    this.updatePagination(response.data.max_pages);
                }
                this.hideLoading();
                this.isLoading = false;
            });
        }

        loadMore() {
            if (this.isLoading) return;

            this.isLoading = true;
            this.showLoading();
            

            const data = {
                action: 'portfolio_filter',
                nonce: cpfAjax.nonce,
                page: this.currentPage,
                project_category: $('#project-category').val(),
                service_type: $('#service-type').val(),
                posts_per_page: this.grid.data('posts-per-page') || 9
            };

            $.post(cpfAjax.ajaxurl, data, (response) => {
                if (response.success) {
                    this.grid.append(response.data.html);
                    this.updatePagination(response.data.max_pages);
                }
                this.hideLoading();
                this.isLoading = false;
            });
        }

        updatePagination(maxPages) {
            if (this.currentPage >= maxPages) {
                this.loadMoreBtn.hide();
            } else {
                this.loadMoreBtn.show();
            }
        }

        showLoading() {
            this.loading.show();
        }

        hideLoading() {
            this.loading.hide();
        }
    }
    $(document).ready(() => {
        new PortfolioFilter();
    });

})(jQuery); 