<template>
    <div class="min-h-screen bg-gray-100">
        <nav class="bg-white shadow">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <h1 class="text-xl font-bold">Dashboard</h1>
                    </div>
                    <div class="flex items-center">
                        <button @click="handleLogout"
                            class="ml-4 px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700">
                            {{ loading ? 'Đang xử lý...' : 'Đăng xuất' }}
                        </button>
                    </div>
                </div>
            </div>
        </nav>
        <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <div class="px-4 py-6 sm:px-0">
                <div class="border-4 border-dashed border-gray-200 rounded-lg h-96 p-4">
                    <h2 class="text-2xl font-bold mb-4">Chào mừng, {{ user?.name }}!</h2>
                    <p>Email của bạn: {{ user?.email }}</p>
                </div>
            </div>
        </main>
    </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

export default {
    name: 'Dashboard',
    setup() {
        const router = useRouter()
        const loading = ref(false)
        const user = ref(null)

        const fetchUser = async () => {
            try {
                const response = await axios.get('/api/user')
                user.value = response.data
            } catch (error) {
                console.error('Error fetching user:', error)
                router.push('/login')
            }
        }

        const handleLogout = async () => {
            loading.value = true
            try {
                await axios.post('/api/logout')
                document.cookie = 'token=; path=/; expires=Thu, 01 Jan 1970 00:00:01 GMT;'
                router.push('/login')
            } catch (error) {
                console.error('Logout error:', error)
            } finally {
                loading.value = false
            }
        }

        onMounted(() => {
            fetchUser()
        })

        return {
            user,
            loading,
            handleLogout
        }
    }
}
</script> 