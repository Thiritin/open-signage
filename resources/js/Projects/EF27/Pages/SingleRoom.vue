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

const primaryRoom = computed(() => {
    return props.screen.rooms.find(room => room.pivot.primary)
})

const nextEvent = computed(() => {
    return props.schedule.filter(event => {
        return event.room_id === primaryRoom.value.id;
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
        <div class="mb-2 whitespace-nowrap text-5xl text-center text-[7vw] leading-normal">
            <div class="magicTextColor">
                <HourTime :time="DateTime.fromISO(nextEvent.starts_at).plus({minutes: nextEvent.delay})"/>
                -
                <HourTime :time="DateTime.fromISO(nextEvent.ends_at).plus({minutes: nextEvent.delay})"/>
            </div>
            <div class="magicTextColor text-[4vw] leading-none">
                Tomorrow
            </div>
            <div v-if="nextEvent.delay > 0" class="text-[5vw] leading-normal opacity-40 magicTextColor text-center">
                <HourTime :time="nextEvent.starts_at"/>
                -
                <HourTime :time="nextEvent.ends_at"/>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>
