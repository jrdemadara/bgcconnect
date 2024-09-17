<script setup lang="ts">
import { ref } from "vue";
import { VueFinalModal } from "vue-final-modal";
import QrcodeVue, { Level, RenderAs } from "qrcode.vue";

const value = ref("");
const level = ref<Level>("H");
const renderAs = ref<RenderAs>("svg");

const props = defineProps<{
    qrcode?: string;
}>();

value.value = `https://bgcconnect.ph/invite/${props.qrcode}`;
const emit = defineEmits<{
    (e: "confirm"): void;
}>();
</script>

<template>
    <VueFinalModal
        class="flex justify-center items-center"
        content-class="flex flex-col max-w-xl mx-4 p-4 bg-white dark:bg-gray-900 border dark:border-gray-700 rounded-lg space-y-4"
    >
        <h1 class="font-bold text-xl text-center">My QR Code</h1>
        <hr />
        <qrcode-vue
            :value="value"
            :level="level"
            :size="300"
            :margin="2"
            class="border-4 border-blue-300 rounded"
            :render-as="renderAs"
        />

        <button
            class="ml-auto px-2 border rounded-lg w-full h-12 bg-blue-500 text-white"
            @click="emit('confirm')"
        >
            Close
        </button>
    </VueFinalModal>
</template>
