<script setup>
import { ref, watch, computed, onMounted } from 'vue';
import AuthenticatedLayout from '@/Layouts/DashboardLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { useAuthStore } from '@/stores/useAuthStore';
import { useNotificationsStore } from '@/stores/useNotifications';

// الحصول على بيانات الصفحة من Inertia
const page = usePage();

const auth = useAuthStore();
const notificationsStore = useNotificationsStore();

// تأكد من تحديث بيانات المستخدم من بيانات Inertia
onMounted(() => {
  // تحديث متجر المستخدم إذا كانت البيانات متوفرة في الصفحة
  if (page.props.auth && page.props.auth.user) {
    auth.setUser(page.props.auth.user);
  }
  
  // تحديث الإشعارات إذا كانت متوفرة في الصفحة
  if (page.props.notifications) {
    notificationsStore.setNotifications(page.props.notifications);
  }
  
  // يمكنك هنا إضافة أي طلبات API إضافية للحصول على بيانات أخرى
  // مثل استدعاء notificationsStore.fetchNotifications() إذا كانت هناك دالة لجلب الإشعارات
});

// نراقب تغيير بيانات الصفحة لتحديث المتاجر
watch(() => page.props.auth?.user, (newUser) => {
  if (newUser) {
    auth.setUser(newUser);
  }
}, { immediate: true });

// computed properties للوصول للبيانات
const user = computed(() => auth.user);
const permissions = computed(() => auth.permissions);
const notifications = computed(() => notificationsStore.getNotifications);

// دوال مساعدة
const hasPermission = auth.hasPermission;
const hasRole = auth.hasRole;
</script>

<template>
  <Head title="لوحة التحكم" />

  <AuthenticatedLayout>
    <div class="py-12" dir="rtl">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <!-- Welcome section -->
          <div class="p-6">
            <h1 class="text-2xl font-bold mb-4" v-if="user">مرحبا {{ user.name }}</h1>
            <h1 class="text-2xl font-bold mb-4" v-else>جاري تحميل البيانات...</h1>

            <div class="mb-6" v-if="user">
              <p class="text-gray-700">📛 الإيميل: {{ user.email }}</p>
              <p class="text-gray-700">🧩 الدور: {{ user.role.name }}</p>
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
              <p v-if="hasPermission('create services')" class="text-green-600">✔ عندك صلاحية إنشاء خدمة</p>
              <p v-else class="text-red-600">⛔ ماعندكش صلاحية إنشاء خدمة</p>
            
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