<template>
    <div class="flex justify-between my-6">
        <div
            v-for="cat in categories"
            :key="cat.name"
            class="flex flex-col items-center"
        >
            <img :src="cat.icon" class="h-12 mb-2 rounded-full" />
            <span class="text-sm font-semibold">{{ cat.name }}</span>
        </div>
    </div>
</template>

<script setup>
const categories = [
    {
        name: "Tươi sạch",
        icon: "https://img.freepik.com/free-photo/lettuce_144627-15414.jpg",
    },
    {
        name: "Hữu cơ",
        icon: "https://img.freepik.com/free-photo/organic-food_144627-15413.jpg",
    },
    {
        name: "Chất lượng",
        icon: "https://img.freepik.com/free-photo/vegetables-basket_74190-4868.jpg",
    },
    {
        name: "Tự nhiên",
        icon: "https://img.freepik.com/free-photo/fruit_144627-15412.jpg",
    },
];
</script>
