
<template>
    <div class="toast-container">
      <transition-group name="toast">
        <div
          v-for="(notification, index) in notifications"
          :key="notification.id"
          :class="['toast', `toast-${notification.type || 'info'}`]"
          @click="removeNotification(notification.id)"
        >
          <div class="toast-icon">
            <div v-if="notification.type === 'success'">✓</div>
            <div v-else-if="notification.type === 'error'">✕</div>
            <div v-else-if="notification.type === 'warning'">⚠</div>
            <div v-else>ℹ</div>
          </div>
          <div class="toast-content">
            <div class="toast-title">{{ notification.title }}</div>
            <div class="toast-message">{{ notification.message }}</div>
          </div>
          <div class="toast-close" @click.stop="removeNotification(notification.id)">✕</div>
        </div>
      </transition-group>
    </div>
  </template>
  
  <script>
  import { defineComponent } from 'vue';
  import { useNotificationsStore } from '@/stores/useNotifications'
  import { storeToRefs } from 'pinia';

  
  export default defineComponent({
    name: 'ToastNotifications',
    props: {
      duration: {
        type: Number,
        default: 5000,
      },
      position: {
        type: String,
        default: 'top-right',
        validator: (value) => ['top-left', 'top-right', 'bottom-left', 'bottom-right'].includes(value),
      },
    },
    setup(props) {
      const notificationsStore = useNotificationsStore();
      const { notifications } = storeToRefs(notificationsStore);  
  
      const addNotification = (notification) => {
        notificationsStore.addNotification(notification);
        
        if (props.duration > 0) {
          setTimeout(() => {
            removeNotification(notification.id);
          }, props.duration);
        }
      };
  
      const removeNotification = (id) => {
        notificationsStore.removeNotification(id);
      };
  
      return {
        notifications,
        addNotification,
        removeNotification,
      };
    },
  });
  </script>
  
  <style scoped>
  .toast-container {
    position: fixed;
    z-index: 9999;
    display: flex;
    flex-direction: column;
    max-width: 350px;
    width: 100%;
    pointer-events: none;
  }
  
  .toast-container[data-position="top-right"] {
    top: 20px;
    right: 20px;
  }
  
  .toast-container[data-position="top-left"] {
    top: 20px;
    left: 20px;
  }
  
  .toast-container[data-position="bottom-right"] {
    bottom: 20px;
    right: 20px;
  }
  
  .toast-container[data-position="bottom-left"] {
    bottom: 20px;
    left: 20px;
  }
  
  .toast {
    display: flex;
    align-items: center;
    background-color: white;
    border-radius: 6px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    margin-bottom: 10px;
    pointer-events: auto;
    overflow: hidden;
    cursor: pointer;
    transition: all 0.3s ease;
    max-height: 100px;
    opacity: 1;
  }
  
  .toast-icon {
    display: flex;
    justify-content: center;
    align-items: center;
    width: 50px;
    height: 100%;
    font-size: 20px;
    color: white;
  }
  
  .toast-content {
    flex: 1;
    padding: 12px 10px;
  }
  
  .toast-title {
    font-weight: bold;
    margin-bottom: 5px;
  }
  
  .toast-message {
    font-size: 14px;
  }
  
  .toast-close {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    cursor: pointer;
    opacity: 0.7;
  }
  
  .toast-close:hover {
    opacity: 1;
  }
  
  .toast-success {
    border-left: 4px solid #10b981;
  }
  
  .toast-success .toast-icon {
    background-color: #10b981;
  }
  
  .toast-error {
    border-left: 4px solid #ef4444;
  }
  
  .toast-error .toast-icon {
    background-color: #ef4444;
  }
  
  .toast-warning {
    border-left: 4px solid #f59e0b;
  }
  
  .toast-warning .toast-icon {
    background-color: #f59e0b;
  }
  
  .toast-info {
    border-left: 4px solid #3b82f6;
  }
  
  .toast-info .toast-icon {
    background-color: #3b82f6;
  }
  
  /* Transition animations */
  .toast-enter-active, 
  .toast-leave-active {
    transition: all 0.3s ease;
  }
  
  .toast-enter-from {
    transform: translateX(100%);
    opacity: 0;
  }
  
  .toast-leave-to {
    transform: translateX(100%);
    opacity: 0;
    max-height: 0;
    margin-bottom: 0;
  }
  </style>