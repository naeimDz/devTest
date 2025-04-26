<template>
    <div class="p-6">
      <h1 class="text-2xl font-bold mb-4">مرحبا {{ user?.name }}</h1>

      <div class="mb-6">
        <p class="text-gray-700">📛 الإيميل: {{ user?.email }}</p>
        <p class="text-gray-700">🧩 الدور: {{ user?.role.name }}</p>
      </div>
  
      <div class="mb-6">
        <h2 class="text-xl font-semibold mb-2">🛡 الصلاحيات:</h2>
        <ul class="list-disc list-inside">
          <li v-for="perm in permissions" :key="perm.id">
            ✅ {{ perm.name }} ({{ perm.module }})
          </li>
        </ul>
      </div>
  
      <div class="border-t pt-4 mt-4">
        <h2 class="text-lg font-medium mb-2">🔐 التحقق:</h2>
        <p v-if="hasPermission('create services')" class="text-green-600">✔ عندك صلاحية إنشاء خدمة</p>
        <p v-else class="text-red-600">⛔ ماعندكش صلاحية إنشاء خدمة</p>
  
        <p v-if="hasRole('admin')" class="text-green-600 mt-2">✔ أنت مدير</p>
        <p v-else class="text-gray-600 mt-2">👤 مستخدم عادي</p>
      </div>
    </div>
  </template>
  
  <script setup lang="ts">
  import { useAuthStore } from '@/stores/useAuthStore'
  
  const auth = useAuthStore()
  const user = auth.user
  const permissions = auth.permissions
  
  const hasPermission = auth.hasPermission
  const hasRole = auth.hasRole
  </script>
  