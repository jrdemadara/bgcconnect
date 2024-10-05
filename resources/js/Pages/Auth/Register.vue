<script setup lang="ts">
import { ref } from "vue";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { Loader2 } from "lucide-vue-next";
import { toast } from "vue3-toastify";

const prop = defineProps<{
    code?: string;
}>();

const form = useForm({
    firstname: "",
    lastname: "",
    phone: "",
    password: "",
    password_confirmation: "",
    code: prop.code || "",
});

const isFilipino = ref(false);
const termsAccept = ref(false);

const submit = () => {
    form.post(route("register"), {
        onFinish: () => {
            form.reset("password", "password_confirmation");
        },
        onSuccess: () => {
            toast.success("Registration successful!");
        },
        onError: () => {
            toast.error("Registration failed. Please try again.");
        },
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Register" />
        <div class="flex justify-between items-center mt-2">
            <h2 class="font-bold text-xl text-black dark:text-gray-50">
                Sign up
            </h2>
            <Link
                :href="route('login')"
                class="underline font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
            >
                Login to my account
            </Link>
        </div>
        <form @submit.prevent="submit" class="mt-6">
            <div>
                <InputLabel for="code" value="Invite Code" />

                <div class="flex">
                    <TextInput
                        id="code"
                        type="text"
                        class="mt-1 block w-full uppercase text-gray-400"
                        v-model="form.code"
                        readonly
                    />
                </div>
            </div>
            <div
                class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4 mt-4"
            >
                <div>
                    <InputLabel for="firstname" value="Firstname" />

                    <TextInput
                        id="firstname"
                        type="text"
                        class="mt-1 block w-full capitalize"
                        v-model="form.firstname"
                        required
                        autofocus
                        autocomplete="firstname"
                    />

                    <InputError class="mt-2" :message="form.errors.firstname" />
                </div>

                <div>
                    <InputLabel for="lastname" value="Lastname" />

                    <TextInput
                        id="lastname"
                        type="text"
                        class="mt-1 block w-full capitalize"
                        v-model="form.lastname"
                        required
                        autofocus
                        autocomplete="lastname"
                    />

                    <InputError class="mt-2" :message="form.errors.lastname" />
                </div>
            </div>

            <div class="mt-4">
                <InputLabel for="phone" value="Phone" />

                <TextInput
                    id="phone"
                    type="tel"
                    class="mt-1 block w-full"
                    v-model="form.phone"
                    pattern="[0-9]{4}[0-9]{3}[0-9]{4}"
                    required
                    placeholder="ex.09123456789"
                    autocomplete="phone"
                />

                <InputError class="mt-2" :message="form.errors.phone" />
            </div>

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

            <div class="flex flex-col space-y-2 mt-4">
                <div
                    class="flex items-center space-x-2 font-medium text-gray-700 dark:text-gray-300"
                >
                    <input type="checkbox" v-model="isFilipino" />
                    <p>I am a Filipino</p>
                </div>

                <div
                    class="flex items-center space-x-2 font-medium text-gray-700 dark:text-gray-300"
                >
                    <input type="checkbox" v-model="termsAccept" />
                    <p>
                        I accept the
                        <a
                            class="text-blue-500"
                            href="#"
                            target="_blank"
                            rel="noopener noreferrer"
                            >Terms & Conditions</a
                        >
                    </p>
                </div>
            </div>

            <PrimaryButton
                class="my-6"
                :class="{
                    'opacity-25':
                        form.processing || !isFilipino || !termsAccept,
                }"
                :disabled="form.processing || !isFilipino || !termsAccept"
            >
                <Loader2 v-if="form.processing" class="w-10 animate-spin" />
                Register
            </PrimaryButton>
        </form>
    </GuestLayout>
</template>
