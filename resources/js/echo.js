import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
import { setActivePinia } from 'pinia';
import { useNotificationsStore } from './stores/useNotifications';

export default function EchoInit(pinia, user) {
    setActivePinia(pinia);

    window.Pusher = Pusher;

    window.Echo = new Echo({
        broadcaster: 'pusher',
        key: import.meta.env.VITE_PUSHER_APP_KEY,
        cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
        wsHost: import.meta.env.VITE_PUSHER_HOST,
        wsPort: import.meta.env.VITE_PUSHER_PORT,
        forceTLS: false,
        disableStats: true,
    });

    window.Echo.private(`App.Models.User.${user.id}`)
        .notification((notification) => {

            const notificationsStore = useNotificationsStore();
            notificationsStore.addNotification(notification);
        });


}
