<script setup>
defineProps({
  notifications: Array,
  unread_count: Number,
});
</script>

<template>
  <div class="max-w-4xl mx-auto p-6">
    <h1 class="text-2xl font-bold mb-6">
        الإشعارات
        <span v-if="unread_count" class="ml-2 text-sm bg-red-500 text-white px-2 py-1 rounded-full">
            {{ unread_count }}
        </span>
    </h1>

    <div v-if="notifications.length">
      <div
        v-for="notification in notifications"
        :key="notification.id"
        class="mb-4 p-4 border rounded-lg shadow-sm"
        :class="notification.read_at ? 'bg-gray-100' : 'bg-white'"
      >{{ notification }}
        <h2 class="text-lg font-semibold">{{ notification.title }}</h2>
        <p class="text-gray-700">{{ notification.body }}</p>
        <small class="text-gray-500">{{ notification.created_at }}</small>
      </div>
    </div>
    <div v-else>
      <p class="text-gray-600 text-center">لا توجد إشعارات حاليا.</p>
    </div>
  </div>
</template>

<style scoped>
</style>
