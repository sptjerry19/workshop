<template>
    <AdminLayout>
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-900">
                Quản lý Toppings
            </h1>
            <button
                @click="showCreateModal = true"
                class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700"
            >
                Thêm Topping mới
            </button>
        </div>

        <!-- Toppings Table -->
        <div class="bg-white rounded-lg shadow">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                            >
                                Tên Topping
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                            >
                                Giá
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                            >
                                Mô tả
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                            >
                                Trạng thái
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                            >
                                Thao tác
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="topping in toppings" :key="topping.id">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ topping.name }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 font-medium">
                                    {{ formatPrice(topping.price) }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div
                                    class="text-sm text-gray-500 max-w-xs truncate"
                                >
                                    {{
                                        topping.description || "Không có mô tả"
                                    }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    :class="
                                        topping.is_active
                                            ? 'bg-green-100 text-green-800'
                                            : 'bg-red-100 text-red-800'
                                    "
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                >
                                    {{
                                        topping.is_active
                                            ? "Hoạt động"
                                            : "Không hoạt động"
                                    }}
                                </span>
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm font-medium"
                            >
                                <button
                                    @click="editTopping(topping)"
                                    class="text-blue-600 hover:text-blue-900 mr-3"
                                >
                                    Sửa
                                </button>
                                <button
                                    @click="deleteTopping(topping.id)"
                                    class="text-red-600 hover:text-red-900"
                                >
                                    Xóa
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Create/Edit Modal -->
        <div
            v-if="showCreateModal || showEditModal"
            class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50"
        >
            <div
                class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white"
            >
                <div class="mt-3">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        {{ showEditModal ? "Sửa Topping" : "Thêm Topping mới" }}
                    </h3>
                    <form @submit.prevent="submitForm">
                        <div class="space-y-4">
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700"
                                    >Tên Topping *</label
                                >
                                <input
                                    type="text"
                                    v-model="form.name"
                                    required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    :class="{ 'border-red-500': errors.name }"
                                />
                                <p
                                    v-if="errors.name"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ errors.name }}
                                </p>
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700"
                                    >Giá *</label
                                >
                                <input
                                    type="number"
                                    v-model="form.price"
                                    required
                                    min="0"
                                    step="1000"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    :class="{ 'border-red-500': errors.price }"
                                />
                                <p
                                    v-if="errors.price"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ errors.price }}
                                </p>
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700"
                                    >Mô tả</label
                                >
                                <textarea
                                    v-model="form.description"
                                    rows="3"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    :class="{
                                        'border-red-500': errors.description,
                                    }"
                                ></textarea>
                                <p
                                    v-if="errors.description"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ errors.description }}
                                </p>
                            </div>

                            <div>
                                <label class="flex items-center">
                                    <input
                                        type="checkbox"
                                        v-model="form.is_active"
                                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    />
                                    <span class="ml-2 text-sm text-gray-700"
                                        >Hoạt động</span
                                    >
                                </label>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-3 mt-6">
                            <button
                                type="button"
                                @click="closeModal"
                                class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300"
                            >
                                Hủy
                            </button>
                            <button
                                type="submit"
                                class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700"
                            >
                                {{ showEditModal ? "Cập nhật" : "Tạo" }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref, onMounted } from "vue";
import AdminLayout from "@/layouts/AdminLayout.vue";
import api from "@/api";

const toppings = ref([]);
const showCreateModal = ref(false);
const showEditModal = ref(false);
const errors = ref({});

const form = ref({
    id: null,
    name: "",
    price: 0,
    description: "",
    is_active: true,
});

onMounted(() => {
    fetchToppings();
});

async function fetchToppings() {
    try {
        const response = await api.get("/admin/toppings");
        if (response.data.success) {
            toppings.value = response.data.data;
        }
    } catch (error) {
        console.error("Failed to fetch toppings:", error);
    }
}

function editTopping(topping) {
    form.value = {
        id: topping.id,
        name: topping.name,
        price: topping.price,
        description: topping.description || "",
        is_active: topping.is_active,
    };
    showEditModal.value = true;
}

async function deleteTopping(id) {
    if (!confirm("Bạn có chắc chắn muốn xóa topping này?")) return;

    try {
        const response = await api.delete(`/admin/toppings/${id}`);
        if (response.data.success) {
            fetchToppings();
        }
    } catch (error) {
        console.error("Failed to delete topping:", error);
    }
}

async function submitForm() {
    errors.value = {};

    try {
        const url = showEditModal.value
            ? `/admin/toppings/${form.value.id}`
            : "/admin/toppings";
        const method = showEditModal.value ? "put" : "post";

        const response = await api[method](url, form.value);

        if (response.data.success) {
            closeModal();
            fetchToppings();
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
    form.value = {
        id: null,
        name: "",
        price: 0,
        description: "",
        is_active: true,
    };
    errors.value = {};
}

function formatPrice(price) {
    return Number(price).toLocaleString("vi-VN") + "đ";
}
</script>
