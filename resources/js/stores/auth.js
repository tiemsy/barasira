// resources/js/stores/auth.js
import { defineStore } from 'pinia'
import { api } from '@/lib/api'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    loaded: false,
  }),

  actions: {
    async fetchUser() {
      try {
        const res = await api.get('/me')
        this.user = res.data
      } catch {
        this.user = null
      } finally {
        this.loaded = true
      }
    },

    async logout() {
      await api.post('/logout')
      this.user = null
      this.loaded = true
    },
  },
})
