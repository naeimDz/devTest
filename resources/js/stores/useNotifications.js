import { defineStore } from 'pinia';


export const useNotificationsStore = defineStore('notifications', {
  state: () => ({
    notifications: [] , 
  }),
  getters: {
    getNotifications: (state) => state.notifications,
    getNotificationCount: (state) => state.notifications.length,
    hasNotifications: (state) => state.notifications.length > 0
  },
  actions: {
    addNotification(notification) {
      if (!notification.id) {
        notification.id = Date.now().toString();
      }
      this.notifications.push(notification);
    },
    removeNotification(id) {
      const index = this.notifications.findIndex(n => n.id === id);
      if (index !== -1) {
        this.notifications.splice(index, 1);
      }
    },
    clearNotifications() {
      this.notifications = [];
    }
  },
});
