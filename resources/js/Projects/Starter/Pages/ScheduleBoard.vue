<script setup>
// ScheduleBoard — an upcoming-events board.
//
// Uses an explicit 1-column <Grid>: a header row plus one row per event. Because
// every row is `minmax(0, 1fr)`, the rows always divide the canvas evenly and
// the board never overflows — we just cap how many events are shown.
import { computed } from "vue";
import { DateTime } from "luxon";
import { Grid, Block } from "@/Components/Grid";
import Clock from "../Components/Clock.vue";

const props = defineProps({
    title: { type: String, default: "What's On" },
    schedule: { type: Array, default: () => [] },
});

const MAX_ROWS = 6;

const events = computed(() => {
    const now = DateTime.now();
    return (props.schedule ?? [])
        .filter((e) => e && e.ends_at && DateTime.fromISO(e.ends_at) >= now)
        .sort(
            (a, b) =>
                DateTime.fromISO(a.starts_at).toMillis() -
                DateTime.fromISO(b.starts_at).toMillis(),
        )
        .slice(0, MAX_ROWS);
});

const fmt = (iso) => (iso ? DateTime.fromISO(iso).toFormat("HH:mm") : "--:--");
</script>

<template>
    <Grid :columns="1" gap="1.25rem" padding="3rem">
        <!-- Header -->
        <Block padding="1.25rem 2.5rem" class="surface-accent !border-0">
            <div class="flex h-full items-center justify-between">
                <span class="text-7xl font-black tracking-tight">{{ title }}</span>
                <span class="text-5xl font-light"><Clock format="HH:mm" /></span>
            </div>
        </Block>

        <!-- One row per upcoming event -->
        <Block
            v-for="event in events"
            :key="event.id ?? event.title"
            padding="0 2.5rem"
        >
            <div class="flex h-full items-center gap-10">
                <div
                    class="w-64 shrink-0 text-6xl font-bold tabular-nums text-accent-400"
                >
                    {{ fmt(event.starts_at) }}
                </div>
                <div class="min-w-0 flex-1">
                    <div class="truncate text-5xl font-semibold">
                        {{ event.title }}
                    </div>
                    <div class="mt-1 text-3xl font-light text-white/60">
                        {{ event.room?.name ?? "" }}
                    </div>
                </div>
                <div
                    v-if="event.delay"
                    class="shrink-0 rounded-full bg-warning-500/20 px-6 py-2 text-3xl font-semibold text-warning-400"
                >
                    +{{ event.delay }} min
                </div>
                <div
                    v-else
                    class="shrink-0 text-3xl font-medium text-success-400"
                >
                    On time
                </div>
            </div>
        </Block>

        <Block v-if="events.length === 0" align="center">
            <span class="text-5xl text-white/50">Nothing scheduled</span>
        </Block>
    </Grid>
</template>
