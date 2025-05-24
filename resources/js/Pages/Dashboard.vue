<script setup>
import { computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/DashboardLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { useNotificationsStore } from '@/stores/useNotifications';

const page = usePage();
const notificationsStore = useNotificationsStore();

const user = computed(() => page.props.auth?.user);
const permissions = computed(() => user.value?.role?.permissions || []);
const notifications = computed(() => notificationsStore.getNotifications || []);

// استخدام الدوال المساعدة للتحقق من الأدوار والصلاحيات
const hasPermission = (permission) => {
  return permissions.value.some(p => p.name === permission);
};

const hasRole = (role) => {
  return user.value?.role?.name === role;
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
              <p v-if="hasPermission('create services')" class="text-green-600">✔ صلاحية إنشاء خدمة</p>
              <p v-else class="text-red-600">⛔ صلاحية إنشاء خدمة</p>
            
              <p v-if="hasRole('admin')" class="text-green-600 mt-2">✔ أنت مدير</p>
              <p v-else-if="hasRole('service_provider')" class="text-blue-600 mt-2">👨‍💼 أنت مزود خدمة</p>
              <p v-else class="text-gray-600 mt-2">👤 مستخدم عادي</p>
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