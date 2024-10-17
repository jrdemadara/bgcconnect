<script setup>
import { ref, onMounted } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, usePage } from "@inertiajs/vue3";
import { Check, CheckCheck, Copy, IdCard, ScanQrCode } from "lucide-vue-next";
import SecondaryButton from "@/Components/SecondaryButton.vue";

const photo = ref("");
const props = usePage().props;

const downloadImage = () => {
    // Create a link element
    const link = document.createElement("a");

    // Set the download attribute with a filename
    link.download = "my_digital_id.png";

    // Set the href to the base64 image string
    link.href = photo.value;

    // Append the link to the body
    document.body.appendChild(link);

    // Programmatically click the link to trigger the download
    link.click();

    // Remove the link from the document
    document.body.removeChild(link);
};

onMounted(() => {
    photo.value = props.digital_id;
});
</script>

<template>
    <Head title="Profile" />

    <AuthenticatedLayout>
        <div class="flex flex-col items-center px-4 py-6 h-screen bg-white">
            <div class="rounded-lg p-2 ring-1 ring-slate-200">
                <IdCard class="text-black mr-0.5" :size="26" />
            </div>
            <h2 class="font-bold text-xl mt-4">Digital ID</h2>
            <p class="font-light text-xs mt-1 text-slate-700">
                Quick, safe access to your ID wherever you go.
            </p>

            <img
                v-if="photo"
                class="rounded-xl border-2 w-full mt-6 border-gray-300 pt-2.5 pl-1 pr-1"
                :src="photo"
                alt="digital id"
            />

            <h4
                class="mt-6 font-semibold text-xl text-red-500 text-center"
                v-else
            >
                Your Digital ID is currently being processed.
            </h4>

            <SecondaryButton class="mt-6" @click="downloadImage"
                >Download ID</SecondaryButton
            >
            <Link
                :href="route('profile.index')"
                class="flex justify-center items-center px-2 mt-4 border rounded-lg w-full h-12 bg-blue-600 text-white"
            >
                I'm done
            </Link>
        </div>
    </AuthenticatedLayout>
</template>
