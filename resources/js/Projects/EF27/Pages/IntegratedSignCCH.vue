<script setup>
import {computed, onMounted, onUnmounted, ref, unref} from "vue";

const props = defineProps({
    title: {
        type: String,
        default: "Event Rooms"
    },
    schedule: {
        type: Array,
        default: []
    },
    screen: {
        type: Array,
        default: []
    },
    pageSwitchingTimer: {
        type: Number,
        default: 15000
    }
})

const currentTime = ref(DateTime.now());
const currentPageIndex = ref(0);

onMounted(() => {
    const interval = setInterval(() => {
        currentTime.value = DateTime.now();
    }, 5000)
    const pageSwitcher = setInterval(() => {
        currentPageIndex.value = (currentPageIndex.value + 1) % roomPages.value.length;
    }, props.pageSwitchingTimer)
    onUnmounted(() => {
        clearInterval(interval);
        clearInterval(pageSwitcher);
    })
})

String.prototype.truncate = String.prototype.truncate ||
    function ( n, useWordBoundary ){
        if (this.length <= n) { return this; }
        const subString = this.slice(0, n-1); // the original check
        return (useWordBoundary
            ? subString.slice(0, subString.lastIndexOf(" "))
            : subString) + "&hellip;";
};

const nextEvent = function (room) {
    return computed(() => {
        return props.schedule.filter(event => {
            return event.room_id === room.id;
        }).filter(event => {
            return currentTime.value <= DateTime.fromISO(event.ends_at).plus({minutes: event.delay}) && !event.title.toLowerCase().includes("seating")
        }).map((event, index) => {
            console.log(event.title);
            event.title = event.title ? event.title
                .replace("Dealers' Den & Art Show", "")
                .replace("Dealers' Den", "")
                .replace("Art Show", "")
                .replace("Fursuit Badge", "")
                .replace("Registration", "")
                .replace("Constore", "")
                .replace("Fursuit Badge", "")
                .replace("Fursuit Lounge", "")
                .replace("Artists' Lounge", "")
                .replace("Locker Service", "")
                .replace("The Electric Lounge Sessions", "")
                // .replace(/^[ ‑–—‐−‐–—⸺|‖•‣]+/g, "")
                // .replace("room.name", "")
                .replace(/^[\W]+/g, "")
        : event.title;
            event.title = event.title.split(" – ")[0].truncate(30, true);
            return event;//event.title.replace(room.name);
        }).shift();
    });
};

function chunkArray(array, chunkSize) {
    const result = [];
    for (let i = 0; i < array.length; i += chunkSize) {
        result.push(array.slice(i, i + chunkSize));
    }
    return result;
}

const roomPages = computed(() => {
    return chunkArray(props.screen.validRooms, 3);
});

const currentSlide = computed(() => {
    return roomPages.value[currentPageIndex.value];
});

import LogoSVG from '@/Projects/EF27/Assets/images/logoEF27e.svg';
import straightSVG from '@/Projects/EF27/Assets/images/straight.svg';
import rightSVG from '@/Projects/EF27/Assets/images/right.svg';
import wheelchairSVG from '@/Projects/EF27/Assets/images/wheelchair.svg';
import MaskSVG from "@/Projects/EF27/Assets/images/logoEF27Mask.svg";
import IconRouter from "@/Projects/System/Components/IconRouter.vue";
import {DateTime} from "luxon";
import HourTime from "@/Components/HourTime.vue";

</script>

<template>

    <!--    <h1 class="relative z-30 text-center text-8xl top-1 mt-4 magicTextColor themeFont">{{ title }}</h1>-->
    <Transition mode="out-in">
        <div  :key="currentPageIndex" class="flex flex-col relative z-30 magicTextColor magic-text themeFont h-[100vh] w-[100vw] p-8 space-y-8 justify-items-center overflow-hidden">

                <TransitionGroup name="list">
                    <div v-for="item in currentSlide"
                         :key="item.id" class="flex flex-col magicTextColor magic-text themeFont">
                        <div class="flex text-[9vw] text-justify">
                            {{ item.name }}
                        </div>

                        <div class="flex flex-row text-[4vw] items-baseline">

                            <div v-if="nextEvent(item).value" class="flex flex-row items-baseline">
                                <div v-if="nextEvent(item).value && DateTime.fromISO(nextEvent(item).value.starts_at) < DateTime.local()"
                                     class="flex text-left magicTextColorGreen">
                                    OPEN
                                </div>
                                <div v-else class="flex text-left magicTextColorRed">
                                    CLOSED
                                </div>
                            </div>

                            <div
                                v-if="nextEvent(item).value && DateTime.fromISO(nextEvent(item).value.starts_at) < DateTime.local() && nextEvent(item).value.title"
                                class="flex text-left">
                                {{ nextEvent(item).value.title }}
                            </div>
                            <div v-else-if="nextEvent(item).value && nextEvent(item).value.title" class="flex text-left">
                                Next: {{ nextEvent(item).value.title }}
                            </div>

                        </div>
                    </div>
                </TransitionGroup>

        </div>
    </Transition>

</template>

<style scoped>
.v-enter-active,
.v-leave-active {
    transition: opacity 1s ease;
}

.v-enter-from,
.v-leave-to {
    opacity: 0;
}

.list-enter-active,
.list-leave-active {
    transition: all 1s ease;
}
.list-enter-from,
.list-leave-to {
    opacity: 0;
}
</style>


<style>

body {
    overflow: hidden;
    @apply bg-primary
}

.magic-text {
    position: relative;
    user-select: none;
//font-family: 'primaryThemeFont', sans-serif; white-space: pre;
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

