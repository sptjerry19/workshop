<template>
    <button
        @click="toggleDarkMode"
        class="fixed top-4 right-4 z-50 p-3 rounded-full shadow-lg hover:shadow-xl transition-all duration-300 border"
        :class="
            isDark ? 'bg-gray-800 border-gray-600' : 'bg-white border-gray-200'
        "
        :title="isDark ? 'Chuyển sang chế độ sáng' : 'Chuyển sang chế độ tối'"
    >
        <!-- Sun icon for light mode -->
        <svg
            v-if="!isDark"
            class="w-6 h-6 text-yellow-500 transition-all duration-300"
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

        <!-- Moon icon for dark mode -->
        <svg
            v-else
            class="w-6 h-6 text-blue-400 transition-all duration-300"
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
import { useDarkMode } from "../composables/useDarkMode";

const { isDark, toggleDarkMode } = useDarkMode();
</script>
