<template>
    <AdminLayout>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <!-- Total Orders -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-500">
                        <svg
                            class="w-8 h-8"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"
                            ></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-500">Total Orders</p>
                        <p class="text-2xl font-semibold">
                            {{ stats.total_orders }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Total Products -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-500">
                        <svg
                            class="w-8 h-8"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"
                            ></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-500">Total Products</p>
                        <p class="text-2xl font-semibold">
                            {{ stats.total_products }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Total Categories -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 text-purple-500">
                        <svg
                            class="w-8 h-8"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"
                            ></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-500">Total Categories</p>
                        <p class="text-2xl font-semibold">
                            {{ stats.total_categories }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Total Revenue -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-500">
                        <svg
                            class="w-8 h-8"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                            ></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-500">Total Revenue</p>
                        <p class="text-2xl font-semibold">
                            {{ formatPrice(stats.total_revenue) }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recent Orders -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b">
                    <h3 class="text-lg font-semibold">Recent Orders</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div
                            v-for="order in stats.recent_orders"
                            :key="order.id"
                            class="flex items-center justify-between"
                        >
                            <div>
                                <p class="font-medium">
                                    {{ order.order_number }}
                                </p>
                                <p class="text-sm text-gray-500">
                                    {{ order.customer_name }}
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="font-medium">
                                    {{ formatPrice(order.total_amount) }}
                                </p>
                                <span
                                    :class="
                                        getStatusBadgeClass(order.order_status)
                                    "
                                    class="px-2 py-1 text-xs rounded-full"
                                >
                                    {{ order.order_status }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Products -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b">
                    <h3 class="text-lg font-semibold">Top Products</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div
                            v-for="product in stats.top_products"
                            :key="product.id"
                            class="flex items-center"
                        >
                            <img
                                :src="product.image"
                                :alt="product.name"
                                class="w-12 h-12 object-cover rounded"
                            />
                            <div class="ml-4">
                                <p class="font-medium">{{ product.name }}</p>
                                <p class="text-sm text-gray-500">
                                    {{ product.orders_count }} orders
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref, onMounted } from "vue";
import AdminLayout from "@/layouts/AdminLayout.vue";
import axios from "axios";

const stats = ref({
    total_orders: 0,
    total_products: 0,
    total_categories: 0,
    total_revenue: 0,
    recent_orders: [],
    top_products: [],
});

onMounted(async () => {
    try {
        const response = await axios.get("/api/admin/dashboard");
        if (response.data.success) {
            stats.value = response.data.data;
        }
    } catch (error) {
        console.error("Failed to fetch dashboard data:", error);
    }
});

function formatPrice(price) {
    return new Intl.NumberFormat("vi-VN", {
        style: "currency",
        currency: "VND",
    }).format(price);
}

function getStatusBadgeClass(status) {
    return {
        "bg-yellow-100 text-yellow-800": status === "pending",
        "bg-blue-100 text-blue-800": status === "processing",
        "bg-green-100 text-green-800": status === "completed",
        "bg-red-100 text-red-800": status === "cancelled",
    };
}
</script>
