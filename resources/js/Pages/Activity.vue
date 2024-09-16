<script setup>
import { ref, computed, createApp } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import { QrcodeStream, QrcodeDropZone, QrcodeCapture } from "vue-qrcode-reader";

// defineProps<{
//     isPhoneVerified?: boolean;
//     status?: string;
// }>();

const result = ref("");
const error = ref("");
const showScanConfirmation = ref(false);

function paintBoundingBox(detectedCodes, ctx) {
    for (const detectedCode of detectedCodes) {
        const {
            boundingBox: { x, y, width, height },
        } = detectedCode;

        ctx.lineWidth = 2;
        ctx.strokeStyle = "#007bff";
        ctx.strokeRect(x, y, width, height);
    }
}

function onError(err) {
    console.log(err);
    error.value = `[${err.name}]: `;

    if (err.name === "NotAllowedError") {
        error.value += "you need to grant camera access permission";
    } else if (err.name === "NotFoundError") {
        error.value += "no camera on this device";
    } else if (err.name === "NotSupportedError") {
        error.value += "secure context required (HTTPS, localhost)";
    } else if (err.name === "NotReadableError") {
        error.value += "is the camera already in use?";
    } else if (err.name === "OverconstrainedError") {
        error.value += "installed cameras are not suitable";
    } else if (err.name === "StreamApiNotSupportedError") {
        error.value += "Stream API is not supported in this browser";
    } else if (err.name === "InsecureContextError") {
        error.value +=
            "Camera access is only permitted in secure context. Use HTTPS or localhost rather than HTTP.";
    } else {
        error.value += err.message;
    }
}

function onCameraOn(detectedCodes) {
    showScanConfirmation.value = false;
}

function onCameraOff(detectedCodes) {
    showScanConfirmation.value = true;
}

function onDetect(detectedCodes) {
    let code = detectedCodes.map((code) => code.rawValue);
    result.value = code[0];
}

const app = createApp({
    setup() {
        return { result, error, onDetect, onError, paintBoundingBox };
    },
});
app.use(QrcodeStream);
app.mount("#app");
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
                <p style="color: red">{{ error }}</p>

                <p>
                    Last result: <b>{{ result }}</b>
                </p>

                <div class="border-4 border-gray-500">
                    <qrcode-stream
                        :track="paintBoundingBox"
                        @detect="onDetect"
                        @camera-on="onCameraOn"
                        @camera-off="onCameraOff"
                        @error="onError"
                    >
                        <div
                            v-show="showScanConfirmation"
                            class="scan-confirmation"
                        ></div
                    ></qrcode-stream>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
