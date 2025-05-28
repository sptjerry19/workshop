<template>
    <Banner />
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 pt-60">
        <h1 class="text-3xl font-bold mb-8">Sản phẩm yêu thích</h1>

        <!-- Loading state -->
        <div v-if="loading" class="text-center py-8">
            <div
                class="animate-spin rounded-full h-12 w-12 border-b-2 border-[#d80000] mx-auto"
            ></div>
            <p class="mt-4 text-gray-600">Đang tải...</p>
        </div>

        <!-- Empty state -->
        <div v-else-if="wishlist.length === 0" class="text-center py-8">
            <p class="text-gray-500">Bạn chưa có sản phẩm yêu thích nào.</p>
        </div>

        <!-- Wishlist grid -->
        <div
            v-else
            class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6"
        >
            <div v-for="item in wishlist" :key="item.id" class="relative">
                <ProductCard :product="item.product" />
                <button
                    @click="removeFromWishlist(item.id)"
                    class="absolute top-2 right-2 bg-white rounded-full p-2 shadow-md hover:bg-red-50 transition-colors"
                    title="Xóa khỏi danh sách yêu thích"
                >
                    <i class="fas fa-heart text-[#d80000]"></i>
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import Banner from "../components/Banner.vue";
import ProductCard from "../components/ProductCard.vue";
import axios from "axios";

const wishlist = ref([]);
const loading = ref(true);

async function fetchWishlist() {
    try {
        loading.value = true;
        const response = await axios.get("/api/wishlist");
        if (response.data.success) {
            wishlist.value = response.data.data;
        }
    } catch (error) {
        console.error("Error fetching wishlist:", error);
    } finally {
        loading.value = false;
    }
}

async function removeFromWishlist(id) {
    try {
        const response = await axios.delete(`/api/wishlist/${id}`);
        if (response.data.success) {
            wishlist.value = wishlist.value.filter((item) => item.id !== id);
        }
    } catch (error) {
        console.error("Error removing from wishlist:", error);
        alert("Có lỗi xảy ra khi xóa sản phẩm khỏi danh sách yêu thích");
    }
}

onMounted(() => {
    fetchWishlist();
});
</script>
