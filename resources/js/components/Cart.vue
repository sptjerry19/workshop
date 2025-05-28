<template>
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold mb-6">Giỏ hàng</h1>

        <div v-if="cartItems.length === 0" class="text-center py-8">
            <p class="text-gray-500">Giỏ hàng của bạn đang trống</p>
            <router-link
                to="/"
                class="text-blue-500 hover:text-blue-700 mt-4 inline-block"
            >
                Tiếp tục mua sắm
            </router-link>
        </div>

        <div v-else>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Danh sách sản phẩm -->
                <div class="md:col-span-2">
                    <div class="bg-white rounded-lg shadow-md p-6 mb-4">
                        <table class="w-full">
                            <thead>
                                <tr>
                                    <th class="text-left font-semibold">
                                        Sản phẩm
                                    </th>
                                    <th class="text-center font-semibold">
                                        Số lượng
                                    </th>
                                    <th class="text-right font-semibold">
                                        Giá
                                    </th>
                                    <th class="text-right font-semibold">
                                        Tổng
                                    </th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="item in cartItems"
                                    :key="item.id"
                                    class="border-b"
                                >
                                    <td class="py-4">
                                        <div class="flex items-center">
                                            <img
                                                :src="item.product.image"
                                                :alt="item.product.name"
                                                class="w-16 h-16 object-cover rounded"
                                            />
                                            <div class="ml-4">
                                                <h3 class="font-medium">
                                                    {{ item.product.name }}
                                                </h3>
                                                <p class="text-gray-500">
                                                    {{
                                                        item.product.category
                                                            .name
                                                    }}
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 text-center">
                                        <div
                                            class="flex items-center justify-center"
                                        >
                                            <button
                                                @click="
                                                    updateQuantity(
                                                        item,
                                                        item.quantity - 1
                                                    )
                                                "
                                                class="px-2 py-1 border rounded-l"
                                                :disabled="item.quantity <= 1"
                                            >
                                                -
                                            </button>
                                            <span
                                                class="px-4 py-1 border-t border-b"
                                                >{{ item.quantity }}</span
                                            >
                                            <button
                                                @click="
                                                    updateQuantity(
                                                        item,
                                                        item.quantity + 1
                                                    )
                                                "
                                                class="px-2 py-1 border rounded-r"
                                            >
                                                +
                                            </button>
                                        </div>
                                    </td>
                                    <td class="py-4 text-right">
                                        {{ formatPrice(item.product.price) }}
                                    </td>
                                    <td class="py-4 text-right">
                                        {{
                                            formatPrice(
                                                item.product.price *
                                                    item.quantity
                                            )
                                        }}
                                    </td>
                                    <td class="py-4 text-right">
                                        <button
                                            @click="removeItem(item)"
                                            class="text-red-500 hover:text-red-700"
                                        >
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Tổng thanh toán -->
                <div class="md:col-span-1">
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-lg font-semibold mb-4">
                            Tổng thanh toán
                        </h2>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span>Tạm tính:</span>
                                <span>{{ formatPrice(subtotal) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Phí vận chuyển:</span>
                                <span>{{ formatPrice(shipping) }}</span>
                            </div>
                            <div class="border-t pt-2 mt-2">
                                <div class="flex justify-between font-semibold">
                                    <span>Tổng cộng:</span>
                                    <span>{{ formatPrice(total) }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Phương thức thanh toán -->
                        <div class="mt-6">
                            <h3 class="font-semibold mb-3">
                                Phương thức thanh toán
                            </h3>
                            <div class="space-y-3">
                                <label class="flex items-center">
                                    <input
                                        type="radio"
                                        v-model="selectedPaymentMethod"
                                        value="momo"
                                        class="mr-2"
                                    />
                                    <span>Ví MoMo</span>
                                </label>
                                <label class="flex items-center">
                                    <input
                                        type="radio"
                                        v-model="selectedPaymentMethod"
                                        value="cod"
                                        class="mr-2"
                                    />
                                    <span>Thanh toán khi nhận hàng (COD)</span>
                                </label>
                            </div>
                        </div>

                        <!-- QR Code Modal -->
                        <div
                            v-if="showQRModal"
                            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
                        >
                            <div
                                class="bg-white p-6 rounded-lg max-w-md w-full"
                            >
                                <h3 class="text-lg font-semibold mb-4">
                                    Quét mã QR để thanh toán
                                </h3>
                                <div class="text-center">
                                    <img
                                        :src="qrCodeUrl"
                                        alt="QR Code"
                                        class="mx-auto mb-4"
                                    />
                                    <p class="text-sm text-gray-600 mb-4">
                                        Vui lòng quét mã QR bằng ứng dụng MoMo
                                        để thanh toán
                                    </p>
                                    <div class="flex justify-end space-x-3">
                                        <button
                                            @click="cancelPayment"
                                            class="px-4 py-2 border rounded hover:bg-gray-100"
                                        >
                                            Hủy
                                        </button>
                                        <button
                                            @click="checkPaymentStatus"
                                            class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
                                        >
                                            Đã thanh toán
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button
                            @click="handlePayment"
                            class="w-full mt-6 bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600"
                            :disabled="isProcessing"
                        >
                            {{ isProcessing ? "Đang xử lý..." : "Thanh toán" }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { ref, computed } from "vue";
import axios from "axios";

export default {
    name: "Cart",
    setup() {
        const cartItems = ref([]);
        const selectedPaymentMethod = ref("momo");
        const isProcessing = ref(false);
        const showQRModal = ref(false);
        const qrCodeUrl = ref("");
        const currentPaymentId = ref(null);
        const paymentCheckInterval = ref(null);

        const subtotal = computed(() => {
            return cartItems.value.reduce((total, item) => {
                return total + item.product.price * item.quantity;
            }, 0);
        });

        const shipping = computed(() => {
            return 30000; // 30,000 VND
        });

        const total = computed(() => {
            return subtotal.value + shipping.value;
        });

        const formatPrice = (price) => {
            return new Intl.NumberFormat("vi-VN", {
                style: "currency",
                currency: "VND",
            }).format(price);
        };

        const fetchCart = async () => {
            try {
                const response = await axios.get("/api/cart");
                cartItems.value = response.data;
            } catch (error) {
                console.error("Error fetching cart:", error);
            }
        };

        const updateQuantity = async (item, quantity) => {
            if (quantity < 1) return;
            try {
                await axios.put(`/api/cart/${item.id}`, { quantity });
                item.quantity = quantity;
            } catch (error) {
                console.error("Error updating quantity:", error);
            }
        };

        const removeItem = async (item) => {
            try {
                await axios.delete(`/api/cart/${item.id}`);
                cartItems.value = cartItems.value.filter(
                    (i) => i.id !== item.id
                );
            } catch (error) {
                console.error("Error removing item:", error);
            }
        };

        const handlePayment = async () => {
            if (selectedPaymentMethod.value === "momo") {
                try {
                    isProcessing.value = true;
                    const response = await axios.post(
                        "/api/payment/momo/create",
                        {
                            amount: total.value,
                            description: `Thanh toan don hang #${Date.now()}`,
                        }
                    );

                    if (response.data.success) {
                        currentPaymentId.value = response.data.payment.id;
                        const qrResponse = await axios.get(
                            `/api/payment/momo/${currentPaymentId.value}/qr`
                        );
                        if (qrResponse.data.success) {
                            qrCodeUrl.value = qrResponse.data.qrCodeUrl;
                            showQRModal.value = true;
                            startPaymentCheck();
                        }
                    }
                } catch (error) {
                    console.error("Payment error:", error);
                    alert("Có lỗi xảy ra khi tạo thanh toán");
                } finally {
                    isProcessing.value = false;
                }
            } else {
                // Xử lý thanh toán COD
                alert("Tính năng đang được phát triển");
            }
        };

        const startPaymentCheck = () => {
            paymentCheckInterval.value = setInterval(checkPaymentStatus, 5000);
        };

        const stopPaymentCheck = () => {
            if (paymentCheckInterval.value) {
                clearInterval(paymentCheckInterval.value);
                paymentCheckInterval.value = null;
            }
        };

        const checkPaymentStatus = async () => {
            if (!currentPaymentId.value) return;

            try {
                const response = await axios.get(
                    `/api/payment/momo/${currentPaymentId.value}/status`
                );
                if (
                    response.data.success &&
                    response.data.status === "completed"
                ) {
                    stopPaymentCheck();
                    showQRModal.value = false;
                    alert("Thanh toán thành công!");
                    // Refresh cart
                    await fetchCart();
                }
            } catch (error) {
                console.error("Error checking payment status:", error);
            }
        };

        const cancelPayment = () => {
            stopPaymentCheck();
            showQRModal.value = false;
            currentPaymentId.value = null;
        };

        // Fetch cart on component mount
        fetchCart();

        return {
            cartItems,
            selectedPaymentMethod,
            isProcessing,
            showQRModal,
            qrCodeUrl,
            subtotal,
            shipping,
            total,
            formatPrice,
            updateQuantity,
            removeItem,
            handlePayment,
            checkPaymentStatus,
            cancelPayment,
        };
    },
};
</script>

<style>
/* No changes to style section */
</style>
