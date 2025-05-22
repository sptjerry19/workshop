<template>
    <Banner />
    <div class="bg-white py-6">
        <div class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8 pt-60">
            <!-- Breadcrumbs -->
            <nav class="text-sm leading-loose mb-4">
                <router-link to="/" class="text-gray-600 hover:text-gray-800"
                    >Trang chủ</router-link
                >
                <span v-if="product?.category_name" class="mx-2">></span>
                <router-link
                    v-if="product?.category_name"
                    :to="`/products`"
                    class="text-gray-600 hover:text-gray-800"
                    >{{ product.category_name }}</router-link
                >
                <span v-if="product?.name" class="mx-2">></span>
                <span v-if="product?.name" class="text-gray-900">{{
                    product.name
                }}</span>
            </nav>

            <div v-if="loading" class="text-center">Đang tải...</div>
            <div v-else-if="error" class="text-center text-red-500">
                {{ error }}
            </div>
            <div v-else-if="product" class="lg:flex lg:gap-8">
                <!-- Product Image -->
                <div class="lg:flex-1 flex justify-center">
                    <img
                        :src="product.image"
                        :alt="product.name"
                        class="max-w-full h-auto object-cover rounded-md"
                    />
                </div>

                <!-- Product Details -->
                <div
                    class="lg:flex-1 mt-8 lg:mt-0 p-6 bg-gray-50 rounded-md shadow-sm"
                >
                    <div class="flex items-center mb-4">
                        <span class="w-4 h-5 bg-red-600 mr-1"></span>
                        <span class="w-4 h-5 bg-red-600 mr-1"></span>
                        <span class="w-4 h-5 bg-red-600"></span>
                    </div>
                    <h1 class="text-2xl font-extrabold text-gray-900 mb-4">
                        {{ product.name }}
                    </h1>

                    <!-- Product Description/Details -->
                    <p class="text-gray-700 text-sm mb-6">
                        {{ product.description }}
                    </p>

                    <!-- Price and Add button -->
                    <div class="flex items-center justify-between">
                        <span class="text-xl font-bold text-gray-900">{{
                            formatPrice(product.price)
                        }}</span>
                        <button
                            class="px-8 py-3 bg-gray-400 text-white text-lg font-semibold rounded-full hover:bg-gray-500 transition"
                        >
                            Thêm
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Banner from "../../components/Banner.vue";
import { ref, onMounted, watch } from "vue";
import { useRoute } from "vue-router";
import axios from "axios";

export default {
    name: "ProductDetail",
    components: {
        Banner,
    },
    setup() {
        const route = useRoute();
        const product = ref(null);
        const loading = ref(true);
        const error = ref(null);

        const fetchProduct = async (id) => {
            loading.value = true;
            error.value = null;
            try {
                // Assuming the API returns a single product object or an array with one object
                const response = await axios.get(`/api/products/${id}`);
                // Adjust based on actual API response structure
                if (
                    Array.isArray(response.data.data) &&
                    response.data.data.length > 0
                ) {
                    product.value = response.data.data[0];
                } else if (response.data.data) {
                    product.value = response.data.data;
                } else {
                    throw new Error("Product not found");
                }
            } catch (err) {
                error.value = "Không thể tải thông tin sản phẩm.";
                console.error(err);
            } finally {
                loading.value = false;
            }
        };

        const formatPrice = (price) => {
            if (!price) return "0";
            return parseFloat(price).toLocaleString("vi-VN") + " ₫";
        };

        onMounted(() => {
            fetchProduct(route.params.id);
        });

        // Watch for route changes (e.g., navigating between product detail pages)
        watch(
            () => route.params.id,
            (newId) => {
                if (newId) {
                    fetchProduct(newId);
                }
            }
        );

        return {
            product,
            loading,
            error,
            formatPrice,
        };
    },
};
</script>
