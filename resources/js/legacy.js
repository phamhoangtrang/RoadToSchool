import jQuery from 'jquery';
import * as bootstrap from 'bootstrap';

window.$ = window.jQuery = jQuery;
window.bootstrap = bootstrap;

const originalLoad = jQuery.fn.load;
jQuery.fn.load = function (url, ...args) {
    if (typeof url === 'function') {
        return this.on('load', url);
    }

    return originalLoad.call(this, url, ...args);
};

jQuery.fn.counterUp = function () {
    return this;
};

const pendingRealtimeListeners = [];
window.__RoadToSchoolRealtimeListeners = pendingRealtimeListeners;
window.RoadToSchoolRealtime = {
    subscribe(channelName) {
        return {
            bind(eventName, listener) {
                pendingRealtimeListeners.push({ channelName, eventName, listener });

                return this;
            },
        };
    },
};

const jQueryPlugins = {
    alert: bootstrap.Alert,
    button: bootstrap.Button,
    carousel: bootstrap.Carousel,
    collapse: bootstrap.Collapse,
    dropdown: bootstrap.Dropdown,
    modal: bootstrap.Modal,
    offcanvas: bootstrap.Offcanvas,
    popover: bootstrap.Popover,
    scrollspy: bootstrap.ScrollSpy,
    tab: bootstrap.Tab,
    toast: bootstrap.Toast,
    tooltip: bootstrap.Tooltip,
};

Object.entries(jQueryPlugins).forEach(([name, Component]) => {
    jQuery.fn[name] = function (config, ...args) {
        return this.each(function () {
            const instance = Component.getOrCreateInstance(
                this,
                typeof config === 'object' ? config : undefined,
            );

            if (typeof config === 'string' && typeof instance[config] === 'function') {
                instance[config](...args);
            }
        });
    };
    jQuery.fn[name].Constructor = Component;
});

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
