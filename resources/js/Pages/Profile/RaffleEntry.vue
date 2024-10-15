<script setup>
import { usePage, Link } from "@inertiajs/vue3";
import { ref, onMounted, onUnmounted } from "vue";

import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import axios from "axios";
import { toast } from "vue3-toastify";
import SecondaryButton from "@/Components/SecondaryButton.vue";

import PrimaryButton from "@/Components/PrimaryButton.vue";

const pageState = ref("start");
const selectedTier = ref(0);
const { props } = usePage();
const user = props.auth.user;
const tiers = props.tier;

const tierStarter = tiers[0];
const tierPremium = tiers[1];
const tierPrestige = tiers[2];

const formatDate = (dateString) => {
    const date = new Date(dateString);
    const options = { year: "numeric", month: "long", day: "2-digit" };
    return date.toLocaleDateString("en-US", options);
};

const drawDate = props.draw_date ? formatDate(props.draw_date) : null;

const selectTierStarter = async () => {
    if (user.points >= tierStarter.points) {
        selectedTier.value = 1;
        pageState.value = "set";
    } else {
        toast.warning("You do not have enough points for starter tier.");
    }
};

const selectTierPremium = async () => {
    if (user.points >= tierPremium.points) {
        selectedTier.value = 2;
        pageState.value = "set";
    } else {
        toast.warning("You do not have enough points for premium tier.");
    }
};

const selectTierPrestige = async () => {
    if (user.points >= tierPrestige.points) {
        selectedTier.value = 3;
        pageState.value = "set";
    } else {
        toast.warning("You do not have enough points for prestige tier.");
    }
};

const submit = () => {
    toast.loading("Submitting your entry...");
    axios
        .post("/raffle", {
            tier: selectedTier.value,
        })
        .then(function (response) {
            if (response.status == 200) {
                window.location.href = "/raffle";
            }
            toast.done();
        })
        .catch(function (error) {
            toast.error("Something went wrong, Try again!");
        });
};

const position = ref(window.innerWidth); // Start position from the right
const speed = 2; // Speed of the running text
let animationFrameId;

const animateText = () => {
    position.value -= speed;

    const textWidth = document.querySelector(".inline-block").offsetWidth;

    if (position.value < -textWidth) {
        position.value = window.innerWidth;
    }

    animationFrameId = requestAnimationFrame(animateText);
};

onMounted(() => {
    animationFrameId = requestAnimationFrame(animateText);
});

onUnmounted(() => {
    cancelAnimationFrame(animationFrameId);
});
</script>

<template>
    <Head title=" Raffle Entry" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
            >
                Raffle Tier
            </h2>
            <div class="flex justify-between items-center mt-2">
                <p>
                    Current Points:
                    <span class="font-bold text-lg text-blue-500">{{
                        props.auth.user.points
                    }}</span>
                </p>
                <p>
                    Total Entries:
                    <span class="font-bold text-lg">{{ props.entries }}</span>
                </p>
            </div>
        </template>
        <div
            class="w-full p-2 bg-gradient-to-r from-pink-500 via-violet-500 to-blue-500"
        >
            <div
                class="inline-block whitespace-nowrap font- text-2xl text-white"
                :style="{ transform: `translateX(${position}px)` }"
            >
                Upcoming draw date:
                <span class="font-bold">{{ drawDate }}</span>
            </div>
        </div>
        <div class="p-4">
            <div
                v-if="pageState == 'start'"
                class="grid grid-cols-1 sm:grid-cols-2 gap-5 w-full mt-6 mb-4"
            >
                <div
                    @click="selectTierStarter"
                    class="flex h-32 rounded-xl p-4 shadow-lg shadow-stone-400 bg-gradient-to-r from-stone-500 to-stone-400 border-2 border-stone-400"
                >
                    <div
                        class="flex flex-col justify-between items-start w-full"
                    >
                        <h4
                            class="text-end font-semibold capitalize text-gray-50"
                        >
                            {{ tierStarter.tier }}
                        </h4>
                        <h6 class="text-end font-black text-6xl text-gray-50">
                            {{ tierStarter.points }}
                        </h6>
                        <p class="text-sm font-bold text-gray-50 uppercase">
                            Prize:
                            <span v-for="prize in tierStarter.prizes">
                                {{ prize.quantity }} {{ prize.name }}
                            </span>
                        </p>
                    </div>
                    <img
                        class="w-32"
                        src="../../../image/bronze.svg"
                        alt=""
                        srcset=""
                    />
                </div>

                <div
                    @click="selectTierPremium"
                    class="flex h-32 rounded-xl p-4 shadow-lg shadow-gray-300 bg-gradient-to-r from-gray-500 to-gray-300 border-2 border-gray-300"
                >
                    <div
                        class="flex flex-col justify-between items-start w-full"
                    >
                        <h4
                            class="text-end font-semibold capitalize text-gray-50"
                        >
                            {{ tierPremium.tier }}
                        </h4>
                        <h6 class="text-end font-black text-6xl text-gray-50">
                            {{ tierPremium.points }}
                        </h6>
                        <p class="text-sm font-bold text-gray-50 uppercase">
                            Prize:
                            <span v-for="prize in tierPremium.prizes">
                                {{ prize.quantity }} {{ prize.name }}
                            </span>
                        </p>
                    </div>
                    <img
                        class="w-32"
                        src="../../../image/silver.svg"
                        alt=""
                        srcset=""
                    />
                </div>

                <div
                    @click="selectTierPrestige"
                    class="flex h-32 rounded-xl p-4 shadow-lg bg-gradient-to-r from-yellow-500 to-yellow-200 border-2 border-yellow-200 shadow-yellow-200"
                >
                    <div
                        class="flex flex-col justify-between items-start w-full"
                    >
                        <h4
                            class="text-end font-semibold capitalize text-gray-50"
                        >
                            {{ tierPrestige.tier }}
                        </h4>
                        <h6 class="text-end font-black text-6xl text-gray-50">
                            {{ tierPrestige.points }}
                        </h6>
                        <p class="text-sm font-bold text-gray-50 uppercase">
                            Prize:
                            <span v-for="prize in tierPrestige.prizes">
                                {{ prize.quantity }} {{ prize.name }}
                            </span>
                        </p>
                    </div>
                    <img
                        class="w-32"
                        src="../../../image/gold.svg"
                        alt=""
                        srcset=""
                    />
                </div>
            </div>

            <div
                class="flex flex-col w-full shadow rounded-xl mt-6 mb-4 p-4 bg-gray-50"
                v-if="pageState == 'set'"
            >
                <div v-if="selectedTier == 1">
                    <h2 class="font-bold text-lg">Starter Tier</h2>
                    <div class="grid grid-cols-1 gap-4 mt-4">
                        <h4 class="font-medium text-lg">Prizes</h4>
                        <div
                            class="flex flex-col justify-center items-center space-y-2"
                        >
                            <img
                                src="../../../image/prize/starter/1.png"
                                alt=""
                                srcset=""
                            />
                            <p class="font-medium text-lg">
                                1 Sack Premium Rice
                            </p>
                        </div>
                    </div>
                </div>

                <div v-if="selectedTier == 2">
                    <h2 class="font-bold text-lg">Premium Tier</h2>
                    <div class="grid grid-cols-1 gap-4 mt-4">
                        <h4 class="font-medium text-lg">Prizes</h4>
                        <div
                            class="flex flex-col justify-center items-center space-y-2"
                        >
                            <img
                                src="../../../image/prize/starter/1.png"
                                alt=""
                                srcset=""
                            />
                            <p class="font-medium text-lg">
                                1 Mobile Android Phone
                            </p>
                        </div>
                    </div>
                </div>

                <div v-if="selectedTier == 3">
                    <h2 class="font-bold text-lg">Prestige Tier</h2>
                    <div class="grid grid-cols-1 gap-4 mt-4">
                        <h4 class="font-medium text-lg">Prizes</h4>
                        <div
                            class="flex flex-col justify-center items-center space-y-2"
                        >
                            <img
                                src="../../../image/prize/starter/1.png"
                                alt=""
                                srcset=""
                            />
                            <p class="font-medium text-lg">1 CRF Motorcycle</p>
                        </div>
                    </div>
                </div>
                <div class="flex flex-col space-y-4 mt-4">
                    <PrimaryButton @click="submit" class="mt-4"
                        >Submit 1 Entry</PrimaryButton
                    >

                    <Link
                        class="w-full sm:w-52 mt-0 sm:mt-4"
                        :href="route('raffle.index')"
                    >
                        <SecondaryButton>Back</SecondaryButton>
                    </Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
