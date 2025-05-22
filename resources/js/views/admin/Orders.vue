<template>
    <AdminLayout>
        <!-- Filters -->
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700"
                            >Status</label
                        >
                        <select
                            v-model="filters.status"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option value="">All</option>
                            <option value="pending">Pending</option>
                            <option value="processing">Processing</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700"
                            >Payment Status</label
                        >
                        <select
                            v-model="filters.payment_status"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option value="">All</option>
                            <option value="pending">Pending</option>
                            <option value="paid">Paid</option>
                            <option value="failed">Failed</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700"
                            >Search</label
                        >
                        <input
                            type="text"
                            v-model="filters.search"
                            placeholder="Order number, customer name..."
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        />
                    </div>
                    <div class="flex items-end">
                        <button
                            @click="fetchOrders"
                            class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700"
                        >
                            Filter
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Orders Table -->
        <div class="bg-white rounded-lg shadow">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                            >
                                Order Number
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                            >
                                Customer
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                            >
                                Total
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                            >
                                Status
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                            >
                                Payment
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                            >
                                Date
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                            >
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="order in orders.data" :key="order.id">
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"
                            >
                                {{ order.order_number }}
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                            >
                                {{ order.customer_name }}<br />
                                <span class="text-xs">{{
                                    order.customer_phone
                                }}</span>
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                            >
                                {{ formatPrice(order.total_amount) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    :class="
                                        getStatusBadgeClass(order.order_status)
                                    "
                                    class="px-2 py-1 text-xs rounded-full"
                                >
                                    {{ order.order_status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    :class="
                                        getPaymentStatusBadgeClass(
                                            order.payment_status
                                        )
                                    "
                                    class="px-2 py-1 text-xs rounded-full"
                                >
                                    {{ order.payment_status }}
                                </span>
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                            >
                                {{ formatDate(order.created_at) }}
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm font-medium"
                            >
                                <button
                                    @click="showOrderDetails(order)"
                                    class="text-blue-600 hover:text-blue-900 mr-3"
                                >
                                    View
                                </button>
                                <button
                                    @click="showUpdateStatusModal(order)"
                                    class="text-green-600 hover:text-green-900"
                                >
                                    Update Status
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                <div class="flex items-center justify-between">
                    <div class="flex-1 flex justify-between sm:hidden">
                        <button
                            @click="prevPage"
                            :disabled="!orders.prev_page_url"
                            class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                        >
                            Previous
                        </button>
                        <button
                            @click="nextPage"
                            :disabled="!orders.next_page_url"
                            class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                        >
                            Next
                        </button>
                    </div>
                    <div
                        class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between"
                    >
                        <div>
                            <p class="text-sm text-gray-700">
                                Showing
                                <span class="font-medium">{{
                                    orders.from
                                }}</span>
                                to
                                <span class="font-medium">{{ orders.to }}</span>
                                of
                                <span class="font-medium">{{
                                    orders.total
                                }}</span>
                                results
                            </p>
                        </div>
                        <div>
                            <nav
                                class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px"
                                aria-label="Pagination"
                            >
                                <button
                                    @click="prevPage"
                                    :disabled="!orders.prev_page_url"
                                    class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
                                >
                                    Previous
                                </button>
                                <button
                                    @click="nextPage"
                                    :disabled="!orders.next_page_url"
                                    class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
                                >
                                    Next
                                </button>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Details Modal -->
        <div
            v-if="selectedOrder"
            class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center"
        >
            <div class="bg-white rounded-lg max-w-2xl w-full p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium">Order Details</h3>
                    <button
                        @click="selectedOrder = null"
                        class="text-gray-400 hover:text-gray-500"
                    >
                        <span class="sr-only">Close</span>
                        <svg
                            class="h-6 w-6"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"
                            />
                        </svg>
                    </button>
                </div>
                <div class="space-y-4">
                    <div>
                        <h4 class="font-medium">Order Information</h4>
                        <p>Order Number: {{ selectedOrder.order_number }}</p>
                        <p>Date: {{ formatDate(selectedOrder.created_at) }}</p>
                        <p>Status: {{ selectedOrder.order_status }}</p>
                        <p>
                            Payment Status: {{ selectedOrder.payment_status }}
                        </p>
                    </div>
                    <div>
                        <h4 class="font-medium">Customer Information</h4>
                        <p>Name: {{ selectedOrder.customer_name }}</p>
                        <p>Phone: {{ selectedOrder.customer_phone }}</p>
                        <p>Email: {{ selectedOrder.customer_email }}</p>
                        <p>Address: {{ selectedOrder.customer_address }}</p>
                    </div>
                    <div>
                        <h4 class="font-medium">Order Items</h4>
                        <div class="mt-2">
                            <div
                                v-for="item in selectedOrder.items"
                                :key="item.id"
                                class="flex justify-between py-2 border-b"
                            >
                                <div>
                                    <p class="font-medium">{{ item.name }}</p>
                                    <p class="text-sm text-gray-500">
                                        Quantity: {{ item.quantity }}
                                    </p>
                                </div>
                                <p class="font-medium">
                                    {{
                                        formatPrice(item.price * item.quantity)
                                    }}
                                </p>
                            </div>
                        </div>
                        <div class="mt-4 flex justify-between font-medium">
                            <p>Total</p>
                            <p>{{ formatPrice(selectedOrder.total_amount) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Update Status Modal -->
        <div
            v-if="showStatusModal"
            class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center"
        >
            <div class="bg-white rounded-lg max-w-md w-full p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium">Update Order Status</h3>
                    <button
                        @click="showStatusModal = false"
                        class="text-gray-400 hover:text-gray-500"
                    >
                        <span class="sr-only">Close</span>
                        <svg
                            class="h-6 w-6"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"
                            />
                        </svg>
                    </button>
                </div>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700"
                            >Order Status</label
                        >
                        <select
                            v-model="updateData.order_status"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option value="pending">Pending</option>
                            <option value="processing">Processing</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700"
                            >Payment Status</label
                        >
                        <select
                            v-model="updateData.payment_status"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option value="pending">Pending</option>
                            <option value="paid">Paid</option>
                            <option value="failed">Failed</option>
                        </select>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button
                            @click="showStatusModal = false"
                            class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300"
                        >
                            Cancel
                        </button>
                        <button
                            @click="updateOrderStatus"
                            class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700"
                        >
                            Update
                        </button>
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

const orders = ref({
    data: [],
    current_page: 1,
    from: 0,
    to: 0,
    total: 0,
    prev_page_url: null,
    next_page_url: null,
});

const filters = ref({
    status: "",
    payment_status: "",
    search: "",
});

const selectedOrder = ref(null);
const showStatusModal = ref(false);
const updateData = ref({
    order_id: null,
    order_status: "",
    payment_status: "",
});

onMounted(() => {
    fetchOrders();
});

async function fetchOrders() {
    try {
        const params = new URLSearchParams();
        if (filters.value.status) params.append("status", filters.value.status);
        if (filters.value.payment_status)
            params.append("payment_status", filters.value.payment_status);
        if (filters.value.search) params.append("search", filters.value.search);
        if (orders.value.current_page > 1)
            params.append("page", orders.value.current_page);

        const response = await axios.get(
            `/api/admin/orders?${params.toString()}`
        );
        if (response.data.success) {
            orders.value = response.data.data;
        }
    } catch (error) {
        console.error("Failed to fetch orders:", error);
    }
}

function showOrderDetails(order) {
    selectedOrder.value = order;
}

function showUpdateStatusModal(order) {
    updateData.value = {
        order_id: order.id,
        order_status: order.order_status,
        payment_status: order.payment_status,
    };
    showStatusModal.value = true;
}

async function updateOrderStatus() {
    try {
        const response = await axios.patch(
            `/api/admin/orders/${updateData.value.order_id}/status`,
            {
                order_status: updateData.value.order_status,
                payment_status: updateData.value.payment_status,
            }
        );

        if (response.data.success) {
            showStatusModal.value = false;
            fetchOrders();
        }
    } catch (error) {
        console.error("Failed to update order status:", error);
    }
}

function prevPage() {
    if (orders.value.prev_page_url) {
        orders.value.current_page--;
        fetchOrders();
    }
}

function nextPage() {
    if (orders.value.next_page_url) {
        orders.value.current_page++;
        fetchOrders();
    }
}

function formatPrice(price) {
    return new Intl.NumberFormat("vi-VN", {
        style: "currency",
        currency: "VND",
    }).format(price);
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

function getStatusBadgeClass(status) {
    return {
        "bg-yellow-100 text-yellow-800": status === "pending",
        "bg-blue-100 text-blue-800": status === "processing",
        "bg-green-100 text-green-800": status === "completed",
        "bg-red-100 text-red-800": status === "cancelled",
    };
}

function getPaymentStatusBadgeClass(status) {
    return {
        "bg-yellow-100 text-yellow-800": status === "pending",
        "bg-green-100 text-green-800": status === "paid",
        "bg-red-100 text-red-800": status === "failed",
    };
}
</script>
