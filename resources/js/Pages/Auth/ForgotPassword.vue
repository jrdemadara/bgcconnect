<script setup>
import GuestLayout from "@/Layouts/GuestLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Head, useForm } from "@inertiajs/vue3";
import { ref } from "vue";
import axios from "axios";
import { toast } from "vue3-toastify";

const phone = ref("");
const password = ref("");
const resetCode = ref("");
const loading = ref(false);
const loadingToastId = ref(null);
const pageState = ref("start");

const checkPhone = () => {
    loading.value = true;
    loadingToastId.value = toast.loading("Requesting password reset code...");
    axios
        .post("/forgot-password/check-phone", {
            phone: phone.value,
        })
        .then(function (response) {
            console.log(response);
            toast.done(loadingToastId.value);
            toast.success("Password reset successful.");
            loading.value = false;
            pageState.value = "reset-code";
        })
        .catch(function (error) {
            console.log(error);
            toast.done(loadingToastId.value);
            toast.error("Phone number does't match.");
            loading.value = false;
        });
};

const checkResetCode = () => {
    loading.value = true;
    loadingToastId.value = toast.loading("Requesting password reset code...");
    axios
        .post("/forgot-password/check-phone", {
            phone: phone.value,
        })
        .then(function (response) {
            console.log(response);
            toast.done(loadingToastId.value);
            toast.success("Password reset successful.");
            loading.value = false;
            pageState.value = "reset-code";
        })
        .catch(function (error) {
            console.log(error);
            toast.done(loadingToastId.value);
            toast.error("Phone number does't match.");
            loading.value = false;
        });
};

const resetPassword = () => {
    loading.value = true;
    loadingToastId.value = toast.loading("Requesting password reset code...");
    axios
        .post("/forgot-password/check-phone", {
            phone: phone.value,
        })
        .then(function (response) {
            console.log(response);
            toast.done(loadingToastId.value);
            toast.success("Password reset successful.");
            loading.value = false;
            pageState.value = "reset-code";
        })
        .catch(function (error) {
            console.log(error);
            toast.done(loadingToastId.value);
            toast.error("Phone number does't match.");
            loading.value = false;
        });
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
                        type="phone"
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
                        type="phone"
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
                A password reset code has been sent to your phone number.
            </div>
            <form @submit.prevent="resetPassword">
                <div class="mt-4">
                    <InputLabel for="password" value="Password" />

                    <TextInput
                        id="password"
                        type="password"
                        class="mt-1 block w-full"
                        v-model="form.password"
                        required
                        autocomplete="new-password"
                    />

                    <InputError class="mt-2" :message="form.errors.password" />
                </div>

                <div class="mt-4">
                    <InputLabel
                        for="password_confirmation"
                        value="Confirm Password"
                    />

                    <TextInput
                        id="password_confirmation"
                        type="password"
                        class="mt-1 block w-full"
                        v-model="form.password_confirmation"
                        required
                        autocomplete="new-password"
                    />

                    <InputError
                        class="mt-2"
                        :message="form.errors.password_confirmation"
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
