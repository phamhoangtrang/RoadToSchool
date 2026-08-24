import axios from 'axios';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.Pusher = Pusher;

const token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
}

if (import.meta.env.VITE_REVERB_APP_KEY) {
    window.Echo = new Echo({
        broadcaster: 'reverb',
        key: import.meta.env.VITE_REVERB_APP_KEY,
        wsHost: import.meta.env.VITE_REVERB_HOST || window.location.hostname,
        wsPort: Number(import.meta.env.VITE_REVERB_PORT || 80),
        wssPort: Number(import.meta.env.VITE_REVERB_PORT || 443),
        forceTLS: (import.meta.env.VITE_REVERB_SCHEME || 'https') === 'https',
        enabledTransports: ['ws', 'wss'],
    });
}

const noOpChannel = {
    bind() {
        return this;
    },
};

const realtime = {
    subscribe(channelName) {
        if (!window.Echo) {
            return noOpChannel;
        }

        const channel = window.Echo.channel(channelName);

        return {
            bind(eventName, listener) {
                channel.listen(`.${eventName}`, listener);

                return this;
            },
        };
    },
};

const pendingRealtimeListeners = window.__RoadToSchoolRealtimeListeners || [];
window.RoadToSchoolRealtime = realtime;
pendingRealtimeListeners.forEach(({ channelName, eventName, listener }) => {
    realtime.subscribe(channelName).bind(eventName, listener);
});
window.__RoadToSchoolRealtimeListeners = [];
