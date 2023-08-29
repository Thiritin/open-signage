<script setup>
import {computed, onMounted, onUnmounted, ref} from "vue";
import {DateTime} from "luxon";
import HourTime from "@/Components/HourTime.vue";

const props = defineProps({
    screen: {
        type: Object,
        required: true
    },
    schedule: {
        type: Array,
        required: true
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

function getDayDescription(starts_at) {
    // Parse the starts_at date string into a Luxon DateTime object
    const eventDate = DateTime.fromISO(starts_at).startOf("day");

    // Get the current date and set it to the start of the day (00:00:00)
    const currentDate = DateTime.local().startOf("day");

    // Calculate the difference in days between the event date and the current date
    const diffInDays = eventDate.diff(currentDate, "days").days;

    // Check the difference and return the appropriate string
    if (diffInDays === 0) {
        return null; // Return nothing if the date is today
    } else if (diffInDays === 1) {
        return "Tomorrow"; // Return "Tomorrow" if the date is tomorrow
    } else {
        return eventDate.toFormat("EEEE"); // Return the weekday name for any other date
    }
}

const nextEvent = computed(() => {
    return props.schedule.filter(event => {
        return event.room_id === props.screen.room_id;
    }).filter(event => {
        return currentTime.value <= DateTime.fromISO(event.ends_at).plus({minutes: event.delay})
    })[0]
})
</script>

<template>
    <div class="flex flex-col items-center justify-center h-screen z-50 themeFontSecondary leading-normal">
        <div class="text-[6vw] leading-normal font-bold text-center magicTextColor">
            {{ nextEvent.title }}
        </div>
        <div class="mb-2 whitespace-nowrap text-5xl text-center text-[8vw] leading-normal">
            <div class="magicTextColor">
                <HourTime :time="DateTime.fromISO(nextEvent.starts_at)"/>
                -
                <HourTime :time="DateTime.fromISO(nextEvent.ends_at)"/>
            </div>
            <div v-if="getDayDescription(DateTime.fromISO(nextEvent.starts_at).plus({minutes: nextEvent.delay}))" class="magicTextColor text-[4vw] leading-none">
                {{ getDayDescription(DateTime.fromISO(nextEvent.starts_at).plus({minutes: nextEvent.delay})) }}
            </div>
            <div v-if="nextEvent.delay > 0" class="text-[6vw] leading-none magicTextColor text-center">
                Delayed by {{ nextEvent.delay }} minutes
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>
