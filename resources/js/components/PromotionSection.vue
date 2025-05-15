<template>
    <section class="my-8 flex">
        <div
            class="w-1/4 bg-gray-100 rounded-lg flex flex-col items-center justify-center p-8 mr-4"
        >
            <h3 class="font-bold text-lg mb-2">Khuyến mãi</h3>
            <img
                src="https://img.freepik.com/free-photo/vegetables-basket_74190-4868.jpg"
                class="h-24 rounded-lg"
            />
        </div>
        <div class="flex-1 grid grid-cols-5 gap-4">
            <ProductCard v-for="p in products" :key="p.id" :product="p" />
        </div>
    </section>
</template>

<script setup>
import ProductCard from "./ProductCard.vue";
defineProps(["products"]);
</script>
