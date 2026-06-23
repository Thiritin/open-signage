<script setup>
// RoomGrid — the auto-fit + scale-to-fit showcase (Alamos-style).
//
// Drops every room into an `auto-fit` <Grid>: the grid automatically arranges
// the N tiles into a near-square layout and sizes them equally. Add a room and
// every tile shrinks to keep the whole grid inside the screen — it never grows
// past the canvas. <FitText> keeps each room name inside its tile.
import { computed } from "vue";
import { Grid, Block, FitText } from "@/Components/Grid";

const props = defineProps({
    title: { type: String, default: "Rooms" },
    // Forwarded by the layout (mock data in preview, live data on a screen).
    rooms: { type: Array, default: () => [] },
});

const roomList = computed(() =>
    (props.rooms ?? []).filter((r) => r && (r.name || r.venue_name)),
);
</script>

<template>
    <div class="flex h-full flex-col p-14">
        <h1 class="mb-8 shrink-0 text-7xl font-black tracking-tight">
            {{ title }}
        </h1>

        <!-- The grid fills the remaining space and auto-fits all rooms. -->
        <div class="min-h-0 flex-1">
            <Grid auto-fit gap="1.5rem">
                <Block
                    v-for="room in roomList"
                    :key="room.id ?? room.name"
                    class="surface !bg-white/[0.06]"
                    align="center"
                    fit
                    padding="2rem"
                >
                    <div class="text-center">
                        <div class="text-6xl font-bold leading-tight">
                            {{ room.name }}
                        </div>
                        <div
                            v-if="room.venue_name && room.venue_name !== room.name"
                            class="mt-3 text-4xl font-light text-white/60"
                        >
                            {{ room.venue_name }}
                        </div>
                    </div>
                </Block>

                <Block v-if="roomList.length === 0" align="center" fit>
                    <span class="text-5xl text-white/50">No rooms assigned</span>
                </Block>
            </Grid>
        </div>
    </div>
</template>
