<template>
    <AdminLayout>
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-900">Products</h1>
            <div class="flex gap-2">
                <button
                    @click="downloadCsv"
                    class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700"
                >
                    Export CSV
                </button>
                <button
                    @click="downloadCsvTemplate"
                    class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700"
                >
                    Download CSV Template
                </button>
                <label
                    class="bg-yellow-500 text-white px-4 py-2 rounded-md hover:bg-yellow-600 cursor-pointer"
                >
                    Import CSV
                    <input
                        type="file"
                        accept=".csv , .xlsx"
                        @change="handleImportCsv"
                        class="hidden"
                    />
                </label>
                <button
                    @click="showCreateModal = true"
                    class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700"
                >
                    Add New Product
                </button>
            </div>
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
                                Options
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                            >
                                Toppings
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
                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                            >
                                <div class="flex flex-wrap gap-1">
                                    <span
                                        v-for="option in product.options"
                                        :key="option.id"
                                        class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800"
                                    >
                                        {{ option.name }}
                                        <span class="ml-1 text-gray-500"
                                            >({{ option.type }})</span
                                        >
                                    </span>
                                </div>
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                            >
                                <div class="flex flex-wrap gap-1">
                                    <span
                                        v-for="topping in product.toppings"
                                        :key="topping.id"
                                        class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800"
                                    >
                                        {{ topping.name }}
                                        <span class="ml-1 text-gray-500"
                                            >({{
                                                formatPrice(topping.price)
                                            }})</span
                                        >
                                    </span>
                                </div>
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
            class="fixed inset-0 z-50 overflow-y-auto"
        >
            <div class="flex min-h-screen items-center justify-center p-4">
                <div
                    class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                ></div>
                <div
                    class="relative w-full max-w-4xl transform overflow-hidden rounded-lg bg-white p-6 shadow-xl transition-all"
                >
                    <div class="absolute right-0 top-0 pr-4 pt-4">
                        <button
                            @click="closeModal"
                            class="rounded-md bg-white text-gray-400 hover:text-gray-500 focus:outline-none"
                        >
                            <span class="sr-only">Close</span>
                            <svg
                                class="h-6 w-6"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="1.5"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M6 18L18 6M6 6l12 12"
                                />
                            </svg>
                        </button>
                    </div>

                    <div class="max-h-[calc(100vh-8rem)] overflow-y-auto pr-2">
                        <h3
                            class="text-lg font-medium leading-6 text-gray-900 mb-4"
                        >
                            {{
                                showEditModal
                                    ? "Edit Product"
                                    : "Add New Product"
                            }}
                        </h3>

                        <form @submit.prevent="submitForm" class="space-y-4">
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700"
                                >
                                    Name <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    v-model="form.name"
                                    :class="{ 'border-red-500': errors.name }"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                />
                                <p
                                    v-if="errors.name"
                                    class="mt-1 text-sm text-red-500"
                                >
                                    {{ errors.name }}
                                </p>
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700"
                                >
                                    Description
                                    <span class="text-red-500">*</span>
                                </label>
                                <textarea
                                    v-model="form.description"
                                    :class="{
                                        'border-red-500': errors.description,
                                    }"
                                    rows="3"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                ></textarea>
                                <p
                                    v-if="errors.description"
                                    class="mt-1 text-sm text-red-500"
                                >
                                    {{ errors.description }}
                                </p>
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700"
                                >
                                    Category <span class="text-red-500">*</span>
                                </label>
                                <select
                                    v-model="form.category_id"
                                    :class="{
                                        'border-red-500': errors.category_id,
                                    }"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                >
                                    <option value="">Select a category</option>
                                    <option
                                        v-for="category in categories"
                                        :key="category.id"
                                        :value="category.id"
                                    >
                                        {{ category.name }}
                                    </option>
                                </select>
                                <p
                                    v-if="errors.category_id"
                                    class="mt-1 text-sm text-red-500"
                                >
                                    {{ errors.category_id }}
                                </p>
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700"
                                    >Sizes</label
                                >
                                <div class="space-y-2">
                                    <div
                                        v-for="(s, index) in form.size"
                                        :key="index"
                                        class="flex items-center space-x-2"
                                    >
                                        <div class="w-1/2">
                                            <input
                                                type="text"
                                                v-model="s.label"
                                                placeholder="Label"
                                                :class="{
                                                    'border-red-500':
                                                        errors[
                                                            `size.${index}.label`
                                                        ],
                                                }"
                                                class="w-full rounded-md border-gray-300 shadow-sm"
                                            />
                                            <p
                                                v-if="
                                                    errors[
                                                        `size.${index}.label`
                                                    ]
                                                "
                                                class="mt-1 text-sm text-red-500"
                                            >
                                                {{
                                                    errors[
                                                        `size.${index}.label`
                                                    ]
                                                }}
                                            </p>
                                        </div>
                                        <div class="w-1/2">
                                            <input
                                                type="number"
                                                v-model="s.price"
                                                placeholder="Price"
                                                min="0"
                                                step="1000"
                                                :class="{
                                                    'border-red-500':
                                                        errors[
                                                            `size.${index}.price`
                                                        ],
                                                }"
                                                class="w-full rounded-md border-gray-300 shadow-sm"
                                            />
                                            <p
                                                v-if="
                                                    errors[
                                                        `size.${index}.price`
                                                    ]
                                                "
                                                class="mt-1 text-sm text-red-500"
                                            >
                                                {{
                                                    errors[
                                                        `size.${index}.price`
                                                    ]
                                                }}
                                            </p>
                                        </div>
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
                                    + Add size
                                </button>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700"
                                    >
                                        Base Price
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        v-model.number="form.price"
                                        type="number"
                                        min="0"
                                        step="0.01"
                                        :class="{
                                            'border-red-500': errors.price,
                                        }"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    />
                                    <p
                                        v-if="errors.price"
                                        class="mt-1 text-sm text-red-500"
                                    >
                                        {{ errors.price }}
                                    </p>
                                </div>
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700"
                                    >
                                        Stock
                                    </label>
                                    <input
                                        v-model.number="form.stock"
                                        type="number"
                                        min="0"
                                        :class="{
                                            'border-red-500': errors.stock,
                                        }"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    />
                                    <p
                                        v-if="errors.stock"
                                        class="mt-1 text-sm text-red-500"
                                    >
                                        {{ errors.stock }}
                                    </p>
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
                                <label
                                    class="block text-sm font-medium text-gray-700"
                                    >Discount (%)</label
                                >
                                <input
                                    v-model.number="form.discount"
                                    type="number"
                                    min="0"
                                    max="100"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                />
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700"
                                >
                                    Image <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="file"
                                    accept="image/*"
                                    @change="handleImageUpload"
                                    :class="{ 'border-red-500': errors.image }"
                                    class="mt-1 block w-full"
                                />
                                <p
                                    v-if="errors.image"
                                    class="mt-1 text-sm text-red-500"
                                >
                                    {{ errors.image }}
                                </p>
                                <div v-if="form.image" class="mt-2">
                                    <img
                                        :src="form.image"
                                        class="h-24 w-24 object-cover rounded"
                                    />
                                </div>
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700"
                                    >Options</label
                                >
                                <div class="space-y-2">
                                    <div
                                        v-for="option in options"
                                        :key="option.id"
                                        class="flex items-center space-x-2"
                                    >
                                        <input
                                            type="checkbox"
                                            :id="'option-' + option.id"
                                            v-model="form.options"
                                            :value="option.id"
                                            class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        />
                                        <label
                                            :for="'option-' + option.id"
                                            class="text-sm text-gray-700"
                                        >
                                            {{ option.name }}
                                            <span class="text-xs text-gray-500"
                                                >({{ option.type }})</span
                                            >
                                            <span
                                                v-if="option.is_required"
                                                class="text-xs text-red-500"
                                                >*</span
                                            >
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700"
                                    >Toppings</label
                                >
                                <div class="space-y-2">
                                    <div
                                        v-for="topping in toppings"
                                        :key="topping.id"
                                        class="flex items-center space-x-2"
                                    >
                                        <input
                                            type="checkbox"
                                            :id="'topping-' + topping.id"
                                            v-model="form.toppings"
                                            :value="topping.id"
                                            class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        />
                                        <label
                                            :for="'topping-' + topping.id"
                                            class="text-sm text-gray-700"
                                        >
                                            {{ topping.name }}
                                            <span class="text-xs text-gray-500"
                                                >({{
                                                    formatPrice(topping.price)
                                                }})</span
                                            >
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <button
                            type="button"
                            @click="closeModal"
                            class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            @click="submitForm"
                            class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                        >
                            {{ showEditModal ? "Update" : "Create" }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
    <AdminChatBox />
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import AdminLayout from "@/layouts/AdminLayout.vue";
import AdminChatBox from "../../components/AdminChatBox.vue";
import api from "@/api";

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
const options = ref([]);
const toppings = ref([]);

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
    options: [],
    toppings: [],
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
        products.value.current_page = page;
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

// Add validation state
const errors = ref({});

// Add validation function
const validateForm = () => {
    errors.value = {};
    let isValid = true;

    if (!form.value.name) {
        errors.value.name = "Name is required";
        isValid = false;
    }

    if (!form.value.description) {
        errors.value.description = "Description is required";
        isValid = false;
    }

    if (!form.value.price || form.value.price < 0) {
        errors.value.price = "Price must be greater than 0";
        isValid = false;
    }

    if (!form.value.category_id) {
        errors.value.category_id = "Category is required";
        isValid = false;
    }

    if (!form.value.image) {
        errors.value.image = "Image is required";
        isValid = false;
    }

    if (form.value.size && form.value.size.length > 0) {
        form.value.size.forEach((size, index) => {
            if (!size.label) {
                errors.value[`size.${index}.label`] = "Size label is required";
                isValid = false;
            }
            if (!size.price || size.price < 0) {
                errors.value[`size.${index}.price`] =
                    "Size price must be greater than 0";
                isValid = false;
            }
        });
    }

    return isValid;
};

onMounted(() => {
    fetchProducts();
    fetchCategories();
    fetchOptions();
    fetchToppings();
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

        const response = await api.get(`/admin/products?${params.toString()}`);
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
        const response = await api.get("/categories");
        if (response.data.success) {
            categories.value = response.data.data;
        }
    } catch (error) {
        console.error("Failed to fetch categories:", error);
    }
}

async function fetchOptions() {
    try {
        const response = await api.get("/options");
        if (response.data.success) {
            options.value = response.data.data;
        }
    } catch (error) {
        console.error("Failed to fetch options:", error);
    }
}

async function fetchToppings() {
    try {
        const response = await api.get("/toppings");
        if (response.data.success) {
            toppings.value = response.data.data;
        }
    } catch (error) {
        console.error("Failed to fetch toppings:", error);
    }
}

function editProduct(product) {
    // Create a new form object with all product data
    const formData = {
        id: product.id,
        name: product.name,
        image: product.image,
        price: product.price,
        size: product.size,
        sold: product.sold,
        rating: product.rating,
        reviews: product.reviews,
        description: product.description,
        category_id: product.category_id,
        stock: product.stock,
        discount: product.discount,
        // Map options and toppings to their IDs
        options: product.options.map((option) => option.id),
        toppings: product.toppings.map((topping) => topping.id),
    };

    // Update the form with the new data
    form.value = formData;
    showEditModal.value = true;
}

async function deleteProduct(id) {
    if (!confirm("Are you sure you want to delete this product?")) return;

    try {
        const response = await api.delete(`/admin/products/${id}`);
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
    if (!validateForm()) {
        return;
    }

    try {
        const url = showEditModal.value
            ? `/admin/products/${form.value.id}`
            : "/admin/products";
        const method = showEditModal.value ? "put" : "post";

        const response = await api[method](url, form.value);

        if (response.data.success) {
            form.value = getDefaultForm();
            closeModal();
            fetchProducts();
        }
    } catch (error) {
        if (error.response?.data?.errors) {
            errors.value = error.response.data.errors;
        }
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
        products.value.current_page--;
        fetchProducts();
    }
}

function nextPage() {
    if (
        products.value.pagination.current_page <
        products.value.pagination.last_page
    ) {
        products.value.pagination.current_page++;
        products.value.current_page++;
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

// Export CSV
async function downloadCsv() {
    try {
        const response = await api.get("/admin/products-export", {
            responseType: "blob",
        });
        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement("a");
        link.href = url;
        link.setAttribute("download", "products_export.csv");
        document.body.appendChild(link);
        link.click();
        link.remove();
    } catch (error) {
        alert("Export CSV failed!");
        console.error(error);
    }
}

// Download CSV Template (Excel with 3 sheets)
async function downloadCsvTemplate() {
    try {
        const response = await api.get("/admin/products-import-template", {
            responseType: "blob",
        });
        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement("a");
        link.href = url;
        link.setAttribute("download", "products_import_template.xlsx");
        document.body.appendChild(link);
        link.click();
        link.remove();
    } catch (error) {
        alert("Download template failed!");
        console.error(error);
    }
}

// Import CSV
async function handleImportCsv(event) {
    const file = event.target.files[0];
    if (!file) return;
    const formData = new FormData();
    formData.append("file", file);

    try {
        const response = await api.post("/admin/products-import", formData, {
            headers: { "Content-Type": "multipart/form-data" },
        });
        if (response.data.success) {
            alert(
                "Import thành công!\nTạo mới: " +
                    response.data.data.created.length +
                    "\nCập nhật: " +
                    response.data.data.updated.length +
                    "\nLỗi: " +
                    response.data.data.errors.length
            );
            fetchProducts();
        } else {
            alert("Import thất bại: " + (response.data.message || ""));
        }
    } catch (error) {
        alert("Import CSV failed!");
        console.error(error);
    }
}
</script>

<style scoped>
/* Add custom scrollbar styles */
.max-h-\[calc\(100vh-8rem\)\] {
    scrollbar-width: thin;
    scrollbar-color: #cbd5e0 #edf2f7;
}

.max-h-\[calc\(100vh-8rem\)\]::-webkit-scrollbar {
    width: 6px;
}

.max-h-\[calc\(100vh-8rem\)\]::-webkit-scrollbar-track {
    background: #edf2f7;
    border-radius: 3px;
}

.max-h-\[calc\(100vh-8rem\)\]::-webkit-scrollbar-thumb {
    background-color: #cbd5e0;
    border-radius: 3px;
}

/* Responsive grid adjustments */
@media (max-width: 640px) {
    .grid-cols-3 {
        grid-template-columns: repeat(1, minmax(0, 1fr));
    }
}

@media (min-width: 641px) and (max-width: 1024px) {
    .grid-cols-3 {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}
</style>
