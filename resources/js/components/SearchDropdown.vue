<template>
    <div class="relative flex flex-1">
        <input
            v-model="searchQuery"
            @input="handleSearch"
            @focus="showDropdown = true"
            @blur="handleBlur"
            aria-label="Search"
            class="flex-grow border border-gray-300 rounded-l-md px-4 py-2 text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-yellow-600 focus:border-yellow-600"
            placeholder="Đang tìm kiếm....."
            type="search"
        />
        <button
            class="bg-[#eeb600] text-white px-5 py-2 rounded-r-md text-xs font-semibold tracking-wider flex items-center gap-2 hover:bg-[#dacb77] transition"
            type="submit"
        >
            TÌM KIẾM
            <i class="fas fa-search"></i>
        </button>

        <!-- Dropdown -->
        <div
            v-if="showDropdown && searchResults.length > 0"
            class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-md shadow-lg max-h-96 overflow-y-auto top-full"
            @scroll="handleScroll"
        >
            <div
                v-for="product in searchResults"
                :key="product.id"
                class="p-2 hover:bg-gray-50"
            >
                <router-link
                    :to="'/products/' + product.id"
                    class="flex items-center gap-3"
                    @click="showDropdown = false"
                >
                    <img
                        :src="product.image"
                        :alt="product.name"
                        class="w-12 h-12 object-cover rounded"
                    />
                    <div>
                        <h4 class="text-sm font-medium text-gray-900">
                            {{ product.name }}
                        </h4>
                        <p class="text-sm text-gray-500">
                            {{ formatPrice(product.price) }}đ
                        </p>
                    </div>
                </router-link>
            </div>
            <div v-if="loading" class="p-4 text-center text-gray-500">
                Đang tải...
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";
import debounce from "lodash/debounce";

const searchQuery = ref("");
const searchResults = ref([]);
const showDropdown = ref(false);
const loading = ref(false);
const currentPage = ref(1);
const hasMore = ref(true);
const baseImageUrl = import.meta.env.VITE_RESPONE_IMAGE_URL;

const handleSearch = debounce(async () => {
    if (!searchQuery.value) {
        searchResults.value = [];
        return;
    }

    loading.value = true;
    currentPage.value = 1;
    try {
        const response = await axios.get(
            `/api/products?q=${searchQuery.value}&page=1`
        );
        searchResults.value = response.data.data;
        hasMore.value = response.data.current_page < response.data.last_page;
    } catch (error) {
        console.error("Error searching products:", error);
    } finally {
        loading.value = false;
    }
}, 300);

const loadMore = async () => {
    if (loading.value || !hasMore.value) return;

    loading.value = true;
    currentPage.value++;
    try {
        const response = await axios.get(
            `/api/products?q=${searchQuery.value}&page=${currentPage.value}`
        );
        searchResults.value = [...searchResults.value, ...response.data.data];
        hasMore.value = response.data.current_page < response.data.last_page;
    } catch (error) {
        console.error("Error loading more products:", error);
    } finally {
        loading.value = false;
    }
};

const handleScroll = (e) => {
    const { scrollTop, scrollHeight, clientHeight } = e.target;
    if (scrollHeight - scrollTop <= clientHeight * 1.5) {
        loadMore();
    }
};

const handleBlur = () => {
    // Delay hiding dropdown to allow for click events
    setTimeout(() => {
        showDropdown.value = false;
    }, 200);
};

const formatPrice = (price) => {
    return new Intl.NumberFormat("vi-VN").format(price);
};
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
