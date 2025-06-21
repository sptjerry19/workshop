<template>
    <Banner />
    <div class="bg-white py-6">
        <div class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8 pt-60">
            <!-- Breadcrumbs -->
            <nav class="text-sm leading-loose mb-4">
                <router-link to="/" class="text-gray-600 hover:text-gray-800"
                    >Trang chủ</router-link
                >
                <span v-if="product?.category_name" class="mx-2">></span>
                <router-link
                    v-if="product?.category_name"
                    :to="`/products`"
                    class="text-gray-600 hover:text-gray-800"
                    >{{ product.category_name }}</router-link
                >
                <span v-if="product?.name" class="mx-2">></span>
                <span v-if="product?.name" class="text-gray-900">{{
                    product.name
                }}</span>
            </nav>

            <div v-if="loading" class="text-center">Đang tải...</div>
            <div v-else-if="error" class="text-center text-red-500">
                {{ error }}
            </div>
            <div v-else-if="product" class="lg:flex lg:gap-8">
                <!-- Product Image -->
                <div class="lg:flex-1 flex justify-center">
                    <img
                        :src="product.image"
                        :alt="product.name"
                        class="max-w-full h-auto object-cover rounded-md"
                    />
                </div>

                <!-- Product Details -->
                <div
                    class="lg:flex-1 mt-8 lg:mt-0 p-6 bg-gray-50 rounded-md shadow-sm"
                >
                    <div class="flex items-center mb-4">
                        <span class="w-4 h-5 bg-red-600 mr-1"></span>
                        <span class="w-4 h-5 bg-red-600 mr-1"></span>
                        <span class="w-4 h-5 bg-red-600"></span>
                    </div>
                    <h1 class="text-2xl font-extrabold text-gray-900 mb-4">
                        {{ product.name }}
                    </h1>

                    <!-- Product Description/Details -->
                    <p class="text-gray-700 text-sm mb-6">
                        {{ product.description }}
                    </p>

                    <!-- Updated Price and Add button -->
                    <div class="flex items-center justify-between">
                        <span
                            v-if="parseFloat(product.discount) > 0"
                            class="text-lg font-bold text-[#b80000]"
                        >
                            {{ formatPrice(calculateTotalPrice()) }}
                        </span>
                        <span v-else class="text-xl font-bold text-gray-900">
                            {{ formatPrice(calculateTotalPrice()) }}
                        </span>
                        <button
                            class="px-8 py-3 bg-gray-400 text-white text-lg font-semibold rounded-full hover:bg-gray-500 transition"
                            @click="addToCart"
                        >
                            Thêm
                        </button>
                    </div>

                    <!-- Size Selection -->
                    <div
                        v-if="product.size && product.size.length > 0"
                        class="mb-6"
                    >
                        <h3 class="text-lg font-semibold mb-3">Size</h3>
                        <div class="flex flex-wrap gap-3">
                            <button
                                v-for="size in product.size"
                                :key="size.label"
                                @click="selectedSize = size"
                                :class="[
                                    'px-4 py-2 border rounded-md transition',
                                    selectedSize?.label === size.label
                                        ? 'border-blue-500 bg-blue-50 text-blue-700'
                                        : 'border-gray-300 hover:border-gray-400',
                                ]"
                            >
                                <div class="font-medium">{{ size.label }}</div>
                                <div class="text-sm text-gray-600">
                                    +{{ formatPrice(size.price) }}
                                </div>
                            </button>
                        </div>
                    </div>

                    <!-- Options Selection -->
                    <div
                        v-if="product.options && product.options.length > 0"
                        class="mb-6"
                    >
                        <div
                            v-for="option in product.options"
                            :key="option.id"
                            class="mb-4"
                        >
                            <h3 class="text-lg font-semibold mb-3">
                                {{ option.name }}
                                <span
                                    v-if="option.is_required"
                                    class="text-red-500"
                                    >*</span
                                >
                            </h3>
                            <div class="flex flex-wrap gap-3">
                                <button
                                    v-for="value in option.values"
                                    :key="value.id"
                                    @click="
                                        selectedOptions[option.id] = value.id
                                    "
                                    :class="[
                                        'px-4 py-2 border rounded-md transition',
                                        selectedOptions[option.id] === value.id
                                            ? 'border-blue-500 bg-blue-50 text-blue-700'
                                            : 'border-gray-300 hover:border-gray-400',
                                    ]"
                                >
                                    <div class="font-medium">
                                        {{ value.value }}
                                    </div>
                                    <div
                                        v-if="parseFloat(value.price) > 0"
                                        class="text-sm text-gray-600"
                                    >
                                        +{{ formatPrice(value.price) }}
                                    </div>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Toppings Selection -->
                    <div
                        v-if="product.toppings && product.toppings.length > 0"
                        class="mb-6"
                    >
                        <h3 class="text-lg font-semibold mb-3">
                            Toppings (Tùy chọn)
                        </h3>
                        <div class="flex flex-wrap gap-3">
                            <button
                                v-for="topping in product.toppings"
                                :key="topping.id"
                                @click="toggleTopping(topping.id)"
                                :class="[
                                    'px-4 py-2 border rounded-md transition',
                                    selectedToppings.includes(topping.id)
                                        ? 'border-blue-500 bg-blue-50 text-blue-700'
                                        : 'border-gray-300 hover:border-gray-400',
                                ]"
                            >
                                <div class="font-medium">
                                    {{ topping.name }}
                                </div>
                                <div class="text-sm text-gray-600">
                                    +{{ formatPrice(topping.price) }}
                                </div>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Banner from "../../components/Banner.vue";
import { ref, onMounted, watch } from "vue";
import { useRoute } from "vue-router";
import axios from "axios";

export default {
    name: "ProductDetail",
    components: {
        Banner,
    },
    setup() {
        const route = useRoute();
        const product = ref(null);
        const loading = ref(true);
        const error = ref(null);
        const selectedSize = ref(null);
        const selectedOptions = ref({});
        const selectedToppings = ref([]);
        const cartItems = ref([]);

        // Load cart from localStorage
        function loadCart() {
            const data = localStorage.getItem("cartItems");
            cartItems.value = data ? JSON.parse(data) : [];
        }

        function saveCart() {
            localStorage.setItem("cartItems", JSON.stringify(cartItems.value));
            window.dispatchEvent(new Event("cart-updated"));
        }

        const fetchProduct = async (id) => {
            loading.value = true;
            error.value = null;
            try {
                const response = await axios.get(`/api/products/${id}`);
                if (
                    Array.isArray(response.data.data) &&
                    response.data.data.length > 0
                ) {
                    product.value = response.data.data[0];
                } else if (response.data.data) {
                    product.value = response.data.data;
                } else {
                    throw new Error("Product not found");
                }

                // Initialize selections
                if (product.value.size && product.value.size.length > 0) {
                    selectedSize.value = product.value.size[0];
                }
                if (product.value.options) {
                    product.value.options.forEach((option) => {
                        if (option.is_required && option.values.length > 0) {
                            selectedOptions.value[option.id] =
                                option.values[0].id;
                        }
                    });
                }
            } catch (err) {
                error.value = "Không thể tải thông tin sản phẩm.";
                console.error(err);
            } finally {
                loading.value = false;
            }
        };

        const formatPrice = (price) => {
            if (!price) return "0";
            return parseFloat(price).toLocaleString("vi-VN") + " ₫";
        };

        const calculateTotalPrice = () => {
            let total = parseFloat(product.value.price) || 0;

            // Add size price
            if (selectedSize.value) {
                if (product.value.discount && product.value.discount != null) {
                    if (product.value.discount <= 100) {
                        total =
                            parseFloat(
                                selectedSize.value.price -
                                    (selectedSize.value.price *
                                        product.value.discount) /
                                        100
                            ) || 0;
                    } else {
                        total =
                            parseFloat(
                                selectedSize.value.price -
                                    product.value.discount
                            ) || 0;
                    }
                } else {
                    total = parseFloat(selectedSize.value.price) || 0;
                }
            }

            // Add options price
            Object.values(selectedOptions.value).forEach((optionValueId) => {
                product.value.options.forEach((option) => {
                    const selectedValue = option.values.find(
                        (v) => v.id === optionValueId
                    );
                    if (selectedValue) {
                        total += parseFloat(selectedValue.price) || 0;
                    }
                });
            });

            // Add toppings price
            selectedToppings.value.forEach((toppingId) => {
                const topping = product.value.toppings.find(
                    (t) => t.id === toppingId
                );
                if (topping) {
                    total += parseFloat(topping.price) || 0;
                }
            });

            return total;
        };

        const toggleTopping = (toppingId) => {
            const index = selectedToppings.value.indexOf(toppingId);
            if (index > -1) {
                selectedToppings.value.splice(index, 1);
            } else {
                selectedToppings.value.push(toppingId);
            }
        };

        const addToCart = () => {
            if (!product.value) return;

            // Create unique key for this product configuration
            const sizeKey = selectedSize.value
                ? `_${selectedSize.value.label}`
                : "";
            const optionsKey =
                Object.keys(selectedOptions.value).length > 0
                    ? `_${Object.values(selectedOptions.value).join("_")}`
                    : "";
            const toppingsKey =
                selectedToppings.value.length > 0
                    ? `_${selectedToppings.value.sort().join("_")}`
                    : "";

            const key = `${product.value.id}${sizeKey}${optionsKey}${toppingsKey}`;

            // Check if this exact configuration already exists in cart
            const existingIndex = cartItems.value.findIndex(
                (item) => item.key === key
            );

            if (existingIndex !== -1) {
                // Increment quantity if already exists
                cartItems.value[existingIndex].quantity += 1;
            } else {
                // Add new item to cart
                const cartItem = {
                    key,
                    id: product.value.id,
                    name: product.value.name,
                    image: product.value.image,
                    size: selectedSize.value ? { ...selectedSize.value } : null,
                    options: Object.keys(selectedOptions.value).map(
                        (optionId) => {
                            const option = product.value.options.find(
                                (o) => o.id == optionId
                            );
                            const selectedValue = option.values.find(
                                (v) => v.id == selectedOptions.value[optionId]
                            );
                            return {
                                id: option.id,
                                name: option.name,
                                value: selectedValue.value,
                                price: selectedValue.price,
                            };
                        }
                    ),
                    toppings: selectedToppings.value.map((toppingId) => {
                        const topping = product.value.toppings.find(
                            (t) => t.id == toppingId
                        );
                        return {
                            id: topping.id,
                            name: topping.name,
                            price: topping.price,
                        };
                    }),
                    price: calculateTotalPrice(),
                    discount: parseFloat(product.value.discount) || 0,
                    quantity: 1,
                    description: product.value.description,
                };

                cartItems.value.push(cartItem);
            }

            saveCart();

            // Show success message (optional)
            alert("Đã thêm sản phẩm vào giỏ hàng!");
        };

        onMounted(() => {
            loadCart();
            fetchProduct(route.params.id);
        });

        watch(
            () => route.params.id,
            (newId) => {
                if (newId) {
                    fetchProduct(newId);
                }
            }
        );

        return {
            product,
            loading,
            error,
            selectedSize,
            selectedOptions,
            selectedToppings,
            formatPrice,
            calculateTotalPrice,
            toggleTopping,
            addToCart,
        };
    },
};
</script>
