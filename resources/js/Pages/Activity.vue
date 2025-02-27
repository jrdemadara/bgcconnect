<script setup>
import { ref, computed, createApp, onMounted } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Link, Head } from "@inertiajs/vue3";
import { QrcodeStream, QrcodeDropZone, QrcodeCapture } from "vue-qrcode-reader";
import { getDistance } from "geolib";
import { toast } from "vue3-toastify";

const isLocation = ref(true);
const latitude = ref("");
const longitude = ref("");

const qrcode = ref("");
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
    qrcode.value = code[0];
    checkLocation(code[0]);
}

// Method to check if the user location is within the radius
const checkLocation = (code) => {
    axios
        .get("/activity/location", {
            params: {
                code: code,
            },
        })
        .then(function (response) {
            if (response.status === 200) {
                const centerLatitude = response.data.centerLatitude;
                const centerLongitude = response.data.centerLongitude;
                const centerRadius = response.data.radius;
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        (position) => {
                            latitude.value = position.coords.latitude;
                            longitude.value = position.coords.longitude;
                            isLocation.value = true;
                            vibrateDevice();

                            const distance = getDistance(
                                {
                                    latitude: centerLatitude,
                                    longitude: centerLongitude,
                                },
                                {
                                    latitude: latitude.value,
                                    longitude: longitude.value,
                                }
                            );

                            const withinRadius =
                                distance <= centerRadius ? true : false;

                            console.log(withinRadius);
                            if (withinRadius) {
                                axios
                                    .post("/activity/store", {
                                        code: code,
                                        latitude: latitude,
                                        longitude: longitude,
                                    })
                                    .then(function (response) {
                                        toast.success(
                                            "Success! Thank you for coming."
                                        );
                                    })
                                    .catch(function (error) {
                                        if (error.status === 401) {
                                            const errorMessage =
                                                error.response.data?.message;
                                            toast.error(errorMessage);
                                        } else {
                                            toast.error(
                                                "Something went wrong, Please try again."
                                            );
                                        }
                                    });
                            } else {
                                toast.warning("You're out of the range.");
                            }
                        },
                        () => {
                            isLocation.value = false;
                        },
                        {
                            enableHighAccuracy: true, // Enable high accuracy mode
                            timeout: 5000, // Timeout after 5 seconds
                            maximumAge: 0, // No caching
                        }
                    );
                } else {
                    isLocation.value = false;
                }
            }
        })
        .catch(function (error) {
            if (error.status === 404) {
                const errorMessage = error.response.data?.message;
                toast.error(errorMessage);
            } else {
                toast.error("Something went wrong, Please try again.");
            }
        });
};

const vibrateDevice = () => {
    if ("vibrate" in navigator) {
        navigator.vibrate(200); // Vibrate for 200ms
    } else {
        console.log("Vibration is not supported in this device/browser.");
    }
};

const app = createApp({
    setup() {
        return { qrcode, error, onDetect, onError, paintBoundingBox };
    },
});

// app.use(QrcodeStream);
// app.mount("#app");

onMounted(() => {
    //getProvinces();
    //updateImageSrc();
});
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

        <div
            class="flex flex-col justify-center items-center max-w-7xl mx-auto p-4 sm:px-6 lg:px-8 space-y-10 mt-0 sm:mt-8 bg-white"
        >
            <div class="flex flex-col justify-center items-center w-full">
                <p class="font-bold text-lg my-4">
                    Point the camera to the QR Code
                </p>
                <p style="color: red">{{ error }}</p>

                <div class="border-4 border-gray-500 w-full">
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
            <Link
                :href="route('profile.index')"
                class="inline-flex justify-center items-center w-full h-10 px-4 py-2 border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest transition ease-in-out duration-150 bg-blue-600 text-white"
            >
                Done
            </Link>
        </div>
    </AuthenticatedLayout>
</template>
