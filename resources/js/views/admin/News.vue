<template>
    <AdminLayout>
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-900">News</h1>
            <button
                @click="showCreateModal = true"
                class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700"
            >
                Add New Article
            </button>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700"
                            >Status</label
                        >
                        <select
                            v-model="filters.status"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option value="">All</option>
                            <option value="published">Published</option>
                            <option value="draft">Draft</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700"
                            >Search</label
                        >
                        <input
                            type="text"
                            v-model="filters.search"
                            placeholder="Title, content..."
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        />
                    </div>
                    <div class="flex items-end">
                        <button
                            @click="fetchNews"
                            class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700"
                        >
                            Filter
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- News Table -->
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
                                Title
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                            >
                                Summary
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                            >
                                Status
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
                        <tr v-for="article in news.data" :key="article.id">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <img
                                    :src="article.image"
                                    :alt="article.title"
                                    class="h-12 w-12 object-cover rounded"
                                />
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ article.title }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-500">
                                    {{ article.summary }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    :class="getStatusBadgeClass(article.status)"
                                    class="px-2 py-1 text-xs rounded-full"
                                >
                                    {{ article.status }}
                                </span>
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                            >
                                {{ formatDate(article.created_at) }}
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm font-medium"
                            >
                                <button
                                    @click="editArticle(article)"
                                    class="text-blue-600 hover:text-blue-900 mr-3"
                                >
                                    Edit
                                </button>
                                <button
                                    @click="deleteArticle(article.id)"
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
                            :disabled="!news.prev_page_url"
                            class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                        >
                            Previous
                        </button>
                        <button
                            @click="nextPage"
                            :disabled="!news.next_page_url"
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
                                <span class="font-medium">{{ news.from }}</span>
                                to
                                <span class="font-medium">{{ news.to }}</span>
                                of
                                <span class="font-medium">{{
                                    news.total
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
                                    :disabled="!news.prev_page_url"
                                    class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
                                >
                                    Previous
                                </button>
                                <button
                                    @click="nextPage"
                                    :disabled="!news.next_page_url"
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

        <!-- Create/Edit Article Modal -->
        <div
            v-if="showCreateModal || showEditModal"
            class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center"
        >
            <div class="bg-white rounded-lg max-w-4xl w-full p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium">
                        {{ showEditModal ? "Edit Article" : "Create Article" }}
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
                            >Title</label
                        >
                        <input
                            type="text"
                            v-model="form.title"
                            required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700"
                            >Summary</label
                        >
                        <textarea
                            v-model="form.summary"
                            rows="2"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        ></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700"
                            >Content</label
                        >
                        <textarea
                            v-model="form.content"
                            rows="10"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        ></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700"
                            >Status</label
                        >
                        <select
                            v-model="form.status"
                            required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option value="draft">Draft</option>
                            <option value="published">Published</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700"
                            >Featured Image</label
                        >
                        <input
                            type="file"
                            @change="handleImageUpload"
                            accept="image/*"
                            class="mt-1 block w-full"
                        />
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
import { ref, onMounted } from "vue";
import AdminLayout from "@/layouts/AdminLayout.vue";
import axios from "axios";

const news = ref({
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
    search: "",
});

const showCreateModal = ref(false);
const showEditModal = ref(false);
const form = ref({
    id: null,
    title: "",
    summary: "",
    content: "",
    status: "draft",
    image: null,
});

onMounted(() => {
    fetchNews();
});

async function fetchNews() {
    try {
        const params = new URLSearchParams();
        if (filters.value.status) params.append("status", filters.value.status);
        if (filters.value.search) params.append("search", filters.value.search);
        if (news.value.current_page > 1)
            params.append("page", news.value.current_page);

        const response = await axios.get(
            `/api/admin/news?${params.toString()}`
        );
        if (response.data.success) {
            news.value = response.data;
        }
    } catch (error) {
        console.error("Failed to fetch news:", error);
    }
}

function editArticle(article) {
    form.value = { ...article };
    showEditModal.value = true;
}

async function deleteArticle(id) {
    if (!confirm("Are you sure you want to delete this article?")) return;

    try {
        const response = await axios.delete(`/api/admin/news/${id}`);
        if (response.data.success) {
            fetchNews();
        }
    } catch (error) {
        console.error("Failed to delete article:", error);
    }
}

async function handleImageUpload(event) {
    const file = event.target.files[0];
    if (file) {
        // Convert image to base64
        const reader = new FileReader();
        reader.onload = (e) => {
            form.value.image = e.target.result; // This will be the base64 string
        };
        reader.readAsDataURL(file);
    }
}

async function submitForm() {
    try {
        const url = showEditModal.value
            ? `/api/admin/news/${form.value.id}`
            : "/api/admin/news";
        const method = showEditModal.value ? "put" : "post";

        const response = await axios[method](url, form.value, {
            headers: {
                "Content-Type": "application/json",
                Accept: "application/json",
            },
        });

        if (response.data.success) {
            closeModal();
            fetchNews();
        }
    } catch (error) {
        console.error("Failed to submit form:", error);
    }
}

function closeModal() {
    showCreateModal.value = false;
    showEditModal.value = false;
    form.value = {
        id: null,
        title: "",
        summary: "",
        content: "",
        status: "draft",
        image: null,
    };
}

function prevPage() {
    if (news.value.prev_page_url) {
        news.value.current_page--;
        fetchNews();
    }
}

function nextPage() {
    if (news.value.next_page_url) {
        news.value.current_page++;
        fetchNews();
    }
}

function formatDate(date) {
    return new Date(date).toLocaleDateString("vi-VN", {
        year: "numeric",
        month: "long",
        day: "numeric",
    });
}

function getStatusBadgeClass(status) {
    return {
        "bg-green-100 text-green-800": status === "published",
        "bg-yellow-100 text-yellow-800": status === "draft",
    };
}
</script>
