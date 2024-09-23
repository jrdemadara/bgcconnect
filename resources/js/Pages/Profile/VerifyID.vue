<script setup lang="ts">
import { ref, onMounted } from "vue";

import GuestLayout from "@/Layouts/GuestLayout.vue";
import InputError from "@/Components/InputError.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import { Head, useForm } from "@inertiajs/vue3";
import axios from "axios";

import cameraSound from "../../../sound/camera.mp3"; // Import the sound file

const videoFront = ref<HTMLVideoElement | null>(null);
const videoBack = ref<HTMLVideoElement | null>(null);
const canvasFront = ref<HTMLCanvasElement | null>(null);
const canvasBack = ref<HTMLCanvasElement | null>(null);
const photoFront = ref<string | null>(null);
const photoBack = ref<string | null>(null);
const captureSoundSrc = cameraSound; // Use the imported sound file
let streamFront: MediaStream | null = null; // To hold the media stream
let streamBack: MediaStream | null = null; // To hold the media stream

const cameraStateFront = ref("start");
const cameraStateBack = ref("start");
const infoPage = ref();
const some_error = ref("");

const send = async () => {
    try {
        const response = await axios.get("/verify/send");

        console.log(response.data.message);

        if (response.data.message === "sent") {
            const verificationDuration = 3600; // 1 hour in seconds

            localStorage.setItem(
                "verificationTimer",
                String(verificationDuration)
            );
        } else {
        }
    } catch (error) {
        some_error.value = "Something went wrong, Please try again!";
    }
};

const hideInfoPage = async () => {
    infoPage.value = false;
};

const startCameraFront = async () => {
    try {
        if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
            streamFront = await navigator.mediaDevices.getUserMedia({
                video: true,
            });
            if (videoFront.value) {
                videoFront.value.srcObject = streamFront;
                cameraStateFront.value = "capture";
            }
        } else {
            console.error("getUserMedia not supported");
        }
    } catch (error) {
        console.error("Error accessing camera: ", error);
    }
};

const startCameraBack = async () => {
    try {
        if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
            streamBack = await navigator.mediaDevices.getUserMedia({
                video: true,
            });
            if (videoBack.value) {
                videoBack.value.srcObject = streamBack;
                cameraStateBack.value = "capture";
            }
        } else {
            console.error("getUserMedia not supported");
        }
    } catch (error) {
        console.error("Error accessing camera: ", error);
    }
};

const capturePhotoFront = () => {
    if (videoFront.value && canvasFront.value) {
        const context = canvasFront.value.getContext("2d");
        if (context) {
            canvasFront.value.width = videoFront.value.videoWidth;
            canvasFront.value.height = videoFront.value.videoHeight;
            context.drawImage(videoFront.value, 0, 0);
            photoFront.value = canvasFront.value.toDataURL("image/png");

            // Play the capture sound
            const audioElement = document.querySelector(
                "audio"
            ) as HTMLAudioElement;
            if (audioElement) {
                audioElement.play();
            }
            cameraStateFront.value = "done";
            if (streamFront) {
                streamFront.getTracks().forEach((track) => track.stop());
            }
        }
    }
};

const capturePhotoBack = () => {
    if (videoBack.value && canvasBack.value) {
        const context = canvasBack.value.getContext("2d");
        if (context) {
            canvasBack.value.width = videoBack.value.videoWidth;
            canvasBack.value.height = videoBack.value.videoHeight;
            context.drawImage(videoBack.value, 0, 0);
            photoBack.value = canvasBack.value.toDataURL("image/png");

            // Play the capture sound
            const audioElement = document.querySelector(
                "audio"
            ) as HTMLAudioElement;
            if (audioElement) {
                audioElement.play();
            }
            cameraStateBack.value = "done";
            if (streamBack) {
                streamBack.getTracks().forEach((track) => track.stop());
            }
        }
    }
};

const retakePhotoFront = () => {
    cameraStateFront.value = "capture";
    if (videoFront.value) {
        // Clear the previous photo
        photoFront.value = null;

        // Restart the camera
        startCameraFront();
    }
};

const retakePhotoBack = () => {
    cameraStateBack.value = "capture";
    if (videoBack.value) {
        // Clear the previous photo
        photoBack.value = null;

        // Restart the camera
        startCameraBack();
    }
};

onMounted(() => {
    infoPage.value = true;
});
</script>

<template>
    <GuestLayout>
        <Head title="Identity Verification" />

        <h2 class="text-center font-semibold text-lg">Identity Verification</h2>

        <div class="mt-4" v-if="infoPage">
            <h4 class="font-medium">1. ID card must be valid</h4>
            <h4 class="font-medium">2. ID card must be clear and readable</h4>
            <h4 class="font-medium">
                3. ID card must be an actual ID
                <small class="text-red-500"
                    >(capturing photo copy or id from photo is not
                    allowed.)</small
                >
            </h4>
            <h4 class="font-medium">4. Capture ID card front and back</h4>
            <small class="text-red-500">
                <span class="font-bold">Reminder:</span> Users who fail to
                comply with the conditions will not be approved.
            </small>
            <div class="w-full h-0.5 mt-4 bg-gray-100 dark:bg-gray-600"></div>

            <div class="mt-4">
                <p class="text-center font-semibold">ID Sample - Front</p>
                <img
                    class="bg-blue-400 w-full h-full rounded-xl border-2 border-gray-300"
                    src="../../../image/id-front.png"
                    alt=""
                />
            </div>

            <div class="mt-4">
                <p class="text-center font-semibold">ID Sample - Back</p>
                <img
                    class="bg-blue-400 w-full h-full rounded-xl border-2 border-gray-300"
                    src="../../../image/id-back.png"
                    alt=""
                />
            </div>

            <div class="w-full h-0.5 mt-4 bg-gray-100 dark:bg-gray-600"></div>

            <h4 class="font-medium text-lg mt-5">List of valid IDs</h4>
            <ul class="list-disc px-5 mt-2">
                <li>e-Card / UMID</li>
                <li>Employee’s ID / Office ID</li>
                <li>Driver’s License*</li>
                <li>Professional Regulation Commission (PRC) ID *</li>
                <li>Passport *</li>
                <li>Senior Citizen ID</li>
                <li>SSS ID</li>
                <li>COMELEC / Voter’s ID / COMELEC Registration Form</li>
                <li>Philippine Identification (PhilID / ePhilID)</li>
                <li>NBI Clearance *</li>
                <li>Integrated Bar of the Philippines (IBP) ID</li>
                <li>Firearms License *</li>
                <li>AFPSLAI ID *</li>
                <li>PVAO ID</li>
                <li>AFP Beneficiary ID</li>
                <li>BIR (TIN)</li>
                <li>Pag-ibig ID</li>
                <li>Person’s With Disability (PWD) ID</li>
                <li>Solo Parent ID</li>
                <li>Pantawid Pamilya Pilipino Program (4Ps) ID *</li>
                <li>Barangay ID *</li>
                <li>Philippine Postal ID *</li>
                <li>Phil-health ID</li>
                <li>School ID **</li>
                <li>Other valid government-issued IDs or documents</li>
            </ul>
            <div class="w-full h-0.5 mt-4 bg-gray-100 dark:bg-gray-600"></div>
            <button
                @click="hideInfoPage"
                class="inline-flex justify-center items-center w-full h-10 px-4 py-2 mt-4 mb-2 bg-[#05203c] dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
            >
                Proceed
            </button>
        </div>

        <div class="mt-4" v-if="!infoPage">
            <div
                class="flex flex-col justify-center items-center mt-4 bg-gradient-to-r from-slate-100 to bg-slate-200 shadow-md rounded-lg p-4"
            >
                <h3 class="mb-2 font-bold">Front ID Card</h3>
                <video
                    v-show="cameraStateFront == 'capture'"
                    ref="videoFront"
                    autoplay
                    class="border-4 border-gray-300 rounded-xl"
                ></video>

                <canvas ref="canvasFront" style="display: none"></canvas>
                <!-- Display the captured photo -->
                <img
                    v-if="photoFront && cameraStateFront == 'done'"
                    :src="photoFront"
                    alt="Captured Photo"
                    class="border-4 border-gray-300 rounded-xl"
                />

                <audio ref="captureSound" :src="captureSoundSrc"></audio>

                <SecondaryButton
                    v-show="cameraStateFront == 'start'"
                    @click="startCameraFront"
                    class="mt-4"
                    >Capture Front ID</SecondaryButton
                >

                <SecondaryButton
                    v-show="cameraStateFront == 'capture'"
                    @click="capturePhotoFront"
                    class="mt-4"
                    >Capture</SecondaryButton
                >

                <SecondaryButton
                    v-show="cameraStateFront == 'done'"
                    @click="retakePhotoFront"
                    class="mt-4"
                    >Retake Photo</SecondaryButton
                >
            </div>
            <div class="w-full h-0.5 mt-4 bg-gray-100 dark:bg-gray-600"></div>
            <div
                class="flex flex-col justify-center items-center mt-4 bg-gradient-to-r from-slate-100 to bg-slate-200 shadow-md rounded-lg p-4"
            >
                <h3 class="mb-2 font-bold">Back ID Card</h3>
                <video
                    v-show="cameraStateBack == 'capture'"
                    ref="videoBack"
                    autoplay
                    class="border-4 border-gray-300 rounded-xl"
                ></video>

                <canvas ref="canvasBack" style="display: none"></canvas>
                <!-- Display the captured photo -->
                <img
                    v-if="photoBack && cameraStateBack == 'done'"
                    :src="photoBack"
                    alt="Captured Photo"
                    class="border-4 border-gray-300 rounded-xl"
                />

                <audio ref="captureSound" :src="captureSoundSrc"></audio>

                <SecondaryButton
                    v-show="cameraStateBack == 'start'"
                    @click="startCameraBack"
                    class="mt-4"
                    >Capture Back ID</SecondaryButton
                >

                <SecondaryButton
                    v-show="cameraStateBack == 'capture'"
                    @click="capturePhotoBack"
                    class="mt-4"
                    >Capture</SecondaryButton
                >

                <SecondaryButton
                    v-show="cameraStateBack == 'done'"
                    @click="retakePhotoBack"
                    class="mt-4"
                    >Retake Photo</SecondaryButton
                >
            </div>

            <div
                class="bg-red-500 rounded-md mb-4 p-2 font-medium text-sm text-white"
                v-if="some_error"
            >
                {{ some_error }}
            </div>

            <div
                class="mt-4 flex flex-col items-center justify-between space-y-6"
            >
                <div class="flex flex-col space-y-4 w-full">
                    <button
                        @click="infoPage = false"
                        class="inline-flex justify-center items-center h-10 px-4 py-2 bg-[#05203c] dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
                    >
                        Verify
                    </button>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>
