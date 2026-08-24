import jQuery from 'jquery';
import * as bootstrap from 'bootstrap';

window.$ = window.jQuery = jQuery;
window.bootstrap = bootstrap;

document.addEventListener('DOMContentLoaded', () => {
    const attributes = {
        'data-toggle': 'data-bs-toggle',
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
});

export { bootstrap, jQuery };
