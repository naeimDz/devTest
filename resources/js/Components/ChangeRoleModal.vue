<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    user: Object,
    roles: Array,
    show: Boolean
});

const emit = defineEmits(['close']);

const form = useForm({
    role_id: props.user?.role_id
});

function closeModal() {
    emit('close');
}

function updateRole() {
    form.put(route('users.update-role', props.user.id), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
    });
}
</script>

<template>
    <Modal :show="show" @close="closeModal">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">
                تغيير دور المستخدم
            </h2>
            
            <div class="mt-4">
                <div class="mb-4">
                    <p class="text-sm text-gray-600 mb-2">المستخدم: {{ user?.name }}</p>
                    <p class="text-sm text-gray-600">البريد الإلكتروني: {{ user?.email }}</p>
                </div>
                
                <div class="mb-4">
                    <label for="role" class="block font-medium text-sm text-gray-700">الدور الجديد</label>
                    <select
                        id="role"
                        v-model="form.role_id"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                    >
                        <option v-for="role in roles" :key="role.id" :value="role.id">
                            {{ role.name }}
                        </option>
                    </select>
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-3 rtl:space-x-reverse">
                <SecondaryButton @click="closeModal">إلغاء</SecondaryButton>
                <PrimaryButton :disabled="form.processing" @click="updateRole">تحديث</PrimaryButton>
            </div>
        </div>
    </Modal>
</template>