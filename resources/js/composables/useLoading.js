import { ref } from "vue";

const isLoading = ref(false);
const loadingCount = ref(0);

export function useLoading() {
    const startLoading = () => {
        loadingCount.value++;
        isLoading.value = true;
    };

    const stopLoading = () => {
        loadingCount.value--;
        if (loadingCount.value <= 0) {
            loadingCount.value = 0;
            isLoading.value = false;
        }
    };

    return {
        isLoading,
        startLoading,
        stopLoading,
    };
}
