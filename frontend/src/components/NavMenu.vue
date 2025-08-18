<script setup lang="ts">
import { computed } from 'vue';
import { useAuth } from '@/composables/useAuth'
import { useUserStore } from '@/stores/user';

const { isLoggedIn, logOut } = useAuth();
const { baseWebURL } = localized;

const goToLogin = () => {
  window.location.href = 'wp-login.php'
}

const userStore = useUserStore();
const hasNewMsg = computed(() => (userStore.user?.pending_messages_count ?? 0) > 0);

const items = computed(() => {

  const iconClass = 'cursor-pointer hover:text-gray-500';

  let items = [
    {
      icon: 'hugeicons:account-setting-02',
      class: iconClass,
      onSelect: () => {
        window.location.href = `${baseWebURL}/?hp_route=user_edit_settings_page#/`;
      },
    },
    {
      icon: 'hugeicons:logout-02',
      class: iconClass,
      onSelect: () => {
        logOut();
      },
    }
  ]

  if (userStore.user?.is_vendor ?? false) {
    items = [{
      icon: hasNewMsg.value ? 'hugeicons:message-notification-02' : 'hugeicons:message-01',
      class: `cursor-pointer ${hasNewMsg.value ? 'text-red-400 hover:text-red-300' : ''}`,
      onSelect: () => {
        window.location.href = `${baseWebURL}/?hp_route=messages_thread_page#/`;
      },
    },
    {
      icon: 'hugeicons:user-02',
      class: iconClass,
      onSelect: () => {
        window.location.href = `${baseWebURL}/?hp_route=vendor_dashboard_page#/`;
      },
    },
    ...items];
  }

  return items
});
</script>

<template>
  <UButton v-if="!isLoggedIn" class="uppercase font-bold" color="primary" variant="ghost" label="Log in"
    @click="goToLogin" />
  <UNavigationMenu v-else :items="items" />
</template>