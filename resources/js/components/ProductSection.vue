<template>
    <section class="my-8">
        <h2 class="text-xl font-bold mb-4">{{ title }}</h2>
        <div class="grid grid-cols-5 gap-4">
            <ProductCard v-for="p in products" :key="p.id" :product="p" />
        </div>
    </section>
</template>

<script setup>
import ProductCard from "./ProductCard.vue";
defineProps(["title", "products"]);
</script>
