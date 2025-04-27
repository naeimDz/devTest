<template>
    <div v-if="show" class="fixed inset-0 z-10 overflow-y-auto">
      <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
          <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>
  
        <!-- Modal panel -->
        <div class="inline-block align-middle bg-white rounded-lg text-right overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
          <div class="px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="sm:flex sm:items-start">
              <div class="mt-3 text-center sm:mt-0 sm:mr-4 sm:text-right w-full">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                  طلب خدمة: {{ service.name }}
                </h3>
                <div class="mt-2">
                  <p class="text-sm text-gray-500">
                    يرجى تعبئة النموذج التالي لطلب هذه الخدمة. سيتواصل معك مزود الخدمة في أقرب وقت.
                  </p>
                </div>
              </div>
            </div>
  
            <form @submit.prevent="submitRequest" class="mt-5 space-y-4">
              <div>
                <label for="name" class="block text-sm font-medium text-gray-700">الاسم</label>
                <input 
                  type="text" 
                  id="name"
                  v-model="form.name"
                  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                  required
                />
                <div v-if="form.errors.name" class="text-red-500 text-sm mt-1">{{ form.errors.name }}</div>
              </div>
  
              <div>
                <label for="email" class="block text-sm font-medium text-gray-700">البريد الإلكتروني</label>
                <input 
                  type="email" 
                  id="email"
                  v-model="form.email"
                  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                  required
                />
                <div v-if="form.errors.email" class="text-red-500 text-sm mt-1">{{ form.errors.email }}</div>
              </div>
  
              <div>
                <label for="phone" class="block text-sm font-medium text-gray-700">رقم الهاتف</label>
                <input 
                  type="tel" 
                  id="phone"
                  v-model="form.phone"
                  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                  required
                />
                <div v-if="form.errors.phone" class="text-red-500 text-sm mt-1">{{ form.errors.phone }}</div>
              </div>
  
              <div>
                <label for="message" class="block text-sm font-medium text-gray-700">تفاصيل الطلب (اختياري)</label>
                <textarea 
                  id="message"
                  v-model="form.message"
                  rows="4"
                  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                ></textarea>
                <div v-if="form.errors.message" class="text-red-500 text-sm mt-1">{{ form.errors.message }}</div>
              </div>
            </form>
          </div>
  
          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button 
              type="button"
              @click="submitRequest"
              :disabled="form.processing"
              class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mr-3 sm:w-auto sm:text-sm"
            >
              <span v-if="form.processing">جاري الإرسال...</span>
              <span v-else>إرسال الطلب</span>
            </button>
            <button 
              type="button"
              @click="$emit('close')"
              class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:mr-3 sm:w-auto sm:text-sm"
            >
              إلغاء
            </button>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, defineProps, defineEmits, watch } from 'vue';
  import { useForm } from '@inertiajs/vue3';
  
  const props = defineProps({
    service: {
      type: Object,
      required: true
    },
    show: {
      type: Boolean,
      default: false
    }
  });
  
  const emit = defineEmits(['close']);
  
  const form = useForm({
    name: '',
    email: '',
    phone: '',
    message: ''
  });
  
  // Reset form when modal is closed
  watch(() => props.show, (newValue) => {
    if (!newValue) {
      form.reset();
      form.clearErrors();
    }
  });
  
  function submitRequest() {
    form.post(route('services.request', props.service.id), {
      onSuccess: () => {
        emit('close');
      }
    });
  }
  </script>