<template>
    <!-- <section class="my-8">
        <h2 class="text-xl font-bold mb-4">{{ title }}</h2>
        <div class="grid grid-cols-5 gap-4">
            <ProductCard v-for="p in products" :key="p.id" :product="p" />
        </div>
    </section> -->
    <div class="relative max-w-[1200px] mx-auto px-4 py-8 mt-20">
        <h2 class="text-lg font-semibold">{{ title }}</h2>
        <!-- Tabs on top right -->
        <div
            class="flex absolute top-3 right-8 justify-end gap-8 mt-6 text-sm text-gray-400 font-semibold max-w-[1200px] mx-auto"
        >
            <div
                class="cursor-pointer hover:font-semibold hover:underline hover:underline-offset-4 hover:decoration-2 hover:decoration-black transition-all duration-200"
            >
                Mới nhất
            </div>
            <div
                class="cursor-pointer hover:font-semibold hover:underline hover:underline-offset-4 hover:decoration-2 hover:decoration-black transition-all duration-200"
            >
                Bán chạy
            </div>
            <div
                class="cursor-pointer hover:font-semibold hover:underline hover:underline-offset-4 hover:decoration-2 hover:decoration-black transition-all duration-200"
            >
                Khuyến mãi
            </div>
        </div>
        <div class="flex gap-6">
            <!-- Left big promo block -->
            <div
                class="flex flex-col justify-between bg-[#6B8443] text-white rounded-md p-6 w-[280px] h-[480px] shrink-0"
            >
                <div class="text-xl leading-snug font-semibold">
                    Trái cây
                    <br />
                    tự nhiên
                    <br />
                    mỗi ngày
                </div>
                <button
                    class="bg-white text-black text-xs font-bold rounded px-3 py-1 w-max mt-4"
                >
                    MUA NGAY
                </button>
                <img
                    alt="Fresh vegetables including carrots, green onions, and leafy greens in a mesh bag on a green background"
                    class="mt-6 rounded-md"
                    height="280"
                    src="https://storage.googleapis.com/a1aa/image/ecd8100f-a632-4940-d857-853d7e490a35.jpg"
                    width="280"
                />
            </div>
            <!-- Right product grid -->
            <div class="flex-1 overflow-hidden">
                <div
                    v-if="!products || products.length === 0"
                    class="text-center py-8"
                >
                    Không có sản phẩm nào
                </div>
                <div v-else>
                    <!-- First two rows -->
                    <div class="grid grid-cols-5 gap-6 mb-6">
                        <ProductCard
                            v-for="p in displayedProducts"
                            :key="p.id"
                            :product="p"
                        />
                    </div>

                    <!-- View more button -->
                    <div class="flex justify-center mt-4">
                        <router-link
                            to="/products"
                            class="inline-flex items-center px-6 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        >
                            Xem thêm
                            <svg
                                class="ml-2 -mr-1 w-4 h-4"
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

                    <!-- Slide controls -->
                    <div class="flex justify-center gap-4 mt-4">
                        <button
                            @click="prevSlide"
                            class="p-2 rounded-full bg-gray-100 hover:bg-gray-200 disabled:opacity-50"
                            :disabled="currentSlide === 0"
                        >
                            <svg
                                class="w-6 h-6"
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
                        </button>
                        <button
                            @click="nextSlide"
                            class="p-2 rounded-full bg-gray-100 hover:bg-gray-200 disabled:opacity-50"
                            :disabled="currentSlide >= maxSlides"
                        >
                            <svg
                                class="w-6 h-6"
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
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from "vue";
import ProductCard from "./ProductCard.vue";

const props = defineProps(["title", "products"]);
const currentSlide = ref(0);
const productsPerPage = 10; // 5 products per row * 2 rows

const maxSlides = computed(() => {
    if (!props.products) return 0;
    return Math.ceil(props.products.length / productsPerPage) - 1;
});

const displayedProducts = computed(() => {
    if (!props.products) return [];
    const start = currentSlide.value * productsPerPage;
    return props.products.slice(start, start + productsPerPage);
});

const nextSlide = () => {
    if (currentSlide.value < maxSlides.value) {
        currentSlide.value++;
    }
};

const prevSlide = () => {
    if (currentSlide.value > 0) {
        currentSlide.value--;
    }
};

// Debug
console.log("ProductSection props:", props);
console.log("Products in ProductSection:", props.products);
</script>

<style>
/* Remove any unnecessary styles */
</style>
