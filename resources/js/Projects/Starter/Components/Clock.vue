<script setup>
// Live clock used across Starter designs.
import { ref, onMounted, onBeforeUnmount } from "vue";
import { DateTime } from "luxon";

const props = defineProps({
    format: { type: String, default: "HH:mm" },
});

const now = ref(DateTime.now());
let timer = null;

onMounted(() => {
    timer = setInterval(() => (now.value = DateTime.now()), 1000);
});
onBeforeUnmount(() => clearInterval(timer));
</script>

<template>
    <span class="tabular-nums">{{ now.toFormat(format) }}</span>
</template>
