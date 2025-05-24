<script setup>
import { ref, computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import ApplicationLogo from '@/Components/ApplicationLogo.vue'
import Dropdown from '@/Components/Dropdown.vue'
import DropdownLink from '@/Components/DropdownLink.vue'
import NavLink from '@/Components/NavLink.vue'
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue'
import ToastNotifications from '@/Components/ToastNotifications.vue'
import { usePage } from '@inertiajs/vue3';
import { useNotificationsStore } from '@/stores/useNotifications'
const page = usePage();
const user = computed(() => page.props.auth?.user);

const hasRole = (role) => {
  return user.value?.role?.name === role;
};

const showingNavigationDropdown = ref(false)
const notificationsStore = useNotificationsStore()
const notifications = computed(() => notificationsStore.getNotifications)

const isAdmin = computed(() => hasRole('admin'))
const isProvider = computed(() => hasRole('service_provider'))


</script>

<template>
  <div class="min-h-screen bg-gray-100">
    <ToastNotifications position="bottom-left" :timeout="5000" :maxToasts="5" />

<!-- Navbar -->
<nav class="bg-white border-b border-gray-100">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between h-16">
      <div class="flex">
        <!-- Logo -->
        <div class="shrink-0 flex items-center">
          <Link :href="route('dashboard')">
            <ApplicationLogo class="block h-9 w-auto" />
          </Link>
        </div>

        <!-- Navigation Links -->
        <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex" v-if="user">
          <NavLink :href="route('dashboard')" :active="route().current('dashboard')">لوحة التحكم</NavLink>
          <NavLink :href="route('requests.index')" :active="route().current('requests.index')">الطلبات</NavLink>

          <template v-if="isProvider || isAdmin">
            <NavLink :href="route('services.admin')" :active="route().current('services.admin')">الخدمات</NavLink>
          </template>

          <template v-if="isAdmin">
            <NavLink :href="route('users.index')" :active="route().current('users.index')">المستخدمين</NavLink>
          </template>
        </div>
      </div>

      <!-- Right Side: Conditional -->
      <div class="hidden sm:flex sm:items-center sm:ms-6 ml-4">
        <!-- Notifications (only if logged in) -->
        <template v-if="user">
          <Link :href="route('notifications.index')" class="relative inline-flex items-center px-3 py-2 text-sm font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 transition">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 00-4-5.7V5a2 2 0 10-4 0v.3C7.7 6.2 6 8.4 6 11v3.2c0 .5-.2 1.1-.6 1.4L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            <span class="ml-1">إشعارات</span>
            <span v-if="notifications.length > 0"
              class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold text-red-100 bg-red-600 rounded-full transform translate-x-1/2 -translate-y-1/2">
              {{ notifications.length }}
            </span>
          </Link>
        </template>

        <!-- Authenticated: User Dropdown -->
        <template v-if="user">
          <div class="ms-3 relative">
            <Dropdown align="right" width="48">
              <template #trigger>
                <span class="inline-flex rounded-md">
                  <button type="button"
                    class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 transition">
                    {{ user.role?.name ?? '...' }}
                    <svg class="ms-2 -me-0.5 h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                      <path fill-rule="evenodd"
                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                        clip-rule="evenodd" />
                    </svg>
                  </button>
                </span>
              </template>

              <template #content>
                <DropdownLink :href="route('profile.edit')">الملف الشخصي</DropdownLink>
                <DropdownLink :href="route('logout')" method="post" as="button">تسجيل الخروج</DropdownLink>
              </template>
            </Dropdown>
          </div>
        </template>

        <!-- Guest: Login / Register -->
        <template v-else>
          <div class="space-x-4">
            <Link :href="route('login')" class="text-sm text-gray-700 hover:text-gray-900">تسجيل الدخول</Link>
            <Link :href="route('register')" class="text-sm text-blue-600 hover:underline">إنشاء حساب</Link>
          </div>
        </template>
      </div>

      <!-- Hamburger -->
      <div class="-me-2 flex items-center sm:hidden">
        <button @click="showingNavigationDropdown = !showingNavigationDropdown"
          class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 transition">
          <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path :class="{ hidden: showingNavigationDropdown, 'inline-flex': !showingNavigationDropdown }"
              stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            <path :class="{ hidden: !showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }"
              stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
    </div>
  </div>

  <!-- Mobile Navigation -->
  <div :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }" class="sm:hidden">
    <div class="pt-2 pb-3 space-y-1" v-if="user">
      <ResponsiveNavLink :href="route('dashboard')" :active="route().current('dashboard')">لوحة التحكم</ResponsiveNavLink>
      <ResponsiveNavLink :href="route('requests.index')" :active="route().current('requests.index')">الطلبات</ResponsiveNavLink>

      <template v-if="isProvider || isAdmin">
        <ResponsiveNavLink :href="route('services.admin')" :active="route().current('services.admin')">الخدمات</ResponsiveNavLink>
      </template>

      <template v-if="isAdmin">
        <ResponsiveNavLink :href="route('users.index')" :active="route().current('users.index')">المستخدمين</ResponsiveNavLink>
      </template>

      <ResponsiveNavLink :href="route('notifications.index')" :active="route().current('notifications.index')">
        إشعارات
        <span v-if="notifications.length > 0"
          class="inline-flex items-center justify-center ml-2 px-2 py-1 text-xs font-bold text-red-100 bg-red-600 rounded-full">
          {{ notifications.length }}
        </span>
      </ResponsiveNavLink>
    </div>

    <!-- Mobile Guest -->
    <div v-else class="pt-4 pb-1 border-t border-gray-200 px-4 space-y-2">
      <Link :href="route('login')" class="block text-sm text-gray-700 hover:text-gray-900">تسجيل الدخول</Link>
      <Link :href="route('register')" class="block text-sm text-blue-600 hover:underline">إنشاء حساب</Link>
    </div>

    <!-- Mobile User Info -->
    <div class="pt-4 pb-1 border-t border-gray-200" v-if="user">
      <div class="px-4">
        <div class="font-medium text-base text-gray-800">{{ user.name ?? '...' }}</div>
        <div class="font-medium text-sm text-gray-500">{{ user.email ?? '...' }}</div>
      </div>
      <div class="mt-3 space-y-1">
        <ResponsiveNavLink :href="route('profile.edit')">الملف الشخصي</ResponsiveNavLink>
        <ResponsiveNavLink :href="route('logout')" method="post" as="button">تسجيل الخروج</ResponsiveNavLink>
      </div>
    </div>
  </div>
</nav>


    <!-- Page Content -->
    <main class="min-h-screen">
      <slot />
    </main>
  </div>
</template>
