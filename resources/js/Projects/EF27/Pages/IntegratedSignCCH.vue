<script setup>
import {computed, onMounted, onUnmounted, ref} from "vue";

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
    page: {
        type: Object,
        required: false
    },
    announcements: {
        type: Array,
        default: []
    },
    showAnnouncements: {
        type: Boolean,
        default: true
    },
    showSchedule: {
        type: Boolean,
        default: true
    },
    showDate: {
        type: String,
        required: false
    },
    showToday: {
        type: Boolean,
        required: false
    }

})

const currentTime = ref(DateTime.now());

onMounted(() => {
    const interval = setInterval(() => {
        currentTime.value = DateTime.now();
    }, 5000)
    onUnmounted(() => {
        clearInterval(interval);
    })
})

const nextEvent = function (room) {
    return computed(() => {
        return props.schedule.filter(event => {
            return event.room_id === room.id;
        }).filter(event => {
            return currentTime.value <= DateTime.fromISO(event.ends_at).plus({minutes: event.delay})
        }).map((event, index) => {
            event.title = event.title ? event.title.replace("Dealers' Den & Art Show Party", "").replace(room.name, "").replace(/^[ ‑–—‐−‐–—⸺|‖•‣]+/g, "") : event.title;
            return event;//event.title.replace(room.name);
        }).shift();
    });
};


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
    <div class="flex flex-col relative z-30 magicTextColor magic-text themeFont h-[100vh] w-[100vw] p-10 space-y-8 justify-items-center">

        <div v-for="item in screen.rooms" class="flex flex-col magicTextColor magic-text themeFont overflow-hidden">

            <div class="flex text-[9vw] text-justify">
                {{ item.name }}
            </div>

            <div class="flex flex-row text-[4vw] items-baseline">

                <div class="flex flex-row items-baseline">
                    <div v-if="DateTime.fromISO(nextEvent(item).value.starts_at) < DateTime.local()"
                         class="flex text-left magicTextColorGreen">
                        OPEN
                    </div>
                    <div v-else class="flex text-left magicTextColorRed">
                        CLOSED
                    </div>
                </div>

                <div
                    v-if="DateTime.fromISO(nextEvent(item).value.starts_at) < DateTime.local() && nextEvent(item).value.title"
                    class="flex text-left">
                    {{ nextEvent(item).value.title }}
                </div>
                <div v-else-if="nextEvent(item).value.title" class="flex text-left">
                    Next: {{ nextEvent(item).value.title }}
                </div>

            </div>

        </div>

    </div>

</template>

<style scoped>

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

