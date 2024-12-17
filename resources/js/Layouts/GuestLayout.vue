<script setup lang="ts">
import { ref, onMounted } from "vue";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import { Link } from "@inertiajs/vue3";
</script>

<template>
    <div
        class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-50 dark:bg-gray-900"
    >
        <div>
            <Link href="/">
                <img
                    class="w-20 fill-current text-gray-500"
                    src="../../image/logo.png"
                    alt=""
                    srcset=""
                />
            </Link>
        </div>
        <h2 class="font-bold text-2xl mt-2 text-gray-800 dark:text-gray-100">
            BGC Connect
        </h2>

        <div
            class="w-full sm:max-w-md mt-6 px-6 py-4 bg-gray-50 sm:bg-gray-100 dark:bg-gray-900 sm:shadow-md overflow-hidden sm:rounded-lg"
        >
            <slot />
        </div>
    </div>
</template>
