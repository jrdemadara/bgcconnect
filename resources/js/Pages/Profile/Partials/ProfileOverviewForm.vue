<script setup>
import { Link, usePage } from "@inertiajs/vue3";
import { ref, computed, onMounted } from "vue";
import {
    Badge,
    BadgeCheck,
    CalendarCheck2,
    ChevronsDown,
    Info,
    Network,
    QrCode,
    SquareArrowUpRight,
    Ticket,
    TrendingDown,
    TrendingUp,
    UsersRound,
    Wallet2,
} from "lucide-vue-next";

import { ModalsContainer, useModal } from "vue-final-modal";
import ModalQR from "./QRModal.vue";

const photo = ref("");

const props = usePage().props;
const user = usePage().props.auth.user;
const completionPercentage = computed(() => {
    switch (user.level) {
        case 1:
            return "25%";
        case 2:
            return "50%";
        case 3:
            return "75%";
        case 4:
            return "100%";
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

const updateImageSrc = () => {
    const storedImage = localStorage.getItem("profilePhoto");
    const avatar = props.avatar;
    if (storedImage) {
        photo.value = storedImage;
    } else {
        photo.value = avatar;
    }
};

const formattedDate = ref(formatDate(props.draw));

function formatDate(dateString) {
    const options = { year: "numeric", month: "long", day: "numeric" };
    return new Date(dateString).toLocaleDateString("en-US", options);
}

onMounted(() => {
    updateImageSrc();
});
</script>

<template>
    <div class="flex flex-col justify-center items-center mt-4">
        <div class="relative">
            <img
                class="w-48 rounded-xl border-2 border-gray-300"
                :src="photo"
                alt="photo"
            />

            <img
                v-if="user.level == 1"
                class="absolute w-9 left-1/2 transform -translate-x-1/2 bottom-0 mb-1"
                src="../../../../image/level1.svg"
                alt="level img"
            />
            <img
                v-if="user.level == 2"
                class="absolute w-14 left-1/2 transform -translate-x-1/2 bottom-0 mb-1"
                src="../../../../image/level2.svg"
                alt="level img"
            />
            <img
                v-if="user.level == 3"
                class="absolute w-14 left-1/2 transform -translate-x-1/2 bottom-0 mb-1"
                src="../../../../image/level3.svg"
                alt="level img"
            />
            <img
                v-if="user.level == 4"
                class="absolute w-14 left-1/2 transform -translate-x-1/2 bottom-0 mb-1"
                src="../../../../image/level4.svg"
                alt="level img"
            />
            <img
                v-if="user.level == 5"
                class="absolute w-14 left-1/2 transform -translate-x-1/2 bottom-0 mb-1"
                src="../../../../image/level5.svg"
                alt="level img"
            />
        </div>

        <Link
            :href="route('profile.edit')"
            class="flex justify-center items-center mt-2 text-blue-500 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
        >
            Profile <SquareArrowUpRight class="ml-1" :size="16" />
        </Link>
        <h4
            class="flex justify-center items-center font-medium text-xl dark:text-white capitalize"
        >
            {{ props.profile.firstname }}
            {{ props.profile.lastname }}
        </h4>

        <div
            class="flex flex-col items-center text-gray-600 dark:text-gray-400"
        >
            {{ user.code }}
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
                    { 'w-[25%]': user.level === 1 },
                    { 'w-[50%]': user.level === 2 },
                    { 'w-[75%]': user.level === 3 },
                    { 'w-[100%]': user.level === 4 },
                ]"
            />
        </div>
        <div
            v-show="user.level === 1"
            class="flex flex-col sm:flex-row justify-between items-center w-full rounded-xl p-2 sm:p-4 bg-orange-100"
        >
            <div class="flex">
                <Info class="text-orange-400 mr-1" :size="24" />
                <p class="text-orange-500">
                    Complete your profile and elevate to level 2.
                </p>
            </div>
            <Link
                :href="route('profile.edit')"
                class="underline mt-1 sm:mt-0 text-blue-600 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
            >
                Complete my profile
            </Link>
        </div>

        <div
            v-show="user.level === 2"
            class="flex flex-col sm:flex-row justify-between items-center w-full rounded-xl p-2 sm:p-4 bg-orange-100"
        >
            <div class="flex">
                <Info class="text-orange-400 mr-1" :size="24" />
                <p class="text-orange-500">
                    Verify your phone number to proceed to level 3.
                </p>
            </div>
            <Link
                :href="route('verify.index')"
                class="underline mt-1 sm:mt-0 text-blue-600 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
            >
                Verify Phone
            </Link>
        </div>

        <div
            v-show="user.level == 3 && !user.id_check"
            class="flex flex-col sm:flex-row justify-between items-center w-full rounded-xl p-2 sm:p-4 bg-orange-100"
        >
            <div class="flex">
                <Info class="text-orange-400 mr-1" :size="24" />
                <p class="text-orange-500">
                    Upload your valid id to proceed to level 3.
                </p>
            </div>
            <Link
                :href="route('verify.id')"
                class="underline mt-1 sm:mt-0 text-blue-600 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
            >
                Upload ID
            </Link>
        </div>

        <div
            v-show="user.id_check"
            class="flex flex-col items-center w-full rounded-xl p-2 mt-3 bg-orange-100"
        >
            <div class="flex w-full">
                <Info class="text-orange-400 mr-1" :size="24" />
                <p class="text-orange-500">
                    We are currently verifying your ID and will <br />
                    notify you once it's complete.
                </p>
            </div>
        </div>

        <div class="w-full h-0.5 mt-4 bg-gray-100 dark:bg-gray-600"></div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 w-full mt-4 mb-4">
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
                class="flex flex-col h-32 rounded-xl p-4 shadow-md bg-gradient-to-r from-blue-50 to-gray-100 dark:from-gray-600 dark:to-gray-700"
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
                    <p class="text-gray-600 dark:text-gray-100">Referrals</p>
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
                class="flex flex-col h-32 rounded-xl p-4 shadow-md bg-gradient-to-r from-violet-50 to-gray-100 dark:from-gray-600 dark:to-gray-700"
            >
                <div class="flex justify-between h-full">
                    <Network class="text-blue-500" :size="38" />
                    <h2
                        class="font-bold text-4xl text-gray-600 dark:text-gray-100"
                    >
                        36
                    </h2>
                </div>
                <div class="flex justify-between">
                    <p class="text-gray-600 dark:text-gray-100">Downlines</p>
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
                class="flex flex-col h-32 rounded-xl p-4 shadow-md bg-gradient-to-r from-red-50 to-gray-100 dark:from-gray-600 dark:to-gray-700"
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
                            />-2% </span
                        >than last year
                    </div>
                </div>
            </div>
        </div>
        <div class="w-full h-0.5 mt-4 bg-gray-100 dark:bg-gray-600"></div>

        <div class="flex flex-col justify-center items-center mt-10">
            <div
                class="flex flex-col justify-center items-center border-4 border-dotted border-orange-200 rounded-lg p-4 w-full text-lg text-center text-gray-600 dark:text-white"
            >
                Next draw:

                <span class="font-bold text-xl text-orange-500">
                    {{ props.draw }}</span
                >
            </div>
            <ChevronsDown
                class="animate-bounce mt-2 text-blue-500"
                :size="42"
            />
        </div>

        <Link
            :href="route('raffle.index')"
            class="flex justify-center items-center animate-pulse w-full h-16 mt-2 rounded-xl font-bold text-xl text-white bg-gradient-to-r from-pink-500 via-violet-500 to-blue-500"
        >
            <Ticket class="mr-1" :size="32" />Submit your raffle entry
        </Link>
    </div>
</template>
