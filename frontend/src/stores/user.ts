import { ref } from 'vue'
import { defineStore } from 'pinia'
import type { User } from '@/types/user-types'

export const useUserStore = defineStore('user', () => {
  const user = ref<User>();

  return { user };
})