import { ref } from 'vue'
import { defineStore } from 'pinia'
import type { WP_User } from '@/types/wordpress/user-types'

export const useUserStore = defineStore('user', () => {
  const user = ref<WP_User>();

  return { user };
})