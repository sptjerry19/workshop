<template>
    <div
        v-if="show"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
    >
        <div class="bg-white rounded-lg p-6 w-full max-w-md">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold">Chỉnh sửa thông tin</h2>
                <button
                    @click="closeModal"
                    class="text-gray-500 hover:text-gray-700"
                >
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form @submit.prevent="handleSubmit" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700"
                        >Họ và tên</label
                    >
                    <input
                        v-model="formData.name"
                        type="text"
                        required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#d80000] focus:ring-[#d80000]"
                    />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700"
                        >Số điện thoại</label
                    >
                    <input
                        v-model="formData.phone"
                        type="tel"
                        required
                        pattern="^(0|\+84)(3|5|7|8|9)\d{8}$"
                        title="Số điện thoại phải bắt đầu 0 hoặc +84, tiếp theo 3|5|7|8|9 và còn 8 số"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#d80000] focus:ring-[#d80000]"
                    />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700"
                        >Địa chỉ</label
                    >
                    <textarea
                        v-model="formData.address"
                        required
                        rows="3"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#d80000] focus:ring-[#d80000]"
                    ></textarea>
                </div>

                <div class="flex justify-end space-x-3 mt-6">
                    <button
                        type="button"
                        @click="closeModal"
                        class="px-4 py-2 border rounded-md text-gray-700 hover:bg-gray-50"
                    >
                        Hủy
                    </button>
                    <button
                        type="submit"
                        :disabled="loading"
                        class="px-4 py-2 bg-[#d80000] text-white rounded-md hover:bg-[#b80000] disabled:opacity-50"
                    >
                        {{ loading ? "Đang lưu..." : "Lưu thay đổi" }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { ref, watch } from "vue";
import api from "@/api";

const props = defineProps({
    show: Boolean,
    user: Object,
});

const emit = defineEmits(["close", "updated"]);

const formData = ref({
    name: "",
    phone: "",
    address: "",
});

const loading = ref(false);

// Watch for user prop changes to update form data
watch(
    () => props.user,
    (newUser) => {
        if (newUser) {
            formData.value = {
                name: newUser.name || "",
                phone: newUser.phone || "",
                address: newUser.address || "",
            };
        }
    },
    { immediate: true }
);

const closeModal = () => {
    emit("close");
};

const handleSubmit = async () => {
    try {
        loading.value = true;
        const response = await api.put("/user/update", formData.value);

        if (response.data.success) {
            // Update user cookie with new data
            const userStr = JSON.stringify(response.data.data);
            document.cookie = `user=${encodeURIComponent(
                userStr
            )}; path=/; max-age=86400`;

            emit("updated", response.data.data);
            closeModal();
        }
    } catch (error) {
        console.error("Error updating user:", error);
        alert(
            error.response?.data?.message ||
                "Có lỗi xảy ra khi cập nhật thông tin"
        );
    } finally {
        loading.value = false;
    }
};
</script>
