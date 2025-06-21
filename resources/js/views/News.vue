<template>
    <Banner />
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 pt-60">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">
                Tin Tức & Blog
            </h1>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                Cập nhật những tin tức mới nhất về sức khỏe, dinh dưỡng và các
                sản phẩm của chúng tôi
            </p>
        </div>

        <!-- Featured Post -->
        <div class="mb-12" v-if="news.length > 0">
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <img
                    :src="news[0].image"
                    :alt="news[0].title"
                    class="w-full h-96 object-cover"
                />
                <div class="p-8">
                    <div class="flex items-center text-sm text-gray-500 mb-4">
                        <span>{{ formatDate(news[0].created_at) }}</span>
                        <span class="mx-2">•</span>
                        <span>{{
                            news[0].status === "published"
                                ? "Đã xuất bản"
                                : "Bản nháp"
                        }}</span>
                    </div>
                    <h2 class="text-3xl font-bold text-gray-900 mb-4">
                        {{ news[0].title }}
                    </h2>
                    <p class="text-gray-600 mb-6">
                        {{ news[0].summary }}
                    </p>
                    <router-link
                        :to="'/news/' + news[0].id"
                        class="inline-flex items-center text-green-600 hover:text-green-700"
                    >
                        Đọc thêm
                        <svg
                            class="ml-2 w-4 h-4"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 5l7 7-7 7"
                            />
                        </svg>
                    </router-link>
                </div>
            </div>
        </div>

        <!-- Recent Posts Grid -->
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <div
                v-for="article in news.slice(1)"
                :key="article.id"
                class="bg-white rounded-lg shadow-md overflow-hidden"
            >
                <img
                    :src="article.image"
                    :alt="article.title"
                    class="w-full h-48 object-cover"
                />
                <div class="p-6">
                    <div class="flex items-center text-sm text-gray-500 mb-4">
                        <span>{{ formatDate(article.created_at) }}</span>
                        <span class="mx-2">•</span>
                        <span>{{
                            article.status === "published"
                                ? "Đã xuất bản"
                                : "Bản nháp"
                        }}</span>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">
                        {{ article.title }}
                    </h3>
                    <p class="text-gray-600 mb-4">
                        {{ article.summary }}
                    </p>
                    <router-link
                        :to="'/news/' + article.id"
                        class="text-green-600 hover:text-green-700 font-medium"
                    >
                        Đọc thêm →
                    </router-link>
                </div>
            </div>
        </div>

        <!-- Newsletter -->
        <div class="mt-16 bg-green-50 rounded-lg p-8">
            <div class="max-w-2xl mx-auto text-center">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">
                    Đăng ký nhận tin
                </h2>
                <p class="text-gray-600 mb-6">
                    Nhận thông tin về các bài viết mới nhất và khuyến mãi đặc
                    biệt
                </p>
                <form class="flex gap-4 max-w-md mx-auto">
                    <input
                        type="email"
                        placeholder="Email của bạn"
                        class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                    />
                    <button
                        type="submit"
                        class="bg-green-600 text-white px-6 py-2 rounded-md hover:bg-green-700 transition-colors"
                    >
                        Đăng ký
                    </button>
                </form>
            </div>
        </div>
    </div>
    <ChatBox />
</template>

<script setup>
import { ref, onMounted } from "vue";
import Banner from "../components/Banner.vue";
import ChatBox from "../components/ChatBox.vue";
import api from "../api.js";

const news = ref([]);

onMounted(async () => {
    try {
        const response = await api.get("/news");
        if (response.data.success) {
            news.value = response.data.data;
        }
    } catch (error) {
        console.error("Failed to fetch news:", error);
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
