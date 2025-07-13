# Dark Mode Implementation

## Tổng quan

Đã triển khai tính năng dark mode cho ứng dụng Vue.js với các tính năng sau:

### ✅ Tính năng đã hoàn thành:

1. **Toggle Button**: Nút chuyển đổi dark/light mode ở góc trên bên phải
2. **LocalStorage**: Lưu trữ preference của user
3. **System Preference**: Tự động detect system preference khi lần đầu vào
4. **Smooth Transitions**: Animation mượt mà khi chuyển đổi
5. **Responsive**: Hoạt động tốt trên mobile và desktop
6. **Vue.js Native**: Sử dụng `v-if` và `:class` thay vì Tailwind dark mode

### 🎨 Components đã được cập nhật:

-   ✅ `App.vue` - Container chính
-   ✅ `Home.vue` - Trang chủ
-   ✅ `Banner.vue` - Header navigation
-   ✅ `ProductSection.vue` - Section sản phẩm
-   ✅ `PromotionSection.vue` - Section khuyến mãi
-   ✅ `ProductCard.vue` - Card sản phẩm
-   ✅ `ChatBox.vue` - Chat widget
-   ✅ `Footer.vue` - Footer (đã có màu tối sẵn)

### 🔧 Files được tạo/cập nhật:

1. **`resources/js/composables/useDarkMode.js`** - Logic quản lý dark mode
2. **`resources/js/components/DarkModeToggle.vue`** - Component toggle button
3. **`resources/js/tailwind.css`** - CSS bổ sung cho dark mode
4. **`resources/js/views/DarkModeDemo.vue`** - Trang demo dark mode
5. **`test-dark-mode-vue.html`** - File test Vue.js với v-if/:class

## Cách sử dụng

### 1. Toggle Dark Mode

-   Click vào nút toggle (icon mặt trời/mặt trăng) ở góc trên bên phải
-   Preference sẽ được lưu vào localStorage

### 2. Thêm Dark Mode cho component mới

```vue
<template>
    <div
        class="transition-colors duration-300"
        :class="isDark ? 'bg-gray-900' : 'bg-white'"
    >
        <h1 :class="isDark ? 'text-white' : 'text-gray-900'">Title</h1>
        <p :class="isDark ? 'text-gray-300' : 'text-gray-600'">Content</p>
    </div>
</template>

<script setup>
import { useDarkMode } from "../composables/useDarkMode";

const { isDark } = useDarkMode();
</script>
```

### 3. Sử dụng composable trong component

```vue
<script setup>
import { useDarkMode } from "../composables/useDarkMode";

const { isDark, toggleDarkMode } = useDarkMode();
</script>
```

## Color Palette

### Light Mode

-   Background: `bg-white`
-   Text: `text-gray-900`
-   Secondary Text: `text-gray-600`
-   Borders: `border-gray-200`

### Dark Mode

-   Background: `bg-gray-900`
-   Text: `text-white`
-   Secondary Text: `text-gray-300`
-   Borders: `border-gray-700`

## Demo

Để xem demo đầy đủ, truy cập route `/dark-mode-demo` (cần thêm vào router).

## Lưu ý

1. **Performance**: Sử dụng CSS transitions để tạo hiệu ứng mượt mà
2. **Accessibility**: Tự động detect system preference
3. **Persistence**: Lưu trữ preference trong localStorage
4. **Vue.js Native**: Sử dụng `v-if` và `:class` thay vì Tailwind dark mode để tương thích tốt hơn

## Troubleshooting

### Nếu dark mode không hoạt động:

1. Kiểm tra `useDarkMode` composable được import đúng
2. Kiểm tra `isDark` reactive variable được sử dụng trong template
3. Kiểm tra localStorage có key `darkMode`
4. Kiểm tra console có lỗi JavaScript

### Để test:

```javascript
// Trong browser console
localStorage.setItem("darkMode", "true"); // Force dark mode
localStorage.setItem("darkMode", "false"); // Force light mode
localStorage.removeItem("darkMode"); // Reset to system preference
```

## Files Test

-   **`test-dark-mode.html`** - Test với Tailwind dark mode
-   **`test-dark-mode-vue.html`** - Test với Vue.js v-if/:class
