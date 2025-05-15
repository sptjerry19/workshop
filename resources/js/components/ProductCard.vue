<template>
    <div class="bg-white rounded-lg shadow p-4 flex flex-col items-center">
        <img :src="product.image" class="h-24 mb-2 rounded" />
        <h3 class="font-semibold text-center">{{ product.name }}</h3>
        <div class="text-green-600 font-bold mt-2">
            {{ product.price.toLocaleString() }}đ
        </div>
        <button class="mt-2 bg-green-600 text-white px-3 py-1 rounded">
            Thêm giỏ
        </button>
    </div>
</template>

<script setup>
defineProps(["product"]);
</script>
