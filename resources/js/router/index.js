import { createRouter, createWebHistory } from "vue-router";
import Login from "../views/auth/Login.vue";
import Register from "../views/auth/Register.vue";
import Dashboard from "../views/admin/Dashboard.vue";
import Home from "../views/Home.vue";
import About from "../views/About.vue";
import Products from "../views/Products.vue";
import News from "../views/News.vue";
import Contact from "../views/Contact.vue";
import Cart from "../views/Cart.vue";
import ProductsAdmin from "../views/admin/Products.vue";
import NewsAdmin from "../views/admin/News.vue";
import OrdersAdmin from "../views/admin/Orders.vue";
import UsersAdmin from "../views/admin/Users.vue";
import CategoriesAdmin from "../views/admin/Categories.vue";

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
        path: "/contact",
        name: "contact",
        component: Contact,
    },

    // Admin
    {
        path: "/dashboard",
        name: "dashboard",
        component: Dashboard,
        // meta: { requiresAuth: true },
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
    } else if (!to.meta.requiresAuth && isAuthenticated) {
        next("/dashboard");
    } else {
        next();
    }
});

export default router;
