<script setup>
import { ref,  watch,computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/DashboardLayout.vue';
import { Head } from '@inertiajs/vue3';
import { useAuthStore } from '@/stores/useAuthStore';
import { useNotificationsStore } from '@/stores/useNotifications';

const auth = useAuthStore();
const user = auth.user;
const permissions = ref([]);
const loading = ref(true);
const notificationsStore = useNotificationsStore();

const notifications = computed(() => notificationsStore.getNotifications);

watch(() => auth.user, (newUser) => {
  if (newUser && newUser.role?.permissions) {
    permissions.value = newUser.role.permissions;
  }
}, { immediate: true });

// Helper functions for role and permissions checking
const hasPermission = (permissionName) => {
  return permissions.value.some(p => p.name.toLowerCase() === permissionName.toLowerCase());
};
const hasRole = (roleName) => {
  return user.role.name.toLowerCase() === roleName.toLowerCase();
};

</script>

<template>
  <Head title="لوحة التحكم" />

  <AuthenticatedLayout>
    <div class="py-12" dir="rtl">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <!-- Welcome section -->
          <div class="p-6">
            <h1 class="text-2xl font-bold mb-4">مرحبا {{ user?.name }}</h1>

            <div class="mb-6">
              <p class="text-gray-700">📛 الإيميل: {{ user?.email }}</p>
              <p class="text-gray-700">🧩 الدور: {{ user?.role.name }}</p>
            </div>
            
            <!-- Stats Cards
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
              <div class="bg-blue-50 p-4 rounded-lg shadow">
                <h3 class="text-lg font-medium text-blue-800 mb-2">الطلبات النشطة</h3>
                <p class="text-3xl font-bold text-blue-600">{{loading ? '...' : '5'}}</p>
              </div>
              
              <div class="bg-green-50 p-4 rounded-lg shadow">
                <h3 class="text-lg font-medium text-green-800 mb-2">الخدمات المتاحة</h3>
                <p class="text-3xl font-bold text-green-600">{{loading ? '...' : '12'}}</p>
              </div>
              
              <div class="bg-purple-50 p-4 rounded-lg shadow">
                <h3 class="text-lg font-medium text-purple-800 mb-2">الإشعارات الجديدة</h3>
                <p class="text-3xl font-bold text-purple-600">
                  {{ notifications.length }}
                </p>
              </div>
            </div>
              -->
            <!-- Permissions section -->
            <div class="mb-6">
              <h2 class="text-xl font-semibold mb-2">🛡 الصلاحيات:</h2>
              <p v-if="permissions.length === 0" class="text-gray-500">لا توجد صلاحيات مخصصة</p>
              <ul v-else class="list-disc list-inside">
                <li v-for="perm in permissions" :key="perm.id">
                  ✅ {{ perm.name }} ({{ perm.module }})
                </li>
              </ul>
            </div>

            <!-- Role verification section -->
            <div class="border-t pt-4 mt-4">
              <h2 class="text-lg font-medium mb-2">🔐 التحقق:</h2>
              <p v-if="hasPermission('create services')" class="text-green-600">✔ عندك صلاحية إنشاء خدمة</p>
              <p v-else class="text-red-600">⛔ ماعندكش صلاحية إنشاء خدمة</p>
            
              <p v-if="hasRole('admin')" class="text-green-600 mt-2">✔ أنت مدير</p>
              <p v-else-if="hasRole('service_provider')" class="text-blue-600 mt-2">👨‍💼 أنت مزود خدمة</p>
              <p v-else class="text-gray-600 mt-2">👤 مستخدم عادي</p>
            </div>
            
            <!-- Quick Actions -->
            <div class="border-t pt-4 mt-4" v-if="hasRole('admin') || hasRole('service_provider')">
              <h2 class="text-lg font-medium mb-3">⚡ الإجراءات السريعة:</h2>
              
              <div class="flex flex-wrap gap-3">
                <button v-if="hasPermission('create services')"
                        class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg flex items-center">
                  <span class="ml-2">➕</span>
                  <span>إنشاء خدمة جديدة</span>
                </button>
                
                <button class="bg-purple-600 hover:bg-purple-700 text-white py-2 px-4 rounded-lg flex items-center">
                  <span class="ml-2">📋</span>
                  <span>عرض الطلبات الجديدة</span>
                </button>
                
                <button v-if="hasRole('admin')" 
                        class="bg-gray-600 hover:bg-gray-700 text-white py-2 px-4 rounded-lg flex items-center">
                  <span class="ml-2">👥</span>
                  <span>إدارة المستخدمين</span>
                </button>
              </div>
            </div>
            
            <!-- Notifications section -->
            <div class="border-t pt-4 mt-4">
              <h2 class="text-lg font-medium mb-2">🔔 الإشعارات:</h2>
              <p v-if="notifications.length === 0" class="text-gray-500">
                لا توجد إشعارات حالياً
              </p>
              <ul v-else class="space-y-2">
                <li v-for="notification in notifications" :key="notification.id" 
                    class="p-3 rounded-md">
                  <div class="flex justify-between">
                    <p>{{ notification.message }}</p>
                  </div>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>