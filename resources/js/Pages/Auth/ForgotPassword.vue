<script setup>
import GuestLayout from "@/Layouts/GuestLayout.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Head } from "@inertiajs/vue3";
import { ref } from "vue";
import axios from "axios";
import { toast } from "vue3-toastify";

const phone = ref("");
const password = ref("");
const confirmPassword = ref("");
const resetCode = ref("");
const loading = ref(false);
const pageState = ref("start");

const checkPhone = () => {
    loading.value = true;
    toast.loading("Requesting password reset code...");
    axios
        .post("/forgot-password/check-phone", {
            phone: phone.value,
        })
        .then(function (response) {
            console.log(response);
            toast.remove();
            toast.success("Password reset code sent.");
            loading.value = false;
            pageState.value = "reset-code";
        })
        .catch(function (error) {
            console.log(error);
            toast.remove();
            toast.error("Phone number doesn't match.");
            loading.value = false;
        });
};

const checkResetCode = () => {
    loading.value = true;
    toast.loading("Checking password reset code...");
    axios
        .post("/forgot-password/check-code", {
            phone: phone.value,
            code: resetCode.value,
        })
        .then(function (response) {
            console.log(response);
            toast.remove();
            toast.success("Reset Code Matched.");
            loading.value = false;
            pageState.value = "reset-password";
        })
        .catch(function (error) {
            console.log(error);
            toast.remove();
            toast.error("Phone number doesn't match.");
            loading.value = false;
        });
};

const resetPassword = () => {
    loading.value = true;
    toast.loading("Resetting password...");
    if (password.value == confirmPassword.value) {
        axios
            .post("/reset-password", {
                phone: phone.value,
                password: password.value,
            })
            .then(function (response) {
                console.log(response);
                toast.remove();
                toast.success("Password reset successful.");
                loading.value = false;
                window.location.href = "/login";
            })
            .catch(function (error) {
                console.log(error);
                toast.remove();
                toast.error("Phone number doesn't match.");
                loading.value = false;
            });
    } else {
        toast.error("Password doesn't match.");
    }
};
</script>

<template>
    <GuestLayout>
        <Head title="Forgot Password" />

        <div v-if="pageState == 'start'">
            <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                Forgot your password? No problem. Just let us know the phone
                number you used to register, and we will send you an SMS with
                the password reset code that will allow you to choose a new one.
            </div>

            <form @submit.prevent="checkPhone">
                <div>
                    <InputLabel for="phone" value="Phone" />

                    <TextInput
                        id="phone"
                        type="tel"
                        class="mt-1 block w-full"
                        v-model="phone"
                        required
                        autofocus
                        autocomplete="phone"
                    />
                </div>

                <div class="flex items-center justify-end mt-4">
                    <PrimaryButton
                        :class="{ 'opacity-25': loading }"
                        :disabled="loading"
                    >
                        Send Password Reset Code
                    </PrimaryButton>
                </div>
            </form>
        </div>

        <div v-if="pageState == 'reset-code'">
            <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                A password reset code has been sent to your phone number.
            </div>
            <form @submit.prevent="checkResetCode">
                <div>
                    <InputLabel for="resetCode" value="Reset Code" />

                    <TextInput
                        id="resetCode"
                        type="text"
                        class="mt-1 block w-full"
                        v-model="resetCode"
                        required
                        autofocus
                    />
                </div>

                <div class="flex items-center justify-end mt-4">
                    <PrimaryButton
                        :class="{ 'opacity-25': loading }"
                        :disabled="loading"
                    >
                        Confirm
                    </PrimaryButton>
                </div>
            </form>
        </div>

        <div v-if="pageState == 'reset-password'">
            <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                Kindly create a new password to secure your account. Make sure
                it is strong and unique!
            </div>
            <form @submit.prevent="resetPassword">
                <div class="mt-4">
                    <InputLabel for="password" value="Password" />

                    <TextInput
                        id="password"
                        type="password"
                        class="mt-1 block w-full"
                        v-model="password"
                        required
                        autocomplete="new-password"
                    />
                </div>

                <div class="mt-4">
                    <InputLabel
                        for="confirmPassword"
                        value="Confirm Password"
                    />

                    <TextInput
                        id="confirmPassword"
                        type="password"
                        class="mt-1 block w-full"
                        v-model="confirmPassword"
                        required
                        autocomplete="new-password"
                    />
                </div>

                <div class="flex items-center justify-end mt-4">
                    <PrimaryButton
                        :class="{ 'opacity-25': loading }"
                        :disabled="loading"
                    >
                        Reset Password
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </GuestLayout>
</template>
