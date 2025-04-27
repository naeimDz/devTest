<template>
    <div class="services-explore">
      <div class="bg-gray-50 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="text-center">
            <h1 class="text-3xl font-extrabold text-gray-900 sm:text-4xl">
              استكشف خدماتنا
            </h1>
            <p class="mt-3 max-w-2xl mx-auto text-xl text-gray-500 sm:mt-4">
              أطلب الخدمة التي تناسب احتياجاتك من خلال مزودينا المحترفين
            </p>
          </div>
  
          <div class="mt-12">
            <!-- Service Filters -->
            <div class="mb-8">
              <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="relative w-full md:w-64">
                  <input
                    v-model="searchQuery"
                    type="text"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    placeholder="ابحث عن خدمة..."
                  />
                </div>
              </div>
            </div>
  
            <!-- No Services Message -->
            <div v-if="filteredServices.length === 0" class="text-center py-12">
              <svg
                class="mx-auto h-12 w-12 text-gray-400"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                aria-hidden="true"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                />
              </svg>
              <h3 class="mt-2 text-sm font-medium text-gray-900">لا توجد خدمات</h3>
              <p class="mt-1 text-sm text-gray-500">
                لم يتم العثور على خدمات متطابقة مع بحثك
              </p>
            </div>
  
            <!-- Services Grid -->
            <div
              v-else
              class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3"
            >
              <service-card
                v-for="service in filteredServices"
                :key="service.id"
                :service="service"
                @request="openRequestModal(service)"
              />
            </div>
          </div>
        </div>
      </div>
      <div v-if="message" class="alert alert-success">
      {{ message }}
    </div>
      <!-- Request Service Modal -->
      <request-service-modal
        v-if="selectedService"
        :service="selectedService"
        :show="isModalOpen"
        @close="closeRequestModal"
      />
    </div>
  </template>
  
  <script setup>
  import { ref, computed } from "vue";
  import { usePage } from "@inertiajs/vue3";
  import ServiceCard from "../../Components/ServiceCard.vue";
  import RequestServiceModal from "../../Components/RequestServiceModal.vue";
  
  const page = usePage();
  const services = ref(page.props.services);
  const searchQuery = ref("");
  const selectedService = ref(null);
  const isModalOpen = ref(false);
  
  // Filter services based on search query
  const filteredServices = computed(() => {
    if (!searchQuery.value) return services.value;
    
    const query = searchQuery.value.toLowerCase();
    return services.value.filter(
      (service) =>
        service.name.toLowerCase().includes(query) ||
        service.description.toLowerCase().includes(query)
    );
  });
  
  // Modal functions
  function openRequestModal(service) {
    selectedService.value = service;
    isModalOpen.value = true;
  }
  
  function closeRequestModal() {
    isModalOpen.value = false;
    // Wait for the modal transition to finish before clearing the service
    setTimeout(() => {
      selectedService.value = null;
    }, 300);
  }
  </script>