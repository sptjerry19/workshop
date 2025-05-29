<template>
    <div
        v-if="show"
        class="fixed top-4 right-4 bg-white rounded-lg shadow-lg p-4 max-w-sm w-full z-50 transform transition-all duration-300"
        :class="{ 'translate-x-0': show, 'translate-x-full': !show }"
    >
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <i class="fas fa-check-circle text-green-500 text-xl"></i>
            </div>
            <div class="ml-3 w-0 flex-1">
                <p class="text-sm font-medium text-gray-900">
                    {{ notification?.content }}
                </p>
                <p class="mt-1 text-xs text-gray-500">
                    {{ notification?.sent_at }}
                </p>
            </div>
            <div class="ml-4 flex-shrink-0 flex">
                <button
                    @click="close"
                    class="bg-white rounded-md inline-flex text-gray-400 hover:text-gray-500 focus:outline-none"
                >
                    <span class="sr-only">Close</span>
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from "vue";

const show = ref(false);
const notification = ref(null);

onMounted(() => {
    console.log("Notification component mounted");
    // Listen for order completed notifications
    window.Echo.channel("notifications").listen(".order.completed", (data) => {
        console.log("Received notification:", data);
        notification.value = data;
        show.value = true;
        console.log("Show notification:", show.value);

        // Auto hide after 5 seconds
        setTimeout(() => {
            show.value = false;
            console.log("Hide notification");
        }, 10000);
    });
});

onUnmounted(() => {
    window.Echo.leave("notifications");
});

function close() {
    show.value = false;
}
</script>

<style scoped>
.transform {
    transition: transform 0.3s ease-in-out;
}

.translate-x-0 {
    transform: translateX(0);
}

.translate-x-full {
    transform: translateX(100%);
}
</style>
