<template>
    <footer class="bg-[#1a112a] text-white py-12 px-6">
        <div class="max-w-7xl mx-auto">
            <div class="flex md:flex-row justify-between md:items-start gap-10">
                <!-- Company Info -->
                <div class="md:w-1/4">
                    <h2 class="text-2xl font-extrabold mb-2">CheeseCake</h2>
                    <p class="italic text-sm mb-4 text-gray-400">
                        Lorem ipsum dolor sit amet.
                    </p>
                    <div class="flex space-x-4 text-[#d9b3ff] text-lg">
                        <a
                            aria-label="X social link"
                            class="hover:text-white"
                            href="#"
                        >
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a
                            aria-label="Facebook social link"
                            class="hover:text-white"
                            href="#"
                        >
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a
                            aria-label="Instagram social link"
                            class="hover:text-white"
                            href="#"
                        >
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a
                            aria-label="Another social link"
                            class="hover:text-white"
                            href="#"
                        >
                            <i class="fas fa-gem"></i>
                            <!-- Using a placeholder icon as the butterfly icon is not standard -->
                        </a>
                    </div>
                </div>

                <!-- Link Columns -->
                <div
                    class="flex flex-wrap justify-between md:w-3/4 gap-8 md:gap-0"
                >
                    <div class="w-1/4 sm:w-1/4">
                        <h3 class="font-semibold mb-2 text-[#d9b3ff]">
                            Column One
                        </h3>
                        <ul class="text-gray-400 space-y-1 text-sm">
                            <li>
                                <a class="hover:text-white" href="#"
                                    >Link One</a
                                >
                            </li>
                            <li>
                                <a class="hover:text-white" href="#"
                                    >Link Two</a
                                >
                            </li>
                            <li>
                                <a class="hover:text-white" href="#"
                                    >Link Three</a
                                >
                            </li>
                            <li>
                                <a class="hover:text-white" href="#"
                                    >Link Four</a
                                >
                            </li>
                        </ul>
                    </div>
                    <div class="w-1/4 sm:w-1/4">
                        <h3 class="font-semibold mb-2 text-[#d9b3ff]">
                            Column Two
                        </h3>
                        <ul class="text-gray-400 space-y-1 text-sm">
                            <li>
                                <a class="hover:text-white" href="#"
                                    >Link One</a
                                >
                            </li>
                            <li>
                                <a class="hover:text-white" href="#"
                                    >Link Two</a
                                >
                            </li>
                            <li>
                                <a class="hover:text-white" href="#"
                                    >Link Three</a
                                >
                            </li>
                            <li>
                                <a class="hover:text-white" href="#"
                                    >Link Four</a
                                >
                            </li>
                        </ul>
                    </div>
                    <div class="w-1/4 sm:w-1/4">
                        <h3 class="font-semibold mb-2 text-[#d9b3ff]">
                            Column Three
                        </h3>
                        <ul class="text-gray-400 space-y-1 text-sm">
                            <li>
                                <a class="hover:text-white" href="#"
                                    >Link One</a
                                >
                            </li>
                            <li>
                                <a class="hover:text-white" href="#"
                                    >Link Two</a
                                >
                            </li>
                            <li>
                                <a class="hover:text-white" href="#"
                                    >Link Three</a
                                >
                            </li>
                            <li>
                                <a class="hover:text-white" href="#"
                                    >Link Four</a
                                >
                            </li>
                        </ul>
                    </div>
                    <div class="w-1/4 sm:w-1/4">
                        <h3 class="font-semibold mb-2 text-[#d9b3ff]">
                            Column Four
                        </h3>
                        <ul class="text-gray-400 space-y-1 text-sm">
                            <li>
                                <a class="hover:text-white" href="#"
                                    >Link One</a
                                >
                            </li>
                            <li>
                                <a class="hover:text-white" href="#"
                                    >Link Two</a
                                >
                            </li>
                            <li>
                                <a class="hover:text-white" href="#"
                                    >Link Three</a
                                >
                            </li>
                            <li>
                                <a class="hover:text-white" href="#"
                                    >Link Four</a
                                >
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Separator with Icon -->
            <div class="flex items-center justify-center my-12">
                <hr class="border-t border-[#2a1e4a] w-full md:w-1/3" />
                <div class="bg-[#2a1e4a] rounded-full p-4 mx-8">
                    <!-- Placeholder icon, replace with actual image/svg if available -->
                    <svg
                        width="40"
                        height="40"
                        viewBox="0 0 24 24"
                        fill="none"
                        xmlns="http://www.w3.org/2000/svg"
                    >
                        <path d="M12 4L20 8L12 12L4 8L12 4Z" fill="#FFFFFF" />
                        <path
                            d="M4 12L12 16L20 12"
                            stroke="#FFFFFF"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        />
                        <path
                            d="M4 16L12 20L20 16"
                            stroke="#FFFFFF"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        />
                    </svg>
                </div>
                <hr class="border-t border-[#2a1e4a] w-full md:w-1/3" />
            </div>

            <!-- Copyright and Coded by -->
            <div
                class="flex flex-col md:flex-row justify-between items-center text-xs md:text-sm text-gray-400 font-semibold pt-8 border-t border-[#2a1e4a]"
            >
                <div class="mb-4 md:mb-0">
                    © 2025 –
                    <a class="hover:text-white" href="#">Company Name</a>
                </div>
                <div class="flex items-center space-x-1">
                    <span>Coded with</span>
                    <svg
                        class="h-4 w-4 text-red-600"
                        fill="currentColor"
                        stroke="none"
                        viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg"
                    >
                        <path
                            d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41 1.01 4.5 2.09C13.09 4.01 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"
                        ></path>
                    </svg>
                    <span>by</span>
                    <span class="text-[#d9b3ff] font-bold">Jerry</span>
                </div>
            </div>
        </div>
    </footer>
</template>

<script>
export default {
    name: "Footer",
};
</script>

<style scoped>
.italic-script {
    font-family: " cursive"; /* You might need to adjust this based on available fonts */
}
</style>
