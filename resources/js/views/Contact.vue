<template>
    <Banner />
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 pt-60">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">Liên Hệ</h1>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                Chúng tôi luôn sẵn sàng lắng nghe ý kiến và hỗ trợ bạn
            </p>
        </div>

        <div class="grid md:grid-cols-2 gap-12">
            <!-- Contact Form -->
            <div class="bg-white rounded-lg shadow-lg p-8">
                <h2 class="text-2xl font-semibold text-gray-900 mb-6">
                    Gửi tin nhắn cho chúng tôi
                </h2>
                <form class="space-y-6" @submit.prevent="submitForm">
                    <div>
                        <label
                            for="name"
                            class="block text-sm font-medium text-gray-700 mb-1"
                            >Họ và tên</label
                        >
                        <input
                            type="text"
                            id="name"
                            v-model="name"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                        />
                    </div>
                    <div>
                        <label
                            for="email"
                            class="block text-sm font-medium text-gray-700 mb-1"
                            >Email</label
                        >
                        <input
                            type="email"
                            id="email"
                            v-model="email"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                        />
                    </div>
                    <div>
                        <label
                            for="phone"
                            class="block text-sm font-medium text-gray-700 mb-1"
                            >Số điện thoại</label
                        >
                        <input
                            type="tel"
                            id="phone"
                            v-model="phone"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                        />
                    </div>
                    <div>
                        <label
                            for="subject"
                            class="block text-sm font-medium text-gray-700 mb-1"
                            >Chủ đề</label
                        >
                        <select
                            id="subject"
                            v-model="type"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                        >
                            <option value="">Chọn chủ đề</option>
                            <option value="support">Hỗ trợ khách hàng</option>
                            <option value="order">Đơn hàng</option>
                            <option value="feedback">Góp ý</option>
                            <option value="other">Khác</option>
                        </select>
                    </div>
                    <div>
                        <label
                            for="message"
                            class="block text-sm font-medium text-gray-700 mb-1"
                            >Nội dung</label
                        >
                        <textarea
                            id="message"
                            v-model="subject"
                            rows="4"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                        ></textarea>
                    </div>
                    <button
                        type="submit"
                        class="w-full bg-green-600 text-white px-6 py-3 rounded-md hover:bg-green-700 transition-colors"
                    >
                        Gửi tin nhắn
                    </button>
                </form>
            </div>

            <!-- Contact Information -->
            <div class="space-y-8">
                <!-- Map -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d566.1321761550887!2d106.5603951805528!3d20.55374743756148!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x314a09590fa33c55%3A0xd1ecd7b03a75bbc4!2zdGjhu6d5IDc1!5e0!3m2!1svi!2s!4v1750004231710!5m2!1svi!2s"
                        width="600"
                        height="450"
                        style="border: 0"
                        allowfullscreen=""
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                    ></iframe>
                </div>

                <!-- Contact Details -->
                <div class="bg-white rounded-lg shadow-lg p-8">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-6">
                        Thông tin liên hệ
                    </h2>
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <svg
                                class="w-6 h-6 text-green-600 mt-1"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"
                                />
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"
                                />
                            </svg>
                            <div class="ml-4">
                                <h3 class="text-lg font-medium text-gray-900">
                                    Địa chỉ
                                </h3>
                                <p class="mt-1 text-gray-600">
                                    74 Quách Hữu Nghiêm
                                </p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <svg
                                class="w-6 h-6 text-green-600 mt-1"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"
                                />
                            </svg>
                            <div class="ml-4">
                                <h3 class="text-lg font-medium text-gray-900">
                                    Điện thoại
                                </h3>
                                <p class="mt-1 text-gray-600">0866612543</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <svg
                                class="w-6 h-6 text-green-600 mt-1"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                                />
                            </svg>
                            <div class="ml-4">
                                <h3 class="text-lg font-medium text-gray-900">
                                    Email
                                </h3>
                                <p class="mt-1 text-gray-600">
                                    duylinhvnu@gmail.com
                                </p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <svg
                                class="w-6 h-6 text-green-600 mt-1"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                                />
                            </svg>
                            <div class="ml-4">
                                <h3 class="text-lg font-medium text-gray-900">
                                    Giờ làm việc
                                </h3>
                                <p class="mt-1 text-gray-600">
                                    Thứ 2 - Thứ 6: 8:00 - 17:30
                                </p>
                                <p class="text-gray-600">Thứ 7: 8:00 - 12:00</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <ChatBox />
</template>

<script setup>
import { ref } from "vue";
import axios from "axios";
import Banner from "../components/Banner.vue";
import ChatBox from "../components/ChatBox.vue";

// Khởi tạo API client
const api = axios.create({
    baseURL: import.meta.env.VITE_API_BASE_URL || "http://localhost:8000/api",
    withCredentials: true,
    headers: {
        "X-Requested-With": "XMLHttpRequest",
    },
});

// Các biến form
const name = ref("");
const email = ref("");
const phone = ref("");
const subject = ref("");
const type = ref("support"); // Có thể điều chỉnh hoặc tự động set từ subject

// Gửi form
const submitForm = async () => {
    try {
        const response = await api.post("/contact", {
            name: name.value,
            email: email.value,
            phone: phone.value,
            subject: subject.value,
            type: type.value,
        });

        alert("Gửi liên hệ thành công!");
        // Reset form
        name.value = "";
        email.value = "";
        phone.value = "";
        subject.value = "";
    } catch (error) {
        console.error("Lỗi khi gửi liên hệ:", error);
        alert("Đã xảy ra lỗi, vui lòng thử lại sau.");
    }
};
</script>
