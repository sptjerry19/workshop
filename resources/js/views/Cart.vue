<template>
    <Banner />
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12 pt-60">
        <h1 class="text-3xl font-bold mb-8">Giỏ hàng của bạn</h1>
        <div
            v-if="cartItems.length === 0"
            class="text-center text-gray-500 py-12"
        >
            Giỏ hàng trống.
        </div>
        <div v-else>
            <div class="space-y-6">
                <div
                    v-for="item in cartItems"
                    :key="item.key"
                    class="flex items-center gap-4 border-b pb-4"
                >
                    <img
                        :src="item.image"
                        :alt="item.name"
                        class="w-20 h-20 object-cover rounded"
                    />
                    <div class="flex-1">
                        <div class="font-semibold">{{ item.name }}</div>
                        <div v-if="item.size" class="text-xs text-gray-500">
                            Size: {{ item.size.label }}
                        </div>
                        <div class="text-xs text-gray-500">
                            {{ item.description }}
                        </div>
                        <div class="flex items-center gap-2 mt-2">
                            <button
                                @click="changeQuantity(item, -1)"
                                class="px-2 py-1 border rounded text-sm"
                            >
                                -
                            </button>
                            <span class="font-semibold">{{
                                item.quantity
                            }}</span>
                            <button
                                @click="changeQuantity(item, 1)"
                                class="px-2 py-1 border rounded text-sm"
                            >
                                +
                            </button>
                        </div>
                    </div>
                    <div class="flex flex-col items-end min-w-[90px]">
                        <span
                            v-if="item.discount > 0"
                            class="text-base font-bold text-[#b80000]"
                            >{{ getDiscountedPrice(item) }}</span
                        >
                        <span
                            v-if="item.discount > 0"
                            class="text-xs text-gray-400 line-through"
                            >{{ formatPrice(item.price) }}</span
                        >
                        <span
                            v-if="item.discount === 0"
                            class="text-base font-bold text-gray-800"
                            >{{ formatPrice(item.price) }}</span
                        >
                        <button
                            @click="removeItem(item)"
                            class="mt-2 text-xs text-red-500 hover:underline"
                        >
                            Xóa
                        </button>
                    </div>
                </div>
            </div>
            <div class="mt-8 flex justify-between items-center border-t pt-6">
                <div class="text-lg font-bold">Tổng cộng:</div>
                <div class="text-2xl font-bold text-[#d80000]">
                    {{ formatPrice(totalPrice) }}
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import Banner from "../components/Banner.vue";

const cartItems = ref([]);

function loadCart() {
    const data = localStorage.getItem("cartItems");
    cartItems.value = data ? JSON.parse(data) : [];
}
function saveCart() {
    localStorage.setItem("cartItems", JSON.stringify(cartItems.value));
    window.dispatchEvent(new Event("cart-updated"));
}

onMounted(() => {
    loadCart();
});

function formatPrice(price) {
    if (!price) return "";
    return Number(price).toLocaleString("vi-VN") + "đ";
}

function getDiscountedPrice(item) {
    if (item.discount === 0) return formatPrice(item.price);
    if (item.discount < 100) {
        return formatPrice(item.price * (1 - item.discount / 100));
    } else {
        return formatPrice(item.price - item.discount);
    }
}

function changeQuantity(item, delta) {
    const idx = cartItems.value.findIndex((i) => i.key === item.key);
    if (idx !== -1) {
        cartItems.value[idx].quantity += delta;
        if (cartItems.value[idx].quantity <= 0) {
            cartItems.value.splice(idx, 1);
        }
        saveCart();
    }
}

function removeItem(item) {
    const idx = cartItems.value.findIndex((i) => i.key === item.key);
    if (idx !== -1) {
        cartItems.value.splice(idx, 1);
        saveCart();
    }
}

const totalPrice = computed(() => {
    return cartItems.value.reduce((sum, item) => {
        let price = item.price;
        if (item.discount === 0) return sum + price * item.quantity;
        if (item.discount < 100) {
            price = price * (1 - item.discount / 100);
        } else {
            price = price - item.discount;
        }
        return sum + price * item.quantity;
    }, 0);
});
</script>

<style scoped></style>
