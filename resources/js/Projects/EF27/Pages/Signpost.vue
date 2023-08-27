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
    screen:{
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

const nextEvent = computed(() => {
    return props.schedule.filter(event => {
        return currentTime.value <= DateTime.fromISO(event.ends_at).plus({minutes: event.delay})
    })[0]
})

import LogoSVG from '@/Projects/EF27/Assets/images/logoEF27e.svg';
import straightSVG from '@/Projects/EF27/Assets/images/straight.svg';
import rightSVG from '@/Projects/EF27/Assets/images/right.svg';
import wheelchairSVG from '@/Projects/EF27/Assets/images/wheelchair.svg';
import MaskSVG from "@/Projects/EF27/Assets/images/logoEF27Mask.svg";
import IconRouter from "@/Projects/System/Components/IconRouter.vue";
import {DateTime} from "luxon";

</script>

<template>

    <h1 class="text-center text-8xl top-1 mt-4 magicTextColor themeFont">{{ title }}</h1>

    <div v-for="item in screen.rooms" class="flex flex-col relative z-30 text-7xl min-w-full magicTextColor top-5 magic-text">
        <div class="mx-12 my-8 flex flex-row z-40 items-center ">

            <IconRouter style="filter: drop-shadow(5px 10px 20px rgb(150, 20, 200, 0.75));" :path="page.path" class="h-48 w-48" :icon="item.pivot.icon" :mirror="item.pivot.mirror" :rotation="item.pivot.rotation"></IconRouter>

            <div ref="section" class="flex flex-col mx-12 z-40">
                <p class="text text-9xl flex text-left items-center">
                    {{ item.name }} <span class="text text-7xl"> {{ item.venue_name }} </span>
                </p>
                <p class="text text-7xl flex text-left items-center">
                    {{ nextEvent.title }}
                </p>
            </div>

            <IconRouter v-if="item.pivot.flags.includes('wheelchair')" style="filter: drop-shadow(5px 10px 20px rgb(150, 20, 200, 0.75));" :path="page.path" class="h-24 w-24" icon="Wheelchair"></IconRouter>

        </div>
    </div>

</template>

<style scoped>

.section {
    /*position: relative;
    display: flex;
    justify-content: center;
    align-items: center;*/
    /*min-height: 90vh;*/
    /*background-color: rgba(10, 10, 10, .5);*/
}

.transmutation {
    animation: movey 20s normal forwards linear, rotation360 240s infinite linear;
}

@keyframes movey {
    from {
        opacity: 0;
        top: -700px;
    }
    to {
        opacity: 0.3;
        top: 10px;
    }
}

@keyframes rotation180 {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(180deg);
    }
}

@keyframes rotation360 {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(359deg);
    }
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
//font-family: 'primaryThemeFont', sans-serif;
    white-space: pre;
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

