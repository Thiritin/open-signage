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

    <h1 class="relative z-30 text-center text-8xl top-1 mt-4 magicTextColor themeFont">{{ title }}</h1>

    <div v-for="item in screen.rooms" class="flex flex-col relative z-30 magicTextColor magic-text themeFontSecondar w-[100vw]">

        <div class="mx-12 my-8 flex flex-row flex-nowrap items-center">

            <IconRouter :path="page.path" class="flex flex-0 magicTextColor w-[10vw] svgIconGlow" :icon="item.pivot.icon"
                        :mirror="item.pivot.mirror" :rotation="item.pivot.rotation"></IconRouter>


            <div class="flex flex-1 flex-col mx-12 w-[70vw]">

                <div class="flex flex-row items-baseline">

                    <div class="flex text-[5vw] text-left items-center">
                        {{ item.name }}
                    </div>

                    <div v-if="item.name !== item.venue_name" class="flex text-[2.5vw] text-left items-center">
                        ( {{ item.venue_name }} )
                    </div>

                </div>

                <div class="flex flex-row items-baseline">

                    <div v-if="DateTime.fromISO(nextEvent(item).value.starts_at) < DateTime.local()" class="flex text-[3vw] text-left items-center">
                        Now:
                    </div>

                    <div v-else class="flex text-[3vw] text-left items-center">
                        Next:
                    </div>

                    <div class="flex text-[3vw]">
                        {{ nextEvent(item).value.title }}
                    </div>

                </div>

            </div>

            <div class="flex flex-0 flex-row magicTextColor w-[20vw] space-x-8">

                <IconRouter v-if="item.pivot.flags.includes('wheelchair')" :path="page.path"
                            class="flex w-[5vw] svgIconGlow"
                            icon="Wheelchair"></IconRouter>

                <IconRouter v-if="item.pivot.flags.includes('first_aid')" :path="page.path"
                            class="flex w-[5vw] svgIconGlow"
                            icon="FirstAid"></IconRouter>

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

