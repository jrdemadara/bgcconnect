<script setup lang="ts">
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Link, useForm, usePage } from "@inertiajs/vue3";
import { ref, computed } from "vue";
import {
    Badge,
    BadgeCheck,
    CalendarCheck2,
    ChevronsDown,
    Info,
    QrCode,
    Ticket,
    TrendingDown,
    TrendingUp,
    UsersRound,
    Wallet2,
} from "lucide-vue-next";

import { ModalsContainer, useModal } from "vue-final-modal";
import ModalQR from "./QRModal.vue";

defineProps<{
    // mustVerifyEmail?: Boolean;
    status?: String;
}>();

const user = usePage().props.auth.user;
const completionPercentage = computed(() => {
    switch (user.level) {
        case 1:
            return "10%";
        case 2:
            return "30%";
        case 3:
            return "50%";
        case 4:
            return "70%";
        case 5:
            return "100%";
        // Add more cases as needed
        default:
            return "0%"; // Default width if level does not match
    }
});

const { open, close } = useModal({
    component: ModalQR,
    attrs: {
        qrcode: user.code,
        onConfirm() {
            close();
        },
    },
    slots: {
        default: "<p>UseModal: The content of the modal</p>",
    },
});
</script>

<template>
    <section>
        <div class="flex flex-col justify-center items-center mt-4">
            <img
                class="bg-blue-400 w-36 h-36 rounded-xl border-2 border-gray-300"
                src="../../../../image/me.jpg"
                alt=""
            />

            <h4 class="font-medium text-xl dark:text-white capitalize">
                {{ user.profile.firstname }}
                {{ user.profile.lastname }}
            </h4>

            <div
                class="flex flex-col items-center text-gray-600 dark:text-gray-400"
            >
                {{ user.phone }}
                <span
                    v-show="!user.phone_verified_at"
                    class="flex justify-center items-center w-fit px-2 py-0.5 mt-1 font-medium text-sm rounded-full text-red-700 bg-red-200"
                >
                    <Badge class="text-red-700 mr-0.5" :size="16" />
                    unverified
                </span>
                <span
                    v-show="user.phone_verified_at"
                    class="flex justify-center items-center w-fit px-2 py-0.5 mt-1 font-medium text-sm rounded-full text-green-700 bg-green-200"
                >
                    <BadgeCheck class="text-green-700 mr-0.5" :size="16" />
                    verified
                </span>
                <button
                    v-show="user.level > 1"
                    class="flex justify-center items-center mt-2 text-gray-900 dark:text-white"
                    @click="() => open()"
                >
                    <QrCode
                        class="text-gray-900 dark:text-white mt-1 mr-1"
                        :size="20"
                    />
                    <span class="underline cursor-default">Show my QR</span>
                </button>

                <ModalsContainer />
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
                        { 'w-[10%]': user.level === 1 },
                        { 'w-[30%]': user.level === 2 },
                        { 'w-[50%]': user.level === 3 },
                        { 'w-[70%]': user.level === 4 },
                        { 'w-[100%]': user.level === 5 },
                    ]"
                />
            </div>
            <div
                v-show="user.level === 1"
                class="flex flex-col items-center w-full rounded p-2 bg-red-100"
            >
                <div class="flex w-full">
                    <Info class="text-red-400 mr-1" :size="24" />
                    <p class="text-red-500">
                        Complete your profile and elevate to level 2.
                    </p>
                </div>
                <Link
                    :href="route('profile.edit')"
                    class="underline mt-1 text-sm text-blue-600 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                >
                    Click to complete
                </Link>
            </div>

            <div
                v-show="user.level === 2"
                class="flex flex-col items-center w-full rounded p-2 mt-3 bg-orange-100"
            >
                <div class="flex w-full">
                    <Info class="text-orange-400 mr-1" :size="24" />
                    <p class="text-orange-500">
                        Verify your phone number to proceed to level 3.
                    </p>
                </div>
                <Link
                    :href="route('verify.index')"
                    class="underline mt-1 text-sm text-blue-600 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                >
                    Verify Phone
                </Link>
            </div>

            <div
                v-show="user.level <= 3"
                class="flex flex-col items-center w-full rounded p-2 mt-3 bg-orange-100"
            >
                <div class="flex w-full">
                    <Info class="text-orange-400 mr-1" :size="24" />
                    <p class="text-orange-500">
                        Upload your valid id to proceed to level 3.
                    </p>
                </div>
                <Link
                    :href="route('verify.id')"
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
        </div>
    </section>
</template>
