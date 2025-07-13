import { ref, watch, onMounted } from "vue";

export function useDarkMode() {
    const isDark = ref(false);

    // Lấy giá trị từ localStorage khi khởi tạo
    onMounted(() => {
        const savedTheme = localStorage.getItem("darkMode");
        if (savedTheme !== null) {
            isDark.value = JSON.parse(savedTheme);
        } else {
            // Kiểm tra system preference nếu chưa có setting
            const prefersDark = window.matchMedia(
                "(prefers-color-scheme: dark)"
            ).matches;
            isDark.value = prefersDark;
        }
    });

    // Theo dõi thay đổi và lưu vào localStorage
    watch(isDark, (newValue) => {
        localStorage.setItem("darkMode", JSON.stringify(newValue));
    });

    // Toggle dark mode
    const toggleDarkMode = () => {
        isDark.value = !isDark.value;
        window.location.reload();
    };

    return {
        isDark,
        toggleDarkMode,
    };
}
