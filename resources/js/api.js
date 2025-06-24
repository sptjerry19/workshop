import axios from "axios";
import { useLoading } from "./composables/useLoading";

const api = axios.create({
    baseURL: import.meta.env.VITE_API_BASE_URL,
    withCredentials: true,
    headers: {
        "X-Requested-With": "XMLHttpRequest",
    },
});

// Flag to prevent multiple refresh attempts
let isRefreshing = false;
let failedQueue = [];

const processQueue = (error, token = null) => {
    failedQueue.forEach((prom) => {
        if (error) {
            prom.reject(error);
        } else {
            prom.resolve(token);
        }
    });

    failedQueue = [];
};

// Function to get token from cookie
const getToken = () => {
    return document.cookie
        .split("; ")
        .find((row) => row.startsWith("token="))
        ?.split("=")[1];
};

// Function to set token in cookie
const setToken = (token) => {
    document.cookie = `token=${token}; path=/; max-age=${
        60 * 60 * 24
    }; SameSite=Lax`;
};

// Function to remove token from cookie
const removeToken = () => {
    document.cookie = "token=; path=/; expires=Thu, 01 Jan 1970 00:00:01 GMT;";
};

// Request interceptor
api.interceptors.request.use(
    (config) => {
        const { startLoading } = useLoading();
        startLoading();

        // Add token to request if available
        const token = getToken();
        if (token) {
            config.headers.Authorization = `Bearer ${token}`;
        }

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
    async (error) => {
        const { stopLoading } = useLoading();
        stopLoading();

        const originalRequest = error.config;

        // If error is 401 and we haven't tried to refresh yet
        if (error.response?.status === 401 && !originalRequest._retry) {
            if (isRefreshing) {
                // If already refreshing, add to queue
                return new Promise((resolve, reject) => {
                    failedQueue.push({ resolve, reject });
                })
                    .then((token) => {
                        originalRequest.headers.Authorization = `Bearer ${token}`;
                        return api(originalRequest);
                    })
                    .catch((err) => {
                        return Promise.reject(err);
                    });
            }

            originalRequest._retry = true;
            isRefreshing = true;

            try {
                // Try to refresh token
                const refreshResponse = await axios.post(
                    `${import.meta.env.VITE_API_BASE_URL}/refresh`,
                    {},
                    {
                        headers: {
                            Authorization: `Bearer ${getToken()}`,
                            "X-Requested-With": "XMLHttpRequest",
                        },
                    }
                );

                const newToken = refreshResponse.data.token;
                setToken(newToken);

                // Update the original request with new token
                originalRequest.headers.Authorization = `Bearer ${newToken}`;

                // Process queued requests
                processQueue(null, newToken);

                // Retry the original request
                return api(originalRequest);
            } catch (refreshError) {
                // Refresh failed, clear token and redirect to login
                processQueue(refreshError, null);
                removeToken();

                // Redirect to login page
                if (window.location.pathname !== "/login") {
                    window.location.href = "/login";
                }

                return Promise.reject(refreshError);
            } finally {
                isRefreshing = false;
            }
        }

        return Promise.reject(error);
    }
);

export default api;
