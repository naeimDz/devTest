<script setup>
import { computed } from 'vue';

const props = defineProps({
  items: Object,
});
const items = computed(() => props.items.data || []);

const links = computed(() => props.items.links || []);

const emit = defineEmits(['page-changed']);

const paginationLinks = computed(() => {
  return links.value.filter((link, index) => {
    return index !== 0 && index !== (links.value.length - 1) || 
           ['Previous', 'Next', 'السابق', 'التالي'].includes(link.label);
  });
});


const hasMultiplePages = computed(() => {
  return props.items && props.items.last_page > 1;
});


const goToPage = (url) => {
  if (url) {

    const pageMatch = url.match(/page=(\d+)/);
    const page = pageMatch ? parseInt(pageMatch[1]) : 1;
    
    emit('page-changed', page);
  }
};
</script>

<template>
  <div v-if="hasMultiplePages" class="pagination-container flex items-center justify-center mt-6 mb-4 select-none" dir="rtl">
    <nav aria-label="صفحات النتائج" class="flex items-center justify-center space-x-1 space-x-reverse">
      
      <!-- زر الصفحة السابقة -->
      <button
        type="button"
        @click="goToPage(links[0].url)"
        :disabled="!links[0].url"
        :class="[
          'relative inline-flex items-center px-3 py-2 text-sm font-medium rounded-md',
          links[0].url 
            ? 'text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500'
            : 'text-gray-400 bg-gray-100 cursor-not-allowed'
        ]"
      >
        <span class="sr-only">الصفحة السابقة</span>
        <svg class="h-5 w-5 rtl:rotate-180" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
          <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
        </svg>
      </button>
      
      <!-- أرقام الصفحات -->
      <template v-for="(link, index) in paginationLinks" :key="index">
        <template v-if="link.label !== 'Previous' && link.label !== 'Next' && link.label !== 'السابق' && link.label !== 'التالي'">
          <button
            type="button"
            @click="goToPage(link.url)"
            :disabled="!link.url"
            :class="[
              'relative inline-flex items-center px-4 py-2 text-sm font-medium rounded-md',
              link.active
                ? 'z-10 bg-blue-50 border-blue-500 text-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500'
                : link.url
                  ? 'bg-white text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500'
                  : 'bg-gray-100 text-gray-400 cursor-not-allowed'
            ]"
            :aria-current="link.active ? 'page' : undefined"
          >
            {{ link.label }}
          </button>
        </template>
      </template>
      
      <!-- زر الصفحة التالية -->
      <button
        type="button"
        @click="goToPage(links[links.length - 1].url)"
        :disabled="!links[links.length - 1].url"
        :class="[
          'relative inline-flex items-center px-3 py-2 text-sm font-medium rounded-md',
          links[links.length - 1].url 
            ? 'text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500'
            : 'text-gray-400 bg-gray-100 cursor-not-allowed'
        ]"
      >
        <span class="sr-only">الصفحة التالية</span>
        <svg class="h-5 w-5 rtl:rotate-180" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
          <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
        </svg>
      </button>
    </nav>
    

    <div class="mr-4 text-sm text-gray-600">
      <span>صفحة {{ props.items.current_page }} من {{ props.items.last_page }}</span>
      <span class="mx-2">|</span>
      <span>{{ props.items.total }} نتيجة</span>
    </div>
  </div>
</template>