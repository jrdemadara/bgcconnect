<script setup>
import { ref, onMounted } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, usePage } from "@inertiajs/vue3";
import { Check, CheckCheck, Copy, IdCard, ScanQrCode } from "lucide-vue-next";

const photo = ref("");
const props = usePage().props;
console.log(props);

const updateImageSrc = () => {
    const storedImage = localStorage.getItem("digitalID");
    const digitalId = props.digital_id;
    if (storedImage) {
        photo.value = storedImage;
    } else {
        photo.value = digitalId;
    }
};

onMounted(() => {
    updateImageSrc();
});
</script>

<template>
    <Head title="Profile" />

    <AuthenticatedLayout>
        <div class="flex flex-col items-center px-12 py-6 h-screen bg-white">
            <div class="rounded-lg p-2 ring-1 ring-slate-200">
                <IdCard class="text-black mr-0.5" :size="26" />
            </div>
            <h2 class="font-bold text-xl mt-4">Digital ID</h2>
            <p class="font-light text-xs mt-1 text-slate-700">
                Quick, safe access to your ID wherever you go.
            </p>

            <img
                v-if="photo"
                class="rounded-xl border-2 w-2/3 mt-6 border-gray-300"
                :src="photo"
                alt="digital id"
            />
            <h4
                class="mt-6 font-semibold text-xl text-red-500 text-center"
                v-else
            >
                Your Digital ID is currently being processed.
            </h4>

            <Link
                :href="route('profile.index')"
                class="flex justify-center items-center px-2 mt-6 border rounded-lg w-full h-12 bg-blue-600 text-white"
            >
                I'm done
            </Link>
        </div>
    </AuthenticatedLayout>
</template>
