<script setup>
import { ref, computed } from 'vue';
import {  router, useForm } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import Modal from '@/Components/Modal.vue';
import { useAuthStore } from '@/stores/useAuthStore';

const props = defineProps({
    services: Object,
    filters: Object,
    success: String,

});

const auth = useAuthStore();
const user = auth.user;
const showDeleteModal = ref(false);
const showCreateModal = ref(false);
const showEditModal = ref(false);
const serviceToDelete = ref(null);
const serviceToEdit = ref(null);

// التحقق من صلاحيات المستخدم
const hasRole = (roleName) => {
  return user?.role?.name?.toLowerCase() === roleName.toLowerCase();
};

// التحقق إذا كان المستخدم أدمن
const isAdmin = computed(() => hasRole('admin'));

// التحقق إذا كان المستخدم مزود خدمة
const isServiceProvider = computed(() => hasRole('service_provider'));

// نموذج إنشاء خدمة جديدة
const createForm = useForm({
    name: '',
    description: '',
    status: 'active',
});

// نموذج تعديل خدمة
const editForm = useForm({
    name: '',
    description: '',
    status: '',
    warning: false,
});

// تأكيد حذف خدمة
const confirmDeleteService = (service) => {
    serviceToDelete.value = service;
    showDeleteModal.value = true;
};

// حذف خدمة
const deleteService = () => {
    router.delete(route('services.destroy', serviceToDelete.value.id), {
        onSuccess: () => {
            showDeleteModal.value = false;
            serviceToDelete.value = null;
        },
    });
};

// إنشاء خدمة جديدة
const createService = () => {
    createForm.post(route('services.store'), {
        onSuccess: () => {
            showCreateModal.value = false;
            createForm.reset();
        },
    });
};

// فتح مودال تعديل الخدمة
const openEditModal = (service) => {
    serviceToEdit.value = service;
    editForm.name = service.name;
    editForm.description = service.description;
    editForm.status = service.status;
    editForm.warning = service.warning || false;
    showEditModal.value = true;
};

// تعديل خدمة
const updateService = () => {
    editForm.put(route('services.update', serviceToEdit.value.id), {
        onSuccess: () => {
            showEditModal.value = false;
            serviceToEdit.value = null;
        },
    });
};

// فتح مودال إنشاء خدمة جديدة
const openCreateModal = () => {
    createForm.reset();
    showCreateModal.value = true;
};

// أنواع البطاقات المعروضة حسب الحالة
const cardClasses = (service) => {
    if (service.warning) return 'border-yellow-400 bg-yellow-50';
    if (service.status === 'active') return 'border-green-200 bg-white';
    return 'border-gray-200 bg-gray-50';
};

// لون النص والخلفية لحالة الخدمة
const statusClasses = (service) => {
    if (service.warning) return 'bg-yellow-100 text-yellow-800';
    if (service.status === 'active') return 'bg-green-100 text-green-800';
    return 'bg-red-100 text-red-800';
};

// النص المعروض لحالة الخدمة
const statusText = (service) => {
    if (service.warning) return 'إنذار';
    if (service.status === 'active') return 'مفعلة';
    return 'غير مفعلة';
};
</script>

<template>
    <DashboardLayout>
        <div class="flex justify-between items-center py-6 px-4 sm:px-6 lg:px-8 bg-white shadow">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">إدارة الخدمات</h2>
            <button
                v-if="isAdmin || isServiceProvider"
                @click="openCreateModal"
                class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-md flex items-center"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-1" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                إضافة خدمة جديدة
            </button>
        </div>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- رسالة نجاح -->
                <div v-if="success" class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-md">
                    {{ success }}
                </div>
                
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <!-- قائمة الخدمات -->
                        <div v-if="services.data.length" class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <div 
                                v-for="service in services.data" 
                                :key="service.id" 
                                class="rounded-lg shadow-md overflow-hidden border transition-all duration-200 hover:shadow-lg"
                                :class="cardClasses(service)"
                            >
                                <div class="p-5">
                                    <div class="flex justify-between items-start mb-3">
                                        <h3 class="text-lg font-semibold text-gray-800">{{ service.name }}</h3>
                                        <span
                                            :class="statusClasses(service)"
                                            class="px-2 py-1 text-xs rounded-full"
                                        >
                                            {{ statusText(service) }}
                                        </span>
                                    </div>
                                    
                                    <p class="text-gray-600 mb-4">{{ service.description }}</p>
                                    
                                    <div class="flex justify-between items-center mt-4">
                                        <div class="text-sm text-gray-500">
                                            تم الإنشاء: {{ new Date(service.created_at).toLocaleDateString('ar-SA') }}
                                        </div>
                                        
                                        <div class="flex space-x-2 space-x-reverse">
                                            <!-- زر التعديل - متاح للأدمن ومزود الخدمة -->
                                            <button
                                                v-if="isAdmin || isServiceProvider"
                                                @click="openEditModal(service)"
                                                class="px-3 py-1 bg-blue-500 hover:bg-blue-600 text-white text-sm rounded"
                                            >
                                                تعديل
                                            </button>
                                            
                                            <!-- زر الحذف - متاح للأدمن فقط -->
                                            <button
                                                v-if="isAdmin"
                                                @click="confirmDeleteService(service)"
                                                class="px-3 py-1 bg-red-500 hover:bg-red-600 text-white text-sm rounded"
                                            >
                                                حذف
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- حالة الفراغ -->
                        <div v-else class="text-center py-12">
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
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">لا توجد خدمات</h3>
                            <p class="mt-1 text-sm text-gray-500">ابدأ بإضافة خدمة جديدة لعرضها هنا.</p>
                            <div class="mt-6">
                                <button
                                    v-if="isAdmin || isServiceProvider"
                                    @click="openCreateModal"
                                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium bg-blue-600 hover:bg-blue-700 text-white"
                                >
                                    <svg
                                        class="-ml-1 mr-2 h-5 w-5"
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20"
                                        fill="currentColor"
                                        aria-hidden="true"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                                            clip-rule="evenodd"
                                        />
                                    </svg>
                                    إضافة خدمة جديدة
                                </button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <!-- مودال إنشاء خدمة جديدة -->
        <Modal :show="showCreateModal" @close="showCreateModal = false">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">إضافة خدمة جديدة</h2>
                
                <form @submit.prevent="createService">
                    <div class="space-y-4">
                        <!-- اسم الخدمة -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">اسم الخدمة</label>
                            <input
                                type="text"
                                id="name"
                                v-model="createForm.name"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                required
                            />
                            <div v-if="createForm.errors.name" class="text-red-500 text-sm mt-1">{{ createForm.errors.name }}</div>
                        </div>
                        
                        <!-- وصف الخدمة -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700">وصف الخدمة</label>
                            <textarea
                                id="description"
                                v-model="createForm.description"
                                rows="4"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                required
                            ></textarea>
                            <div v-if="createForm.errors.description" class="text-red-500 text-sm mt-1">{{ createForm.errors.description }}</div>
                        </div>
                        
                        <!-- حالة الخدمة -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700">حالة الخدمة</label>
                            <select
                                id="status"
                                v-model="createForm.status"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                            >
                                <option value="active">مفعلة</option>
                                <option value="inactive">غير مفعلة</option>
                            </select>
                            <div v-if="createForm.errors.status" class="text-red-500 text-sm mt-1">{{ createForm.errors.status }}</div>
                        </div>
                    </div>
                    
                    <!-- أزرار الإرسال -->
                    <div class="flex justify-end space-x-3 space-x-reverse mt-6">
                        <button
                            type="button"
                            class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-md"
                            @click="showCreateModal = false"
                        >
                            إلغاء
                        </button>
                        <button
                            type="submit"
                            class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-md"
                            :disabled="createForm.processing"
                        >
                            {{ createForm.processing ? 'جاري الإرسال...' : 'إضافة الخدمة' }}
                        </button>
                    </div>
                </form>
            </div>
        </Modal>

        <!-- مودال تعديل الخدمة -->
        <Modal :show="showEditModal" @close="showEditModal = false">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">تعديل الخدمة</h2>
                
                <form @submit.prevent="updateService">
                    <div class="space-y-4">
                        <!-- اسم الخدمة -->
                        <div>
                            <label for="edit_name" class="block text-sm font-medium text-gray-700">اسم الخدمة</label>
                            <input
                                type="text"
                                id="edit_name"
                                v-model="editForm.name"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                required
                            />
                            <div v-if="editForm.errors.name" class="text-red-500 text-sm mt-1">{{ editForm.errors.name }}</div>
                        </div>
                        
                        <!-- وصف الخدمة -->
                        <div>
                            <label for="edit_description" class="block text-sm font-medium text-gray-700">وصف الخدمة</label>
                            <textarea
                                id="edit_description"
                                v-model="editForm.description"
                                rows="4"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                required
                            ></textarea>
                            <div v-if="editForm.errors.description" class="text-red-500 text-sm mt-1">{{ editForm.errors.description }}</div>
                        </div>
                        
                        <!-- حالة الخدمة - للمزود فقط -->
                        <div v-if="isServiceProvider">
                            <label for="edit_status" class="block text-sm font-medium text-gray-700">حالة الخدمة</label>
                            <select
                                id="edit_status"
                                v-model="editForm.status"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                            >
                                <option value="active">مفعلة</option>
                                <option value="inactive">غير مفعلة</option>
                            </select>
                            <div v-if="editForm.errors.status" class="text-red-500 text-sm mt-1">{{ editForm.errors.status }}</div>
                        </div>
                        
                        <!-- حالة الإنذار - للأدمن فقط -->
                        <div v-if="isAdmin">
                            <div class="flex items-center">
                                <input
                                    type="checkbox"
                                    id="edit_warning"
                                    v-model="editForm.warning"
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                />
                                <label for="edit_warning" class="mr-2 block text-sm text-gray-900">
                                    وضع الخدمة تحت الإنذار
                                </label>
                            </div>
                            <p class="text-sm text-gray-500 mt-1">
                                سيظهر إشعار للمستخدمين بأن هذه الخدمة تحت الإنذار
                            </p>
                        </div>
                        
                        <!-- حالة الخدمة - للأدمن فقط لتغيير حالة التفعيل -->
                        <div v-if="isAdmin">
                            <label for="admin_edit_status" class="block text-sm font-medium text-gray-700">حالة الخدمة</label>
                            <select
                                id="admin_edit_status"
                                v-model="editForm.status"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                            >
                                <option value="active">مفعلة</option>
                                <option value="inactive">غير مفعلة</option>
                            </select>
                            <div v-if="editForm.errors.status" class="text-red-500 text-sm mt-1">{{ editForm.errors.status }}</div>
                        </div>
                    </div>
                    
                    <!-- أزرار الإرسال -->
                    <div class="flex justify-end space-x-3 space-x-reverse mt-6">
                        <button
                            type="button"
                            class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-md"
                            @click="showEditModal = false"
                        >
                            إلغاء
                        </button>
                        <button
                            type="submit"
                            class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-md"
                            :disabled="editForm.processing"
                        >
                            {{ editForm.processing ? 'جاري التحديث...' : 'تحديث الخدمة' }}
                        </button>
                    </div>
                </form>
            </div>
        </Modal>

        <!-- مودال تأكيد الحذف -->
        <Modal :show="showDeleteModal" @close="showDeleteModal = false">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">تأكيد الحذف</h2>
                <p class="mb-6 text-gray-600">
                    هل أنت متأكد من حذف الخدمة "{{ serviceToDelete?.name }}"؟ هذا الإجراء لا يمكن التراجع عنه.
                </p>
                <div class="flex justify-end space-x-3 space-x-reverse">
                    <button
                        type="button"
                        class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-md"
                        @click="showDeleteModal = false"
                    >
                        إلغاء
                    </button>
                    <button
                        type="button"
                        class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-md"
                        @click="deleteService"
                    >
                        حذف
                    </button>
                </div>
            </div>
        </Modal>
    </DashboardLayout>
</template>