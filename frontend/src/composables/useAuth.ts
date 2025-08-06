import { computed, onMounted } from 'vue';
import { useAPI } from '@/composables/useAPI';
import { useUserStore } from '@/stores/user';
import type { WP_User } from '@/types/wordpress/user-types';

export function useAuth() {
    const { get } = useAPI();
    const userStore = useUserStore();

    const user = computed((): WP_User | undefined => userStore.user);
    const isLoggedIn = computed((): boolean => user.value !== undefined);

    const getCurrentUser = async () => {
        const data = await get('/user');

        userStore.user = data;
    }

    const logOut = () => {
        userStore.user = undefined;
        window.location.href = '/#/';
    }

    onMounted(() => {
        getCurrentUser();
    });

    return { user, isLoggedIn, logOut };
}