<template>
    <button
        @click="toggleDarkMode"
        class="fixed top-0 right-4 p-2 rounded-full bg-gray-200 dark:bg-gray-700 transition-colors duration-200"
    >
        <!-- Sun icon -->
        <svg
            v-if="isDark"
            class="w-6 h-6 text-yellow-500"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
        >
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"
            />
        </svg>
        <!-- Moon icon -->
        <svg
            v-else
            class="w-6 h-6 text-gray-700"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
        >
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"
            />
        </svg>
    </button>
</template>

<script setup>
import { ref, onMounted } from "vue";

const isDark = ref(false);

const toggleDarkMode = () => {
    isDark.value = !isDark.value;
    if (isDark.value) {
        document.documentElement.classList.add("dark");
        localStorage.setItem("darkMode", "true");
    } else {
        document.documentElement.classList.remove("dark");
        localStorage.setItem("darkMode", "false");
    }
};

onMounted(() => {
    // Check for saved dark mode preference
    const savedDarkMode = localStorage.getItem("darkMode");
    if (savedDarkMode === "true") {
        isDark.value = true;
        document.documentElement.classList.add("dark");
    }
});
</script>
