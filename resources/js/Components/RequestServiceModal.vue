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
          <!-- Authenticated user view - simple confirmation -->
          <div v-if="authUser" class="sm:flex sm:items-start">
            <div class="mt-3 text-center sm:mt-0 sm:mr-4 sm:text-right w-full">
              <h3 class="text-lg leading-6 font-medium text-gray-900">
                طلب خدمة: {{ service.name }}
              </h3>
              <div class="mt-2">
                <p class="text-sm text-gray-500">
                  هل أنت متأكد من رغبتك في طلب هذه الخدمة؟
                </p>
              </div>
              <div class="mt-4">
                <div class="flex justify-between">
                  <button @click="submitAuthenticatedRequest" :disabled="form.processing" class="bg-blue-600 text-white px-4 py-2 rounded">
                    <span v-if="form.processing">جاري الإرسال...</span>
                    <span v-else>نعم، أرسل الطلب</span>
                  </button>
                  <button @click="closeModal" class="bg-gray-300 text-gray-700 px-4 py-2 rounded">إلغاء</button>
                </div>
              </div>
            </div>
          </div>

          <!-- Guest user view - form -->
          <div v-else class="sm:flex sm:items-start">
            <div class="mt-3 text-center sm:mt-0 sm:mr-4 sm:text-right w-full">
              <h3 class="text-lg leading-6 font-medium text-gray-900">
                طلب خدمة: {{ service.name }}
              </h3>
              <div class="mt-2">
                <p class="text-sm text-gray-500">
                  يرجى تعبئة النموذج التالي لطلب هذه الخدمة. سيتواصل معك مزود الخدمة في أقرب وقت.
                </p>
              </div>
              
              <form @submit.prevent="submitGuestRequest" class="mt-5 space-y-4">
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
                
                <div class="mt-4 flex justify-between">
                  <button 
                    type="submit" 
                    :disabled="form.processing" 
                    class="bg-blue-600 text-white px-4 py-2 rounded"
                  >
                    <span v-if="form.processing">جاري الإرسال...</span>
                    <button :disabled="form.processing">إرسال الطلب</button>
                  </button>
                  <button 
                    type="button" 
                    @click="closeModal" 
                    class="bg-gray-300 text-gray-700 px-4 py-2 rounded"
                  >
                    إلغاء
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, defineProps, defineEmits, watch, onMounted } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { useAuthStore } from '@/stores/useAuthStore'
  
const auth = useAuthStore()
const user = auth.user

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
  email: '',
});

const authUser = auth.isAuthenticated;

// Pre-fill email if authenticated
onMounted(() => {
  if (authUser && user?.email) {
    form.email = user.email;
  }
});


watch(() => props.show, (newValue) => {
  if (!newValue) {
    form.reset();
    form.clearErrors();

  }
});

function submitAuthenticatedRequest() {
  form.post(route('request.explore', props.service.id), {
    onSuccess: () => {
      emit('close');
      form.reset();
    }
  });
}

function submitGuestRequest() {
  form.post(route('services.explore', props.service.id), {
    onSuccess: () => {
      emit('close');
      form.reset();
    }
  });
}

function closeModal() {
  emit('close');
}
</script>