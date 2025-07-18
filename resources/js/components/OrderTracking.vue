<template>
    <Banner />
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12 pt-60">
        <h2 class="text-2xl font-bold mb-6">Chi tiết đơn hàng</h2>
        <div v-if="loading" class="text-center py-8">
            <div
                class="animate-spin rounded-full h-12 w-12 border-b-2 border-[#d80000] mx-auto"
            ></div>
            <p class="mt-4 text-gray-600">Đang tải...</p>
        </div>

        <div v-else-if="order" class="bg-white rounded-lg shadow-lg p-6">
            <!-- Order Header -->
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h2 class="text-2xl font-bold">
                        Đơn hàng #{{ order.order_number }}
                    </h2>
                    <p class="text-gray-500">
                        {{ formatDate(order.created_at) }}
                    </p>
                </div>
                <div class="text-right">
                    <span
                        :class="getStatusClass(order.order_status)"
                        class="px-3 py-1 rounded-full text-sm"
                    >
                        {{ getStatusText(order.order_status) }}
                    </span>
                    <p class="text-lg font-bold text-[#d80000] mt-2">
                        {{ formatPrice(order.total_amount) }}
                    </p>
                </div>
            </div>

            <!-- Customer Information -->
            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                <h3 class="text-lg font-semibold mb-3">Thông tin khách hàng</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-gray-600 text-sm">Tên khách hàng:</p>
                        <p class="font-medium">{{ order.customer_name }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600 text-sm">Số điện thoại:</p>
                        <p class="font-medium">{{ order.customer_phone }}</p>
                    </div>
                </div>
            </div>

            <!-- Order Timeline -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold mb-4">Trạng thái đơn hàng</h3>
                <div class="relative">
                    <div
                        class="absolute left-4 top-0 bottom-0 w-0.5 bg-gray-200"
                    ></div>
                    <div class="space-y-6">
                        <div
                            v-for="(status, index) in orderStatuses"
                            :key="index"
                            class="relative flex items-start"
                        >
                            <div
                                class="flex items-center justify-center w-8 h-8 rounded-full"
                                :class="
                                    isStatusActive(status.value)
                                        ? 'bg-[#d80000]'
                                        : 'bg-gray-200'
                                "
                            >
                                <svg
                                    v-if="isStatusActive(status.value)"
                                    class="w-4 h-4 text-white"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M5 13l4 4L19 7"
                                    ></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p
                                    class="font-medium"
                                    :class="
                                        isStatusActive(status.value)
                                            ? 'text-[#d80000]'
                                            : 'text-gray-500'
                                    "
                                >
                                    {{ status.label }}
                                </p>
                                <p
                                    v-if="isStatusActive(status.value)"
                                    class="text-sm text-gray-500"
                                >
                                    {{ formatDate(order.updated_at) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Details -->
            <div class="border-t pt-6">
                <h3 class="text-lg font-semibold mb-4">Chi tiết đơn hàng</h3>
                <div class="space-y-6">
                    <div
                        v-for="item in order.items"
                        :key="item.key"
                        class="border rounded-lg p-4"
                    >
                        <div class="flex items-start gap-4">
                            <img
                                :src="item.image"
                                :alt="item.name"
                                class="w-20 h-20 object-cover rounded"
                            />
                            <div class="flex-1">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h4 class="font-medium text-lg">
                                            {{ item.name }}
                                        </h4>
                                        <p class="text-sm text-gray-500">
                                            {{ item.description }}
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-medium">
                                            {{ formatPrice(item.price) }}
                                        </p>
                                        <p class="text-sm text-gray-500">
                                            x{{ item.quantity }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Size Information -->
                                <div v-if="item.size" class="mt-2">
                                    <span
                                        class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded"
                                    >
                                        Size: {{ item.size.label }} (+{{
                                            formatPrice(item.size.price)
                                        }})
                                    </span>
                                </div>

                                <!-- Options -->
                                <div
                                    v-if="
                                        item.options && item.options.length > 0
                                    "
                                    class="mt-2"
                                >
                                    <p
                                        class="text-sm font-medium text-gray-700 mb-1"
                                    >
                                        Tùy chọn:
                                    </p>
                                    <div class="flex flex-wrap gap-1">
                                        <span
                                            v-for="option in item.options"
                                            :key="option.id"
                                            class="inline-block bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded"
                                        >
                                            {{ option.name }}:
                                            {{ option.value }}
                                            <span v-if="option.price > 0"
                                                >(+{{
                                                    formatPrice(option.price)
                                                }})</span
                                            >
                                        </span>
                                    </div>
                                </div>

                                <!-- Toppings -->
                                <div
                                    v-if="
                                        item.toppings &&
                                        item.toppings.length > 0
                                    "
                                    class="mt-2"
                                >
                                    <p
                                        class="text-sm font-medium text-gray-700 mb-1"
                                    >
                                        Toppings:
                                    </p>
                                    <div class="flex flex-wrap gap-1">
                                        <span
                                            v-for="topping in item.toppings"
                                            :key="topping.id"
                                            class="inline-block bg-green-100 text-green-800 text-xs px-2 py-1 rounded"
                                        >
                                            {{ topping.name }} (+{{
                                                formatPrice(topping.price)
                                            }})
                                        </span>
                                    </div>
                                </div>

                                <!-- Discount -->
                                <div
                                    v-if="item.discount && item.discount > 0"
                                    class="mt-2"
                                >
                                    <span
                                        class="inline-block bg-red-100 text-red-800 text-xs px-2 py-1 rounded"
                                    >
                                        Giảm giá:
                                        {{ formatPrice(item.discount) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="border-t pt-6 mt-6">
                <div class="flex justify-between items-center">
                    <span class="text-lg font-semibold">Tổng cộng:</span>
                    <span class="text-xl font-bold text-[#d80000]">{{
                        formatPrice(order.total_amount)
                    }}</span>
                </div>
            </div>

            <!-- Payment Information -->
            <div class="border-t pt-6 mt-6">
                <h3 class="text-lg font-semibold mb-4">Thông tin thanh toán</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-gray-600">Phương thức thanh toán:</p>
                        <p class="font-medium">
                            {{ getPaymentMethodText(order.payment_method) }}
                        </p>
                    </div>
                    <div>
                        <p class="text-gray-600">Trạng thái thanh toán:</p>
                        <p
                            :class="getPaymentStatusClass(order.payment_status)"
                            class="font-medium"
                        >
                            {{ getPaymentStatusText(order.payment_status) }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Shipping Information -->
            <div class="border-t pt-6 mt-6">
                <h3 class="text-lg font-semibold mb-4">Thông tin giao hàng</h3>
                <p class="text-gray-600">Địa chỉ giao hàng:</p>
                <p class="font-medium">{{ order.customer_address }}</p>
            </div>

            <!-- Notes -->
            <div v-if="order.notes" class="border-t pt-6 mt-6">
                <h3 class="text-lg font-semibold mb-4">Ghi chú</h3>
                <p class="text-gray-700">{{ order.notes }}</p>
            </div>
        </div>

        <div v-else class="text-center py-8">
            <p class="text-gray-500">Không tìm thấy thông tin đơn hàng</p>
        </div>
    </div>
</template>

<script setup>
import Banner from "./Banner.vue";
import { ref, onMounted, onUnmounted } from "vue";
import { useRoute } from "vue-router";
import api from "@/api";

const route = useRoute();
const order = ref(null);
const loading = ref(true);

const orderStatuses = [
    { value: "pending", label: "Chờ xử lý" },
    { value: "processing", label: "Đang xử lý" },
    { value: "completed", label: "Hoàn thành" },
    { value: "cancelled", label: "Đã hủy" },
];

async function fetchOrderDetails() {
    try {
        const response = await api.get(`/orders/${route.params.id}`);
        if (response.data.success) {
            order.value = response.data.data;
        }
    } catch (error) {
        console.error("Error fetching order details:", error);
    } finally {
        loading.value = false;
    }
}

function isStatusActive(status) {
    if (!order.value) return false;
    const statusOrder = orderStatuses.map((s) => s.value);
    const currentIndex = statusOrder.indexOf(order.value.order_status);
    const statusIndex = statusOrder.indexOf(status);
    return statusIndex <= currentIndex;
}

function formatDate(date) {
    return new Date(date).toLocaleDateString("vi-VN", {
        year: "numeric",
        month: "long",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    });
}

function formatPrice(price) {
    return Number(price).toLocaleString("vi-VN") + "đ";
}

function getStatusClass(status) {
    const classes = {
        pending: "bg-yellow-100 text-yellow-800",
        processing: "bg-blue-100 text-blue-800",
        completed: "bg-green-100 text-green-800",
        cancelled: "bg-red-100 text-red-800",
    };
    return classes[status] || "bg-gray-100 text-gray-800";
}

function getStatusText(status) {
    const texts = {
        pending: "Chờ xử lý",
        processing: "Đang xử lý",
        completed: "Hoàn thành",
        cancelled: "Đã hủy",
    };
    return texts[status] || status;
}

function getPaymentMethodText(method) {
    const texts = {
        cod: "Thanh toán khi nhận hàng",
        momo: "Ví MoMo",
        bank: "Chuyển khoản ngân hàng",
    };
    return texts[method] || method;
}

function getPaymentStatusClass(status) {
    const classes = {
        pending: "text-yellow-600",
        paid: "text-green-600",
        failed: "text-red-600",
    };
    return classes[status] || "text-gray-600";
}

function getPaymentStatusText(status) {
    const texts = {
        pending: "Chờ thanh toán",
        paid: "Đã thanh toán",
        failed: "Thanh toán thất bại",
    };
    return texts[status] || status;
}

onMounted(() => {
    fetchOrderDetails();

    // Subscribe to order updates using public channel
    window.Echo.channel(`order.${route.params.id}`).listen(
        "OrderStatusUpdated",
        (e) => {
            if (order.value) {
                order.value.order_status = e.order.order_status;
                order.value.payment_status = e.order.payment_status;
                order.value.updated_at = e.order.updated_at;
            }
        }
    );

    window.Echo.channel(`order.${route.params.id}`).listen(
        ".OrderStatusUpdated",
        (e) => {
            if (order.value) {
                order.value.order_status = e.order.order_status;
                order.value.payment_status = e.order.payment_status;
                order.value.updated_at = e.order.updated_at;
            }
        }
    );
});

onUnmounted(() => {
    // Unsubscribe from order updates
    window.Echo.leave(`order.${route.params.id}`);
});
</script>
