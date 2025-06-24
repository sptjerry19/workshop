<template>
    <AdminLayout>
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-900">
                Quản lý Options
            </h1>
            <button
                @click="showCreateModal = true"
                class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700"
            >
                Thêm Option mới
            </button>
        </div>

        <!-- Options Table -->
        <div class="bg-white rounded-lg shadow">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                            >
                                Tên Option
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                            >
                                Loại
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                            >
                                Giá trị
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                            >
                                Thao tác
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="option in options" :key="option.id">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ option.name }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800"
                                >
                                    {{ getTypeText(option.type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1">
                                    <span
                                        v-for="value in option.values"
                                        :key="value.id"
                                        class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800"
                                    >
                                        {{ value.value }}
                                        <span
                                            v-if="value.price > 0"
                                            class="ml-1 text-green-600"
                                        >
                                            (+{{ formatPrice(value.price) }})
                                        </span>
                                    </span>
                                </div>
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm font-medium"
                            >
                                <button
                                    @click="editOption(option)"
                                    class="text-blue-600 hover:text-blue-900 mr-3"
                                >
                                    Sửa
                                </button>
                                <button
                                    @click="deleteOption(option.id)"
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
                        {{ showEditModal ? "Sửa Option" : "Thêm Option mới" }}
                    </h3>
                    <form @submit.prevent="submitForm">
                        <div class="space-y-4">
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700"
                                    >Tên Option *</label
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
                                    >Loại *</label
                                >
                                <select
                                    v-model="form.type"
                                    required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    :class="{ 'border-red-500': errors.type }"
                                >
                                    <option value="">Chọn loại</option>
                                    <option value="select">Select</option>
                                    <option value="radio">Radio</option>
                                    <option value="checkbox">Checkbox</option>
                                </select>
                                <p
                                    v-if="errors.type"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ errors.type }}
                                </p>
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700"
                                    >Giá trị *</label
                                >
                                <div class="space-y-2">
                                    <div
                                        v-for="(value, index) in form.values"
                                        :key="index"
                                        class="flex gap-2"
                                    >
                                        <input
                                            type="text"
                                            v-model="value.value"
                                            placeholder="Tên giá trị"
                                            required
                                            class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        />
                                        <input
                                            type="number"
                                            v-model="value.price"
                                            placeholder="Giá"
                                            min="0"
                                            step="1000"
                                            class="w-24 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        />
                                        <button
                                            type="button"
                                            @click="removeValue(index)"
                                            class="px-3 py-2 bg-red-500 text-white rounded-md hover:bg-red-600"
                                        >
                                            Xóa
                                        </button>
                                    </div>
                                    <button
                                        type="button"
                                        @click="addValue"
                                        class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600"
                                    >
                                        Thêm giá trị
                                    </button>
                                </div>
                                <p
                                    v-if="errors.values"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ errors.values }}
                                </p>
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

const options = ref([]);
const showCreateModal = ref(false);
const showEditModal = ref(false);
const errors = ref({});

const form = ref({
    id: null,
    name: "",
    type: "",
    values: [{ value: "", price: 0 }],
});

onMounted(() => {
    fetchOptions();
});

async function fetchOptions() {
    try {
        const response = await api.get("/admin/options");
        if (response.data.success) {
            options.value = response.data.data;
        }
    } catch (error) {
        console.error("Failed to fetch options:", error);
    }
}

function editOption(option) {
    form.value = {
        id: option.id,
        name: option.name,
        type: option.type,
        values: option.values.map((value) => ({
            value: value.value,
            price: value.price,
        })),
    };
    showEditModal.value = true;
}

async function deleteOption(id) {
    if (!confirm("Bạn có chắc chắn muốn xóa option này?")) return;

    try {
        const response = await api.delete(`/admin/options/${id}`);
        if (response.data.success) {
            fetchOptions();
        }
    } catch (error) {
        console.error("Failed to delete option:", error);
    }
}

function addValue() {
    form.value.values.push({ value: "", price: 0 });
}

function removeValue(index) {
    if (form.value.values.length > 1) {
        form.value.values.splice(index, 1);
    }
}

async function submitForm() {
    errors.value = {};

    try {
        const url = showEditModal.value
            ? `/admin/options/${form.value.id}`
            : "/admin/options";
        const method = showEditModal.value ? "put" : "post";

        const response = await api[method](url, form.value);

        if (response.data.success) {
            closeModal();
            fetchOptions();
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
        type: "",
        values: [{ value: "", price: 0 }],
    };
    errors.value = {};
}

function getTypeText(type) {
    const types = {
        select: "Select",
        radio: "Radio",
        checkbox: "Checkbox",
    };
    return types[type] || type;
}

function formatPrice(price) {
    return Number(price).toLocaleString("vi-VN") + "đ";
}
</script>
