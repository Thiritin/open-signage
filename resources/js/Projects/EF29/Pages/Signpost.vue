<script setup>
import { computed, onMounted, onUnmounted, ref } from "vue";

const props = defineProps({
    title: {
        type: String,
        default: "Event Rooms",
    },
    schedule: {
        type: Array,
        default: [],
    },
    appScreen: {
        type: Array,
        default: [],
    },
    rooms: {
        type: Array,
        default: [],
    },
    page: {
        type: Object,
        required: false,
    },
    pageSwitchingTimer: {
        type: Number,
        default: 15000,
    },
});

const currentTime = ref(DateTime.now());
const currentPageIndex = ref(0);

onMounted(() => {
    const interval = setInterval(() => {
        currentTime.value = DateTime.now();
    }, 5000);
    const pageSwitcher = setInterval(() => {
        currentPageIndex.value =
            (currentPageIndex.value + 1) % signPostPages.value.length;
    }, props.pageSwitchingTimer);
    onUnmounted(() => {
        clearInterval(interval);
        clearInterval(pageSwitcher);
    });
});

const nextEvent = function (room) {
    return computed(() => {
        return cloneDeep(props.schedule)
            .filter((event) => {
                return event.room_id === room.id;
            })
            .filter((event) => {
                return (
                    currentTime.value <=
                    DateTime.fromISO(event.ends_at).plus({
                        minutes: event.delay,
                    })
                );
            })
            .shift();
    });
};

function containsOnly(title) {
    return (
        title.includes("Only") ||
        title.includes("only") ||
        title.includes("ONLY")
    );
}

const signPostPages = computed(() => {
    return chunkArray(props.rooms, 3);
});

const currentSignPostPage = computed(() => {
    return signPostPages.value[currentPageIndex.value];
});

import IconRouter from "@/Projects/System/Components/IconRouter.vue";
import { DateTime } from "luxon";
import { cloneDeep } from "lodash";
import chunkArray from "@/chunkArray.js";
</script>

<template>
    <Transition mode="out-in">
        <div
            :key="currentPageIndex"
            class="h-screen overflow-hidden flex flex-col justify-between w-screen"
        >
            <div
                v-for="(item, _index) in currentSignPostPage"
                class="flex flex-col relative z-30 text-white magic-text themeFont w-[100vw]"
            >
                <div class="mx-12 my-8 flex flex-row flex-nowrap items-center">
                    <div v-if="item.pivot.icon" class="min-w-[200px] mr-6">
                        <IconRouter
                            :path="page.path"
                            class="fill-white w-[200px] svgIconGlow"
                            :icon="item.pivot.icon"
                            :mirror="item.pivot.mirror"
                            :rotation="item.pivot.rotation"
                        ></IconRouter>
                    </div>
                    <div class="flex flex-1 flex-col w-[70vw]">
                        <div class="flex flex-row items-baseline">
                            <div
                                class="flex text-[7vw] text-left items-center leading-none"
                            >
                                {{ item.name }}
                            </div>

                            <div
                                v-if="
                                    item.name !== item.venue_name &&
                                    item.venue_name
                                "
                                class="flex text-[2.5vw] text-left items-center leading-none"
                            >
                                ( {{ item.venue_name }} )
                            </div>
                        </div>

                        <div
                            v-if="nextEvent(item).value"
                            class="flex text-[5vw] leading-none"
                        >
                            <div class="mr-3">
                                <div
                                    v-if="
                                        DateTime.fromISO(
                                            nextEvent(item).value.starts_at
                                        ) < DateTime.local()
                                    "
                                    class="text-left leading-none"
                                >
                                    Now:
                                </div>
                                <div
                                    v-else
                                    class="text-left leading-none items-center"
                                >
                                    Next:
                                </div>
                            </div>
                            <div>
                                <div>
                                    <div
                                        class="leading-none"
                                        v-if="
                                            nextEvent(item).value.title.split(
                                                ' – '
                                            )[0]
                                        "
                                    >
                                        {{
                                            nextEvent(item)
                                                .value.title.split(" – ")[0]
                                                .truncate(30)
                                        }}
                                    </div>
                                    <div
                                        :class="{
                                            'text-green-300':
                                                containsOnly(
                                                    nextEvent(item).value.title
                                                ),
                                        }"
                                        class="text-[3vw] leading-none"
                                        v-if="
                                            nextEvent(item).value.title.split(
                                                ' – '
                                            )[1]
                                        "
                                    >
                                        {{
                                            nextEvent(item)
                                                .value.title.split(" – ")[1]
                                                .truncate(45)
                                        }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div
                        class="flex flex-0 flex-row text-white w-[20vw] space-x-8"
                    >
                        <IconRouter
                            v-if="
                                item.pivot.flags
                                    ? item.pivot.flags.includes('wheelchair')
                                    : false
                            "
                            :path="page.path"
                            class="flex fill-white w-[5vw] svgIconGlow"
                            icon="Wheelchair"
                        ></IconRouter>

                        <IconRouter
                            v-if="
                                item.pivot.flags
                                    ? item.pivot.flags.includes('first_aid')
                                    : false
                            "
                            :path="page.path"
                            class="flex w-[5vw] svgIconGlow"
                            icon="FirstAid"
                        ></IconRouter>
                    </div>
                </div>
            </div>
        </div>
    </Transition>
</template>

<style scoped>
/* we will explain what these classes do next! */
.v-enter-active,
.v-leave-active {
    transition: opacity 1s ease;
}

.v-enter-from,
.v-leave-to {
    opacity: 0;
}
</style>

<style>
@reference "../theme.css";

body {
    overflow: hidden;
    @apply bg-primary;
}

.magic-text {
    position: relative;
    user-select: none;
    /*
    font-family: 'primaryThemeFont', sans-serif;
    white-space: pre;
    */
}

.magic-text span {
    position: relative;
    white-space: pre;
    display: inline-block;
    cursor: pointer;
    opacity: 1;
}

.w-digit-15 {
    width: 1.5ch;
}

.w-digit-15 span {
    width: 1ch;
}

.w-digit-2 {
    width: 2ch;
}

.w-digit-2 span {
    width: 1ch;
}

.w-digit-45 {
    width: 4.5ch;
}

.w-digit-45 span {
    width: 1ch;
}

.w-digit-5 {
    width: 5ch;
}

.w-digit-5 span {
    width: 1ch;
}
</style>
