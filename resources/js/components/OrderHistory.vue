<template>
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <h2 class="text-2xl font-bold mb-6">Lịch sử đơn hàng</h2>

        <!-- Loading state -->
        <div v-if="loading" class="text-center py-8">
            <div
                class="animate-spin rounded-full h-12 w-12 border-b-2 border-[#d80000] mx-auto"
            ></div>
            <p class="mt-4 text-gray-600">Đang tải...</p>
        </div>

        <!-- Empty state -->
        <div v-else-if="orders.length === 0" class="text-center py-8">
            <p class="text-gray-500">Bạn chưa có đơn hàng nào.</p>
        </div>

        <!-- Orders list -->
        <div v-else class="space-y-6">
            <div
                v-for="order in orders"
                :key="order.id"
                class="bg-white rounded-lg shadow p-6"
            >
                <router-link
                    :to="{
                        name: 'order-tracking',
                        params: { id: order.id },
                    }"
                >
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-lg font-semibold">
                                Đơn hàng #{{ order.id }}
                            </h3>
                            <p class="text-sm text-gray-500">
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

                    <!-- Order items -->
                    <div class="border-t pt-4">
                        <div
                            v-for="item in order.order_items"
                            :key="item.id"
                            class="flex items-center gap-4 mb-4"
                        >
                            <img
                                :src="item.product.image"
                                :alt="item.product.name"
                                class="w-16 h-16 object-cover rounded"
                            />
                            <div class="flex-1">
                                <h4 class="font-medium">
                                    {{ item.product.name }}
                                </h4>
                                <p class="text-sm text-gray-500">
                                    Số lượng: {{ item.quantity }}
                                </p>
                                <p class="text-sm text-gray-500">
                                    {{ formatPrice(item.price) }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Order details -->
                    <div class="border-t pt-4 mt-4">
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <p class="text-gray-600">
                                    Phương thức thanh toán:
                                </p>
                                <p class="font-medium">
                                    {{
                                        getPaymentMethodText(
                                            order.payment_method
                                        )
                                    }}
                                </p>
                            </div>
                            <div>
                                <p class="text-gray-600">
                                    Trạng thái thanh toán:
                                </p>
                                <p
                                    :class="
                                        getPaymentStatusClass(
                                            order.payment_status
                                        )
                                    "
                                    class="font-medium"
                                >
                                    {{
                                        getPaymentStatusText(
                                            order.payment_status
                                        )
                                    }}
                                </p>
                            </div>
                            <div class="col-span-2">
                                <p class="text-gray-600">Địa chỉ giao hàng:</p>
                                <p class="font-medium">
                                    {{ order.customer_address }}
                                </p>
                            </div>
                        </div>
                    </div>
                </router-link>
            </div>
        </div>

        <!-- Pagination -->
        <div v-if="totalPages > 1" class="mt-8 flex justify-center">
            <div class="flex space-x-2">
                <button
                    @click="changePage(currentPage - 1)"
                    :disabled="currentPage === 1"
                    class="px-4 py-2 border rounded-md disabled:opacity-50"
                >
                    Trước
                </button>
                <button
                    v-for="page in totalPages"
                    :key="page"
                    @click="changePage(page)"
                    :class="[
                        'px-4 py-2 border rounded-md',
                        currentPage === page ? 'bg-[#d80000] text-white' : '',
                    ]"
                >
                    {{ page }}
                </button>
                <button
                    @click="changePage(currentPage + 1)"
                    :disabled="currentPage === totalPages"
                    class="px-4 py-2 border rounded-md disabled:opacity-50"
                >
                    Sau
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";

const orders = ref([]);
const loading = ref(true);
const currentPage = ref(1);
const totalPages = ref(1);
const perPage = 10;

// Cookie getter
function getCookie(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return parts.pop().split(";").shift();
}

async function fetchOrders(page = 1) {
    try {
        loading.value = true;
        const userId = getCookie("user")
            ? JSON.parse(decodeURIComponent(getCookie("user"))).id
            : localStorage.getItem("chat_user_id");

        const response = await axios.get("/api/orders/histories", {
            params: {
                page,
                per_page: perPage,
                user_id: userId,
            },
        });

        if (response.data.success) {
            orders.value = response.data.data;
            totalPages.value = Math.ceil(
                response.data.pagination.total / perPage
            );
            currentPage.value = page;
        }
    } catch (error) {
        console.error("Error fetching orders:", error);
    } finally {
        loading.value = false;
    }
}

function changePage(page) {
    if (page >= 1 && page <= totalPages.value) {
        fetchOrders(page);
    }
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
        completed: "text-green-600",
        failed: "text-red-600",
    };
    return classes[status] || "text-gray-600";
}

function getPaymentStatusText(status) {
    const texts = {
        pending: "Chờ thanh toán",
        completed: "Đã thanh toán",
        failed: "Thanh toán thất bại",
    };
    return texts[status] || status;
}

onMounted(() => {
    fetchOrders();
});
</script>
