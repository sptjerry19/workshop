<template>
    <Banner />
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 pt-40">
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
            <div
                class="flex flex-wrap justify-start gap-6 w-full max-w-[960px] mx-auto"
            >
                <div
                    v-for="product in group"
                    :key="product.id"
                    class="bg-white rounded-lg shadow-md overflow-hidden relative group border border-gray-200 max-w-[300px] w-full flex-shrink-0 flex-grow-0"
                >
                    <div class="w-full flex flex-col items-center p-4">
                        <div
                            class="relative w-full h-[180px] overflow-hidden group"
                        >
                            <router-link :to="`/products/${product.id}`">
                                <img
                                    :alt="product.name"
                                    class="w-full h-full object-cover rounded-md transition-transform duration-300 group-hover:scale-105"
                                    :src="product.image"
                                />
                            </router-link>
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
                                    v-if="
                                        product.discount == null ||
                                        parseFloat(product.discount) === 0
                                    "
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
                            @click="
                                addToCart(product, selectedSizes[product.id])
                            "
                        >
                            Thêm
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <ChatBox />
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import Banner from "../components/Banner.vue";
import ChatBox from "../components/ChatBox.vue";
import api from "../api.js";

const products = ref([]);
const selectedSizes = ref({});
const cartItems = ref([]);

// Load cart from localStorage
function loadCart() {
    const data = localStorage.getItem("cartItems");
    cartItems.value = data ? JSON.parse(data) : [];
}
function saveCart() {
    localStorage.setItem("cartItems", JSON.stringify(cartItems.value));
    window.dispatchEvent(new Event("cart-updated"));
}

onMounted(async () => {
    loadCart();
    try {
        const response = await api.get("/products/all");
        products.value = response.data.data;
        // Khởi tạo selectedSizes cho từng sản phẩm có size
        for (const p of products.value) {
            if (p.size && p.size.length > 0) {
                selectedSizes.value[p.id] = p.size[0];
            }
        }

        console.log("Setting up WebSocket listener...");
        console.log("Pusher key:", import.meta.env.VITE_PUSHER_APP_KEY);
        console.log("Pusher cluster:", import.meta.env.VITE_PUSHER_APP_CLUSTER);
        console.log("Echo config:", window.Echo);

        window.Pusher = Pusher;
        Pusher.logToConsole = true;

        // Listen for product updates
        const channel = window.Echo.channel("products");
        console.log("Channel created:", channel);

        // Listen for all events on the channel
        channel.listenToAll((event, data) => {
            console.log("Received event on channel:", event, data);
        });

        // Try different event names
        channel.listen(".ProductUpdated", (e) => {
            console.log("Received .ProductUpdated event:", e);
            refreshProducts();
        });

        // channel.listen("ProductUpdated", (e) => {
        //     console.log("Received ProductUpdated event:", e);
        //     refreshProducts();
        // });

        // channel.listen("App\\Events\\ProductUpdated", (e) => {
        //     console.log("Received App\\Events\\ProductUpdated event:", e);
        //     refreshProducts();
        // });

        channel.error((error) => {
            console.error("WebSocket error:", error);
        });

        // Test connection
        channel.subscribed(() => {
            console.log("Successfully subscribed to channel");
        });

        console.log("WebSocket listener set up successfully");
    } catch (error) {
        console.error("Lỗi khi lấy sản phẩm:", error);
    }
});

// Add refreshProducts function
async function refreshProducts() {
    console.log("Refreshing products...");
    try {
        const response = await api.get("/products/all");
        console.log("New products data:", response.data.data);
        products.value = response.data.data;
        // Reinitialize selectedSizes for products with sizes
        for (const p of products.value) {
            if (p.size && p.size.length > 0) {
                selectedSizes.value[p.id] = p.size[0];
            }
        }
        console.log("Products refreshed successfully");
    } catch (error) {
        console.error("Lỗi khi cập nhật sản phẩm:", error);
    }
}

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

function addToCart(product, size) {
    // Tìm xem đã có sản phẩm này (id + size) trong giỏ chưa
    const key = size ? `${product.id}_${size.label}` : `${product.id}`;
    const idx = cartItems.value.findIndex((item) => item.key === key);
    if (idx !== -1) {
        cartItems.value[idx].quantity += 1;
    } else {
        cartItems.value.push({
            key,
            id: product.id,
            name: product.name,
            image: product.image,
            size: size ? { ...size } : null,
            price: size ? size.price : Number(product.price),
            discount: parseFloat(product.discount),
            quantity: 1,
            description: product.description,
        });
    }
    saveCart();
    // Phát sự kiện để các component khác cập nhật
    window.dispatchEvent(new Event("cart-updated"));
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
