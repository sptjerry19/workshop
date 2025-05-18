import "./bootstrap";
import { createApp } from "vue";
import App from "./App.vue";
import "./tailwind.css";
import router from './router'
import axios from 'axios'

// Cấu hình axios
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'
axios.defaults.withCredentials = true

// Tạo Vue app
const app = createApp(App)

// Sử dụng router
app.use(router)

// Mount app
app.mount('#app')
