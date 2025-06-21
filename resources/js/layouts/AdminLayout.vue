<template>
    <div class="min-h-screen bg-gray-100">
        <!-- Sidebar -->
        <div class="fixed inset-y-0 left-0 w-64 bg-[#1a1a1a] text-white">
            <div
                class="flex items-center justify-center h-16 border-b border-gray-700"
            >
                <h1 class="text-xl font-bold">Admin Panel</h1>
            </div>
            <nav class="mt-6">
                <router-link
                    to="/dashboard"
                    class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-700 hover:text-white"
                    active-class="bg-gray-700 text-white"
                >
                    <svg
                        class="w-5 h-5 mr-3"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"
                        ></path>
                    </svg>
                    Dashboard
                </router-link>
                <router-link
                    to="/dashboard/orders"
                    class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-700 hover:text-white"
                    active-class="bg-gray-700 text-white"
                >
                    <svg
                        class="w-5 h-5 mr-3"
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
                    Orders
                </router-link>
                <router-link
                    to="/dashboard/products"
                    class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-700 hover:text-white"
                    active-class="bg-gray-700 text-white"
                >
                    <svg
                        class="w-5 h-5 mr-3"
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
                    Products
                </router-link>
                <router-link
                    to="/dashboard/categories"
                    class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-700 hover:text-white"
                    active-class="bg-gray-700 text-white"
                >
                    <svg
                        class="w-5 h-5 mr-3"
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
                    Categories
                </router-link>
                <router-link
                    to="/dashboard/news"
                    class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-700 hover:text-white"
                    active-class="bg-gray-700 text-white"
                >
                    <svg
                        class="w-5 h-5 mr-3"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"
                        ></path>
                    </svg>
                    News
                </router-link>
                <router-link
                    to="/dashboard/users"
                    class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-700 hover:text-white"
                    active-class="bg-gray-700 text-white"
                >
                    <svg
                        class="w-5 h-5 mr-3"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"
                        ></path>
                    </svg>
                    Users
                </router-link>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="ml-64">
            <!-- Top Navigation -->
            <div class="bg-white shadow">
                <div class="flex items-center justify-between h-16 px-6">
                    <h2 class="text-xl font-semibold text-gray-800">
                        {{ title }}
                    </h2>
                    <div class="flex items-center">
                        <button
                            @click="logout"
                            class="flex items-center text-gray-600 hover:text-gray-800"
                        >
                            <svg
                                class="w-5 h-5 mr-2"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"
                                ></path>
                            </svg>
                            Logout
                        </button>
                    </div>
                </div>
            </div>

            <!-- Page Content -->
            <div class="p-6">
                <slot></slot>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from "vue";
import { useRouter, useRoute } from "vue-router";
import api from "@/api";

const router = useRouter();
const route = useRoute();

const title = computed(() => {
    const path = route.path.split("/").pop();
    return path.charAt(0).toUpperCase() + path.slice(1);
});

async function logout() {
    try {
        await api.post("/logout");
        router.push("/login");
    } catch (error) {
        console.error("Logout failed:", error);
    }
}
</script>
