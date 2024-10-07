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
const loading = ref(false);
const loadingToastId = ref(null);

const submit = () => {
    loading.value = true;
    loadingToastId.value = toast.loading("Requesting password reset code...");
    axios
        .post("/password-reset", {
            phone: phone.value,
            password: password.value,
        })
        .then(function (response) {
            console.log(response);
            toast.done(loadingToastId.value);
            toast.success("Password reset successful.");
            loading.value = false;
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

        <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
            Forgot your password? No problem. Just let us know your phone number
            and we will sent you a sms with the password reset code that will
            allow you to choose a new one.
        </div>

        <form @submit.prevent="submit">
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
    </GuestLayout>
</template>
