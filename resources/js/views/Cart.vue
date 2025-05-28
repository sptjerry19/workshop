<template>
    <Banner />
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12 pt-60">
        <h1 class="text-3xl font-bold mb-8">Giỏ hàng của bạn</h1>
        <div
            v-if="cartItems.length === 0"
            class="text-center text-gray-500 py-12"
        >
            Giỏ hàng trống.
        </div>
        <div v-else>
            <div class="space-y-6">
                <div
                    v-for="item in cartItems"
                    :key="item.key"
                    class="flex items-center gap-4 border-b pb-4"
                >
                    <img
                        :src="item.image"
                        :alt="item.name"
                        class="w-20 h-20 object-cover rounded"
                    />
                    <div class="flex-1">
                        <div class="font-semibold">{{ item.name }}</div>
                        <div v-if="item.size" class="text-xs text-gray-500">
                            Size: {{ item.size.label }}
                        </div>
                        <div class="text-xs text-gray-500">
                            {{ item.description }}
                        </div>
                        <div class="flex items-center gap-2 mt-2">
                            <button
                                @click="changeQuantity(item, -1)"
                                class="px-2 py-1 border rounded text-sm"
                            >
                                -
                            </button>
                            <span class="font-semibold">{{
                                item.quantity
                            }}</span>
                            <button
                                @click="changeQuantity(item, 1)"
                                class="px-2 py-1 border rounded text-sm"
                            >
                                +
                            </button>
                        </div>
                    </div>
                    <div class="flex flex-col items-end min-w-[90px]">
                        <span
                            v-if="item.discount > 0"
                            class="text-base font-bold text-[#b80000]"
                            >{{ getDiscountedPrice(item) }}</span
                        >
                        <span
                            v-if="item.discount > 0"
                            class="text-xs text-gray-400 line-through"
                            >{{ formatPrice(item.price) }}</span
                        >
                        <span
                            v-if="item.discount === 0"
                            class="text-base font-bold text-gray-800"
                            >{{ formatPrice(item.price) }}</span
                        >
                        <button
                            @click="removeItem(item)"
                            class="mt-2 text-xs text-red-500 hover:underline"
                        >
                            Xóa
                        </button>
                    </div>
                </div>
            </div>
            <div class="mt-8 flex justify-between items-center border-t pt-6">
                <div class="text-lg font-bold">Tổng cộng:</div>
                <div class="text-2xl font-bold text-[#d80000]">
                    {{ formatPrice(totalPrice) }}
                </div>
            </div>
            <div class="mt-8 flex justify-end">
                <button
                    @click="showCustomerInfoModal = true"
                    class="bg-[#d80000] text-white px-8 py-3 rounded-lg hover:bg-[#b80000] transition-colors"
                >
                    Thanh toán
                </button>
            </div>
        </div>
    </div>

    <!-- Add OrderHistory component -->
    <OrderHistory />

    <!-- Customer Info Modal -->
    <div
        v-if="showCustomerInfoModal"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
    >
        <div class="bg-white rounded-lg p-6 w-full max-w-md">
            <h2 class="text-xl font-bold mb-4">Thông tin giao hàng</h2>
            <form @submit.prevent="handleCustomerInfoSubmit">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700"
                            >Họ và tên</label
                        >
                        <input
                            v-model="customerInfo.name"
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
                            v-model="customerInfo.phone"
                            type="tel"
                            required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#d80000] focus:ring-[#d80000]"
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700"
                            >Địa chỉ</label
                        >
                        <textarea
                            v-model="customerInfo.address"
                            required
                            rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#d80000] focus:ring-[#d80000]"
                        ></textarea>
                    </div>
                </div>
                <div class="mt-6 flex justify-end space-x-3">
                    <button
                        type="button"
                        @click="showCustomerInfoModal = false"
                        class="px-4 py-2 border rounded-md text-gray-700 hover:bg-gray-50"
                    >
                        Hủy
                    </button>
                    <button
                        type="submit"
                        class="px-4 py-2 bg-[#d80000] text-white rounded-md hover:bg-[#b80000]"
                    >
                        Tiếp tục
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Payment Options Modal -->
    <div
        v-if="showPaymentOptions"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
    >
        <div class="bg-white rounded-lg p-6 w-full max-w-md">
            <h2 class="text-xl font-bold mb-4">Chọn phương thức thanh toán</h2>
            <div class="space-y-4">
                <button
                    @click="handlePaymentMethod('cod')"
                    class="w-full p-4 border rounded-lg hover:bg-gray-50 text-left"
                >
                    <div class="font-medium">
                        Thanh toán khi nhận hàng (COD)
                    </div>
                    <div class="text-sm text-gray-500">
                        Thanh toán bằng tiền mặt khi nhận hàng
                    </div>
                </button>
                <button
                    @click="handlePaymentMethod('momo')"
                    class="w-full p-4 border rounded-lg hover:bg-gray-50 text-left"
                >
                    <div class="font-medium">Thanh toán qua Momo</div>
                    <div class="text-sm text-gray-500">
                        Thanh toán nhanh chóng và an toàn qua ví Momo
                    </div>
                </button>
                <button
                    @click="handlePaymentMethod('bank')"
                    class="w-full p-4 border rounded-lg hover:bg-gray-50 text-left"
                >
                    <div class="font-medium">Thanh toán qua ngân hàng</div>
                    <div class="text-sm text-gray-500">
                        Quét mã QR để chuyển khoản ngân hàng
                    </div>
                </button>
            </div>
        </div>
    </div>

    <!-- Order Result Modal -->
    <div
        v-if="showOrderResult"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
    >
        <div class="bg-white rounded-lg p-6 w-full max-w-md">
            <div class="text-center">
                <div v-if="orderSuccess" class="mb-4">
                    <svg
                        class="mx-auto h-16 w-16 text-green-500"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M5 13l4 4L19 7"
                        ></path>
                    </svg>
                    <h2 class="text-xl font-bold text-green-600 mt-4">
                        Đặt hàng thành công!
                    </h2>
                    <p class="text-gray-600 mt-2">
                        Cảm ơn bạn đã đặt hàng. Chúng tôi sẽ liên hệ với bạn sớm
                        nhất.
                    </p>
                </div>
                <div v-else class="mb-4">
                    <svg
                        class="mx-auto h-16 w-16 text-red-500"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"
                        ></path>
                    </svg>
                    <h2 class="text-xl font-bold text-red-600 mt-4">
                        Đặt hàng không thành công!
                    </h2>
                    <p class="text-gray-600 mt-2">{{ orderErrorMessage }}</p>
                </div>
            </div>
            <div class="mt-6 flex justify-center">
                <button
                    @click="closeOrderResult"
                    class="px-6 py-2 bg-[#d80000] text-white rounded-md hover:bg-[#b80000]"
                >
                    Đóng
                </button>
            </div>
        </div>
    </div>

    <!-- Momo QR Modal -->
    <div
        v-if="showMomoQR"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
    >
        <div class="bg-white rounded-lg p-6 w-full max-w-md">
            <h2 class="text-xl font-bold mb-4">Quét mã QR để thanh toán</h2>
            <div class="text-center">
                <img :src="momoQRUrl" alt="Momo QR Code" class="mx-auto mb-4" />
                <p class="text-sm text-gray-500 mb-4">
                    Vui lòng quét mã QR bằng ứng dụng Momo để thanh toán
                </p>
                <p class="text-lg font-bold text-[#d80000]">
                    {{ formatPrice(totalPrice) }}
                </p>
            </div>
            <div class="mt-6 flex justify-end">
                <button
                    @click="showMomoQR = false"
                    class="px-4 py-2 border rounded-md text-gray-700 hover:bg-gray-50"
                >
                    Đóng
                </button>
            </div>
        </div>
    </div>

    <!-- Bank QR Modal -->
    <div
        v-if="showBankQR"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
    >
        <div class="bg-white rounded-lg p-6 w-full max-w-md">
            <h2 class="text-xl font-bold mb-4">Quét mã QR để chuyển khoản</h2>
            <div class="text-center">
                <img :src="bankQRUrl" alt="Bank QR Code" class="mx-auto mb-4" />
                <p class="text-sm text-gray-500 mb-4">
                    Vui lòng chuyển khoản đúng số tiền và nội dung
                </p>
                <p class="text-lg font-bold text-[#d80000]">
                    {{ formatPrice(totalPrice) }}
                </p>
            </div>
            <div class="mt-6 flex justify-end space-x-2">
                <button
                    @click="showBankQR = false"
                    class="px-4 py-2 border rounded-md text-gray-700 hover:bg-gray-50"
                >
                    Đóng
                </button>
                <button
                    @click="confirmBankPayment"
                    class="px-4 py-2 bg-[#078a83] text-white rounded-md hover:bg-[#056e6b]"
                >
                    Tôi đã chuyển khoản
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import Banner from "../components/Banner.vue";
import OrderHistory from "../components/OrderHistory.vue";
import axios from "axios";

const cartItems = ref([]);
const showCustomerInfoModal = ref(false);
const showPaymentOptions = ref(false);
const showMomoQR = ref(false);
const momoQRUrl = ref("");
const paymentId = ref("");
// Cookie getter
function getCookie(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return parts.pop().split(";").shift();
}
const customerInfo = ref({
    name: "",
    phone: "087654321",
    address: "tòa xyz Thành phố Hà Nội",
});
const showOrderResult = ref(false);
const orderSuccess = ref(false);
const orderErrorMessage = ref("");
const bankQRUrl = ref(""); // URL ảnh QR trả về từ server
const showBankQR = ref(false); // Hiển thị modal ngân hàng

function loadCart() {
    const data = localStorage.getItem("cartItems");
    cartItems.value = data ? JSON.parse(data) : [];
}

function saveCart() {
    localStorage.setItem("cartItems", JSON.stringify(cartItems.value));
    window.dispatchEvent(new Event("cart-updated"));
}

onMounted(() => {
    loadCart();

    const userCookie = getCookie("user");
    if (userCookie) {
        try {
            const decoded = decodeURIComponent(userCookie); // Step 2
            const user = JSON.parse(decoded); // Step 3
            customerInfo.value.name = user.name; // Step 4
        } catch (e) {
            console.error("Lỗi xử lý cookie user:", e);
        }
    }
});

function formatPrice(price) {
    if (!price) return "";
    return Number(price).toLocaleString("vi-VN") + "đ";
}

function getDiscountedPrice(item) {
    if (item.discount === 0) return formatPrice(item.price);
    if (item.discount < 100) {
        return formatPrice(item.price * (1 - item.discount / 100));
    } else {
        return formatPrice(item.price - item.discount);
    }
}

function changeQuantity(item, delta) {
    const idx = cartItems.value.findIndex((i) => i.key === item.key);
    if (idx !== -1) {
        cartItems.value[idx].quantity += delta;
        if (cartItems.value[idx].quantity <= 0) {
            cartItems.value.splice(idx, 1);
        }
        saveCart();
    }
}

function removeItem(item) {
    const idx = cartItems.value.findIndex((i) => i.key === item.key);
    if (idx !== -1) {
        cartItems.value.splice(idx, 1);
        saveCart();
    }
}

const totalPrice = computed(() => {
    return cartItems.value.reduce((sum, item) => {
        let price = item.price;
        if (item.discount === 0) return sum + price * item.quantity;
        if (item.discount < 100) {
            price = price * (1 - item.discount / 100);
        } else {
            price = price - item.discount;
        }
        return sum + price * item.quantity;
    }, 0);
});

async function handleCustomerInfoSubmit() {
    showCustomerInfoModal.value = false;
    showPaymentOptions.value = true;
}

async function handlePaymentMethod(method) {
    if (method === "bank") {
        await createBankQR();
        return;
    }

    try {
        const orderData = {
            items: cartItems.value,
            customer: customerInfo.value,
            total: totalPrice.value,
            payment_method: method,
            user_id: getCookie("user")
                ? JSON.parse(decodeURIComponent(getCookie("user"))).id
                : localStorage.getItem("chat_user_id"),
        };

        const response = await axios.post("/api/orders", orderData);

        if (response.data.success) {
            cartItems.value = [];
            saveCart();
            if (method === "momo") {
                await createMomoPayment(
                    response.data.data.id,
                    Math.round(response.data.data.total_amount)
                );
                return;
            }
            orderSuccess.value = true;
            showOrderResult.value = true;
            showPaymentOptions.value = false;
        }
    } catch (error) {
        orderSuccess.value = false;
        orderErrorMessage.value =
            error.response?.data?.message ||
            "Có lỗi xảy ra khi đặt hàng. Vui lòng thử lại.";
        showOrderResult.value = true;
        showPaymentOptions.value = false;
    }
}

async function createBankQR() {
    try {
        const response = await axios.post("/api/orders/generate", {
            order_number: generateOrderNumber(), // Tự tạo mã đơn tạm
            amount: totalPrice.value,
        });

        console.log(response.data.success);

        if (response.data.success) {
            console.log(response.data.data);
            bankQRUrl.value = response.data.data; // Giả sử trả về URL QR
            showPaymentOptions.value = false;
            showBankQR.value = true;
        }
    } catch (error) {
        console.error("Không tạo được mã QR ngân hàng:", error);
    }
}

async function confirmBankPayment() {
    try {
        const orderData = {
            items: cartItems.value,
            customer: customerInfo.value,
            total: totalPrice.value,
            payment_method: "bank",
            user_id: getCookie("user")
                ? JSON.parse(decodeURIComponent(getCookie("user"))).id
                : localStorage.getItem("chat_user_id"),
        };

        const response = await axios.post("/api/orders", orderData);

        if (response.data.success) {
            cartItems.value = [];
            saveCart();
            orderSuccess.value = true;
            showOrderResult.value = true;
            showBankQR.value = false;
        }
    } catch (error) {
        orderSuccess.value = false;
        orderErrorMessage.value =
            error.response?.data?.message ||
            "Có lỗi xảy ra khi đặt hàng. Vui lòng thử lại.";
        showOrderResult.value = true;
        showBankQR.value = false;
    }
}

function generateOrderNumber() {
    const now = new Date();
    return "ORDER_" + now.getTime(); // Hoặc thêm prefix bạn muốn
}

async function createMomoPayment(orderId, amount) {
    try {
        const paymentData = {
            amount: amount,
            order_id: orderId, // Tạo mã đơn hàng tạm
            currency: "VND",
            description: `Thanh toan don hang - ${customerInfo.value.name} - ${customerInfo.value.phone} - ${customerInfo.value.address}`,
        };

        const response = await axios.post(
            "/api/payment/momo/create",
            paymentData
        );

        if (response.data.success) {
            paymentId.value = response.data.payment.id;

            if (response.data.payment.payUrl) {
                // Redirect to Momo payment page
                window.location.href = response.data.payment.payUrl;
            } else {
                // Get QR code URL from payment response
                const qrResponse = await axios.get(
                    `/api/payment/momo/${paymentId.value}/qr`
                );
                if (qrResponse.data.success) {
                    momoQRUrl.value = qrResponse.data.qrCodeUrl;
                    showMomoQR.value = true;
                    // Start polling payment status
                    pollPaymentStatus();
                }
            }
        } else {
            alert(response.data.message || "Có lỗi xảy ra khi tạo thanh toán");
        }
    } catch (error) {
        console.error("Error creating Momo payment:", error);
        alert(
            error.response?.data?.message ||
                "Có lỗi xảy ra khi tạo thanh toán. Vui lòng thử lại."
        );
    }
}

async function pollPaymentStatus() {
    const checkStatus = async () => {
        try {
            const response = await axios.get(
                `/api/payment/momo/${paymentId.value}/status`
            );

            if (response.data.status === "completed") {
                // Thanh toán thành công, tạo đơn hàng
                const orderData = {
                    items: cartItems.value,
                    customer: customerInfo.value,
                    total: totalPrice.value,
                    payment_method: "momo",
                    payment_id: paymentId.value,
                    user_id: getCookie("user")
                        ? JSON.parse(decodeURIComponent(getCookie("user"))).id
                        : null,
                };

                const orderResponse = await axios.post(
                    "/api/orders",
                    orderData
                );

                if (orderResponse.data.success) {
                    showMomoQR.value = false;
                    cartItems.value = [];
                    saveCart();
                    orderSuccess.value = true;
                    showOrderResult.value = true;
                }
            } else if (response.data.status === "failed") {
                showMomoQR.value = false;
                orderSuccess.value = false;
                orderErrorMessage.value =
                    "Thanh toán thất bại. Vui lòng thử lại.";
                showOrderResult.value = true;
            } else {
                // Tiếp tục kiểm tra
                setTimeout(checkStatus, 3000);
            }
        } catch (error) {
            console.error("Error checking payment status:", error);
            setTimeout(checkStatus, 3000);
        }
    };

    checkStatus();
}

function closeOrderResult() {
    showOrderResult.value = false;
    if (orderSuccess.value) {
        // Redirect to home page or order history
        window.location.href = "/";
    }
}
</script>

<style scoped></style>
