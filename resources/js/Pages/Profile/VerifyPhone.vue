<script setup lang="ts">
import { ref, computed } from "vue";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import axios from "axios";

const props = defineProps<{
    status?: string;
}>();

const verification_code = ref("");
const verification_code_error = ref("");
const resend_verification_code = ref(false);

const status = ref("");

const verify = () => {
    axios
        .get("/verify/phone", {
            params: {
                verification_code: verification_code.value,
            },
        })
        .then(function (response) {
            // handle success
            status.value = "verified";
            console.log(response);
            window.location.href = "/profile";
        })
        .catch(function (error) {
            console.log(error);
            // handle error
            status.value = "unverified";
            verification_code_error.value = "Verification Code is not valid.";
        });
};

const form = useForm({});

const submit = () => {
    form.post(route("verification.send"));
};
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
            v-if="resend_verification_code"
        >
            A new verification link has been sent to the email address you
            provided during registration.
        </div>

        <form @submit.prevent="submit">
            <div
                class="mt-4 flex flex-col items-center justify-between space-y-6"
            >
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

                    <InputError
                        :message="verification_code_error"
                        class="mt-2"
                    />
                    <p class="text-red-500">
                        The code is valid for 30 minutes.
                    </p>
                </div>
                <PrimaryButton
                    @click="verify"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Verify
                </PrimaryButton>
                <SecondaryButton
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Resend Verification Code
                </SecondaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
