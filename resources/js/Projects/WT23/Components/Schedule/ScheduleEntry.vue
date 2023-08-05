<script setup>
    import HourTime from "@/Components/HourTime.vue";

    function isCurrentTimeBetween(startTime,endTime) {
        const now = new Date();
        const startTimeCompare = new Date(startTime);
        const endTimeCompare = new Date(endTime);
        return now >= startTimeCompare && now <= endTimeCompare;
    }

    defineProps(['entry'])
</script>

<template>
    <div class="text-white flex justify-between flex-row">
        <div>
            <div class="text-4xl themeFont">{{ entry.title }}</div>
            <div class="text-3xl themeFont text-secondary">{{ entry.room }}</div>
        </div>
        <div class="flex gap-3">
            <div v-if="isCurrentTimeBetween(entry.starts_at,entry.ends_at)">
                <div
                    class="inline-block bg-red-500 text-white font-bold shadow-2xl rounded-full py-2 px-4 text-sm">
                    ONGOING <span class="animate-blink">●</span>
                </div>
            </div>
            <div class="text-2xl themeFont">
                <HourTime :time="entry.starts_at"/> - <HourTime :time="entry.ends_at"/>
            </div>
        </div>
    </div>
</template>


<style scoped>
/* Styles for the chip */
.chip {
    display: inline-block;
    background-color: #e0e0e0;
    color: #333;
    border-radius: 16px;
    padding: 8px 12px;
    font-size: 14px;
    cursor: pointer;
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
