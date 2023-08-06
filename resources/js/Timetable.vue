<script setup>
import 'vue3-carousel/dist/carousel.css'
import {Carousel, Slide, Pagination, Navigation} from 'vue3-carousel'
import HourTime from "@/Components/HourTime.vue";
import {computed} from "vue";

const cleanUpPrevention = ['grid-cols-1', 'grid-cols-2', 'grid-cols-3', 'grid-cols-4', 'grid-cols-5', 'grid-cols-6', 'grid-cols-7', 'grid-cols-8']

const props = defineProps({
  schedule: {
    type: Array,
    required: true
  }
})

const earliestTimeAllDays = computed(() => {
  let earliestTime = null;
  props.schedule.forEach(day => {
    day.forEach(room => {
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
  props.schedule.forEach(day => {
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
  let timeDifference = toMinutes(new Date(nextPanel.starts_at)) - toMinutes(new Date(currentPanel.ends_at));
  return timeDifference + 10;
}


function marginToFirstEvent(rooms) {
  let firstPanel = rooms[0];
  let firstPanelStartsDate = new Date(firstPanel.starts_at);
  let timeDifference = toMinutes(firstPanelStartsDate) - earliestTimeAllDays.value;
  timeDifference = timeDifference + 10;
  console.log(firstPanel.title, toMinutes(firstPanelStartsDate), earliestTimeAllDays.value)
  if (toMinutes(firstPanelStartsDate) === earliestTimeAllDays.value) {
    timeDifference = 10;
  }

  return timeDifference;
}

const showItemsBasedOnScreenSize = computed(() => {
  let width = window.innerWidth;
  let showItems;
  if (width < 640) {
    showItems = 1;
  } else if (width < 1280) {
    showItems = 2;
  } else if (width < 1536) {
    showItems = 3;
  } else if (width < 1920) {
    showItems = 5;
  } else {
    showItems = props.schedule.length;
  }
  if (showItems > props.schedule.length) {
    showItems = props.schedule.length;
  }
  return showItems;
})

</script>

<template>
  <div class="bg-primary-400 overflow-hidden w-screen h-screen">
    <div class="w-screen h-screen">
      <Carousel :wrapAround="true" class="w-screen" :autoplay="0" :itemsToShow="showItemsBasedOnScreenSize" :center-mode="false">
        <Slide class="w-screen block border-l-4 first:border-l-0 border-accent" :key="dayIndex" v-for="(day,dayIndex) in schedule">
          <div class="h-screen flex flex-col">
            <!-- Day Name -->
            <div
                class="p-8 m-0 text-2xl font-sans font-bold text-center text-white border-0 border-collapse bg-primary-600 border-b-[15px] border-secondary"
            >
              {{
                new Date(day[0][0].starts_at).toLocaleDateString('en-GB', {
                  weekday: 'long',
                  day: 'numeric',
                  month: 'long'
                })
              }}
            </div>
            <!-- Timetable of Day -->
            <div class="grid items-stretch items themeFontSecondary h-full flex-1" :class="'grid-cols-'+day.length">
              <div class="flex flex-col" :class="{'bg-primary-600': (index % 2)}"
                   v-for="(room, index) in day">
                <!-- Room Name -->
                <div :class="{'bg-primary-700': (index % 2)}"
                     class="text-center p-4 themeFont tracking-widest font-bold text-white bg-primary-500">
                  {{ room[0].room.name }}
                </div>
                <div class="overflow-auto h-[calc(100vh-(111px+56px))]">
                  <!-- Events -->
                  <div class="pb-4" :style="'padding-top:'+marginToFirstEvent(room)+'px;'">
                    <!-- Event Entry -->
                    <div :style="'margin-bottom:'+marginToNextEvent(room,panelIndex)+'px;'"
                         class="px-2 w-full overflow-hidden" v-for="(panel,panelIndex) in room">
                      <div :style="'height:'+eventHeight(panel.starts_at,panel.ends_at)+'px;'"
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
        </Slide>
      </Carousel>
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
</style>
