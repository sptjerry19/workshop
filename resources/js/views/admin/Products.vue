<template>
    <AdminLayout>
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-900">Products</h1>
            <button
                @click="showCreateModal = true"
                class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700"
            >
                Add New Product
            </button>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700"
                            >Category</label
                        >
                        <select
                            v-model="filters.category"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option value="">All Categories</option>
                            <option
                                v-for="category in categories"
                                :key="category.id"
                                :value="category.id"
                            >
                                {{ category.name }}
                            </option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700"
                            >Status</label
                        >
                        <select
                            v-model="filters.status"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option value="">All</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700"
                            >Search</label
                        >
                        <input
                            type="text"
                            v-model="filters.search"
                            placeholder="Product name, description..."
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        />
                    </div>
                    <div class="flex items-end">
                        <button
                            @click="fetchProducts"
                            class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700"
                        >
                            Filter
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Products Table -->
        <div class="bg-white rounded-lg shadow">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                            >
                                Image
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                            >
                                Name
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                            >
                                Category
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                            >
                                Price
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                            >
                                Stock
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                            >
                                Status
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                            >
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="product in products.data" :key="product.id">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <img
                                    :src="product.image"
                                    :alt="product.name"
                                    class="h-12 w-12 object-cover rounded"
                                />
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ product.name }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ product.description }}
                                </div>
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                            >
                                {{ product.category_name }}
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                            >
                                {{ formatPrice(product.price) }}
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                            >
                                {{ product.stock }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    :class="getStatusBadgeClass(product.status)"
                                    class="px-2 py-1 text-xs rounded-full"
                                >
                                    {{ product.status }}
                                </span>
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm font-medium"
                            >
                                <button
                                    @click="editProduct(product)"
                                    class="text-blue-600 hover:text-blue-900 mr-3"
                                >
                                    Edit
                                </button>
                                <button
                                    @click="deleteProduct(product.id)"
                                    class="text-red-600 hover:text-red-900"
                                >
                                    Delete
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
                            :disabled="products.pagination.current_page <= 1"
                            class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                        >
                            Previous
                        </button>
                        <div class="ml-4 flex items-center space-x-1">
                            <button
                                v-for="page in pages"
                                :key="page"
                                @click="goToPage(page)"
                                :class="[
                                    'px-3 py-1 border text-sm font-medium rounded-md',
                                    page === products.pagination.current_page
                                        ? 'bg-blue-500 text-white'
                                        : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50',
                                ]"
                            >
                                {{ page }}
                            </button>
                        </div>
                        <button
                            @click="nextPage"
                            :disabled="
                                products.pagination.current_page >=
                                products.pagination.last_page
                            "
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
                                Page
                                <span class="font-medium">{{
                                    products.pagination.current_page
                                }}</span>
                                of
                                <span class="font-medium">{{
                                    products.pagination.last_page
                                }}</span>
                                — Total:
                                <span class="font-medium">{{
                                    products.pagination.total
                                }}</span>
                            </p>
                        </div>
                        <div>
                            <nav
                                class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px"
                                aria-label="Pagination"
                            >
                                <button
                                    @click="prevPage"
                                    :disabled="!products.prev_page_url"
                                    class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
                                >
                                    Previous
                                </button>
                                <button
                                    @click="nextPage"
                                    :disabled="!products.next_page_url"
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

        <!-- Create/Edit Product Modal -->
        <div
            v-if="showCreateModal || showEditModal"
            class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center"
        >
            <div class="bg-white rounded-lg max-w-2xl w-full p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium">
                        {{ showEditModal ? "Edit Product" : "Create Product" }}
                    </h3>
                    <button
                        @click="closeModal"
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
                <form @submit.prevent="submitForm" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700"
                            >Name</label
                        >
                        <input
                            type="text"
                            v-model="form.name"
                            required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700"
                            >Description</label
                        >
                        <textarea
                            v-model="form.description"
                            rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        ></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700"
                            >Category</label
                        >
                        <select
                            v-model="form.category_id"
                            required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option
                                v-for="category in categories"
                                :key="category.id"
                                :value="category.id"
                            >
                                {{ category.name }}
                            </option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700"
                            >Sizes</label
                        >
                        <div class="space-y-2">
                            <div
                                v-for="(s, index) in form.size"
                                :key="index"
                                class="flex items-center space-x-2"
                            >
                                <input
                                    type="text"
                                    v-model="s.label"
                                    placeholder="Label"
                                    class="w-1/2 rounded-md border-gray-300 shadow-sm"
                                />
                                <input
                                    type="number"
                                    v-model="s.price"
                                    placeholder="Price"
                                    min="0"
                                    step="1000"
                                    class="w-1/2 rounded-md border-gray-300 shadow-sm"
                                />
                                <button
                                    type="button"
                                    @click="removeSize(index)"
                                    class="text-red-500 hover:text-red-700"
                                >
                                    ✕
                                </button>
                            </div>
                        </div>
                        <button
                            type="button"
                            @click="addSize"
                            class="mt-2 text-blue-500 hover:underline"
                        >
                            + Thêm size
                        </button>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700"
                                >Base Price</label
                            >
                            <input
                                v-model.number="form.price"
                                type="number"
                                required
                                min="0"
                                step="0.01"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            />
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700"
                                >Stock</label
                            >
                            <input
                                v-model.number="form.stock"
                                type="number"
                                required
                                min="0"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            />
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700"
                                >Sold</label
                            >
                            <input
                                v-model.number="form.sold"
                                type="number"
                                min="0"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            />
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700"
                                >Rating</label
                            >
                            <input
                                v-model.number="form.rating"
                                type="number"
                                min="0"
                                step="0.1"
                                max="5"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            />
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700"
                                >Reviews</label
                            >
                            <input
                                v-model.number="form.reviews"
                                type="number"
                                min="0"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            />
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700"
                            >Discount (%) or Price</label
                        >
                        <input
                            v-model.number="form.discount"
                            type="number"
                            min="0"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        />
                    </div>

                    <!-- Image upload with base64 preview -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700"
                            >Image</label
                        >
                        <input
                            type="file"
                            accept="image/*"
                            @change="handleImageUpload"
                            class="mt-1 block w-full"
                        />
                        <div v-if="form.image" class="mt-2">
                            <img
                                :src="form.image"
                                class="h-24 w-24 object-cover rounded"
                            />
                        </div>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button
                            type="button"
                            @click="closeModal"
                            class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700"
                        >
                            {{ showEditModal ? "Update" : "Create" }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import AdminLayout from "@/layouts/AdminLayout.vue";
import axios from "axios";

const products = ref({
    data: [],
    pagination: {
        total: 0,
        per_page: 10,
        current_page: 1,
        last_page: 1,
    },
});

const categories = ref([]);
const filters = ref({
    category: "",
    status: "",
    search: "",
});

const showCreateModal = ref(false);
const showEditModal = ref(false);

const getDefaultForm = () => ({
    name: "",
    image: null,
    price: 0,
    size: [
        { label: "S", price: 0 },
        { label: "M", price: 0 },
        { label: "L", price: 0 },
    ],
    sold: 0,
    rating: 0,
    reviews: 0,
    description: "",
    category_id: "",
    stock: 0,
    discount: null,
});
const form = ref(getDefaultForm());

const pages = computed(() => {
    const total = products.value.pagination.last_page;
    const current = products.value.pagination.current_page;

    // Giới hạn số lượng trang hiển thị (ví dụ 5 trang quanh trang hiện tại)
    const delta = 2;
    const range = [];

    const start = Math.max(1, current - delta);
    const end = Math.min(total, current + delta);

    for (let i = start; i <= end; i++) {
        range.push(i);
    }

    return range;
});

function goToPage(page) {
    if (page !== products.value.pagination.current_page) {
        products.value.pagination.current_page = page;
        fetchProducts();
    }
}

// Thêm một size mới
const addSize = () => {
    form.value.size.push({ label: "", price: 0 });
};

// Xóa size theo index
const removeSize = (index) => {
    form.value.size.splice(index, 1);
};

onMounted(() => {
    fetchProducts();
    fetchCategories();
});

async function fetchProducts() {
    try {
        const params = new URLSearchParams();
        if (filters.value.category)
            params.append("category_id", filters.value.category);
        if (filters.value.status) params.append("status", filters.value.status);
        if (filters.value.search) params.append("q", filters.value.search);
        if (products.value.current_page > 1)
            params.append("page", products.value.pagination.current_page);

        const response = await axios.get(
            `/api/admin/products?${params.toString()}`
        );
        if (response.data.success) {
            products.value.data = response.data.data;
            products.value.pagination = response.data.pagination;
        }
    } catch (error) {
        console.error("Failed to fetch products:", error);
    }
}

async function fetchCategories() {
    try {
        const response = await axios.get("/api/categories");
        if (response.data.success) {
            categories.value = response.data.data;
        }
    } catch (error) {
        console.error("Failed to fetch categories:", error);
    }
}

function editProduct(product) {
    form.value = { ...product };
    showEditModal.value = true;
}

async function deleteProduct(id) {
    if (!confirm("Are you sure you want to delete this product?")) return;

    try {
        const response = await axios.delete(`/api/admin/products/${id}`);
        if (response.data.success) {
            fetchProducts();
        }
    } catch (error) {
        console.error("Failed to delete product:", error);
    }
}

const handleImageUpload = (event) => {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            form.value.image = e.target.result; // base64 string
        };
        reader.readAsDataURL(file);
    }
};

async function submitForm() {
    try {
        const url = showEditModal.value
            ? `/api/admin/products/${form.value.id}`
            : "/api/admin/products";
        const method = showEditModal.value ? "put" : "post";

        const response = await axios[method](url, form.value); // Gửi raw JSON

        if (response.data.success) {
            form.value = getDefaultForm();
            closeModal();
            fetchProducts();
        }
    } catch (error) {
        console.error("Failed to submit form:", error.response?.data || error);
    }
}

function closeModal() {
    showCreateModal.value = false;
    showEditModal.value = false;
}

function prevPage() {
    if (products.value.pagination.current_page > 1) {
        products.value.pagination.current_page--;
        fetchProducts();
    }
}

function nextPage() {
    if (
        products.value.pagination.current_page <
        products.value.pagination.last_page
    ) {
        products.value.pagination.current_page++;
        fetchProducts();
    }
}

function formatPrice(price) {
    return new Intl.NumberFormat("vi-VN", {
        style: "currency",
        currency: "VND",
    }).format(price);
}

function getStatusBadgeClass(status) {
    return {
        "bg-green-100 text-green-800": status === "active",
        "bg-red-100 text-red-800": status === "inactive",
    };
}
</script>
