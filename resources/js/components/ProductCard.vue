<template>
    <div
        class="w-[150px] h-[240px] bg-white rounded-md shadow-lg p-4 flex flex-col items-center transition-all duration-300 group overflow-hidden"
    >
        <!-- Image + Wishlist + Action buttons -->
        <div class="relative w-full h-[180px] overflow-hidden group">
            <img
                :alt="product.name"
                class="w-full h-full object-cover rounded-md transition-transform duration-300 group-hover:scale-105"
                :src="product.image"
            />
            <div
                class="absolute top-2 right-2 text-gray-400 cursor-pointer"
                title="Add to wishlist"
            >
                <i class="far fa-heart"></i>
            </div>

            <!-- Hover Buttons -->
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
            </div>
        </div>

        <!-- Product Name & Price -->
        <div class="mt-3 w-full flex justify-between items-center text-sm">
            <div class="font-semibold truncate">{{ product.name }}</div>
            <div class="text-gray-700">
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
            class="mt-2 w-full flex justify-end gap-2 text-gray-400 text-xs"
        >
            <div
                v-for="(s, index) in product.size"
                :key="index"
                class="border border-gray-300 rounded-full w-6 h-6 flex items-center justify-center cursor-pointer transition hover:bg-gray-200 hover:border-gray-500"
                :class="{
                    'bg-gray-300 border-gray-600 text-black':
                        selectedSize?.label === s.label,
                }"
                :title="s.label"
                @click="selectedSize = s"
            >
                {{ s.label }}
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, watch, onMounted } from "vue";

const props = defineProps(["product"]);
const selectedSize = ref(null);

onMounted(() => {
    if (props.product?.size?.length > 0) {
        selectedSize.value = props.product.size[0];
    }
});

// Nếu muốn theo dõi props thay đổi (nếu product là động)
watch(
    () => props.product,
    (newProduct) => {
        if (newProduct?.size?.length > 0) {
            selectedSize.value = { ...newProduct.size[0] };
        }
    }
);
</script>
