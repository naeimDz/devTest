<script setup>
import { Link } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';

// Define props para recibir información de error personalizada
defineProps({
    status: {
        type: Number,
        default: 403,
    },
    message: {
        type: String,
        default: 'غير مصرح لك بالوصول إلى هذه الصفحة',
    }
});
</script>

<template>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
        <div class="mb-8">
            <Link href="/">
                <ApplicationLogo class="w-20 h-20" />
            </Link>
        </div>

        <div class="w-full sm:max-w-md mt-6 px-6 py-10 bg-white shadow-md overflow-hidden sm:rounded-lg text-center">
            <div class="mb-8">
                <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-16 w-16 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>

            <div class="text-4xl font-bold text-red-500 mb-4">
                {{ status }}
            </div>

            <h1 class="text-2xl font-semibold text-gray-800 mb-4">
                غير مسموح بالوصول
            </h1>

            <p class="text-gray-600 mb-8">
                {{ message }}
            </p>

            <div class="flex justify-center gap-4">
                <Link 
                    href="/"
                    class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                >
                    العودة للرئيسية
                </Link>
                
                <button 
                    @click="$page.props.auth && $page.props.auth.user ? window.history.back() : null"
                    class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150"
                >
                    الرجوع للخلف
                </button>
            </div>
        </div>

        <div class="mt-8 text-center text-sm text-gray-500">
            إذا كنت تعتقد أن هذا خطأ، يرجى التواصل مع مسؤول النظام
        </div>
    </div>
</template>