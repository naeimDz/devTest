<script setup>
import { ref } from 'vue';
import { router,Head, Link} from '@inertiajs/vue3';
import { useForm } from '@inertiajs/vue3';
import { useAuthStore } from '@/stores/useAuthStore';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import Modal from '@/Components/Modal.vue';
import Pagination from '@/Components/Pagination.vue';

const props = defineProps({
    requestServices: Object,
});
const auth = useAuthStore();
const user = auth.user;

const selectedRequest = ref(null);
const showUpdateModal = ref(false);
const showDetailsModal = ref(false);

const updateForm = useForm({
    status: '',
});

const statusOptions = [
    { value: 'pending', label: 'قيد الانتظار' },
    { value: 'accepted', label: 'مقبول' },
    { value: 'processing', label: 'قيد التنفيذ' },
    { value: 'completed', label: 'مكتمل' },
    { value: 'cancelled', label: 'ملغي' },
];

const isServiceProvider = () => {
    return user?.role?.name === 'service_provider';
};



const openUpdateModal = (request) => {
    selectedRequest.value = request;
    updateForm.status = request.status;
    showUpdateModal.value = true;
};

const openDetailsModal = (request) => {
    selectedRequest.value = request;
    showDetailsModal.value = true;
};

const updateStatus = () => {
    updateForm.put(route('requests.update-status', selectedRequest.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            showUpdateModal.value = false;
            selectedRequest.value = null;
        },
    });
};

const getStatusLabel = (status) => {
    const option = statusOptions.find(opt => opt.value === status);
    return option ? option.label : status;
};

const getStatusColor = (status) => {
    switch (status) {
        case 'pending': return 'yellow';
        case 'accepted': return 'blue';
        case 'processing': return 'indigo';
        case 'completed': return 'green';
        case 'cancelled': return 'red';
        default: return 'gray';
    }
};

const formatDate = (dateString) => {
    const date = new Date(dateString);
    return new Intl.DateTimeFormat('ar-SA', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    }).format(date);
};


const handlePageChange = (page) => {
  router.get(route(route().current()), 
    { 
      ...route().params,
      page: page 
    }, 
    { 
      preserveState: true,
      preserveScroll: true,
      only: ['requestServices']
    }
  );
};


</script>

<template>
    <Head title="إدارة الطلبات" />

    <DashboardLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">إدارة الطلبات</h2>
                <div class="flex space-x-2 rtl:space-x-reverse">
                    <Link
                        :href="route('dashboard')"
                        class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150"
                    >
                        العودة للوحة التحكم
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">

                        <!-- Requests Table -->
                        <div class="overflow-x-auto flex justify-center">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            رقم الطلب
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            اسم الخدمة
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            البريد الإلكتروني
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            الحالة
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            الإجراءات
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="request in requestServices.data" :key="request.id">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            #{{ request.id }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ request.service ? request.service.name : 'خدمة محذوفة' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ request.email }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                :class="`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-${getStatusColor(request.status)}-100 text-${getStatusColor(request.status)}-800`"
                                            >
                                                {{ getStatusLabel(request.status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <div class="flex items-center space-x-2 rtl:space-x-reverse">
                                                <!-- إظهار زر تحديث الحالة فقط لمزود الخدمة -->
                                                <button
                                                    v-if="isServiceProvider()"
                                                    @click="openUpdateModal(request)"
                                                    class="inline-flex items-center px-3 py-1 border border-transparent text-xs leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:border-indigo-700 focus:shadow-outline-indigo active:bg-indigo-700 transition ease-in-out duration-150"
                                                >
                                                    تحديث الحالة
                                                </button>
                                                
                                                <!-- زر عرض التفاصيل للأدمن -->
                                                <button
                                                    @click="openDetailsModal(request)"
                                                    class="inline-flex items-center px-3 py-1 border border-transparent text-xs leading-4 font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:border-indigo-300 focus:shadow-outline-indigo active:bg-indigo-200 transition ease-in-out duration-150"
                                                >
                                                    عرض التفاصيل
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    
                                    <!-- حالة الفراغ -->
                                    <tr v-if="requestServices && requestServices.data.length === 0">
                                        <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                                            <div class="flex flex-col items-center justify-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                                <span class="text-lg font-medium">لا توجد طلبات متاحة</span>
                                                <p class="mt-1">ستظهر هنا الطلبات المتعلقة بخدماتك بمجرد إضافتها.</p>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <Pagination 
                            :items="props.requestServices" 
                            @page-changed="handlePageChange" 
                            />
                    </div>
                </div>
            </div>
        </div>

        <Modal :show="showUpdateModal" @close="showUpdateModal = false">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">تحديث حالة الطلب</h2>
                
                <div v-if="selectedRequest" class="mb-6">
                    <div class="mb-2">
                        <span class="font-bold">رقم الطلب:</span> #{{ selectedRequest.id }}
                    </div>
                    <div class="mb-2">
                        <span class="font-bold">البريد الإلكتروني:</span> {{ selectedRequest.email }}
                    </div>
                    <div class="mb-4">
                        <span class="font-bold">الحالة الحالية:</span>
                        <span 
                            :class="`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-${getStatusColor(selectedRequest.status)}-100 text-${getStatusColor(selectedRequest.status)}-800`"
                        >
                            {{ getStatusLabel(selectedRequest.status) }}
                        </span>
                    </div>
                </div>
                
                <form @submit.prevent="updateStatus">
                    <div class="mb-4">
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">تحديث الحالة</label>
                        <select
                            id="status"
                            v-model="updateForm.status"
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            required
                        >
                            <option v-for="option in statusOptions" :key="option.value" :value="option.value">
                                {{ option.label }}
                            </option>
                        </select>
                        <p v-if="updateForm.errors.status" class="mt-1 text-sm text-red-600">{{ updateForm.errors.status }}</p>
                    </div>
                    
                    <div class="flex justify-end mt-6 space-x-2 rtl:space-x-reverse">
                        <button
                            type="button"
                            class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150"
                            @click="showUpdateModal = false"
                        >
                            إلغاء
                        </button>
                        <button
                            type="submit"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150"
                            :disabled="updateForm.processing"
                        >
                            تحديث الحالة
                        </button>
                    </div>
                </form>
            </div>
        </Modal>

        <Modal :show="showDetailsModal" @close="showDetailsModal = false">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">تفاصيل الطلب</h2>
                
                <div v-if="selectedRequest" class="mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="mb-2">
                            <span class="font-bold">رقم الطلب:</span> #{{ selectedRequest.id }}
                        </div>
                        <div class="mb-2">
                            <span class="font-bold">البريد الإلكتروني:</span> {{ selectedRequest.email }}
                        </div>
                        <div class="mb-2">
                            <span class="font-bold">الخدمة:</span> {{ selectedRequest.service ? selectedRequest.service.name : 'خدمة محذوفة' }}
                        </div>
                        <div class="mb-2">
                            <span class="font-bold">الحالة:</span>
                            <span 
                                :class="`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-${getStatusColor(selectedRequest.status)}-100 text-${getStatusColor(selectedRequest.status)}-800`"
                            >
                                {{ getStatusLabel(selectedRequest.status) }}
                            </span>
                        </div>
                        <div class="mb-2">
                            <span class="font-bold">تاريخ الإنشاء:</span> {{ selectedRequest.created_at ? formatDate(selectedRequest.created_at) : 'غير متوفر' }}
                        </div>
                        <div class="mb-2">
                            <span class="font-bold">تاريخ آخر تحديث:</span> {{ selectedRequest.updated_at ? formatDate(selectedRequest.updated_at) : 'غير متوفر' }}
                        </div>
                    </div>

                    <div v-if="selectedRequest.details" class="mt-4">
                        <h3 class="font-bold mb-2">التفاصيل:</h3>
                        <div class="bg-gray-50 p-3 rounded-md">
                            {{ selectedRequest.details }}
                        </div>
                    </div>

                    <div v-if="selectedRequest.notes" class="mt-4">
                        <h3 class="font-bold mb-2">ملاحظات:</h3>
                        <div class="bg-gray-50 p-3 rounded-md">
                            {{ selectedRequest.notes }}
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-end mt-6">
                    <button
                        type="button"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150"
                        @click="showDetailsModal = false"
                    >
                        إغلاق
                    </button>
                </div>
            </div>
        </Modal>
    </DashboardLayout>
</template>