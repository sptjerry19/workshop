<template>
    <div>
        <!-- <Header /> -->
        <Banner />
        <!-- <CategoryList /> -->
        <!-- <ProductSection title="Sản phẩm chất lượng" :products="products" />
        <ProductSection title="Bán chạy" :products="products" /> -->
        <ProductSection title="Best seller" :products="products" />
        <ProductSection title="Must try" :products="products" />
        <PromotionSection :products="products" />
        <Footer />
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";
import Header from "../components/Header.vue";
import Banner from "../components/Banner.vue";
import CategoryList from "../components/CategoryList.vue";
import ProductSection from "../components/ProductSection.vue";
import PromotionSection from "../components/PromotionSection.vue";
import Footer from "../components/Footer.vue";

import api from "../api.js";

const products = ref([]);
const productsPromote = ref([]);

onMounted(async () => {
    try {
        const response = await api.get("/products?take=20");
        console.log("API Response:", response.data);
        products.value = response.data.data;
        console.log("Products after assignment:", products.value);
    } catch (error) {
        console.error("Lỗi khi lấy sản phẩm:", error);
    }

    try {
        const response = await api.get("/products?take=20&sort_by=discount");
        console.log("API Response:", response.data);
        productsPromote.value = response.data.data;
        console.log("Products after assignment:", productsPromote.value);
    } catch (error) {
        console.error("Lỗi khi lấy sản phẩm:", error);
    }
});
</script>
