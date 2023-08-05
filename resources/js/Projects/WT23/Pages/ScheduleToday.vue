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
    }
})
import ScheduleEntry from "@/Projects/WT23/Components/Schedule/ScheduleEntry.vue";
import AnnouncementCard from "@/Projects/WT23/Components/AnnouncementCard.vue";
import {computed, onMounted, onUnmounted, ref} from "vue";

const scrollLeft = ref(null);
const scrollRight = ref(null);

onMounted(() => {
    pageScroll(scrollLeft)
    pageScroll(scrollRight)
    const intervalId = setInterval(updateComputedProperties, 5000); // Update every 1 second
    onUnmounted(() => clearInterval(intervalId));
})

let scrollingDown = true;
const currentTime = ref(new Date());

function pageScroll(ref) {
    if (ref.value === null) return;

    const windowHeight = ref.value.clientHeight;
    const pageHeight = ref.value.offsetHeight;
    const scrollHeight = ref.value.scrollHeight;
    const endScoll = scrollHeight - pageHeight;
    if (scrollHeight === pageHeight) return;

    let stop = false;
    // Check if we reached the bottom of the page
    if (ref.value.scrollTop >= endScoll) {
        if (scrollingDown === true) {
            setTimeout(() => {
                scrollingDown = false;
            }, 5000);
        }
    } else if (ref.value.scrollTop <= 0) {
        if (scrollingDown === false) {
            setTimeout(() => {
                scrollingDown = true;
            }, 5000);
        }
    }

    // Scroll down or up depending on the scrollingDown flag
    if (scrollingDown) {
        ref.value.scrollTop++; // Scroll down by 1 pixel
    } else {
        ref.value.scrollTop--; // Scroll up by 1 pixel
    }

    // Repeat the scrolling process after a short delay (10ms in this case)
    setTimeout(() => {
        pageScroll(ref);
    }, 15);
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
    return props.schedule.filter((entry) => {
        const now = currentTime.value;
        const start = new Date(entry.starts_at);
        const end = new Date(entry.ends_at);
        return (end > now) && (end.getDate() >= now.getDate() || start.getDate() <= now.getDate());
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
        class="gap-8 overflow-hidden flex flex-col"
        :class="{'xl:flex-row': hasFilteredAnnouncements && hasFilteredSchedule}">
        <div ref="scrollLeft" class="overflow-auto flex-grow pt-6" :class="{'xl:w-1/2': hasFilteredAnnouncements}"
             v-if="hasFilteredSchedule">
            <ScheduleList class="pb-6">
                <TransitionGroup name="list">
                    <ScheduleEntry :key="entry.id" :entry="entry" v-for="entry in filteredSchedule"></ScheduleEntry>
                </TransitionGroup>
            </ScheduleList>
        </div>
        <div ref="scrollRight" :class="{'xl:w-1/2': hasFilteredSchedule}" class="space-y-6 overflow-auto flex-grow pb-6 pt-6"
             v-if="hasFilteredAnnouncements">
            <TransitionGroup name="list">
                <AnnouncementCard :key="entry.id" v-for="entry in filteredAnnouncements" :entry="entry"></AnnouncementCard>
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
