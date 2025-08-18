import { computed, onMounted } from 'vue';
import { useAPI } from '@/composables/useAPI';
import { useUserStore } from '@/stores/user';
import type { User } from '@/types/user-types';

export function useAuth() {
    const { get } = useAPI();
    const userStore = useUserStore();

    const user = computed((): User | undefined | null => userStore.user);
    const isLoggedIn = computed((): boolean => !!user.value);

    const getCurrentUser = async () => {
        const data = await get('/user');

        console.log("User: ", data);

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