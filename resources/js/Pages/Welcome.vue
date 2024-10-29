<script setup lang="ts">
import { ref } from "vue";
import { Head, Link } from "@inertiajs/vue3";

defineProps<{
    canLogin?: boolean;
    canRegister?: boolean;
}>();

function handleImageError() {
    document.getElementById("screenshot-container")?.classList.add("!hidden");
    document.getElementById("docs-card")?.classList.add("!row-span-1");
    document.getElementById("docs-card-content")?.classList.add("!flex-row");
    document.getElementById("background")?.classList.add("!hidden");
}

const isMenuOpen = ref(false); // State for tracking the menu's visibility

// Toggle the menu open and closed
const toggleMenu = () => {
    isMenuOpen.value = !isMenuOpen.value;
};

// Close the menu
const closeMenu = () => {
    isMenuOpen.value = false;
};
</script>

<template>
    <Head title="Welcome" />
    <header class="shadow-sm bg-gray-50">
        <div class="flex justify-between items-center p-4 sm:px-6">
            <div class="flex justify-center items-center">
                <img alt="Vue logo" class="w-16" src="../../image/logo.png" />
                <h2 class="font-sa font-bold text-xl ml-1">BGC Connect</h2>
            </div>

            <div class="hidden md:flex space-x-4">
                <!-- Navigation Links -->
                <RouterLink to="/">Home</RouterLink>
                <RouterLink to="/about">About</RouterLink>
            </div>

            <!-- Hamburger Menu for Mobile -->
            <div class="md:hidden">
                <button @click="toggleMenu" class="focus:outline-none">
                    <!-- Hamburger Icon -->
                    <svg
                        v-if="!isMenuOpen"
                        class="w-8 h-8 transition-transform duration-300 ease-in-out"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M4 6h16M4 12h16m-7 6h7"
                        />
                    </svg>
                    <!-- X Icon -->
                    <svg
                        v-else
                        class="w-8 h-8 transition-transform duration-300 ease-in-out"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"
                        />
                    </svg>
                </button>
            </div>
        </div>
        <!-- Mobile Navigation Menu -->

        <nav v-if="isMenuOpen" class="flex flex-col space-y-2 md:hidden p-4">
            <Link
                :href="route('login')"
                class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
            >
                Log in
            </Link>

            <Link
                :href="route('register')"
                class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
            >
                Register
            </Link>
        </nav>
    </header>

    <div class="bg-gray-50 text-black/50 dark:bg-black dark:text-white/50">
        <div
            class="relative min-h-screen flex flex-col items-center justify-center selection:bg-[#FF2D20] selection:text-white"
        >
            <div class="relative w-full max-w-2xl px-6 lg:max-w-7xl">
                <main class="mt-6">
                    <div class="flex sm:hidden flex-col">
                        <section class="flex flex-col items-center sm:flex-row">
                            <h2
                                class="font-bold text-5xl text-center text-gray-800"
                            >
                                Welcme to <br />
                                <span class="text-blue-500">BGC</span> Connect
                            </h2>
                            <p class="text-center text-lg text-gray-400 mt-4">
                                Your hub for meaningful connections and active
                                community participation. We focus on
                                communication and engagement, offering a space
                                for members to connect, stay informed, and
                                collaborate through activities and events.
                            </p>
                            <Link
                                :href="route('register')"
                                class="rounded-md w-full py-2 font-medium text-center text-lg mt-4 bg-blue-500 text-white hover:bg-blue-600"
                            >
                                Join us!
                            </Link>
                            <Link
                                :href="route('login')"
                                class="rounded-md w-full py-2 font-medium text-center text-lg mt-4 ring-1 ring-gray-300"
                            >
                                Go to my account
                            </Link>
                        </section>
                        <img
                            src="../../image/community.svg"
                            class="mt-6"
                            alt=""
                            srcset=""
                        />
                    </div>

                    <section
                        class="hidden sm:flex items-center justify-between sm:flex-row"
                    >
                        <div class="flex flex-col w-full">
                            <h2
                                class="font-bold text-5xl text-center text-gray-800"
                            >
                                Welcome to <br />
                                <span class="text-blue-500">BGC</span>
                                Connect
                            </h2>
                            <p class="text-center text-lg text-gray-400 mt-4">
                                Your hub for meaningful connections and active
                                community participation. We focus on
                                communication and engagement, offering a space
                                for members to connect, stay informed, and
                                collaborate through activities and events.
                            </p>

                            <Link
                                :href="route('register')"
                                class="rounded-md w-full py-2 font-medium text-center text-lg mt-4 bg-blue-500 text-white hover:bg-blue-600"
                            >
                                Join us!
                            </Link>
                            <Link
                                :href="route('login')"
                                class="rounded-md w-full py-2 font-medium text-center text-lg mt-4 ring-1 ring-gray-300"
                            >
                                Go to my account
                            </Link>
                        </div>
                        <img
                            src="../../image/community.svg"
                            width="700"
                            alt=""
                            srcset=""
                        />
                    </section>
                </main>

                <footer
                    class="py-16 text-center text-sm text-black dark:text-white/70"
                >
                    © 2024 BGC Connect. All rights reserved.
                </footer>
            </div>
        </div>
    </div>
</template>
