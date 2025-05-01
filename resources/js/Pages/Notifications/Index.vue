<script setup>
import { ref, computed } from 'vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';

const props = defineProps({
  notifications: {
    type: Array,
    default: () => []
  },
  unread_count: {
    type: Number,
    default: 0
  }
});

// تصفية الإشعارات
const filter = ref('all'); // all, read, unread
const searchQuery = ref('');

// حساب الإشعارات المعروضة حسب الفلتر
const filteredNotifications = computed(() => {
  let result = [...props.notifications];
  
  // تطبيق فلتر الحالة
  if (filter.value === 'read') {
    result = result.filter(notification => notification.read_at);
  } else if (filter.value === 'unread') {
    result = result.filter(notification => !notification.read_at);
  }
  
  // تطبيق فلتر البحث
  if (searchQuery.value.trim()) {
    const query = searchQuery.value.toLowerCase();
    result = result.filter(notification => 
      (notification.data.title && notification.data.title.toLowerCase().includes(query)) || 
      (notification.data.message && notification.data.message.toLowerCase().includes(query))
    );
  }
  
  return result;
});



const markAsRead = (notification) => {
  console.log(`تم تحديد الإشعار كمقروء: ${notification.id}`);
};


const deleteNotification = (notification) => {
  console.log(`تم حذف الإشعار: ${notification.id}`);
};

</script>

<template>
  <DashboardLayout>

    <div class="max-w-4xl mx-auto p-6">
      <!-- أدوات الفلترة والبحث -->
      <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="relative rounded-md shadow-sm w-full sm:w-1/2">
          <input
            type="text"
            v-model="searchQuery"
            placeholder="بحث في الإشعارات..."
            class="block w-full pr-10 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
          />
          <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
            </svg>
          </div>
        </div>
        
        <div class="flex space-x-2 space-x-reverse">
          <button 
            @click="filter = 'all'" 
            class="px-3 py-2 text-sm rounded-md" 
            :class="filter === 'all' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
          >
            الكل
            <span v-if="props.notifications.length" class="mr-1 text-xs">({{ props.notifications.length }})</span>
          </button>
          <button 
            @click="filter = 'unread'" 
            class="px-3 py-2 text-sm rounded-md" 
            :class="filter === 'unread' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
          >
            غير مقروء
            <span v-if="unread_count" class="mr-1 text-xs">({{ unread_count }})</span>
          </button>
          <button 
            @click="filter = 'read'" 
            class="px-3 py-2 text-sm rounded-md" 
            :class="filter === 'read' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
          >
            مقروء
            <span v-if="props.notifications.length - unread_count > 0" class="mr-1 text-xs">({{ props.notifications.length - unread_count }})</span>
          </button>
        </div>
      </div>

      <!-- قائمة الإشعارات -->
      <div v-if="filteredNotifications.length">
        <div class="space-y-4">
          <div
            v-for="notification in filteredNotifications"
            :key="notification.id"
            class="transition-all duration-200 mb-4 p-4 border rounded-lg shadow-sm hover:shadow-md relative"
            :class="notification.read_at ? 'bg-gray-50 border-gray-200' : 'bg-white border-r-4 border-blue-500'"
          >
            <div class="flex">
              <!-- محتوى الإشعار -->
              <div class="flex-1">
                <div class="flex justify-between items-start">
                  <h2 
                    class="text-lg font-semibold mb-1"
                    :class="notification.read_at ? 'text-gray-700' : 'text-gray-900'"
                  >
                    {{ notification.title }}
                  </h2>
                  <div class="flex space-x-2 space-x-reverse">
                    <!-- زر تحديد كمقروء إذا كان الإشعار غير مقروء -->
                    <button
                      v-if="!notification.read_at"
                      @click="markAsRead(notification)"
                      class="text-blue-600 hover:text-blue-800"
                      title="تحديد كمقروء"
                    >
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                      </svg>
                    </button>
                    
                    <!-- زر الحذف -->
                    <button
                      @click="deleteNotification(notification)"
                      class="text-red-500 hover:text-red-700"
                      title="حذف الإشعار"
                    >
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                      </svg>
                    </button>
                  </div>
                </div>
                
                <p 
                  class="text-gray-700 mb-2"
                  :class="notification.read_at ? 'text-gray-500' : 'text-gray-700'"
                >
                  {{ notification.data.message }}
                </p>
                

              </div>
            </div>
          </div>
        </div>
      </div>
      <div v-else class="flex flex-col items-center justify-center py-12 bg-gray-50 rounded-lg">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>
        <p class="text-gray-600 text-center">لا توجد إشعارات تطابق معايير البحث الحالية.</p>
      </div>
    </div>
  </DashboardLayout>
</template>