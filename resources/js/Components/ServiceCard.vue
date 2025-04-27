<template>
    <div class="bg-white rounded-lg shadow overflow-hidden">
      <div class="p-6">
        <div class="flex items-center">
          <div class="bg-blue-100 rounded-full p-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
          </div>
          <div class="mr-4">
            <h3 class="text-lg font-medium text-gray-900">{{ service.name }}</h3>
            <p class="text-sm text-gray-500">
              {{ service.user.name }}
            </p>
          </div>
        </div>
        
        <div class="mt-4">
          <p class="text-gray-600">{{ service.description }}</p>
        </div>
        
        <div class="mt-6">
          <button
            @click="$emit('request', service)"
            class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors"
          >
            طلب الخدمة
          </button>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  defineProps({
    service: {
      type: Object,
      required: true
    }
  });
  
  defineEmits(['request']);
  </script>