<script setup>
import ScheduleList from "@/Projects/WT23/Components/Schedule/ScheduleList.vue";

const props = defineProps({
    schedule: {
        type: Array,
        default: []
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
import ScheduleEntry from "@/Projects/WT23/Components/Schedule/ScheduleEntry.vue";
import AnnouncementCard from "@/Projects/WT23/Components/AnnouncementCard.vue";
import {computed, onMounted, onUnmounted, ref} from "vue";

const scrollLeft = ref(null);
const scrollRight = ref(null);

onMounted(() => {
    const intervalId = setInterval(updateComputedProperties, 2000); // Update every 1 second
    const intervalScoll = setInterval(() => {
        pageScroll(scrollLeft)
        pageScroll(scrollRight)
    }, 50); // Update every 1 second
    onUnmounted(() => {
        clearInterval(intervalId);
        clearInterval(intervalScoll);
    });
})

let scrollingDown = true;
let stop = true;
setTimeout(() => stop = false, 5000)
const currentTime = ref(new Date());

function pageScroll(ref) {
    if (ref.value === null) return;
    if (stop) return;
    const windowHeight = ref.value.clientHeight;
    const pageHeight = ref.value.offsetHeight;
    const scrollHeight = ref.value.scrollHeight;
    const endScoll = scrollHeight - pageHeight;

    if (scrollHeight === pageHeight) {
        return;
    }

    // Check if we reached the bottom of the page
    if (ref.value.scrollTop >= endScoll) {
        if (scrollingDown === true) {
            stop = true;
            setTimeout(() => stop = false, 5000)
        }
        scrollingDown = false;
    } else if (ref.value.scrollTop === 0) {
        if (scrollingDown === false) {
            stop = true;
            setTimeout(() => stop = false, 5000)
        }
        scrollingDown = true
    }

    // Scroll down or up depending on the scrollingDown flag
    if (scrollingDown) {
        ref.value.scrollTop = ref.value.scrollTop + 1.5;
    } else {
        ref.value.scrollTop = ref.value.scrollTop - 1.5;
    }
}

// Filter announcements, make sure they are today and ends at is not in the past
const filteredAnnouncements = computed(() => {
    return props.announcements.filter((announcement) => {
        const now = currentTime.value;
        const start = new Date(announcement.starts_at);
        const end = new Date(announcement.ends_at);
        return (end > now) && (start < now);
    })
})

// Filtered Schedules
const filteredSchedule = computed(() => {
    const now = currentTime.value;
    return props.schedule.filter((entry) => {
        const start = new Date(entry.starts_at);
        const end = new Date(new Date(entry.ends_at).getTime() + (entry.delay * 1000 * 60));

        if (props.showToday === true) {
            return (end.getDate() === now.getDate())
        }

        if (props.showDate !== null) {
            return (end.getDate() >= now.getDate()) && (new Date(entry.starts_at).getDate() === new Date(props.showDate).getDate());
        }
        return (end.getDate() >= now.getDate())
    })
})

const updateComputedProperties = () => {
    currentTime.value = new Date();
};

// hasFileredAnnouncements
const hasFilteredAnnouncements = computed(() => {
    return filteredAnnouncements.value.length > 0 && props.showAnnouncements === true;
})

const hasFilteredSchedule = computed(() => {
    return filteredSchedule.value.length > 0 && props.showSchedule === true;
})

</script>

<template>
    <div
        class="gap-8 overflow-hidden flex flex-col mx-4"
        :class="{'xl:flex-row': hasFilteredAnnouncements && hasFilteredSchedule}">
        <div ref="scrollLeft" class="overflow-auto flex-grow pt-6 pb-6" :class="{'xl:w-1/2': hasFilteredAnnouncements}"
             v-if="hasFilteredSchedule">
            <ScheduleList class="pb-6">
                <TransitionGroup name="list">
                    <ScheduleEntry :key="entry.id" :entry="entry" v-for="entry in filteredSchedule"></ScheduleEntry>
                </TransitionGroup>
            </ScheduleList>
        </div>
        <div ref="scrollRight" :class="{'xl:w-1/2': hasFilteredSchedule}"
             class="space-y-6 overflow-auto flex-grow pb-6 pt-6"
             v-if="hasFilteredAnnouncements">
            <TransitionGroup name="list">
                <AnnouncementCard :key="entry.id" v-for="entry in filteredAnnouncements"
                                  :entry="entry"></AnnouncementCard>
            </TransitionGroup>
        </div>
        <div class="mt-32 text-center"
             v-if="!hasFilteredSchedule && !hasFilteredAnnouncements">
            <div class="themeFont text-white text-7xl">There are currently no entries</div>
        </div>
    </div>
</template>

<style scoped>
.list-move, /* apply transition to moving elements */
.list-enter-active,
.list-leave-active {
    transition: all 0.5s ease;
}

.list-enter-from,
.list-leave-to {
    opacity: 0;
    transform: translateX(30px);
}

/* ensure leaving items are taken out of layout flow so that moving
   animations can be calculated correctly. */
.list-leave-active {
    position: absolute;
}

.overflow-auto::-webkit-scrollbar {
    display: none;
}
</style>
