<template>
  <div class="min-h-screen bg-gray-100">
    <nav class="bg-white border-b border-gray-100">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex">
            <div class="flex-shrink-0 flex items-center">
              <h1 class="text-xl font-bold">{{ appName }}</h1>
            </div>
          </div>
          <div class="flex items-center">
            <template v-if="user">
              <span class="text-gray-700 mr-4">{{ user.name }}</span>
              <button @click="logout" class="text-gray-700 hover:text-gray-900">
                Logout
              </button>
            </template>
            <template v-else>
              <router-link
                to="/login"
                class="text-gray-700 hover:text-gray-900 mr-4"
                >Login</router-link
              >
              <router-link
                to="/register"
                class="text-gray-700 hover:text-gray-900"
                >Register</router-link
              >
            </template>
          </div>
        </div>
      </div>
    </nav>

    <main class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <router-view></router-view>
      </div>
    </main>
  </div>
</template>

<script>
import { ref, onMounted } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";

export default {
  name: "App",
  setup() {
    const router = useRouter();
    const user = ref(null);
    const appName = ref("Laravel Vue App");

    const checkAuth = async () => {
      try {
        const response = await axios.get("/api/user");
        user.value = response.data;
      } catch (error) {
        user.value = null;
      }
    };

    const logout = async () => {
      try {
        await axios.post("/api/logout");
        user.value = null;
        router.push("/login");
      } catch (error) {
        console.error("Logout failed:", error);
      }
    };

    onMounted(() => {
      checkAuth();
    });

    return {
      user,
      appName,
      logout,
    };
  },
};
</script>
