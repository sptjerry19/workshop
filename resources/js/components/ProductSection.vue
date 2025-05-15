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
        <div class="flex flex-wrap gap-6">
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
            <div class="flex-1 grid grid-cols-5 gap-x-6 gap-y-8">
                <!-- Products -->
                <ProductCard v-for="p in products" :key="p.id" :product="p" />
            </div>
        </div>
    </div>
</template>

<script setup>
import ProductCard from "./ProductCard.vue";
defineProps(["title", "products"]);
</script>
