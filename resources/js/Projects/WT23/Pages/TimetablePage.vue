<script setup>
import 'vue3-carousel/dist/carousel.css'
import {Carousel, Slide, Pagination, Navigation} from 'vue3-carousel'
import HourTime from "@/Components/HourTime.vue";
import {computed, onMounted, onUnmounted, ref} from "vue";
import CurrentTime from "@/Components/CurrentTime.vue";

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
    showItems: {
        type: Number,
        required: false,
        default: null
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

    // Sort by date and map each group
    return Object.entries(groupedByDate)
        .sort(([dateA], [dateB]) => dateA.localeCompare(dateB))
        .map(([date, entries]) => {
            // Sort by start time and group by room name
            let sorted = entries.sort((a, b) => a.starts_at.localeCompare(b.starts_at));
            let groupedByRoom = sorted.reduce((grouped, entry) => {
                if (!grouped[entry.room.name]) {
                    grouped[entry.room.name] = [];
                }
                grouped[entry.room.name].push(entry);
                return grouped;
            }, {});
            return {date, rooms: Object.values(groupedByRoom)};
        });
})

const earliestTimeAllDays = computed(() => {
    let earliestTime = null;
    groupedSchedule.value.forEach(day => {
        day.rooms.forEach(room => {
            room.forEach(panel => {
                if (earliestTime === null || toMinutes(new Date(panel.starts_at)) < earliestTime) {
                    earliestTime = toMinutes(new Date(panel.starts_at));
                }
            })
        })
    })
    return earliestTime;
})

const latestTimeAllDays = computed(() => {
    let latestTime = null;
    groupedSchedule.value.forEach(day => {
        day.forEach(room => {
            room.forEach(panel => {
                if (latestTime === null || panel.ends_at > latestTime) {
                    latestTime = panel.ends_at;
                }
            })
        })
    })
    return new Date(latestTime);
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

function marginToNextEvent(rooms, index) {
    let panelCount = rooms.length;
    if (index === (panelCount - 1)) {
        return 0;
    }
    let currentPanel = rooms[index];
    let nextPanel = rooms[index + 1];
    return toMinutes(new Date(nextPanel.starts_at)) - toMinutes(new Date(currentPanel.ends_at));
}


function marginToFirstEvent(rooms) {
    let firstPanel = rooms[0];
    let firstPanelStartsDate = toMinutes(new Date(firstPanel.starts_at));
    let timeDifference = firstPanelStartsDate - earliestTimeAllDays.value;
    timeDifference = timeDifference + 10;

    if (firstPanelStartsDate === earliestTimeAllDays.value) {
        return 10;
    }
    return timeDifference;
}

const showItemsBasedOnScreenSize = computed(() => {
    let width = window.innerWidth;

    if (props.showItems) {
        return props.showItems;
    }

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
        showItems = 4;
    }
    if (showItems > groupedSchedule.value.length) {
        showItems = groupedSchedule.value.length;
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

const timeSinceStart = computed(() => {
    return toMinutes(todaysDate.value) - earliestTimeAllDays.value;
})

function returnDivOrComponent(component) {
    if (props.carousel === true) {
        return component;
    }
    return 'div';
}

</script>

<template>
    <div class="bg-primary-400 w-screen">
        <div class="w-screen h-screen">
            <component :is="returnDivOrComponent(Carousel)" snapAlign="start" wrapAround="true" class="w-screen"
                       :itemsToShow="showItemsBasedOnScreenSize" :center-mode="false">
                <component :is="returnDivOrComponent(Slide)" class="w-screen first:border-l-0 block" :key="dayIndex"
                           :class="{'border-accent border-l-4': (dayIndex !== 0 || carousel)}"
                           v-for="(day,dayIndex) in groupedSchedule">
                    <div class="h-screen flex flex-col">
                        <!-- Day Name -->
                        <div
                            class="p-6 m-0 text-2xl font-sans font-bold text-center text-white border-0 border-collapse bg-primary-600 border-b-[15px] border-secondary"
                        >
                            {{
                                new Date(day.date).toLocaleDateString('en-GB', {
                                    weekday: 'long',
                                    day: 'numeric',
                                    month: 'long'
                                })
                            }}
                        </div>
                        <!-- Timetable of Day -->
                        <div class="grid items-stretch themeFontSecondary h-full flex-1"
                             :class="'grid-cols-'+day.rooms.length">
                            <div class="flex flex-col" :class="{'bg-primary-600': (index % 2)}"
                                 v-for="(room, index) in day.rooms">
                                <!-- Room Name -->
                                <div :class="{'bg-primary-700': (index % 2)}"
                                     class="text-center whitespace-nowrap p-4 themeFont tracking-widest font-bold text-white bg-primary-500">
                                    {{ room[0].room.name }}
                                </div>
                                <div class="overflow-auto h-[calc(100vh-(94px+56px))]">
                                    <!-- Events -->
                                    <div class="pb-4 relative" :style="'padding-top:'+marginToFirstEvent(room)+'px;'">
                                        <div
                                            v-if="todaysDate.getDate() === new Date(room[0].starts_at).getDate()"
                                            :style="'top:'+timeSinceStart+'px;'"
                                            class="bg-red-700 absolute w-full h-1 top-0 z-40">
                                        </div>
                                        <!-- Event Entry -->
                                        <div :style="'margin-bottom:'+marginToNextEvent(room,panelIndex)+'px;'"
                                             class="px-2 w-full overflow-hidden" v-for="(panel,panelIndex) in room">
                                            <div
                                                :style="'height:'+eventHeight(panel.starts_at,panel.ends_at)+'px;'+'background:'+panel.schedule_type?.color+'!important;'"
                                                class="bg-primary-100 rounded">
                                                <div class="p-2 text-left">
                                                    <!-- Event Name -->
                                                    <div
                                                        class="text-primary-950  text-sm border-b pb-1 mb-1 border-primary-500 font-bold">
                                                <span
                                                    v-if="panel.flags.find((f) => f === 'after_dark')">[After Dark] </span>{{
                                                            panel.title
                                                        }}
                                                    </div>
                                                    <!-- Event Time -->
                                                    <div class="text-primary-700 text-sm font-semibold">
                                                        <HourTime :time="panel.starts_at"></HourTime>
                                                        -
                                                        <HourTime :time="panel.ends_at"></HourTime>
                                                    </div>
                                                </div>
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
    display: block!important;
}
</style>
