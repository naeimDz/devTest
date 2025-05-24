import { defineStore } from 'pinia'
import type { User } from '@/types'

interface AuthState {
  user: User | null
}

export const useAuthStore = defineStore('auth', {
  state: (): AuthState => ({
    user: null,
  }),

  actions: {
    setUser(user: User) {
      this.user = user
    },

    clearUser() {
      this.user = null
    },
    
  },

  getters: {
    isAuthenticated: (state) => !!state.user,

    roleName: (state) => state.user?.role?.name ?? null,

    hasRole: (state) => {
      return (roleName: string) => state.user?.role?.name === roleName
    },

    hasPermission: (state) => {
      return (permissionName: string) => {
        return state.user?.role?.permissions?.some(p => p.name === permissionName)
      }
    },

    permissions: (state) => state.user?.role?.permissions ?? [],
  },
})
