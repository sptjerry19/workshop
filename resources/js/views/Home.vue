<template>
    <div>
        <!-- <Header /> -->
        <Banner />
        <!-- <CategoryList /> -->
        <!-- <ProductSection title="Sản phẩm chất lượng" :products="products" />
        <ProductSection title="Bán chạy" :products="products" /> -->
        <ProductSection title="Best seller" :products="products" />
        <PromotionSection :products="products" />
        <!-- <Footer /> -->
    </div>
</template>

<script setup>
import Header from "../components/Header.vue";
import Banner from "../components/Banner.vue";
import CategoryList from "../components/CategoryList.vue";
import ProductSection from "../components/ProductSection.vue";
import PromotionSection from "../components/PromotionSection.vue";
import Footer from "../components/Footer.vue";

const products = [
    {
        id: 1,
        name: "Nho xanh",
        image: "https://img.freepik.com/free-photo/grapes_144627-15415.jpg",
        price: 250000,
        size: [
            { label: "S", price: 130000 },
            { label: "M", price: 180000 },
            { label: "L", price: 220000 },
        ],
    },
    {
        id: 2,
        name: "Đào",
        image: "https://img.freepik.com/free-photo/peach_144627-15416.jpg",
        price: 210000,
        size: [
            { label: "M", price: 180000 },
            { label: "L", price: 220000 },
        ],
    },
    {
        id: 3,
        name: "Chuối",
        image: "https://img.freepik.com/free-photo/banana_144627-15417.jpg",
        price: 180000,
        size: [
            { label: "S", price: 130000 },
            { label: "L", price: 220000 },
        ],
    },
    {
        id: 4,
        name: "Rau cải",
        image: "https://img.freepik.com/free-photo/lettuce_144627-15414.jpg",
        price: 35000,
        size: [
            { label: "S", price: 130000 },
            { label: "M", price: 180000 },
        ],
    },
    {
        id: 5,
        name: "Cà chua",
        image: "https://img.freepik.com/free-photo/tomato_144627-15418.jpg",
        price: 40000,
        size: [
            { label: "S", price: 130000 },
            { label: "M", price: 180000 },
            { label: "L", price: 220000 },
        ],
    },
    {
        id: 6,
        name: "Cà chua",
        image: "https://img.freepik.com/free-photo/tomato_144627-15418.jpg",
        price: 40000,
        size: [
            { label: "S", price: 130000 },
            { label: "M", price: 180000 },
            { label: "L", price: 220000 },
        ],
    },
    {
        id: 7,
        name: "Cà chua",
        image: "https://img.freepik.com/free-photo/tomato_144627-15418.jpg",
        price: 40000,
        size: [
            { label: "S", price: 100000 },
            { label: "M", price: 150000 },
            { label: "L", price: 180000 },
        ],
    },
    {
        id: 8,
        name: "Chuối",
        image: "https://img.freepik.com/free-photo/banana_144627-15417.jpg",
        price: 180000, // có thể là giá mặc định hoặc bỏ đi nếu không cần
        size: [
            { label: "S", price: 100000 },
            { label: "M", price: 150000 },
            { label: "L", price: 180000 },
        ],
    },
];
</script>
