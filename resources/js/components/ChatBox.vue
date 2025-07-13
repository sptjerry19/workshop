<template>
    <div class="fixed bottom-4 right-4 z-50">
        <!-- Chat Button -->
        <button
            v-if="!isOpen"
            @click="toggleChat"
            class="bg-[#eeb600] text-white p-4 rounded-full shadow-lg hover:bg-[#dacb77] transition-all duration-300"
        >
            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-6 w-6"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"
                />
            </svg>
        </button>

        <!-- Chat Window -->
        <div
            v-if="isOpen"
            class="rounded-lg shadow-xl w-96 h-[500px] flex flex-col transition-colors duration-300"
            :class="isDark ? 'bg-gray-800' : 'bg-white'"
        >
            <!-- Header -->
            <div
                class="bg-[#eeb600] text-white p-4 rounded-t-lg flex justify-between items-center"
            >
                <h3 class="font-semibold">Chat với chúng tôi</h3>
                <button
                    @click="toggleChat"
                    class="text-white hover:text-gray-200"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-6 w-6"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"
                        />
                    </svg>
                </button>
            </div>

            <!-- Messages -->
            <div
                ref="messagesContainer"
                class="flex-1 overflow-y-auto p-4 space-y-4"
                :class="isDark ? 'bg-gray-800' : 'bg-white'"
                @scroll="handleScroll"
            >
                <div
                    v-if="loading"
                    class="text-center"
                    :class="isDark ? 'text-gray-400' : 'text-gray-500'"
                >
                    Đang tải tin nhắn...
                </div>
                <template v-else>
                    <div
                        v-for="message in messages"
                        :key="message.id"
                        :class="[
                            'flex',
                            message.from_sender_id === userId
                                ? 'justify-end'
                                : 'justify-start',
                        ]"
                    >
                        <div
                            :class="[
                                'max-w-[80%] rounded-lg p-3',
                                message.from_sender_id === userId
                                    ? 'bg-[#eeb600] text-white'
                                    : isDark
                                    ? 'bg-gray-700 text-gray-200'
                                    : 'bg-gray-100 text-gray-800',
                            ]"
                        >
                            <p class="text-sm">{{ message.content }}</p>
                            <span class="text-xs opacity-75 mt-1 block">
                                {{ formatTime(message.created_at) }}
                            </span>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Input -->
            <div
                class="p-4 border-t"
                :class="
                    isDark
                        ? 'border-gray-700 bg-gray-800'
                        : 'border-gray-200 bg-white'
                "
            >
                <form @submit.prevent="sendMessage" class="flex gap-2">
                    <input
                        v-model="newMessage"
                        type="text"
                        placeholder="Nhập tin nhắn..."
                        class="flex-1 border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-[#eeb600] focus:border-transparent transition-colors duration-200"
                        :class="
                            isDark
                                ? 'border-gray-600 bg-gray-700 text-white placeholder-gray-400'
                                : 'border-gray-300 bg-white text-gray-900 placeholder-gray-500'
                        "
                        :disabled="!userId"
                    />
                    <button
                        type="submit"
                        class="bg-[#eeb600] text-white px-4 py-2 rounded-lg hover:bg-[#dacb77] transition-colors disabled:opacity-50"
                        :disabled="!newMessage.trim() || !userId"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"
                            />
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, nextTick, watch } from "vue";
import axios from "axios";
import Echo from "laravel-echo";
import Pusher from "pusher-js";
import { useDarkMode } from "../composables/useDarkMode.js";

const { isDark } = useDarkMode();
const isOpen = ref(false);
const messages = ref([]);
const newMessage = ref("");
const loading = ref(true);
const messagesContainer = ref(null);
const userId = ref(null);
const page = ref(1);
const hasMore = ref(true);

// Khởi tạo userId từ localStorage hoặc tạo mới
const initUserId = () => {
    let id = localStorage.getItem("chat_user_id");
    if (!id) {
        id = "user_" + Math.random().toString(36).substr(2, 9);
        localStorage.setItem("chat_user_id", id);
    }
    userId.value = id;
};

// Khởi tạo Pusher
const initPusher = () => {
    window.Pusher = Pusher;
    window.Echo = new Echo({
        broadcaster: "pusher",
        key: import.meta.env.VITE_PUSHER_APP_KEY,
        cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
        forceTLS: true,
    });

    // Lắng nghe tin nhắn mới
    window.Echo.channel("chat").listen("NewMessage", (e) => {
        // Kiểm tra xem tin nhắn đã tồn tại chưa
        const messageExists = messages.value.some(
            (msg) => msg.id === e.message.id
        );
        if (!messageExists) {
            messages.value.push(e.message);
            scrollToBottom();
        }
    });
};

// Load tin nhắn
const loadMessages = async () => {
    try {
        const response = await axios.get(
            `/api/messages?page=${page.value}&sender_id=${localStorage.getItem(
                "chat_user_id"
            )}`
        );
        const newMessages = response.data.data;
        messages.value = [...messages.value, ...newMessages];
        hasMore.value = response.data.current_page < response.data.last_page;
    } catch (error) {
        console.error("Error loading messages:", error);
    } finally {
        loading.value = false;
    }
};

// Gửi tin nhắn
const sendMessage = async () => {
    if (!newMessage.value.trim() || !userId.value) return;

    try {
        const response = await axios.post("/api/messages", {
            content: newMessage.value,
            sender_id: userId.value,
        });
        // Không thêm tin nhắn vào đây vì sẽ được thêm qua Pusher event
        newMessage.value = "";
    } catch (error) {
        console.error("Error sending message:", error);
    }
};

// Scroll to bottom
const scrollToBottom = async () => {
    await nextTick();
    if (messagesContainer.value) {
        messagesContainer.value.scrollTop =
            messagesContainer.value.scrollHeight;
    }
};

// Handle scroll for pagination
const handleScroll = (e) => {
    const { scrollTop } = e.target;
    if (scrollTop === 0 && hasMore.value && !loading.value) {
        page.value++;
        loadMessages();
    }
};

// Toggle chat window
const toggleChat = () => {
    isOpen.value = !isOpen.value;
    if (isOpen.value) {
        scrollToBottom();
    }
};

// Format time
const formatTime = (timestamp) => {
    const date = new Date(timestamp);
    return date.toLocaleTimeString("vi-VN", {
        hour: "2-digit",
        minute: "2-digit",
    });
};

// Watch messages for auto-scroll
watch(messages, () => {
    if (isOpen.value) {
        scrollToBottom();
    }
});

onMounted(() => {
    initUserId();
    initPusher();
    loadMessages();
});
</script>

<style scoped>
.overflow-y-auto {
    scrollbar-width: thin;
    scrollbar-color: #eeb600 #f3f4f6;
}

.overflow-y-auto::-webkit-scrollbar {
    width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
    background: #f3f4f6;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
    background-color: #eeb600;
    border-radius: 3px;
}
</style>
