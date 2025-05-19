<template>
  <AppLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">حساب محظور</h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200 text-center">
            <h1 class="text-2xl font-bold text-red-600">عذراً، تم حظر حسابك</h1>
            <p class="mt-4 text-gray-600">لا يمكنك الوصول إلى هذه الصفحة لأن حسابك محظور.</p>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '../../Layouts/DashboardLayout.vue';
</script>
