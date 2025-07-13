<template>
    <div
        class="min-h-screen bg-white dark:bg-gray-900 transition-colors duration-300"
    >
        <div class="max-w-4xl mx-auto p-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-8">
                Dark Mode Demo
            </h1>

            <!-- Toggle Button -->
            <div class="mb-8">
                <DarkModeToggle />
            </div>

            <!-- Content Sections -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Card 1 -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 border border-gray-200 dark:border-gray-700"
                >
                    <h2
                        class="text-xl font-semibold text-gray-900 dark:text-white mb-4"
                    >
                        Card Title
                    </h2>
                    <p class="text-gray-600 dark:text-gray-300 mb-4">
                        This is a sample card to demonstrate dark mode styling.
                        The text and background colors should change when
                        toggling between light and dark modes.
                    </p>
                    <button
                        class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md transition-colors"
                    >
                        Sample Button
                    </button>
                </div>

                <!-- Card 2 -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 border border-gray-200 dark:border-gray-700"
                >
                    <h2
                        class="text-xl font-semibold text-gray-900 dark:text-white mb-4"
                    >
                        Another Card
                    </h2>
                    <p class="text-gray-600 dark:text-gray-300 mb-4">
                        Another example card showing how different elements look
                        in dark mode. All text, borders, and backgrounds should
                        adapt properly.
                    </p>
                    <div class="flex space-x-2">
                        <button
                            class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md transition-colors"
                        >
                            Success
                        </button>
                        <button
                            class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-md transition-colors"
                        >
                            Error
                        </button>
                    </div>
                </div>
            </div>

            <!-- Form Section -->
            <div
                class="mt-8 bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 border border-gray-200 dark:border-gray-700"
            >
                <h2
                    class="text-xl font-semibold text-gray-900 dark:text-white mb-4"
                >
                    Form Example
                </h2>
                <form class="space-y-4">
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                        >
                            Name
                        </label>
                        <input
                            type="text"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Enter your name"
                        />
                    </div>
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                        >
                            Email
                        </label>
                        <input
                            type="email"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Enter your email"
                        />
                    </div>
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                        >
                            Message
                        </label>
                        <textarea
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            rows="4"
                            placeholder="Enter your message"
                        ></textarea>
                    </div>
                    <button
                        type="submit"
                        class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-md transition-colors"
                    >
                        Submit
                    </button>
                </form>
            </div>

            <!-- Status Indicators -->
            <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div
                    class="bg-green-100 dark:bg-green-900 border border-green-200 dark:border-green-800 rounded-lg p-4"
                >
                    <h3
                        class="text-green-800 dark:text-green-200 font-semibold"
                    >
                        Success
                    </h3>
                    <p class="text-green-600 dark:text-green-300 text-sm">
                        This is a success message
                    </p>
                </div>
                <div
                    class="bg-yellow-100 dark:bg-yellow-900 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4"
                >
                    <h3
                        class="text-yellow-800 dark:text-yellow-200 font-semibold"
                    >
                        Warning
                    </h3>
                    <p class="text-yellow-600 dark:text-yellow-300 text-sm">
                        This is a warning message
                    </p>
                </div>
                <div
                    class="bg-red-100 dark:bg-red-900 border border-red-200 dark:border-red-800 rounded-lg p-4"
                >
                    <h3 class="text-red-800 dark:text-red-200 font-semibold">
                        Error
                    </h3>
                    <p class="text-red-600 dark:text-red-300 text-sm">
                        This is an error message
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import DarkModeToggle from "../components/DarkModeToggle.vue";
</script>
