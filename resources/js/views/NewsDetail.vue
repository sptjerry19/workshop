<template>
    <Banner />
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 pt-60">
        <!-- Back Button -->
        <div class="mb-8">
            <router-link
                to="/news"
                class="inline-flex items-center text-green-600 hover:text-green-700"
            >
                <svg
                    class="mr-2 w-4 h-4"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M15 19l-7-7 7-7"
                    />
                </svg>
                Quay lại tin tức
            </router-link>
        </div>

        <!-- Article Content -->
        <div
            v-if="article"
            class="bg-white rounded-lg shadow-lg overflow-hidden"
        >
            <img
                :src="article.image"
                :alt="article.title"
                class="w-full h-96 object-cover"
            />
            <div class="p-8">
                <div class="flex items-center text-sm text-gray-500 mb-4">
                    <span>{{ formatDate(article.created_at) }}</span>
                    <span class="mx-2">•</span>
                    <span>{{
                        article.status === "published"
                            ? "Đã xuất bản"
                            : "Bản nháp"
                    }}</span>
                </div>
                <h1 class="text-4xl font-bold text-gray-900 mb-6">
                    {{ article.title }}
                </h1>
                <div class="prose max-w-none">
                    <p class="text-xl text-gray-600 mb-8">
                        {{ article.summary }}
                    </p>
                    <div
                        class="text-gray-700 prose prose-sm max-w-none"
                        v-html="article.content"
                    ></div>
                </div>
            </div>
        </div>

        <!-- Loading State -->
        <div v-else-if="loading" class="text-center py-12">
            <div
                class="animate-spin rounded-full h-12 w-12 border-b-2 border-green-600 mx-auto"
            ></div>
            <p class="mt-4 text-gray-600">Đang tải bài viết...</p>
        </div>

        <!-- Error State -->
        <div v-else class="text-center py-12">
            <p class="text-red-600">Không tìm thấy bài viết</p>
        </div>
    </div>
    <ChatBox />
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRoute } from "vue-router";
import Banner from "../components/Banner.vue";
import ChatBox from "../components/ChatBox.vue";
import api from "../api.js";

const route = useRoute();
const article = ref(null);
const loading = ref(true);

onMounted(async () => {
    try {
        const response = await api.get(`/news/${route.params.id}`);
        if (response.data.success) {
            article.value = response.data.data;
        }
    } catch (error) {
        console.error("Failed to fetch article:", error);
    } finally {
        loading.value = false;
    }
});

function formatDate(date) {
    return new Date(date).toLocaleDateString("vi-VN", {
        year: "numeric",
        month: "long",
        day: "numeric",
    });
}
</script>

<style>
.prose {
    max-width: 100%;
    overflow-wrap: break-word;
    word-wrap: break-word;
    word-break: break-word;
}

.prose p {
    margin-bottom: 1em;
    white-space: pre-wrap;
}

.prose img {
    max-width: 100%;
    height: auto;
}

.prose table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 1em;
}

.prose td,
.prose th {
    border: 1px solid #e5e7eb;
    padding: 0.5em;
}

.prose pre {
    white-space: pre-wrap;
    word-wrap: break-word;
    background-color: #f3f4f6;
    padding: 1em;
    border-radius: 0.375rem;
    margin-bottom: 1em;
}
</style>
