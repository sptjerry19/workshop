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
            class="bg-white rounded-lg shadow-xl w-[800px] h-[600px] flex"
        >
            <!-- User List -->
            <div class="w-1/3 border-r flex flex-col">
                <div class="p-4 bg-gray-50 border-b">
                    <h3 class="font-semibold text-gray-700">
                        Danh sách người dùng
                    </h3>
                </div>
                <div class="flex-1 overflow-y-auto">
                    <div
                        v-for="user in users"
                        :key="user.from_sender_id"
                        @click="selectUser(user)"
                        :class="[
                            'p-4 border-b cursor-pointer hover:bg-gray-50 transition-colors',
                            selectedUser?.from_sender_id === user.from_sender_id
                                ? 'bg-gray-100'
                                : '',
                        ]"
                    >
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-medium text-gray-800">
                                    User {{ user.from_sender_id }}
                                </p>
                                <p class="text-sm text-gray-500 truncate">
                                    {{ user.last_message }}
                                </p>
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ formatTime(user.last_message_time) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Chat Area -->
            <div class="w-2/3 flex flex-col">
                <!-- Header -->
                <div
                    class="bg-[#eeb600] text-white p-4 flex justify-between items-center"
                >
                    <h3 class="font-semibold">
                        {{
                            selectedUser
                                ? `Chat với User ${selectedUser.from_sender_id}`
                                : "Chọn người dùng để chat"
                        }}
                    </h3>
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
                    @scroll="handleScroll"
                >
                    <div v-if="loading" class="text-center text-gray-500">
                        Đang tải tin nhắn...
                    </div>
                    <template v-else>
                        <div
                            v-for="message in filteredMessages"
                            :key="message.id"
                            :class="[
                                'flex',
                                message.from_sender_id === 'admin'
                                    ? 'justify-end'
                                    : 'justify-start',
                            ]"
                        >
                            <div
                                :class="[
                                    'max-w-[80%] rounded-lg p-3',
                                    message.from_sender_id === 'admin'
                                        ? 'bg-[#eeb600] text-white'
                                        : 'bg-gray-100 text-gray-800',
                                ]"
                            >
                                <p class="text-sm">{{ message.content }}</p>
                                <div class="text-xs opacity-75 mt-1">
                                    {{ formatTime(message.created_at) }}
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Input -->
                <div class="p-4 border-t">
                    <form @submit.prevent="sendReply" class="flex gap-2">
                        <input
                            v-model="newMessage"
                            type="text"
                            placeholder="Nhập tin nhắn..."
                            class="flex-1 border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-[#eeb600] focus:border-transparent"
                            :disabled="!selectedUser"
                        />
                        <button
                            type="submit"
                            class="bg-[#eeb600] text-white px-4 py-2 rounded-lg hover:bg-[#dacb77] transition-colors disabled:opacity-50"
                            :disabled="!newMessage.trim() || !selectedUser"
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
    </div>
</template>

<script setup>
import { ref, onMounted, nextTick, watch, computed } from "vue";
import axios from "axios";
import Echo from "laravel-echo";
import Pusher from "pusher-js";

const isOpen = ref(false);
const messages = ref([]);
const users = ref([]);
const newMessage = ref("");
const loading = ref(true);
const messagesContainer = ref(null);
const page = ref(1);
const hasMore = ref(true);
const selectedUser = ref(null);

// Lọc tin nhắn theo user được chọn
const filteredMessages = computed(() => {
    if (!selectedUser.value) return [];
    return messages.value.filter(
        (message) =>
            (message.from_sender_id === selectedUser.value.from_sender_id &&
                message.to_sender_id === "admin") ||
            (message.from_sender_id === "admin" &&
                message.to_sender_id === selectedUser.value.from_sender_id)
    );
});

// Load danh sách users
const loadUsers = async () => {
    try {
        const response = await axios.get("/api/messages/users");
        users.value = response.data;
    } catch (error) {
        console.error("Error loading users:", error);
    }
};

// Load tin nhắn của user được chọn
const loadMessages = async () => {
    if (!selectedUser.value) return;

    try {
        const response = await axios.get(
            `/api/messages?page=${page.value}&sender_id=${selectedUser.value.from_sender_id}`
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

// Chọn user để chat
const selectUser = async (user) => {
    selectedUser.value = user;
    messages.value = [];
    page.value = 1;
    loading.value = true;
    await loadMessages();
    scrollToBottom();
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
        const messageExists = messages.value.some(
            (msg) => msg.id === e.message.id
        );
        if (!messageExists) {
            messages.value.push(e.message);
            // Cập nhật danh sách users nếu có tin nhắn mới
            if (!e.message.is_admin) {
                loadUsers();
            }
            if (
                selectedUser.value?.from_sender_id === e.message.from_sender_id
            ) {
                scrollToBottom();
            }
        }
    });
};

// Gửi tin nhắn trả lời
const sendReply = async () => {
    if (!newMessage.value.trim() || !selectedUser.value) return;

    try {
        const response = await axios.post(
            `/api/messages/${selectedUser.value.from_sender_id}/reply`,
            {
                content: newMessage.value,
            }
        );
        newMessage.value = "";
        // Cập nhật danh sách users sau khi gửi tin nhắn
        loadUsers();
    } catch (error) {
        console.error("Error sending reply:", error);
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
    if (isOpen.value && selectedUser.value) {
        scrollToBottom();
    }
});

onMounted(() => {
    initPusher();
    loadUsers();
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
