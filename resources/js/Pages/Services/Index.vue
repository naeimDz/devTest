<script setup>
import { ref, computed } from 'vue';
import { Link, router, useForm } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
    services: Object,
    filters: Object,
    success: String,
});

const searchQuery = ref(props.filters.search || '');
const statusFilter = ref(props.filters.status || '');
const showDeleteModal = ref(false);
const showCreateModal = ref(false);
const serviceToDelete = ref(null);

// نموذج إنشاء خدمة جديدة
const createForm = useForm({
    name: '',
    description: '',
    status: 'active',
});

// نموذج البحث
const searchForm = useForm({
    search: props.filters.search || '',
    status: props.filters.status || '',
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

// البحث عن خدمات
const search = () => {
    searchForm.get(route('services.admin'), {
        preserveState: true,
        preserveScroll: true,
    });
};

// إعادة ضبط عوامل البحث
const resetFilters = () => {
    searchForm.search = '';
    searchForm.status = '';
    searchForm.get(route('services.admin'));
};

// التحقق من وجود عوامل بحث نشطة
const isFiltering = computed(() => {
    return searchForm.search || searchForm.status;
});

// فتح مودال إنشاء خدمة جديدة
const openCreateModal = () => {
    createForm.reset();
    showCreateModal.value = true;
};
</script>

<template>
    <DashboardLayout>
        <template #header>
            <div class="flex justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">إدارة الخدمات</h2>
                <button
                    @click="openCreateModal"
                    class="px-4 py-2 bg-blue-500 hover:bg-blue-600 rounded-md"
                >
                    إضافة خدمة جديدة
                </button>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">

                        <!-- عوامل البحث -->
                        <div class="mb-6 bg-gray-50 p-4 rounded-lg">
                            <div class="flex flex-col md:flex-row md:items-center space-y-3 md:space-y-0 md:space-x-4 md:space-x-reverse">
                                <div class="flex-grow">
                                    <input
                                        v-model="searchForm.search"
                                        type="text"
                                        placeholder="ابحث عن خدمة..."
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 placeholder-gray-400"
                                        @keyup.enter="search"
                                    />
                                </div>
                                <div class="w-full md:w-48">
                                    <select
                                        v-model="searchForm.status"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                    >
                                        <option value="">جميع الحالات</option>
                                        <option value="active">مفعلة</option>
                                        <option value="inactive">غير مفعلة</option>
                                    </select>
                                </div>
                                <div class="flex items-center space-x-2 space-x-reverse">
                                    <button
                                        type="button"
                                        class="px-4 py-2 bg-blue-500 hover:bg-blue-600 rounded-md"
                                        @click="search"
                                    >
                                        بحث
                                    </button>
                                    <button
                                        v-if="isFiltering"
                                        type="button"
                                        class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-md"
                                        @click="resetFilters"
                                    >
                                        إعادة ضبط
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- قائمة الخدمات -->
                        <div v-if="services.data.length" class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <div v-for="service in services.data" :key="service.id" class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200">
                                <div class="p-5">
                                    <div class="flex justify-between items-start mb-3">
                                        <h3 class="text-lg font-semibold text-gray-800">{{ service.name }}</h3>
                                        <span
                                            :class="{
                                                'bg-green-100 text-green-800': service.status === 'active',
                                                'bg-red-100 text-red-800': service.status === 'inactive',
                                            }"
                                            class="px-2 py-1 text-xs rounded-full"
                                        >
                                            {{ service.status === 'active' ? 'مفعلة' : 'غير مفعلة' }}
                                        </span>
                                    </div>
                                    
                                    <p class="text-gray-600 mb-4">{{ service.description }}</p>
                                    
                                    <div class="flex justify-between items-center mt-4">
                                        <div class="text-sm text-gray-500">
                                            تم الإنشاء: {{ new Date(service.created_at).toLocaleDateString('ar-SA') }}
                                        </div>
                                        
                                        <div class="flex space-x-2 space-x-reverse">
                                            <Link
                                                :href="route('services.edit', service.id)"
                                                class="px-3 py-1 bg-blue-500 hover:bg-blue-600 text-sm rounded"
                                            >
                                                تعديل
                                            </Link>
                                            <button
                                                @click="confirmDeleteService(service)"
                                                class="px-3 py-1 bg-red-500 hover:bg-red-600 text-sm rounded"
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
                                    @click="openCreateModal"
                                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium bg-blue-600 hover:bg-blue-700"
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
                            class="px-4 py-2 bg-blue-500 hover:bg-blue-600  rounded-md"
                            :disabled="createForm.processing"
                        >
                            {{ createForm.processing ? 'جاري الإرسال...' : 'إضافة الخدمة' }}
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
                        class="px-4 py-2 bg-red-500 hover:bg-red-600 rounded-md"
                        @click="deleteService"
                    >
                        حذف
                    </button>
                </div>
            </div>
        </Modal>
    </DashboardLayout>
</template>