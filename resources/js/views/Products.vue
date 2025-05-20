<template>
    <Banner />
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div
            v-for="(group, categoryId) in groupedProducts"
            :key="categoryId"
            class="mb-12"
        >
            <h2
                class="text-2xl font-bold text-[#d80000] uppercase mb-4 flex items-center gap-2"
            >
                {{ group[0]?.category_name || "Danh mục" }}
            </h2>
            <div class="flex flex-wrap justify-center gap-6">
                <div
                    v-for="product in group"
                    :key="product.id"
                    class="bg-white rounded-lg shadow-md overflow-hidden relative group border border-gray-200 max-w-[300px] w-full flex-shrink-0 flex-grow-0"
                >
                    <div class="w-full flex flex-col items-center p-4">
                        <div
                            class="relative w-full h-[180px] overflow-hidden group"
                        >
                            <img
                                :alt="product.name"
                                class="w-full h-full object-cover rounded-md transition-transform duration-300 group-hover:scale-105"
                                :src="product.image"
                            />
                            <div
                                v-if="parseFloat(product.discount) > 0"
                                class="absolute top-2 left-2 bg-[#d80000] text-white text-xs font-bold px-2 py-1 rounded"
                            >
                                -{{ parseFloat(product.discount)
                                }}{{
                                    parseFloat(product.discount) < 100
                                        ? "%"
                                        : "đ"
                                }}
                            </div>
                            <div
                                v-if="product.is_new"
                                class="absolute top-2 right-2 bg-[#fbbf24] text-white text-xs font-bold px-2 py-1 rounded"
                            >
                                MỚI
                            </div>
                        </div>
                        <div
                            class="mt-3 w-full flex justify-between items-center text-sm"
                        >
                            <div class="font-semibold truncate">
                                {{ product.name }}
                            </div>
                            <div class="flex flex-col items-end w-[90px]">
                                <span
                                    v-if="parseFloat(product.discount) > 0"
                                    class="text-lg font-bold text-[#b80000]"
                                >
                                    {{
                                        getDiscountedPrice(
                                            product,
                                            selectedSizes[product.id]
                                        )
                                    }}
                                </span>
                                <span
                                    v-if="parseFloat(product.discount) > 0"
                                    class="text-xs text-gray-400 line-through"
                                >
                                    {{
                                        getBasePrice(
                                            product,
                                            selectedSizes[product.id]
                                        )
                                    }}
                                </span>
                                <span
                                    v-if="parseFloat(product.discount) === 0"
                                    class="text-lg font-bold text-gray-800"
                                >
                                    {{
                                        getBasePrice(
                                            product,
                                            selectedSizes[product.id]
                                        )
                                    }}
                                </span>
                            </div>
                        </div>
                        <!-- Sizes -->
                        <div
                            v-if="product.size && product.size.length > 0"
                            class="mt-2 w-full flex justify-end gap-2 text-gray-400 text-xs"
                        >
                            <div
                                v-for="(s, index) in product.size"
                                :key="index"
                                class="border border-gray-300 rounded-full w-6 h-6 flex items-center justify-center cursor-pointer transition hover:bg-gray-200 hover:border-gray-500"
                                :class="
                                    selectedSizes[product.id]?.label === s.label
                                        ? 'bg-gray-300 border-gray-600 text-black'
                                        : ''
                                "
                                :title="s.label"
                                @click="selectSize(product.id, s)"
                            >
                                {{ s.label }}
                            </div>
                        </div>
                        <p
                            class="text-xs text-gray-600 mb-2 line-clamp-2 w-full text-left mt-2"
                        >
                            {{ product.description }}
                        </p>
                        <button
                            class="mt-auto w-full bg-gray-200 text-gray-700 rounded-b-md py-2 font-semibold text-sm group-hover:bg-[#d80000] group-hover:text-white transition"
                        >
                            Thêm
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import Banner from "../components/Banner.vue";
import api from "../api.js";

const products = ref([]);
const selectedSizes = ref({});

onMounted(async () => {
    try {
        const response = await api.get("/products");
        products.value = response.data.data;
        // Khởi tạo selectedSizes cho từng sản phẩm có size
        for (const p of products.value) {
            if (p.size && p.size.length > 0) {
                selectedSizes.value[p.id] = p.size[0];
            }
        }
    } catch (error) {
        console.error("Lỗi khi lấy sản phẩm:", error);
    }
});

// Gộp sản phẩm theo category_id, lấy category_name động
const groupedProducts = computed(() => {
    const groups = {};
    for (const p of products.value) {
        if (!groups[p.category_id]) groups[p.category_id] = [];
        groups[p.category_id].push(p);
    }
    return groups;
});

function formatPrice(price) {
    if (!price) return "";
    return Number(price).toLocaleString("vi-VN") + "đ";
}

function getBasePrice(product, size) {
    return formatPrice(size ? size.price : product.price);
}

function getDiscountedPrice(product, size) {
    const discount = parseFloat(product.discount);
    let basePrice = size ? size.price : Number(product.price);
    if (discount === 0) return formatPrice(basePrice);
    if (discount < 100) {
        // Discount theo phần trăm
        return formatPrice(basePrice * (1 - discount / 100));
    } else {
        // Discount theo số tiền
        return formatPrice(basePrice - discount);
    }
}

function selectSize(productId, size) {
    selectedSizes.value[productId] = size;
}
</script>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
