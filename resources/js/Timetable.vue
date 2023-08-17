<script setup>
import 'vue3-carousel/dist/carousel.css'
import {Carousel, Slide} from 'vue3-carousel'
import HourTime from "@/Components/HourTime.vue";
import {computed, onMounted, onUnmounted, ref} from "vue";

const cleanUpPrevention = ['grid-cols-1', 'grid-cols-2', 'grid-cols-3', 'grid-cols-4', 'grid-cols-5', 'grid-cols-6', 'grid-cols-7', 'grid-cols-8']

const props = defineProps({
    initialSchedule: {
        type: Array,
        required: true
    },
    autoplay: {
        type: Number,
        required: false,
        default: 0
    },
    carousel: {
        type: Boolean,
        required: false,
        default: true
    },
    showDate: {
        type: String,
        required: false
    },
})

const schedule = ref(props.initialSchedule)
const currentTime = ref(new Date());

Echo.channel('ScreenAll')
    .listen('.schedule.update', (e) => {
        schedule.value = e.schedule;
    })

const groupedSchedule = computed(() => {
    // Group by date
    return schedule.value
        .filter((entry) => {
            const now = currentTime.value;
            const start = new Date(entry.starts_at);
            const end = new Date(new Date(entry.ends_at).getTime() + (entry.delay * 1000 * 60));

            return (end.getDate() >= now.getDate() || start.getDate() >= now.getDate())
            return true;
        })
        .filter((entry) => {
          if (props.showDate) {
            return (new Date(entry.starts_at)).getDate() === (new Date(props.showDate)).getDate();
          }
          return true;
    }).reduce((grouped, entry) => {
        let date = entry.starts_at.split('T')[0];
        if (!grouped[date]) {
            grouped[date] = [];
        }
        grouped[date].push(entry);
        return grouped;
    }, {});
})


let count = 0
for (let schedule in groupedSchedule.value) {
    count = count + 1
}

function eventHeight(startTime, endTime) {
    let timeDifference = (new Date(endTime)).getTime() - (new Date(startTime)).getTime();
    timeDifference = timeDifference / (1000 * 60);
    if (timeDifference < 60) {
        timeDifference = 60;
    }
    return timeDifference;
}

function toMinutes(date) {
    return date.getHours() * 60 + date.getMinutes();
}

const showItemsBasedOnScreenSize = computed(() => {
    let width = window.innerWidth;

    let showItems;
    if (width < 640) {
        showItems = 1;
    } else if (width < 1280) {
        showItems = 2;
    } else if (width < 1536) {
        showItems = 2;
    } else if (width < 1920) {
        showItems = 3;
    } else {
        showItems = count;
    }

    if (showItems >= count) {
        showItems = count;
    }

    return showItems;
})

const todaysDate = ref(new Date())

// Run watcher on todaysDate to update the date every minute
onMounted(() => {
    const interval = setInterval(() => {
        todaysDate.value = new Date();
    }, 1000);
    onUnmounted(() => {
        clearInterval(interval);
    });
})

function returnDivOrComponent(component) {
    if (props.carousel === true) {
        return component;
    }
    return 'div';
}

function isCurrentTimeBetween(startTime, endTime, delay) {
    const now = new Date();
    const startTimeCompare = new Date(new Date(startTime).getTime() + delay * 60 * 1000);
    const endTimeCompare = new Date(new Date(endTime).getTime() + delay * 60 * 1000);
    return now >= startTimeCompare && now <= endTimeCompare;
}

function entryInPast(entry) {
    const now = new Date();
    const endTimeCompare = new Date(new Date(entry.ends_at).getTime() + (entry.delay * 60 * 1000 ?? 0));
    return now.getTime() >= endTimeCompare.getTime();
}

</script>

<template>
    <div class="bg-primary-400 min-h-screen">
        <component :is="returnDivOrComponent(Carousel)" snapAlign="start" class=""
                   :itemsToShow="showItemsBasedOnScreenSize" :center-mode="false">
            <component :is="returnDivOrComponent(Slide)" class="first:border-l-0 block" :key="dayIndex"
                       v-for="(day,dayIndex) in groupedSchedule">
                <div class="min-h-screen">
                    <!-- Day Name -->
                    <div
                        class="p-6 m-0 text-2xl font-sans font-bold text-center text-white border-0 border-collapse bg-primary-600 border-b-[15px] border-secondary"
                    >
                        {{
                            new Date(dayIndex).toLocaleDateString('en-GB', {
                                weekday: 'long',
                                day: 'numeric',
                                month: 'long'
                            })
                        }}
                        <div v-if="showItemsBasedOnScreenSize < 4">&leftarrow; Swipe to switch day &rightarrow;</div>
                    </div>
                    <!-- Timetable of Day -->
                    <div class="relative pt-2">
                        <!-- Event Entry -->
                        <div
                            class="px-2 pb-4 w-full" v-for="(panel,panelIndex) in day">
                            <div
                                :style="'min-height:'+eventHeight(panel.starts_at,panel.ends_at)+'px;'+'background:'+panel.schedule_type?.color+'!important;'"
                                class="bg-primary-100 rounded">
                                <div class="p-2 text-left">
                                    <!-- Event Name -->
                                    <div class="pb-1 text-primary-950">
                                        <div class="flex justify-between gap-3">
                                            <div
                                                class="text-sm font-bold">
                                                <span
                                                    v-if="panel.flags.find((f) => f === 'after_dark')">[After Dark] </span>{{
                                                    panel.title
                                                }}
                                            </div>
                                            <div class="text-white text-sm">
                                                <div
                                                    v-if="isCurrentTimeBetween(panel.starts_at,panel.ends_at,(panel.delay ?? 0))">
                                                    <div
                                                        class="flex bg-green-600 rounded font-bold w-fit justify-between items-center align-middle gap-2 px-2 themeFontSecondary">
                                                        <div class="animate-blink rounded-full bg-white h-3 w-3"></div>
                                                        <div class="whitespace-nowrap">NOW</div>
                                                    </div>
                                                </div>
                                                <div v-else-if="panel.flags.find((e) => e === 'cancelled')"
                                                     class="rounded">
                                                    <div
                                                        class="font-mono px-1 font-bold text-center w-full bg-black rounded">
                                                        CANCELLED
                                                    </div>
                                                </div>
                                                <div v-else-if="panel.flags.find((e) => e === 'moved')" class="rounded">
                                                    <div
                                                        class="font-mono px-1 font-bold text-center w-full bg-yellow-600 rounded">
                                                        MOVED
                                                    </div>
                                                </div>
                                                <div v-else-if="panel.delay && !entryInPast(panel)" class="rounded">
                                                    <div
                                                        class="font-mono px-1 font-bold text-center w-full bg-red-900 rounded">
                                                        DELAYED
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div v-if="panel.schedule_organizer" class="text-xs">Organized by
                                            {{ panel.schedule_organizer.name }}
                                        </div>
                                    </div>
                                    <div class="border-b border-primary-500 mb-1"></div>
                                    <!-- Event Time -->
                                    <div>
                                        <div class="flex justify-between text-sm font-semibold">
                                            <div>
                                                <div v-if="panel.delay > 0 && !entryInPast(panel)"
                                                     class="line-through opacity-60  text-left text-xs">
                                                    <HourTime :time="new Date(panel.starts_at).getTime()"/>
                                                    -
                                                    <HourTime :time="new Date(panel.ends_at).getTime()"/>
                                                </div>
                                                <div :class="{'font-bold': panel.delay > 0 && !entryInPast(panel)}">
                                                    <HourTime
                                                        :time="new Date(panel.starts_at).getTime() + (panel.delay * 60 * 1000)"/>
                                                    -
                                                    <HourTime
                                                        :time="new Date(panel.ends_at).getTime() + (panel.delay * 60 * 1000)"/>
                                                </div>
                                            </div>
                                            <div>
                                                {{ panel.room.name }}
                                            </div>
                                        </div>
                                    </div>

                                    <div v-if="panel.message" class="text-xs mt-2 p-1 rounded bg-white font-semibold">{{ panel.message }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </component>
        </component>
    </div>
</template>

<style scoped>
* {
    scrollbar-width: thin;
    scrollbar-color: #feff99 #feff99;
}

/* Chrome, Edge, and Safari */
*::-webkit-scrollbar {
    width: 10px;
    background-color: #afb16b;
}

*::-webkit-scrollbar-track {
    background: none;
}

*::-webkit-scrollbar-thumb {
    background-color: #feff99;
    border-radius: 0px;
    border: none;
}

.carousel__slide {
    display: block !important;
}

/* Styles for the animated blinking effect */
@keyframes blink {
    0% {
        opacity: 0;
    }
    50% {
        opacity: 1;
    }
    100% {
        opacity: 0;
    }
}

.animate-blink {
    animation: blink 1s infinite;
}
</style>
