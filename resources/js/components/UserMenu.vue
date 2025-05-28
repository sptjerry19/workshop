<template>
    <div class="relative user-menu">
        <!-- When not logged in -->
        <div v-if="!isAuthenticated" class="flex items-center space-x-4">
            <router-link
                to="/login"
                class="text-gray-600 hover:text-black transition"
            >
                Đăng nhập
            </router-link>
            <div class="text-gray-600 hover:text-black transition">/</div>
            <router-link
                to="/register"
                class="text-gray-600 hover:text-black transition"
            >
                Đăng ký
            </router-link>
        </div>

        <!-- When logged in -->
        <div v-else>
            <button
                @click="toggleMenu"
                class="hover:text-black transition"
                type="button"
            >
                <i class="far fa-user-circle"></i>
            </button>
            <button
                aria-label="Shopping bag"
                class="hover:text-black transition"
                type="button"
            >
                <i class="far fa-shopping-bag"> </i>
            </button>
            <button
                aria-label="Favorites"
                class="hover:text-black transition"
                type="button"
            >
                <i class="far fa-heart"> </i>
            </button>

            <!-- Dropdown menu -->
            <div
                v-if="isMenuOpen"
                class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1"
            >
                <div class="px-4 py-2 text-sm text-gray-700 border-b">
                    {{ user?.name }}
                </div>
                <button
                    @click="showEditModal = true"
                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                >
                    Chỉnh sửa thông tin
                </button>
                <button
                    @click="handleLogout"
                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                >
                    Đăng xuất
                </button>
            </div>
        </div>

        <!-- Edit User Modal -->
        <EditUser
            :show="showEditModal"
            :user="user"
            @close="showEditModal = false"
            @updated="handleUserUpdated"
        />
    </div>
</template>

<script>
import { ref, onMounted, onUnmounted } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";
import EditUser from "./EditUser.vue";

export default {
    name: "UserMenu",
    components: {
        EditUser,
    },
    setup() {
        const router = useRouter();
        const isMenuOpen = ref(false);
        const isAuthenticated = ref(false);
        const user = ref(null);
        const showEditModal = ref(false);

        const setUserCookie = (userData) => {
            const userStr = JSON.stringify(userData);
            document.cookie = `user=${encodeURIComponent(
                userStr
            )}; path=/; max-age=86400`; // 24 hours
        };

        const checkAuth = () => {
            console.log("Checking auth...");
            const token = document.cookie
                .split("; ")
                .find((row) => row.startsWith("token="));
            console.log("Token from cookie:", token);
            isAuthenticated.value = !!token;

            if (isAuthenticated.value) {
                // First try to get user from cookie
                const userData = getUserCookie();
                console.log("User data from cookie:", userData);
                if (userData) {
                    user.value = userData;
                } else {
                    // If not in cookie, fetch from API
                    const tokenValue = token.split("=")[1];
                    axios
                        .get("/api/user", {
                            headers: {
                                Authorization: `Bearer ${tokenValue}`,
                            },
                        })
                        .then((response) => {
                            console.log("User data from API:", response.data);
                            user.value = response.data;
                            setUserCookie(response.data);
                        })
                        .catch((error) => {
                            console.error("Error fetching user:", error);
                            // If token is invalid, clear everything
                            document.cookie =
                                "token=; path=/; expires=Thu, 01 Jan 1970 00:00:01 GMT;";
                            document.cookie =
                                "user=; path=/; expires=Thu, 01 Jan 1970 00:00:01 GMT;";
                            isAuthenticated.value = false;
                            user.value = null;
                        });
                }
            }
            console.log("isAuthenticated:", isAuthenticated.value);
            console.log("user:", user.value);
        };

        const getUserCookie = () => {
            const userCookie = document.cookie
                .split("; ")
                .find((row) => row.startsWith("user="));
            console.log("Raw user cookie:", userCookie);
            if (userCookie) {
                try {
                    const decodedUser = JSON.parse(
                        decodeURIComponent(userCookie.split("=")[1])
                    );
                    console.log("Decoded user:", decodedUser);
                    return decodedUser;
                } catch (e) {
                    console.error("Error parsing user cookie:", e);
                    return null;
                }
            }
            return null;
        };

        const toggleMenu = () => {
            isMenuOpen.value = !isMenuOpen.value;
        };

        const handleLogout = async () => {
            try {
                await axios.post("/api/logout");
                document.cookie =
                    "token=; path=/; expires=Thu, 01 Jan 1970 00:00:01 GMT;";
                document.cookie =
                    "user=; path=/; expires=Thu, 01 Jan 1970 00:00:01 GMT;";
                isAuthenticated.value = false;
                user.value = null;
                router.push("/");
            } catch (error) {
                console.error("Logout failed:", error);
            }
        };

        const handleUserUpdated = (updatedUser) => {
            user.value = updatedUser;
            isMenuOpen.value = false;
        };

        // Close menu when clicking outside
        const handleClickOutside = (event) => {
            if (isMenuOpen.value && !event.target.closest(".user-menu")) {
                isMenuOpen.value = false;
            }
        };

        onMounted(() => {
            checkAuth();
            document.addEventListener("click", handleClickOutside);
        });

        onUnmounted(() => {
            document.removeEventListener("click", handleClickOutside);
        });

        return {
            isMenuOpen,
            isAuthenticated,
            user,
            showEditModal,
            toggleMenu,
            handleLogout,
            handleUserUpdated,
        };
    },
};
</script>
