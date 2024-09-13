<script setup lang="ts">
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Link, useForm, usePage } from "@inertiajs/vue3";
import { computed } from "vue";
import {
    Activity,
    CalendarCheck,
    CalendarCheck2,
    Info,
    TrendingDown,
    TrendingUp,
    UsersRound,
    Wallet,
    Wallet2,
} from "lucide-vue-next";

defineProps<{
    // mustVerifyEmail?: Boolean;
    status?: String;
}>();

const user = usePage().props.auth.user;

const completionPercentage = computed(() => {
    switch (user.level) {
        case 1:
            return "30%";
        case 2:
            return "70%";
        case 3:
            return "100%";
        // Add more cases as needed
        default:
            return "0%"; // Default width if level does not match
    }
});

const form = useForm({
    firstname: user.profile.firstname,
    middlename: user.profile.middlename,
    lastname: user.profile.lastname,
    extension: user.profile.extension,
    precinct_number: user.profile.precinct_number,
    avatar: user.profile.avatar,
    id_type: user.profile.id_type,
    id_card_front: user.profile.id_card_front,
    id_card_back: user.profile.id_card_back,
    region: user.profile.region,
    province: user.profile.province,
    municipality_city: user.profile.municipality_city,
    barangay: user.profile.barangay,
    street: user.profile.street,
    gender: user.profile.gender,
    birthdate: user.profile.birthdate,
    civil_status: user.profile.civil_status,
    blood_type: user.profile.blood_type,
    religion: user.profile.religion,
    tribe: user.profile.tribe,
    industry_sector: user.profile.industry_sector,
    occupation: user.profile.occupation,
    income_level: user.profile.income_level,
    affiliation: user.profile.affiliation,
    facebook: user.profile.facebook,
});
</script>

<template>
    <section>
        <form
            @submit.prevent="form.patch(route('profile.update'))"
            class="flex flex-col justify-center items-center mt-2"
        >
            <div class="bg-blue-400 w-36 h-36 rounded-xl"></div>
            <h4 class="font-medium text-xl mt-2">Johnny Roger Demadara</h4>
            <h5 class="text-sm text-gray-600">+63 997 2430 944</h5>
            <div class="flex flex-col w-full my-4">
                <div class="flex justify-between mb-1">
                    <div class="flex">Profile Completion</div>
                    <div class="flex">{{ completionPercentage }}</div>
                </div>
                <div
                    :class="[
                        'h-2 rounded-full bg-green-500 transition-width duration-500 ease-in-out',
                        { 'w-[33.33%]': user.level === 1 },
                        { 'w-[66.66%]': user.level === 2 },
                        { 'w-[100%]': user.level === 3 },
                    ]"
                />
            </div>
            <div class="flex items-center w-full rounded p-2 bg-red-100">
                <Info class="text-red-400 mr-1" :size="24" />
                <p class="text-red-500">
                    Complete your profile to proceed to level 2
                </p>
            </div>

            <div
                class="flex items-center w-full rounded p-2 mt-2 bg-orange-100"
            >
                <Info class="text-orange-400 mr-1" :size="24" />
                <p class="text-orange-500">
                    Verify your phone number and upload <br />
                    your valid id to proceed to level 3
                </p>
            </div>

            <div class="w-full h-0.5 bg-gray-100"></div>

            <div class="flex flex-col w-full space-y-4 mt-4 mb-4">
                <div
                    class="flex flex-col h-32 rounded-xl p-4 shadow-md bg-gray-50"
                >
                    <div class="flex justify-between h-full">
                        <Wallet2 class="text-green-500" :size="38" />
                        <h2 class="font-bold text-4xl text-gray-600">69</h2>
                    </div>
                    <div class="flex justify-between">
                        <p class="text-gray-600">Points</p>
                        <div class="flex">
                            <span
                                class="flex items-center font-medium rounded-full px-2 text-green-700 bg-green-200 mr-1"
                            >
                                <TrendingUp
                                    class="text-green-600 mr-1"
                                    :size="22"
                                />+16% </span
                            >than last month
                        </div>
                    </div>
                </div>

                <div
                    class="flex flex-col h-32 rounded-xl p-4 shadow-md bg-gray-50"
                >
                    <div class="flex justify-between h-full">
                        <UsersRound class="text-blue-500" :size="38" />
                        <h2 class="font-bold text-4xl text-gray-600">36</h2>
                    </div>
                    <div class="flex justify-between">
                        <p class="text-gray-600">Referrals</p>
                        <div class="flex">
                            <span
                                class="flex items-center font-medium rounded-full px-2 text-green-700 bg-green-200 mr-1"
                            >
                                <TrendingUp
                                    class="text-green-600 mr-1"
                                    :size="22"
                                />+7% </span
                            >than last month
                        </div>
                    </div>
                </div>

                <div
                    class="flex flex-col h-32 rounded-xl p-4 shadow-md bg-gray-50"
                >
                    <div class="flex justify-between h-full">
                        <CalendarCheck2 class="text-red-500" :size="38" />
                        <h2 class="font-bold text-4xl text-gray-600">4</h2>
                    </div>
                    <div class="flex justify-between">
                        <p class="text-gray-600">Activity</p>
                        <div class="flex">
                            <span
                                class="flex items-center font-medium rounded-full px-2 text-red-700 bg-red-200 mr-1"
                            >
                                <TrendingDown
                                    class="text-red-600 mr-1"
                                    :size="22"
                                />+2% </span
                            >than last year
                        </div>
                    </div>
                </div>
            </div>
            <div v-if="user.email_verified_at === null">
                <p class="text-sm mt-2 text-gray-800 dark:text-gray-200">
                    Your email address is unverified.
                    <Link
                        :href="route('verification.send')"
                        method="post"
                        as="button"
                        class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                    >
                        Click here to re-send the verification email.
                    </Link>
                </p>

                <div
                    v-show="status === 'verification-link-sent'"
                    class="mt-2 font-medium text-sm text-green-600 dark:text-green-400"
                >
                    A new verification link has been sent to your email address.
                </div>
            </div>
        </form>
    </section>
</template>
