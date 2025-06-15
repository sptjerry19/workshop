<template>
    <Transition
        enter-active-class="transition duration-200 ease-out"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition duration-200 ease-in"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div
            v-if="isLoading"
            class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center"
        >
            <div class="rounded-lg p-6 flex flex-col items-center">
                <div
                    class="animate-spin rounded-full h-12 w-12 border-b-2 border-green-600"
                ></div>
                <p class="mt-4 text-white">Đang tải...</p>
            </div>
        </div>
    </Transition>
</template>

<script setup>
import { useLoading } from "../composables/useLoading";

const { isLoading } = useLoading();
</script>
