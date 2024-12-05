<template>
    <div class="flex items-center justify-center h-screen bg-gray-100">
        <div class="text-center">
            <h1 class="text-9xl font-extrabold text-gray-700">404</h1>
            <p class="text-2xl text-gray-600 mt-4">Oops! The page you're looking for doesn't exist.</p>
            <router-link
                to="/"
                class="mt-6 inline-block px-6 py-3 text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition"
            >
                Go Back Home
            </router-link>
        </div>
    </div>
</template>

<script setup>
</script>
