import './bootstrap'
import '../css/app.css'

import { createApp, h, DefineComponent } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import { ZiggyVue } from '../../vendor/tightenco/ziggy'
import { createPinia } from 'pinia'
import { useAuthStore } from '@/stores/useAuthStore'

const appName = import.meta.env.VITE_APP_NAME || 'Laravel'

createInertiaApp({
  title: (title) => `${title} - ${appName}`,
  resolve: (name) =>
    resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob<DefineComponent>('./Pages/**/*.vue')),

  setup({ el, App, props, plugin }) {
    const app = createApp({ render: () => h(App, props) })

    const pinia = createPinia()
    app.use(plugin).use(ZiggyVue).use(pinia)

    const authStore = useAuthStore()

    const user = props.initialPage.props.auth?.user || null
    if (user) {
      authStore.setUser(user)
    } else {
      authStore.clearUser()
    }

    app.mount(el)
  },

  progress: {
    color: '#4B5563',
  },
})

