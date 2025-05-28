<template>
    <div class="flex items-center justify-center min-h-screen">
        <div class="text-center">
            <div
                class="animate-spin rounded-full h-12 w-12 border-b-2 border-gray-900 mx-auto"
            ></div>
            <p class="mt-4 text-gray-600">Đang xử lý đăng nhập...</p>
        </div>
    </div>
</template>

<script>
import { onMounted } from "vue";
import { useRouter } from "vue-router";

export default {
    name: "GoogleCallback",
    setup() {
        const router = useRouter();

        onMounted(() => {
            const urlParams = new URLSearchParams(window.location.search);
            const token = urlParams.get("token");
            const user = urlParams.get("user");

            if (token && user) {
                // Set token in cookie
                document.cookie = `token=${token}; path=/; max-age=86400`;

                // Set user in cookie
                document.cookie = `user=${user}; path=/; max-age=86400`;

                // Redirect to home page
                router.push("/");
            } else {
                // If no token or user data, redirect to login
                router.push("/login");
            }
        });

        return {};
    },
};
</script>
