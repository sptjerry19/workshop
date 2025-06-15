import axios from "axios";
import { useLoading } from "./composables/useLoading";

const api = axios.create({
    baseURL: import.meta.env.VITE_API_BASE_URL,
    withCredentials: true,
    headers: {
        "X-Requested-With": "XMLHttpRequest",
    },
});

// Request interceptor
api.interceptors.request.use(
    (config) => {
        const { startLoading } = useLoading();
        startLoading();
        return config;
    },
    (error) => {
        const { stopLoading } = useLoading();
        stopLoading();
        return Promise.reject(error);
    }
);

// Response interceptor
api.interceptors.response.use(
    (response) => {
        const { stopLoading } = useLoading();
        stopLoading();
        return response;
    },
    (error) => {
        const { stopLoading } = useLoading();
        stopLoading();
        return Promise.reject(error);
    }
);

export default api;
