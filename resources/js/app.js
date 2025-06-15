import "./bootstrap";
import { createApp } from "vue";
import App from "./App.vue";
import "./tailwind.css";
import router from "./router";
import axios from "axios";
import Editor from "@tinymce/tinymce-vue";

// Cấu hình axios
axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";
axios.defaults.withCredentials = true;

// Tạo Vue app
const app = createApp(App);

// Đăng ký TinyMCE Editor component
app.component("editor", Editor);

// Sử dụng router
app.use(router);

// Mount app
app.mount("#app");
