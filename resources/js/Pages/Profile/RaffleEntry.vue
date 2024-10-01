<script setup>
import { Link, useForm, usePage } from "@inertiajs/vue3";
import { ref, computed, onMounted } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";

import {
    Badge,
    BadgeCheck,
    CalendarCheck2,
    ChevronsDown,
    Info,
    Link2,
    Network,
    QrCode,
    SquareArrowUpRight,
    Ticket,
    TrendingDown,
    TrendingUp,
    UsersRound,
    Wallet2,
} from "lucide-vue-next";

const pageState = ref("start");
const selectedTier = ref(0);

const { props } = usePage();
const user = props.auth.user;
const tiers = props.tier;

const tierStarter = tiers[0];

const tierPremium = tiers[1];

const tierPrestige = tiers[2];

console.log(tierStarter.value);

onMounted(() => {
    // updateImageSrc();
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
        </template>
        <div class="p-4">
            <div
                v-if="pageState == 'start'"
                class="grid grid-cols-1 sm:grid-cols-2 gap-5 w-full mt-4 mb-4"
            >
                <div
                    @click="selectedTier = 1"
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
                    @click="selectedTier = 2"
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
                    @click="selectedTier = 3"
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
        </div>
    </AuthenticatedLayout>
</template>
