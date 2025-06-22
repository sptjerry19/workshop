<template>
    <footer class="bg-[#1a112a] text-white py-12 px-6">
        <div class="max-w-7xl mx-auto">
            <div class="flex md:flex-row justify-between md:items-start gap-10">
                <!-- Company Info -->
                <div class="md:w-1/4">
                    <h2 class="text-2xl font-extrabold mb-2">CheeseCake</h2>
                    <p class="italic text-sm mb-4 text-gray-400">
                        Chuyên cung cấp các loại bánh cheesecake thủ công chất
                        lượng cao, được làm từ những nguyên liệu tươi ngon nhất.
                    </p>
                    <div class="flex space-x-4 text-[#d9b3ff] text-lg">
                        <a
                            aria-label="Facebook"
                            class="hover:text-white"
                            href="https://www.facebook.com/pham.duy.linh.771687/"
                            ><i class="fab fa-facebook-f"></i
                        ></a>
                        <a
                            aria-label="Instagram"
                            class="hover:text-white"
                            href="https://www.instagram.com/jerry_1qo1"
                            ><i class="fab fa-instagram"></i
                        ></a>
                    </div>
                </div>

                <!-- Link Columns -->
                <div
                    class="flex flex-wrap justify-between md:w-3/4 gap-8 md:gap-0"
                >
                    <!-- Sản phẩm -->
                    <div class="w-1/4 sm:w-1/4">
                        <h3 class="font-semibold mb-2 text-[#d9b3ff]">
                            Sản phẩm
                        </h3>
                        <ul class="text-gray-400 space-y-1 text-sm">
                            <li>
                                <a class="hover:text-white" href="#"
                                    >Bánh phô mai Nhật Bản</a
                                >
                            </li>
                            <li>
                                <a class="hover:text-white" href="#"
                                    >Bánh vị trà xanh</a
                                >
                            </li>
                            <li>
                                <a class="hover:text-white" href="#"
                                    >Combo quà tặng</a
                                >
                            </li>
                            <li>
                                <a class="hover:text-white" href="#"
                                    >Topping đặc biệt</a
                                >
                            </li>
                        </ul>
                    </div>

                    <!-- Hỗ trợ khách hàng -->
                    <div class="w-1/4 sm:w-1/4">
                        <h3 class="font-semibold mb-2 text-[#d9b3ff]">
                            Hỗ trợ
                        </h3>
                        <ul class="text-gray-400 space-y-1 text-sm">
                            <li>
                                <a class="hover:text-white" href="#"
                                    >Chính sách giao hàng</a
                                >
                            </li>
                            <li>
                                <a class="hover:text-white" href="#"
                                    >Chính sách đổi trả</a
                                >
                            </li>
                            <li>
                                <a class="hover:text-white" href="#"
                                    >Hướng dẫn đặt hàng</a
                                >
                            </li>
                            <li>
                                <a class="hover:text-white" href="#"
                                    >Câu hỏi thường gặp</a
                                >
                            </li>
                        </ul>
                    </div>

                    <!-- Về chúng tôi -->
                    <div class="w-1/4 sm:w-1/4">
                        <h3 class="font-semibold mb-2 text-[#d9b3ff]">
                            Về chúng tôi
                        </h3>
                        <ul class="text-gray-400 space-y-1 text-sm">
                            <li>
                                <router-link
                                    to="/about"
                                    class="hover:text-white"
                                    >Giới thiệu</router-link
                                >
                            </li>
                            <li>
                                <router-link to="/news" class="hover:text-white"
                                    >Tin tức</router-link
                                >
                            </li>
                            <li>
                                <router-link
                                    to="/story"
                                    class="hover:text-white"
                                    >Câu chuyện</router-link
                                >
                            </li>
                            <li>
                                <router-link
                                    to="/contact"
                                    class="hover:text-white"
                                    >Liên hệ</router-link
                                >
                            </li>
                        </ul>
                    </div>

                    <!-- Thông tin liên hệ -->
                    <div class="w-1/4 sm:w-1/4">
                        <h3 class="font-semibold mb-2 text-[#d9b3ff]">
                            Liên hệ
                        </h3>
                        <ul class="text-gray-400 space-y-1 text-sm">
                            <li>
                                Hotline:
                                <a class="hover:text-white" href="tel:19001234"
                                    >0866612543</a
                                >
                            </li>
                            <li>
                                Email:
                                <a
                                    class="hover:text-white"
                                    href="mailto:support@cheesecake.vn"
                                    >duylinhvnu@gmail.com</a
                                >
                            </li>
                            <li>
                                Địa chỉ: 74 Quách Hữu Nghiêm, Diêm Điền, Thái
                                Thụy, Thái Bình
                            </li>
                            <li>Giờ mở cửa: 8:00 – 22:00 mỗi ngày</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Separator with Icon -->
            <div class="flex items-center justify-center my-12">
                <hr class="border-t border-[#2a1e4a] w-full md:w-1/3" />
                <div class="bg-[#2a1e4a] rounded-full p-4 mx-8">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none">
                        <path d="M12 4L20 8L12 12L4 8L12 4Z" fill="#FFFFFF" />
                        <path
                            d="M4 12L12 16L20 12"
                            stroke="#FFFFFF"
                            stroke-width="2"
                        />
                        <path
                            d="M4 16L12 20L20 16"
                            stroke="#FFFFFF"
                            stroke-width="2"
                        />
                    </svg>
                </div>
                <hr class="border-t border-[#2a1e4a] w-full md:w-1/3" />
            </div>

            <!-- Copyright -->
            <div
                class="flex flex-col md:flex-row justify-between items-center text-xs md:text-sm text-gray-400 font-semibold"
            >
                <div class="mb-4 md:mb-0">
                    © 2025 –
                    <a class="hover:text-white" href="#">Cheese Cake</a>
                </div>
                <div class="flex items-center space-x-1">
                    <span>Coded with</span>
                    <svg
                        class="h-4 w-4 text-red-600"
                        fill="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5
                        2 5.42 4.42 3 7.5 3c1.74 0 3.41 1.01 4.5
                        2.09C13.09 4.01 14.76 3 16.5 3
                        19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55
                        11.54L12 21.35z"
                        />
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
