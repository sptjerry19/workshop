import { createRouter, createWebHistory } from "vue-router";
import Login from "../views/auth/Login.vue";
import Register from "../views/auth/Register.vue";
import Dashboard from "../views/admin/Dashboard.vue";
import Home from "../views/Home.vue";
import About from "../views/About.vue";
import Products from "../views/Products.vue";
import News from "../views/News.vue";
import NewsDetail from "../views/NewsDetail.vue";
import Contact from "../views/Contact.vue";
import Cart from "../views/Cart.vue";
import ProductsAdmin from "../views/admin/Products.vue";
import NewsAdmin from "../views/admin/News.vue";
import OrdersAdmin from "../views/admin/Orders.vue";
import UsersAdmin from "../views/admin/Users.vue";
import CategoriesAdmin from "../views/admin/Categories.vue";
import GoogleCallback from "../views/auth/GoogleCallback.vue";
import Wishlist from "../views/Wishlist.vue";

const routes = [
    {
        path: "/",
        name: "home",
        component: Home,
        meta: { requiresAuth: false },
    },
    {
        path: "/cart",
        name: "cart",
        component: Cart,
    },
    {
        path: "/wishlist",
        name: "wishlist",
        component: Wishlist,
        meta: { requiresAuth: true },
    },
    {
        path: "/login",
        name: "login",
        component: Login,
        meta: { requiresAuth: false },
    },
    {
        path: "/register",
        name: "register",
        component: Register,
        meta: { requiresAuth: false },
    },
    {
        path: "/about",
        name: "about",
        component: About,
    },
    {
        path: "/products",
        name: "products",
        component: Products,
    },
    {
        path: "/news",
        name: "news",
        component: News,
    },
    {
        path: "/news/:id",
        name: "news-detail",
        component: NewsDetail,
    },
    {
        path: "/contact",
        name: "contact",
        component: Contact,
    },

    // Admin
    {
        path: "/dashboard",
        name: "dashboard",
        component: Dashboard,
        meta: { requiresAuth: true },
    },
    {
        path: "/products/:id",
        name: "product-detail",
        component: () => import("../views/products/ProductDetail.vue"),
        meta: { requiresAuth: false },
    },
    {
        path: "/dashboard/products",
        name: "productsadmin",
        component: ProductsAdmin,
    },
    {
        path: "/dashboard/news",
        name: "newsadmin",
        component: NewsAdmin,
    },
    {
        path: "/dashboard/orders",
        name: "ordersadmin",
        component: OrdersAdmin,
    },
    {
        path: "/dashboard/users",
        name: "usersadmin",
        component: UsersAdmin,
    },
    {
        path: "/dashboard/categories",
        name: "categoriesadmin",
        component: CategoriesAdmin,
    },
    {
        path: "/auth/google/callback",
        name: "GoogleCallback",
        component: GoogleCallback,
    },
    {
        path: "/orders/:id/tracking",
        name: "order-tracking",
        component: () => import("@/components/OrderTracking.vue"),
        meta: {
            requiresAuth: true,
        },
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

// Navigation guard
router.beforeEach((to, from, next) => {
    const token = document.cookie
        .split("; ")
        .find((row) => row.startsWith("token="));
    const isAuthenticated = !!token;

    if (to.meta.requiresAuth && !isAuthenticated) {
        next("/login");
    } else if (
        !to.meta.requiresAuth &&
        isAuthenticated &&
        (to.name === "login" || to.name === "register")
    ) {
        next("/");
    } else {
        next();
    }
});

export default router;
