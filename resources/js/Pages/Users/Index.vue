<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import ChangeRoleModal from '@/Components/ChangeRoleModal.vue'; 
import Pagination from '@/Components/Pagination.vue';


const props = defineProps({
    users: Object,
    roles: Array,
    filters: Object
});

const showChangeRoleModal = ref(false);
const selectedUser = ref(null);


function toggleUserStatus(user) {
    router.put(route('users.toggle-status', user.id), {}, {
        preserveScroll: true,
    });
}

function openChangeRoleModal(user) {
    selectedUser.value = user;
    showChangeRoleModal.value = true;
}

const handlePageChange = (page) => {
  router.get(route(route().current()), 
    { 
      ...route().params,
      page: page 
    }, 
    { 
      preserveState: true,
      preserveScroll: true,
      only: ['users']
    }
  );
};
</script>

<template>
    <DashboardLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">قائمة المستخدمين</h2>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">

                        <!-- Users Table -->
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            الاسم
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            البريد الإلكتروني
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            الدور
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
                                    <tr v-for="user in users.data" :key="user.id">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ user.name }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-500">{{ user.email }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ user.role.name }}</div>
                                            <button 
                                                @click="openChangeRoleModal(user)"
                                                class="text-sm text-indigo-600 hover:text-indigo-900 mr-2"
                                            >
                                                تغيير الدور
                                            </button>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span 
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                                :class="{
                                                    'bg-green-100 text-green-800': user.status === 'active',
                                                    'bg-red-100 text-red-800': user.status === 'inactive'
                                                }">
                                                {{ user.status === 'active' ? 'نشط' : 'غير نشط' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex items-center space-x-2 rtl:space-x-reverse">
                                                <button 
                                                    @click="toggleUserStatus(user)"
                                                    class="text-sm"
                                                    :class="user.status === 'active' ? 'text-red-600 hover:text-red-900' : 'text-green-600 hover:text-green-900'"
                                                >
                                                    {{ user.status === 'active' ? 'حظر' : 'إلغاء الحظر' }}
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    
                                    <!-- Empty state -->
                                    <tr v-if="users.data.length === 0">
                                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                            لا يوجد مستخدمين مطابقين للبحث
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <Pagination 
                            :items="props.users" 
                            @page-changed="handlePageChange" 
                            />
                    </div>
                </div>
            </div>
        </div>
        <ChangeRoleModal 
            :show="showChangeRoleModal"
            :user="selectedUser"
            :roles="roles"
            @close="showChangeRoleModal = false"
        />
    </DashboardLayout>
</template>