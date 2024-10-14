<script setup lang="ts">
import { ref } from "vue";
import { useClipboard } from "@vueuse/core";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import { Check, CheckCheck, Copy, ScanQrCode } from "lucide-vue-next";
import QrcodeVue, { Level, RenderAs } from "qrcode.vue";

const source = ref("");
const value = ref("");
const level = ref<Level>("H");
const renderAs = ref<RenderAs>("svg");

const props = defineProps<{
    qrcode?: string;
}>();

value.value = `https://bgcconnect.ph/invite?code=${props.qrcode}`;
source.value = props.qrcode as string;

const { text, copy, copied, isSupported } = useClipboard({ source });
</script>

<template>
    <Head title="Profile" />

    <AuthenticatedLayout>
        <div class="flex flex-col items-center py-6 h-screen bg-white">
            <div class="rounded-lg p-2 ring-1 ring-slate-200">
                <ScanQrCode class="text-black mr-0.5" :size="26" />
            </div>
            <h2 class="font-bold text-xl mt-4">Scan QR code</h2>
            <p class="font-light text-xs mt-1 text-slate-700">
                Scan this QR code to refer or invite anyone.
            </p>

            <qrcode-vue
                :value="value"
                :level="level"
                :size="250"
                :margin="2"
                class="ring-1 ring-slate-200 rounded-lg mt-4"
                :render-as="renderAs"
            />

            <div class="flex flex-col justify-center items-center w-3/4 mt-5">
                <div class="flex justify-center items-center w-full space-x-2">
                    <div class="flex-grow h-0.5 bg-slate-100 rounded"></div>
                    <p class="font-light text-xs text-slate-600">
                        or copy the code manually
                    </p>
                    <div class="flex-grow h-0.5 bg-slate-100 rounded"></div>
                </div>
                <div class="flex mt-5 w-full space-x-2">
                    <div
                        class="flex justify-start items-center w-full h-10 px-2 rounded font-bold ring-1 ring-slate-200 text-black"
                    >
                        {{ props.qrcode }}
                    </div>
                    <div
                        @click="copy(source)"
                        class="flex items-center justify-center w-10 h-10 rounded text-sm font-semibold ring-1 ring-slate-200 text-black"
                    >
                        <span v-if="!copied">
                            <Copy class="text-slate-800 mr-0.5" :size="16"
                        /></span>
                        <span v-else>
                            <Check class="text-green-600 mr-0.5" :size="16"
                        /></span>
                    </div>
                </div>
                <Link
                    :href="route('profile.index')"
                    class="flex justify-center items-center px-2 mt-5 border rounded-lg w-full h-12 bg-blue-600 text-white"
                >
                    I'm done
                </Link>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
