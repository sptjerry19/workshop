<template>
    <Banner />
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">
                Tin Tức & Blog
            </h1>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                Cập nhật những tin tức mới nhất về sức khỏe, dinh dưỡng và các
                sản phẩm của chúng tôi
            </p>
        </div>

        <!-- Featured Post -->
        <div class="mb-12">
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <img
                    src="https://images.unsplash.com/photo-1542838132-92c53300491e?ixlib=rb-1.2.1&auto=format&fit=crop&w=1200&h=600&q=80"
                    alt="Featured post"
                    class="w-full h-96 object-cover"
                />
                <div class="p-8">
                    <div class="flex items-center text-sm text-gray-500 mb-4">
                        <span>20 Tháng 3, 2024</span>
                        <span class="mx-2">•</span>
                        <span>5 phút đọc</span>
                    </div>
                    <h2 class="text-3xl font-bold text-gray-900 mb-4">
                        Lợi ích của việc ăn trái cây và rau củ mỗi ngày
                    </h2>
                    <p class="text-gray-600 mb-6">
                        Trái cây và rau củ là nguồn cung cấp vitamin, khoáng
                        chất và chất xơ quan trọng cho cơ thể. Việc tiêu thụ đều
                        đặn các loại thực phẩm này giúp tăng cường sức khỏe và
                        phòng ngừa bệnh tật.
                    </p>
                    <a
                        href="#"
                        class="inline-flex items-center text-green-600 hover:text-green-700"
                    >
                        Đọc thêm
                        <svg
                            class="ml-2 w-4 h-4"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 5l7 7-7 7"
                            />
                        </svg>
                    </a>
                </div>
            </div>
        </div>

        <!-- Recent Posts Grid -->
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <div
                v-for="i in 6"
                :key="i"
                class="bg-white rounded-lg shadow-md overflow-hidden"
            >
                <img
                    src="https://images.unsplash.com/photo-1542838132-92c53300491e?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&h=300&q=80"
                    alt="Blog post"
                    class="w-full h-48 object-cover"
                />
                <div class="p-6">
                    <div class="flex items-center text-sm text-gray-500 mb-4">
                        <span>15 Tháng 3, 2024</span>
                        <span class="mx-2">•</span>
                        <span>3 phút đọc</span>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">
                        Cách chọn và bảo quản trái cây tươi ngon
                    </h3>
                    <p class="text-gray-600 mb-4">
                        Hướng dẫn chi tiết về cách chọn và bảo quản các loại
                        trái cây để giữ được độ tươi ngon lâu nhất.
                    </p>
                    <a
                        href="#"
                        class="text-green-600 hover:text-green-700 font-medium"
                    >
                        Đọc thêm →
                    </a>
                </div>
            </div>
        </div>

        <!-- Newsletter -->
        <div class="mt-16 bg-green-50 rounded-lg p-8">
            <div class="max-w-2xl mx-auto text-center">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">
                    Đăng ký nhận tin
                </h2>
                <p class="text-gray-600 mb-6">
                    Nhận thông tin về các bài viết mới nhất và khuyến mãi đặc
                    biệt
                </p>
                <form class="flex gap-4 max-w-md mx-auto">
                    <input
                        type="email"
                        placeholder="Email của bạn"
                        class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                    />
                    <button
                        type="submit"
                        class="bg-green-600 text-white px-6 py-2 rounded-md hover:bg-green-700 transition-colors"
                    >
                        Đăng ký
                    </button>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
// Component logic here
import Banner from "../components/Banner.vue";
</script>
