<script setup>
import HourTime from "@/Components/HourTime.vue";

defineProps(['entry'])
import Card from "@/Components/Card.vue";
</script>

<template>
    <Card class="p-6" color="bg-orange-700">
        <div class="text-white flex justify-between flex-row-reverse">
            <div class="text-2xl themeFont"><HourTime :time="entry.starts_at"/></div>
            <div>
                <div class="text-4xl themeFont mb-2">{{ entry.title }}</div>
                <p class="text-3xl whitespace-pre-wrap themeFontSecondary">{{ entry.content }}</p>
            </div>
        </div>
    </Card>
</template>

<style scoped>

</style>
