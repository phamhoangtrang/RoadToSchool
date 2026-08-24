import jQuery from 'jquery';
import * as bootstrap from 'bootstrap';
import PerfectScrollbar from 'perfect-scrollbar';
import Chart from 'chart.js/auto';
import DataTable from 'datatables.net-bs5';

window.$ = window.jQuery = jQuery;
window.bootstrap = bootstrap;
window.Chart = Chart;
DataTable(window, jQuery);

const $ = jQuery;

window.app = {
    colors: {
        primary: '#6569df',
        success: '#24d5d8',
        info: '#04a1f4',
        warning: '#fecd2f',
        danger: '#fd3259',
        transparent: 'rgba(255, 255, 255, 0)',
        white: '#fff',
        borderColor: '#e9e9e9',
        gradientSuccessStart: '#1dccdf',
        gradientSuccessStop: '#1de4bd',
    },
};

$.notify = function (content, options = {}) {
    const message = typeof content === 'string' ? content : content.message;
    const type = options.type || 'info';
    const placement = options.placement || { from: 'top', align: 'right' };
    const alert = document.createElement('div');

    alert.className = `alert alert-${type} alert-dismissible fade show position-fixed shadow`;
    alert.style.zIndex = '2000';
    alert.style[placement.from] = '1rem';
    alert.style[placement.align] = '1rem';
    alert.setAttribute('role', 'alert');
    alert.innerHTML = `${message}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>`;
    document.body.append(alert);

    window.setTimeout(() => bootstrap.Alert.getOrCreateInstance(alert).close(), options.delay || 3000);

    return alert;
};

function initializeBootstrapComponents() {
    const componentToggles = new Set(['collapse', 'dropdown', 'modal', 'popover', 'tab', 'tooltip']);

    document.querySelectorAll('[data-toggle]').forEach((element) => {
        if (componentToggles.has(element.getAttribute('data-toggle'))) {
            element.setAttribute('data-bs-toggle', element.getAttribute('data-toggle'));
        }
    });

    const attributes = {
        'data-target': 'data-bs-target',
        'data-dismiss': 'data-bs-dismiss',
        'data-parent': 'data-bs-parent',
    };

    Object.entries(attributes).forEach(([legacy, current]) => {
        document.querySelectorAll(`[${legacy}]`).forEach((element) => {
            if (!element.hasAttribute(current)) {
                element.setAttribute(current, element.getAttribute(legacy));
            }
        });
    });

    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach((element) => new bootstrap.Tooltip(element));
    document.querySelectorAll('[data-bs-toggle="popover"]').forEach((element) => new bootstrap.Popover(element));
}

function initializeShell() {
    $('.side-nav .side-nav-menu li a').on('click', function () {
        const item = $(this).parent();

        item.siblings('.open').removeClass('open').children('.dropdown-menu').slideUp(200);
        item.toggleClass('open').children('.dropdown-menu').slideToggle(200);
    });

    $('.sidenav-fold-toggler').on('click', function (event) {
        $('.app').toggleClass('side-nav-folded');
        event.preventDefault();
    });

    $('.sidenav-expand-toggler').on('click', function (event) {
        $('.side-nav-backdrop').remove();
        $('.app').toggleClass('side-nav-expand');

        if ($('.app').hasClass('side-nav-expand')) {
            $('<div class="side-nav-backdrop"></div>').appendTo('.app').on('click', function () {
                $('.app').removeClass('side-nav-expand');
                $(this).remove();
            });
        }

        event.preventDefault();
    });

    $('.quick-view-toggler').on('click', () => $('.app').toggleClass('quick-view-expand'));
    $('.conversation-toggler').on('click', () => $('.quick-view-chat').toggleClass('conversation-expand'));
    $('.aside-toggle').on('click', () => $('.aside').toggleClass('open'));
    $('.search-toggle').on('click', (event) => {
        $('.search-box, .search-input').toggleClass('active');
        $('.search-input input').trigger('focus');
        event.preventDefault();
    });

    $('[data-toggle="card-collapse"]').on('click', function () {
        $(this).toggleClass('active').parents('.card').find('.card-collapsible').slideToggle();
    });
    $('[data-toggle="card-delete"]').on('click', function (event) {
        $(this).parents('.card').remove();
        event.preventDefault();
    });
    $('[data-toggle="card-refresh"]').on('click', function (event) {
        const card = $(this).parents('.card').addClass('card-refresh');
        window.setTimeout(() => card.removeClass('card-refresh'), 2000);
        event.preventDefault();
    });

    $('.checkAll').on('change', function () {
        $(this).closest('table').find('tbody :checkbox').prop('checked', this.checked).closest('tr').toggleClass('selected', this.checked);
    });

    document.querySelectorAll('.scrollable').forEach((element) => new PerfectScrollbar(element));
    $('.chat-app .list-media .list-item, .chat-app .chat-content .conversation-toggler').on('click', (event) => {
        $('.chat-content').toggleClass('open');
        event.preventDefault();
    });
}

function initializeForms() {
    ['#selectize-dropdown', '#selectize-dropdown-2', '#selectize-group'].forEach((selector) => {
        if ($(selector).length && !$(selector)[0].selectize) {
            $(selector).selectize({
                create: false,
                sortField: 'text',
                dropdownParent: 'body',
            });
        }
    });

    ['#selectize-tags-1', '#selectize-tags-2'].forEach((selector) => {
        if ($(selector).length && !$(selector)[0].selectize) {
            $(selector).selectize({ delimiter: ',', persist: false, create: true });
        }
    });

    ['#summernote', '#summernote-standard', '#summernote-custom'].forEach((selector) => {
        if ($(selector).length) {
            $(selector).summernote({ height: 200 });
        }
    });

    $('.input-daterange input, .datepicker').datepicker({ autoclose: true });
}

function chartOptions(fill = false) {
    return {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        elements: { line: { tension: fill ? 0.4 : 0, borderWidth: 2 } },
        scales: {
            x: { display: false, stacked: true },
            y: { display: false, stacked: true },
        },
    };
}

function initializeDashboard() {
    const labels = ['W1', 'W2', 'W3', 'W4', 'W5', 'W6', 'W7', 'W8', 'W9', 'W10', 'W11', 'W12'];
    const values = [52, 66, 64, 71, 68, 74, 66, 73, 68, 72, 70, 78];
    const earningCanvas = document.getElementById('earning-chart');

    if (earningCanvas) {
        const context = earningCanvas.getContext('2d');
        const gradient = context.createLinearGradient(40, 0, 50, 170);
        gradient.addColorStop(0, window.app.colors.success);
        gradient.addColorStop(1, window.app.colors.transparent);
        new Chart(context, {
            type: 'line',
            data: { labels, datasets: [{ data: values, backgroundColor: gradient, borderColor: window.app.colors.success, fill: true }] },
            options: chartOptions(true),
        });
    }

    const pageViewCanvas = document.getElementById('page-view-chart');
    if (pageViewCanvas) {
        new Chart(pageViewCanvas, {
            type: 'line',
            data: { labels, datasets: [{ data: values, borderColor: window.app.colors.info }] },
            options: chartOptions(),
        });
    }

    const salesCanvas = document.getElementById('sales-stat-chart');
    if (salesCanvas) {
        new Chart(salesCanvas, {
            type: 'bar',
            data: {
                labels: Array.from({ length: 15 }, (_, index) => `${index + 1}`),
                datasets: [
                    { data: [15, 20, 25, 30, 25, 20, 15, 20, 25, 30, 25, 20, 15, 10, 15], backgroundColor: window.app.colors.success },
                    { data: [15, 20, 25, 30, 25, 20, 15, 20, 25, 30, 25, 20, 15, 10, 15], backgroundColor: '#eaeaea' },
                ],
            },
            options: chartOptions(),
        });
    }
}

Promise.all([
    import('@selectize/selectize'),
    import('summernote/dist/summernote-bs5'),
    import('bootstrap-datepicker'),
    import('jquery-ui/dist/jquery-ui'),
]).then(() => {
    document.addEventListener('DOMContentLoaded', () => {
        initializeBootstrapComponents();
        initializeShell();
        initializeForms();
        initializeDashboard();

        if (document.querySelector('#dt-opt')) {
            new DataTable('#dt-opt');
        }
    });
});
