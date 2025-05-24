import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
import { setActivePinia } from 'pinia';
import { useNotificationsStore } from './stores/useNotifications';
import { useAuthStore } from './stores/useAuthStore';

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
            notificationsStore.addNotification({
                id: Date.now().toString(),
                title: notification.title || 'New Notification',
                message: notification.message,
                type: notification.type || 'info',
                timestamp: new Date()
              });
              console.log('Notification received:', notification);
        }).listen('UserRoleUpdatedEvent', (event) => { 
    console.log('Received role update event:', event); // للتأكد من البيانات
    const authStore = useAuthStore();
    authStore.setUser(event.user); 
    console.log('Store updated with:', authStore.user); // للتأكد من التحديث
});


}
