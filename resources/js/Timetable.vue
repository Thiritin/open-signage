<script setup>
import 'vue3-carousel/dist/carousel.css'
import {Carousel, Slide, Pagination, Navigation} from 'vue3-carousel'
import HourTime from "@/Components/HourTime.vue";
import {computed, onMounted, onUnmounted, ref} from "vue";

const cleanUpPrevention = ['grid-cols-1', 'grid-cols-2', 'grid-cols-3', 'grid-cols-4', 'grid-cols-5', 'grid-cols-6', 'grid-cols-7', 'grid-cols-8']

const props = defineProps({
    schedule: {
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

const groupedSchedule = computed(() => {
    // Group by date
    let groupedByDate = props.schedule.filter((entry) => {
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

    return groupedByDate;
})

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
        showItems = 5;
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
                                    <div
                                        class="text-primary-950 text-sm border-b pb-1 mb-1 border-primary-500 font-bold">
                                                <span
                                                    v-if="panel.flags.find((f) => f === 'after_dark')">[After Dark] </span>{{
                                            panel.title
                                        }}
                                    </div>
                                    <!-- Event Time -->
                                    <div class="flex justify-between text-sm font-semibold">
                                        <div class="text-primary-700">
                                            <HourTime :time="panel.starts_at"></HourTime>
                                            -
                                            <HourTime :time="panel.ends_at"></HourTime>
                                        </div>
                                        <div>
                                            {{ panel.room.name }}
                                        </div>
                                    </div>
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
</style>
