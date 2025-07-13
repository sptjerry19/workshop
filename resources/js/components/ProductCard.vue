<template>
    <div
        class="w-[150px] h-[240px] rounded-md shadow-lg p-4 flex flex-col items-center transition-all duration-300 group overflow-hidden"
        :class="isDark ? 'bg-gray-800' : 'bg-white'"
    >
        <!-- Image + Wishlist + Action buttons -->
        <div class="relative w-full h-[180px] overflow-hidden group">
            <router-link :to="'products/' + product.id">
                <img
                    :alt="product.name"
                    class="w-full h-full object-cover rounded-md transition-transform duration-300 group-hover:scale-105"
                    :src="product.image"
                />
            </router-link>
            <div
                class="absolute top-2 right-2 cursor-pointer"
                :class="[
                    isDark ? 'text-gray-500' : 'text-gray-400',
                    { 'text-[#d80000]': product.is_favorite },
                ]"
                :title="
                    product.is_favorite
                        ? 'Remove from wishlist'
                        : 'Add to wishlist'
                "
                @click="toggleWishlist"
            >
                <i
                    :class="
                        product.is_favorite ? 'fas fa-heart' : 'far fa-heart'
                    "
                ></i>
            </div>

            <!-- Hover Buttons
            <div
                class="absolute inset-0 bg-white bg-opacity-90 flex opacity-0 group-hover:opacity-100 transition-opacity rounded-md"
            >
                <button
                    aria-label="Add to cart"
                    class="flex-1 flex items-center justify-center text-xl"
                >
                    <i
                        class="fas fa-plus transition-transform duration-200 hover:scale-125"
                    ></i>
                </button>

                <button
                    aria-label="View details"
                    class="flex-1 flex items-center justify-center text-xl"
                >
                    <i
                        class="far fa-eye transition-transform duration-200 hover:scale-125"
                    ></i>
                </button>
            </div> -->
        </div>

        <!-- Product Name & Price -->
        <div class="mt-3 w-full flex justify-between items-center text-sm">
            <div
                class="font-semibold truncate"
                :class="isDark ? 'text-white' : 'text-gray-900'"
            >
                {{ product.name }}
            </div>
            <div :class="isDark ? 'text-gray-300' : 'text-gray-700'">
                {{
                    selectedSize
                        ? selectedSize.price.toLocaleString()
                        : product.price.toLocaleString()
                }}₫
            </div>
        </div>

        <!-- Sizes -->
        <div
            v-if="product.size && product.size.length > 0"
            class="mt-2 w-full flex justify-end gap-2 text-xs"
            :class="isDark ? 'text-gray-500' : 'text-gray-400'"
        >
            <div
                v-for="(s, index) in product.size"
                :key="index"
                class="border rounded-full w-6 h-6 flex items-center justify-center cursor-pointer transition"
                :class="[
                    isDark
                        ? 'border-gray-600 hover:bg-gray-700 hover:border-gray-500'
                        : 'border-gray-300 hover:bg-gray-200 hover:border-gray-500',
                    selectedSize?.label === s.label
                        ? isDark
                            ? 'bg-gray-600 border-gray-500 text-white'
                            : 'bg-gray-300 border-gray-600 text-black'
                        : '',
                ]"
                :title="s.label"
                @click="selectedSize = s"
            >
                {{ s.label }}
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, watch } from "vue";
import axios from "axios";
import { useDarkMode } from "../composables/useDarkMode.js";

const props = defineProps(["product"]);
const selectedSize = ref(null);
const { isDark } = useDarkMode();

if (props.product?.size?.length > 0) {
    selectedSize.value = props.product.size[0];
}

// Toggle wishlist status
async function toggleWishlist() {
    try {
        if (props.product.is_favorite) {
            // Remove from wishlist
            const wishlistItem = await axios.get("/api/wishlist");
            const item = wishlistItem.data.data.find(
                (item) => item.product.id === props.product.id
            );
            if (item) {
                await axios.delete(`/api/wishlist/${item.id}`);
                props.product.is_favorite = false;
            }
        } else {
            // Add to wishlist
            await axios.post("/api/wishlist", {
                product_id: props.product.id,
            });
            props.product.is_favorite = true;
        }
    } catch (error) {
        console.error("Error toggling wishlist:", error);
        alert("Có lỗi xảy ra khi thêm/xóa sản phẩm khỏi danh sách yêu thích");
    }
}

// Watch for product changes
watch(
    () => props.product,
    (newProduct) => {
        if (newProduct?.size?.length > 0) {
            selectedSize.value = { ...newProduct.size[0] };
        }
    }
);
</script>
