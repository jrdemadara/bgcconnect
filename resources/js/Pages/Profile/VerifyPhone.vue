<script setup lang="ts">
import { ref, onMounted, onUnmounted } from "vue";

import GuestLayout from "@/Layouts/GuestLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import { Head, useForm } from "@inertiajs/vue3";
import axios from "axios";
import SecondaryButton from "@/Components/SecondaryButton.vue";

const timer = ref(0);
let interval: ReturnType<typeof setInterval> | null = null;

const verification_code = ref("");
const verification_code_error = ref("");
const some_error = ref("");

const send = async () => {
    if (timer.value == 0) {
        try {
            const response = await axios.get("/verify/send");

            console.log(response);

            if (response.data.message === "sent") {
                const verificationDuration = 5; // 1 hour in seconds
                timer.value = verificationDuration;
                localStorage.setItem(
                    "verificationTimer",
                    String(verificationDuration)
                );
                startTimer();
            } else {
                if (interval) clearInterval(interval);
            }
        } catch (error) {
            some_error.value = "Something went wrong, Please try again!";
        }
    }
};

const verify = () => {
    axios
        .get("/verify/phone", {
            params: {
                verification_code: verification_code.value,
            },
        })
        .then(function (response) {
            console.log(response);
            if (response.status == 200) {
                if (interval) clearInterval(interval);
                window.location.href = "/profile";
            }
        })
        .catch(function (error) {
            console.log(error);
            if (error.status == 400) {
                verification_code_error.value = "Invalid Verification Code";
            } else {
                some_error.value = "Something went wrong, Please try again!";
            }
        });
};

const startTimer = () => {
    if (interval) clearInterval(interval);

    interval = setInterval(() => {
        if (timer.value > 0) {
            timer.value--;
            localStorage.setItem("verificationTimer", String(timer.value)); // Update localStorage
        } else {
            clearInterval(interval!);
            localStorage.removeItem("verificationTimer"); // Clear when expired
        }
    }, 1000); // Update every second
};

const formatTime = (seconds: number): string => {
    const minutes = Math.floor(seconds / 60);
    const remainingSeconds = seconds % 60;
    return `${minutes}:${remainingSeconds < 10 ? "0" : ""}${remainingSeconds}`;
};

// Restore timer on component mount
onMounted(() => {
    const storedTimer = localStorage.getItem("verificationTimer");
    if (storedTimer) {
        timer.value = Number(storedTimer);
    }
});

// Cleanup on component unmount
onUnmounted(() => {
    if (interval) clearInterval(interval);
});
</script>

<template>
    <GuestLayout>
        <Head title="Phone Verification" />

        <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
            Thank you for joining! A verification code has been sent to your
            mobile number. Please check your messages and enter the code to
            proceed.
        </div>

        <div
            class="mb-4 font-medium text-sm text-green-600 dark:text-green-400"
            v-if="timer > 0"
        >
            A new verification code has been sent to your phone number.
        </div>

        <div
            class="bg-red-500 rounded-md mb-4 p-2 font-medium text-sm text-white"
            v-if="some_error"
        >
            {{ some_error }}
        </div>

        <div class="mt-4 flex flex-col items-center justify-between space-y-6">
            <div class="w-full">
                <InputLabel
                    for="verrification_code"
                    value="Verification Code"
                />

                <TextInput
                    id="verrification_code"
                    v-model="verification_code"
                    type="text"
                    class="mt-1 block w-full"
                />

                <InputError :message="verification_code_error" class="mt-2" />
            </div>

            <div class="flex flex-col space-y-4 w-full">
                <button
                    @click="verify"
                    class="inline-flex justify-center items-center h-10 px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
                >
                    Verify
                </button>

                <button
                    @click="send"
                    type="button"
                    :class="{
                        'inline-flex justify-center items-center h-10 px-4 py-2 border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest transition ease-in-out duration-150': true,
                        'bg-gray-500 text-white hover:bg-gray-600 focus:bg-gray-700 active:bg-gray-600':
                            timer === 0,
                        'bg-gray-500 text-gray-300 cursor-not-allowed':
                            timer > 0,
                    }"
                >
                    Resend Verification Code
                </button>
                <p class="text-red-500 text-center font-bold" v-if="timer > 0">
                    <span class="text-gray-600 font-normal">
                        Verification code will expire in:
                    </span>
                    {{ formatTime(timer) }}
                </p>
            </div>
        </div>
    </GuestLayout>
</template>
