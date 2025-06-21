<template>
    <section class="my-8 flex max-h-120 promotion-section-mobile">
        <div
            class="w-1/4 bg-gray-100 rounded-lg flex flex-col items-center justify-center p-8 mr-4 promo-block"
        >
            <h3 class="font-bold text-lg mb-2">Khuyến mãi</h3>
            <img
                :src="baseImageUrl + 'discount.png'"
                class="h-30 max-h-40 rounded-lg"
            />
        </div>
        <div class="flex-1 grid grid-cols-5 gap-4 product-grid">
            <ProductCard v-for="p in products" :key="p.id" :product="p" />
        </div>
    </section>
</template>

<script setup>
import ProductCard from "./ProductCard.vue";
const baseImageUrl = import.meta.env.VITE_RESPONE_IMAGE_URL;

defineProps(["products"]);
</script>

<style>
@media (max-width: 640px) {
    .promotion-section-mobile {
        flex-direction: column !important;
        max-height: none !important;
        gap: 0.5rem !important;
    }
    .promotion-section-mobile .promo-block {
        width: 100% !important;
        margin-right: 0 !important;
        margin-bottom: 1rem !important;
        padding: 1rem !important;
    }
    .promotion-section-mobile .product-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        gap: 0.5rem !important;
    }
}
</style>
