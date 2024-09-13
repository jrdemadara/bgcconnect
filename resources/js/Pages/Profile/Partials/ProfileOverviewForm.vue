<script setup lang="ts">
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Link, useForm, usePage } from "@inertiajs/vue3";
import { computed } from "vue";
import {
    Activity,
    Badge,
    BadgeCheck,
    CalendarCheck,
    CalendarCheck2,
    ChevronDown,
    ChevronsDown,
    Info,
    LoaderPinwheel,
    Phone,
    QrCode,
    Smartphone,
    Ticket,
    Tickets,
    TrendingDown,
    TrendingUp,
    UserRoundCheck,
    UserRoundX,
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
            class="flex flex-col justify-center items-center mt-4"
        >
            <img
                class="bg-blue-400 w-36 h-36 rounded-xl"
                src="../../../../image/me.jpg"
                alt=""
            />

            <h4 class="font-medium text-xl dark:text-white capitalize">
                {{ user.profile.firstname }}
                {{ user.profile.lastname }}
                {{ user.profile.middlename }}
                {{ user.profile.extension }}
            </h4>

            <div
                class="flex flex-col items-center text-gray-600 dark:text-gray-400"
            >
                {{ user.phone }}
                <span
                    v-show="!user.phone_verified_at"
                    class="flex justify-center items-center w-fit px-2 py-0.5 mt-1 font-medium rounded-full text-red-700 bg-red-200"
                >
                    <Badge class="text-red-700 mr-1" :size="16" />
                    unverified
                </span>
                <span
                    v-show="user.phone_verified_at"
                    class="flex justify-center items-center w-fit px-2 py-0.5 mt-1 font-medium rounded-full text-green-700 bg-green-200"
                >
                    <BadgeCheck class="text-green-700 mr-1" :size="16" />
                    verified
                </span>
                <button
                    class="flex justify-center items-center mt-2 text-gray-900 dark:text-white"
                >
                    <QrCode
                        class="text-gray-900 dark:text-white mt-1 mr-1"
                        :size="20"
                    />
                    <span class="underline">Show my QR</span>
                </button>
            </div>
            <div class="flex flex-col w-full my-6">
                <div class="flex justify-between mb-1">
                    <div class="flex dark:text-white">Profile Completion</div>
                    <div class="flex dark:text-white">
                        {{ completionPercentage }}
                    </div>
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
            <div
                v-show="user.level < 2"
                class="flex flex-col items-center w-full rounded p-2 bg-red-100"
            >
                <div class="flex w-full">
                    <Info class="text-red-400 mr-1" :size="24" />
                    <p class="text-red-500">
                        Complete your profile to proceed to level 2
                    </p>
                </div>
                <Link
                    :href="route('login')"
                    class="underline mt-1 text-sm text-blue-600 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                >
                    Click to complete
                </Link>
            </div>

            <div
                v-show="user.level < 3"
                class="flex flex-col items-center w-full rounded p-2 mt-3 bg-orange-100"
            >
                <div class="flex w-full">
                    <Info class="text-orange-400 mr-1" :size="24" />
                    <p class="text-orange-500">
                        Verify your phone number and upload <br />
                        your valid id to proceed to level 3.
                    </p>
                </div>
                <Link
                    :href="route('login')"
                    class="underline mt-1 text-sm text-blue-600 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                >
                    Verify Phone & ID
                </Link>
            </div>

            <div class="w-full h-0.5 mt-4 bg-gray-100 dark:bg-gray-600"></div>

            <div class="flex flex-col w-full mt-4 mb-4">
                <div
                    class="flex flex-col h-32 rounded-xl p-4 shadow-md bg-gradient-to-r from-green-50 to-gray-100 dark:from-gray-600 dark:to-gray-700"
                >
                    <div class="flex justify-between h-full">
                        <Wallet2 class="text-green-500" :size="38" />
                        <h2
                            class="font-bold text-4xl text-gray-600 dark:text-gray-100"
                        >
                            {{ user.points }}
                        </h2>
                    </div>
                    <div class="flex justify-between">
                        <p class="text-gray-600 dark:text-gray-100">Points</p>
                        <div class="flex dark:text-gray-100">
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
                    class="flex flex-col h-32 rounded-xl p-4 mt-4 shadow-md bg-gradient-to-r from-blue-50 to-gray-100 dark:from-gray-600 dark:to-gray-700"
                >
                    <div class="flex justify-between h-full">
                        <UsersRound class="text-blue-500" :size="38" />
                        <h2
                            class="font-bold text-4xl text-gray-600 dark:text-gray-100"
                        >
                            36
                        </h2>
                    </div>
                    <div class="flex justify-between">
                        <p class="text-gray-600 dark:text-gray-100">
                            Referrals
                        </p>
                        <div class="flex dark:text-gray-100">
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
                    class="flex flex-col h-32 rounded-xl p-4 mt-4 shadow-md bg-gradient-to-r from-red-50 to-gray-100 dark:from-gray-600 dark:to-gray-700"
                >
                    <div class="flex justify-between h-full">
                        <CalendarCheck2 class="text-red-500" :size="38" />
                        <h2
                            class="font-bold text-4xl text-gray-600 dark:text-gray-100"
                        >
                            4
                        </h2>
                    </div>
                    <div class="flex justify-between">
                        <p class="text-gray-600 dark:text-gray-100">Activity</p>
                        <div class="flex dark:text-gray-100">
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
                <div
                    class="w-full h-0.5 mt-6 bg-gray-100 dark:bg-gray-600"
                ></div>

                <div class="flex flex-col justify-center items-center mt-10">
                    <div
                        class="flex flex-col justify-center items-center border-4 border-dotted border-orange-200 rounded-lg p-4 w-full text-lg text-center text-gray-600 dark:text-white"
                    >
                        Next draw:
                        <br />
                        <span class="font-bold text-xl text-orange-500"
                            >September 14, 2024 at 10:00 AM</span
                        >
                    </div>
                    <ChevronsDown
                        class="animate-bounce mt-2 text-blue-500"
                        :size="42"
                    />
                </div>

                <button
                    class="flex justify-center items-center animate-pulse h-16 mt-2 rounded-xl font-medium text-xl text-white bg-gradient-to-r from-yellow-400 via-pink-500 to-purple-500"
                >
                    <Ticket class="mr-1" :size="28" />Submit your raffle entry
                </button>
            </div>
        </form>
    </section>
</template>
