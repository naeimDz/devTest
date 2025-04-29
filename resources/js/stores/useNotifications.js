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
      this.notifications.push(notification);
    },
    clearNotifications() {
      this.notifications = [];
    }
  },
});
