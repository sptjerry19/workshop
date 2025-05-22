<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-100">
        <div
            class="max-w-md w-full space-y-8 p-8 bg-white rounded-lg shadow-md"
        >
            <div>
                <h2
                    class="mt-6 text-center text-3xl font-extrabold text-gray-900"
                >
                    Đăng ký tài khoản
                </h2>
            </div>
            <form class="mt-8 space-y-6" @submit.prevent="handleRegister">
                <div class="rounded-md shadow-sm -space-y-px">
                    <div>
                        <label for="name" class="sr-only">Họ tên</label>
                        <input
                            v-model="form.name"
                            id="name"
                            name="name"
                            type="text"
                            required
                            class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                            placeholder="Họ tên"
                        />
                    </div>
                    <div>
                        <label for="email" class="sr-only">Email</label>
                        <input
                            v-model="form.email"
                            id="email"
                            name="email"
                            type="email"
                            required
                            class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                            placeholder="Email"
                        />
                    </div>
                    <div>
                        <label for="password" class="sr-only">Mật khẩu</label>
                        <input
                            v-model="form.password"
                            id="password"
                            name="password"
                            type="password"
                            required
                            class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                            placeholder="Mật khẩu"
                        />
                    </div>
                    <div>
                        <label for="password_confirmation" class="sr-only"
                            >Xác nhận mật khẩu</label
                        >
                        <input
                            v-model="form.password_confirmation"
                            id="password_confirmation"
                            name="password_confirmation"
                            type="password"
                            required
                            class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                            placeholder="Xác nhận mật khẩu"
                        />
                    </div>
                </div>

                <div>
                    <button
                        type="submit"
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                        {{ loading ? "Đang xử lý..." : "Đăng ký" }}
                    </button>
                </div>
            </form>
            <div class="text-center">
                <router-link
                    to="/login"
                    class="font-medium text-indigo-600 hover:text-indigo-500"
                >
                    Đã có tài khoản? Đăng nhập
                </router-link>
            </div>
        </div>
    </div>
</template>

<script>
import { ref } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";

export default {
    name: "Register",
    setup() {
        const router = useRouter();
        const loading = ref(false);
        const form = ref({
            name: "",
            email: "",
            password: "",
            password_confirmation: "",
        });

        const handleRegister = async () => {
            loading.value = true;
            try {
                const response = await axios.post("/api/register", form.value);
                document.cookie = `token=${response.data.token}; path=/; max-age=86400`;
                // Lưu thông tin user vào cookie
                const userStr = JSON.stringify(response.data.user);
                document.cookie = `user=${encodeURIComponent(
                    userStr
                )}; path=/; max-age=86400`;
                router.push("/");
            } catch (error) {
                alert(error.response?.data?.message || "Đăng ký thất bại");
            } finally {
                loading.value = false;
            }
        };

        return {
            form,
            loading,
            handleRegister,
        };
    },
};
</script>
