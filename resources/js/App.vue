<template>
    <div>
        <router-view></router-view>
        <Notification />
        <LoadingOverlay />
    </div>
</template>

<script setup>
// Sẽ import các component sau
// Import the functions you need from the SDKs you need
import { initializeApp } from "firebase/app";
import { getAnalytics } from "firebase/analytics";
import Notification from "./components/Notification.vue";
import LoadingOverlay from "./components/LoadingOverlay.vue";
import Echo from "laravel-echo";
import Pusher from "pusher-js";
// TODO: Add SDKs for Firebase products that you want to use
// https://firebase.google.com/docs/web/setup#available-libraries

// Configure Pusher
window.Pusher = Pusher;

// Initialize Laravel Echo
window.Echo = new Echo({
    broadcaster: "pusher",
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: true,
});

// Firebase configuration
const firebaseConfig = {
    apiKey: "AIzaSyCvhBj3HKY6kTTzKvd4ctizmShph_gQrcg",
    authDomain: "yumic-83e7d.firebaseapp.com",
    projectId: "yumic-83e7d",
    storageBucket: "yumic-83e7d.firebasestorage.app",
    messagingSenderId: "939100829101",
    appId: "1:939100829101:web:9c3d1ff11efcf1d3e69d24",
    measurementId: "G-EFX3EM988G",
};

// Initialize Firebase
const app = initializeApp(firebaseConfig);
const analytics = getAnalytics(app);
</script>
