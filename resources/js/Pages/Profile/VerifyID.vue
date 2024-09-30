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
const photoFrontImage = ref<string | null>(null);
const photoBackImage = ref<string | null>(null);
const photoFront = ref<File | null>(null);
const photoBack = ref<File | null>(null);
const captureSoundSrc = cameraSound; // Use the imported sound file
let streamFront: MediaStream | null = null; // To hold the media stream
let streamBack: MediaStream | null = null; // To hold the media stream

const cameraStateFront = ref("start");
const cameraStateBack = ref("start");
const infoPage = ref();
const some_error = ref("");

const id_type = ref("");

const uploadPhoto = async () => {
    if (photoFront.value instanceof File && photoBack.value instanceof File) {
        // Ensure it's a File
        const formData = new FormData();
        formData.append("idType", id_type.value);
        formData.append("idPhotoFront", photoFront.value);
        formData.append("idPhotoBack", photoBack.value);

        try {
            const response = await axios.post("/verify/store", formData, {
                headers: {
                    "Content-Type": "multipart/form-data",
                },
            });
            console.log(response);
            console.log("Upload successful:", response.data);
            if (response.status == 200) {
                window.location.href = "/profile";
            }
        } catch (error) {
            console.log(error);
            some_error.value = "Something went wrong, Please try again!";
        }
    } else {
        console.error("No photo available for upload.");
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
            // Set canvas dimensions to match the video
            canvasFront.value.width = videoFront.value.videoWidth;
            canvasFront.value.height = videoFront.value.videoHeight;

            // Draw the video frame onto the canvas
            context.drawImage(videoFront.value, 0, 0);

            photoFrontImage.value = canvasFront.value.toDataURL("image/jpeg");

            // Get base64 encoded image data
            const dataUrl = canvasFront.value.toDataURL("image/jpeg");

            // Convert base64 to Blob
            const blob = dataURLToBlob(dataUrl);

            // If you specifically need a File object, convert the Blob to a File
            photoFront.value = new File([blob], "photo.jpeg", {
                type: "image/jpeg",
            });

            // Play the capture sound
            const audioElement = document.querySelector(
                "audio"
            ) as HTMLAudioElement;
            if (audioElement) {
                audioElement.play();
            }

            // Set camera state to done
            cameraStateFront.value = "done";

            // Stop all video tracks to release the camera
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
            // Set canvas dimensions to match the video
            canvasBack.value.width = videoBack.value.videoWidth;
            canvasBack.value.height = videoBack.value.videoHeight;

            // Draw the video frame onto the canvas
            context.drawImage(videoBack.value, 0, 0);

            photoBackImage.value = canvasBack.value.toDataURL("image/jpeg");

            // Get base64 encoded image data
            const dataUrl = canvasBack.value.toDataURL("image/jpeg");

            // Convert base64 to Blob
            const blob = dataURLToBlob(dataUrl);

            // If you specifically need a File object, convert the Blob to a File
            photoBack.value = new File([blob], "photo.jpeg", {
                type: "image/jpeg",
            });

            // Play the capture sound
            const audioElement = document.querySelector(
                "audio"
            ) as HTMLAudioElement;
            if (audioElement) {
                audioElement.play();
            }

            // Set camera state to done
            cameraStateBack.value = "done";

            // Stop all video tracks to release the camera
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

// Helper function to convert a base64-encoded Data URL to a Blob
function dataURLToBlob(dataUrl: string): Blob {
    const byteString = atob(dataUrl.split(",")[1]);
    const mimeString = dataUrl.split(",")[0].split(":")[1].split(";")[0];
    const buffer = new ArrayBuffer(byteString.length);
    const uintArray = new Uint8Array(buffer);

    for (let i = 0; i < byteString.length; i++) {
        uintArray[i] = byteString.charCodeAt(i);
    }

    return new Blob([uintArray], { type: mimeString });
}

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
                class="inline-flex justify-center items-center w-full h-10 px-4 py-2 mt-4 mb-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
            >
                Proceed
            </button>
        </div>

        <div class="mt-4" v-if="!infoPage">
            <div>
                <InputLabel for="id_type" value="ID Type" />

                <select
                    id="id_type"
                    v-model="id_type"
                    class="mt-1 block w-full capitalize rounded shadow-sm border border-gray-300"
                    autofocus
                >
                    <option>e-Card / UMID</option>
                    <option>Employee’s ID / Office ID</option>
                    <option>Driver’s License</option>
                    <option>Professional Regulation Commission (PRC) ID</option>
                    <option>Passport</option>
                    <option>Senior Citizen ID</option>
                    <option>SSS ID</option>
                    <option>
                        COMELEC / Voter’s ID / COMELEC Registration Form
                    </option>
                    <option>
                        Philippine Identification (PhilID / ePhilID)
                    </option>
                    <option>NBI Clearance</option>
                    <option>Integrated Bar of the Philippines (IBP) ID</option>
                    <option>Firearms License</option>
                    <option>AFPSLAI ID</option>
                    <option>PVAO ID</option>
                    <option>AFP Beneficiary ID</option>
                    <option>BIR (TIN)</option>
                    <option>Pag-ibig ID</option>
                    <option>Person’s With Disability (PWD) ID</option>
                    <option>Solo Parent ID</option>
                    <option>Pantawid Pamilya Pilipino Program (4Ps) ID</option>
                    <option>Barangay ID</option>
                    <option>Philippine Postal ID</option>
                    <option>Phil-health ID</option>
                    <option>School ID</option>
                    <option>
                        Other valid government-issued IDs or documents
                    </option>
                </select>
            </div>
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
                    v-if="photoFrontImage && cameraStateFront == 'done'"
                    :src="photoFrontImage"
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
                    v-if="photoBackImage && cameraStateBack == 'done'"
                    :src="photoBackImage"
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
                <div v-if="infoPage" class="flex flex-col space-y-4 w-full">
                    <button
                        @click="infoPage = false"
                        class="inline-flex justify-center items-center h-10 px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
                    >
                        Proceed
                    </button>
                </div>
                <div v-if="!infoPage" class="flex flex-col space-y-4 w-full">
                    <button
                        @click="uploadPhoto"
                        class="inline-flex justify-center items-center h-10 px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
                    >
                        Proceed
                    </button>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>
