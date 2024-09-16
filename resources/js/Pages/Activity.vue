<script setup lang="ts">
import ref from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";

defineProps<{
    isPhoneVerified?: boolean;
    status?: string;
}>();
</script>
<template>
    <Head title="Profile" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
            >
                Scan Activity
            </h2>
        </template>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6 bg-white">
            <div class="flex flex-col justify-center items-center w-full px-4">
                <p class="font-bold text-lg my-4">
                    Point the camera to the QR Code
                </p>
                <img
                    class="w-full h-32 bg-blue-100 rounded-lg"
                    src=""
                    alt=""
                    srcset=""
                />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
