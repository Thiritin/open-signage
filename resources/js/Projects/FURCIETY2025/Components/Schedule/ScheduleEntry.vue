<script setup>
import HourTime from "@/Components/HourTime.vue";
import Card from "@/Projects/WT23/Components/Card.vue";

function isCurrentTimeBetween(startTime, endTime, delay) {
    const now = new Date();
    const startTimeCompare = new Date(new Date(startTime).getTime() + delay * 60 * 1000);
    const endTimeCompare = new Date(new Date(endTime).getTime() + delay * 60 * 1000);
    return now >= startTimeCompare && now <= endTimeCompare;
}

function showInProgress(entry) {
    if (isCurrentTimeBetween(entry.starts_at, entry.ends_at, (entry.delay ?? 0)))
        return true;
}

function entryInPast(entry) {
    const now = new Date();
    const endTimeCompare = new Date(new Date(entry.ends_at).getTime() + (entry.delay * 60 * 1000 ?? 0));
    return now.getTime() >= endTimeCompare.getTime();
}

function timeBackgroundFont(entry) {
    if (isCurrentTimeBetween(entry.starts_at, entry.ends_at, (entry.delay ?? 0)))
        return "bg-green-500";
    else if (entryInPast(entry) || entry.flags.find((e) => e === 'cancelled'))
        return "bg-gray-600 text-gray-300";
    else if (entry.delay) {
        return "bg-red-500"
    } else
        return "";
}
const delayTime = (time, delay) => new Date(new Date(time).getTime() + (delay * 60 * 1000 ?? 0)).getTime();

const returnStyleWidthInPercent = (entry) => {
    const now = new Date().getTime();
    const totalMinutes = (delayTime(entry.ends_at, entry.delay) - delayTime(entry.starts_at, entry.delay)) / 1000 / 60;
    const minutesPassed = (now - delayTime(entry.starts_at, entry.delay)) / 1000 / 60;
    return (minutesPassed / totalMinutes) * 100;
}

defineProps(['entry'])
</script>

<template>
    <Card class="text-white bg-primary-500 flex justify-between flex-row">
        <div class="p-6">
            <div v-if="entry.message" class="text-3xl mb-3 rounded font-semibold text-red-700 bg-white p-2">{{ entry.message }}</div>
            <div class="text-4xl themeFont">
                <span v-if="entry.flags.find((f) => f === 'after_dark')">[After Dark] </span>
                {{ entry.title }}
            </div>
            <div class="text-3xl themeFont text-secondary">{{ entry.room.name }}</div>
            <div class="text-2xl themeFontSecondary text-white">{{ entry.description }}</div>
        </div>
        <div class="flex gap-3">
            <div class="text-3xl 2xl:text-5xl themeFont rounded-r p-6" :class="timeBackgroundFont(entry)">
                <div class="mb-2 whitespace-nowrap">
                    <div v-if="entry.delay > 0 && !entryInPast(entry)" :class="{'line-through text-red-100 text-center': entry.delay > 0}">
                    <HourTime :time="new Date(entry.starts_at).getTime()"/>
                    -
                    <HourTime :time="new Date(entry.ends_at).getTime()"/>
                    </div>
                    <div>
                        <HourTime :time="new Date(entry.starts_at).getTime() + (entry.delay * 60 * 1000)"/>
                        -
                        <HourTime :time="new Date(entry.ends_at).getTime() + (entry.delay * 60 * 1000)"/>
                    </div>
                </div>
                <div v-if="isCurrentTimeBetween(entry.starts_at,entry.ends_at,(entry.delay ?? 0))">
                    <div
                        class="flex font-bold w-fit mt-2 text-2xl justify-between items-center align-middle gap-2 rounded-full ml-auto themeFontSecondary">
                        <div class="animate-blink rounded-full bg-white h-3 w-3"></div>
                        <div>IN PROGRESS</div>
                    </div>
                    <div class="border-[3px] bg-opacity-60 h-4 p-[3px]">
                        <div class="bg-white h-full" :style="'width:'+returnStyleWidthInPercent(entry)+'%;'"></div>
                    </div>
                </div>
                <div v-else-if="entry.flags.find((e) => e === 'cancelled')" class="rounded">
                    <div class="font-mono p-2 text-2xl font-bold text-center w-full bg-black">
                        CANCELLED
                    </div>
                </div>
                <div v-else-if="entry.flags.find((e) => e === 'moved')" class="rounded">
                    <div class="font-mono p-2 text-2xl font-bold text-center w-full bg-yellow-600">
                        MOVED
                    </div>
                </div>
                <div v-else-if="entry.delay && !entryInPast(entry)" class="rounded">
                    <div class="font-mono p-2 text-2xl font-bold text-center w-full bg-red-900">
                        DELAYED +{{entry.delay}}m
                    </div>
                </div>
                <div v-else-if="entryInPast(entry)">
                    <div class="font-mono p-2 text-2xl font-bold text-center">
                        ENDED
                    </div>
                </div>
            </div>
        </div>
    </Card>
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
