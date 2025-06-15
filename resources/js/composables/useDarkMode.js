import { ref, onMounted } from "vue";

export function useDarkMode() {
    const isDark = ref(false);

    const toggleDarkMode = () => {
        if (isDark.value) {
            // Chuyển sang light mode
            localStorage.theme = "light";
            document.documentElement.classList.remove("dark");
        } else {
            // Chuyển sang dark mode
            localStorage.theme = "dark";
            document.documentElement.classList.add("dark");
        }
        isDark.value = !isDark.value;
    };

    onMounted(() => {
        // Kiểm tra theme khi component được mount
        isDark.value = document.documentElement.classList.contains("dark");
    });

    return {
        isDark,
        toggleDarkMode,
    };
}
