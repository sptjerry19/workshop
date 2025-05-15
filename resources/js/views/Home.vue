<template>
    <div>
        <Header />
        <Banner />
        <CategoryList />
        <ProductSection title="Sản phẩm chất lượng" :products="products" />
        <ProductSection title="Bán chạy" :products="products" />
        <PromotionSection :products="products" />
        <Footer />
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
    },
    {
        id: 2,
        name: "Đào",
        image: "https://img.freepik.com/free-photo/peach_144627-15416.jpg",
        price: 210000,
    },
    {
        id: 3,
        name: "Chuối",
        image: "https://img.freepik.com/free-photo/banana_144627-15417.jpg",
        price: 180000,
    },
    {
        id: 4,
        name: "Rau cải",
        image: "https://img.freepik.com/free-photo/lettuce_144627-15414.jpg",
        price: 35000,
    },
    {
        id: 5,
        name: "Cà chua",
        image: "https://img.freepik.com/free-photo/tomato_144627-15418.jpg",
        price: 40000,
    },
];
</script>
