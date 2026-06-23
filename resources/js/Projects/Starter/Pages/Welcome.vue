<script setup>
// Welcome — a hero splash design.
//
// Demonstrates the layout backbone: a 12-column <Grid> with explicit row spans
// that always fills the 1920x1080 canvas, plus <FitText> so a long title scales
// down to fit its tile instead of overflowing.
import { Grid, Block, FitText } from "@/Components/Grid";
import Clock from "../Components/Clock.vue";

defineProps({
    // Page-configured props (from the playlist item content / page schema).
    title: { type: String, default: "Welcome to Open Signage" },
    subtitle: { type: String, default: "Build signage designs, fast." },
    // Screen data forwarded by the layout.
    appScreen: { type: Object, default: () => ({}) },
});
</script>

<template>
    <Grid :columns="12" gap="2rem" padding="3.5rem">
        <!-- Top bar -->
        <Block :col-span="12" :row-span="1" padding="1.5rem 2.5rem">
            <div class="flex h-full items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="h-10 w-10 rounded-xl surface-accent"></div>
                    <span class="text-4xl font-semibold tracking-tight"
                        >Open Signage</span
                    >
                </div>
                <span class="text-5xl font-light tabular-nums text-white/80">
                    <Clock format="HH:mm" />
                </span>
            </div>
        </Block>

        <!-- Hero -->
        <Block :col-span="12" :row-span="4" align="center" fit padding="4rem">
            <div class="text-center">
                <h1 class="text-9xl font-black leading-none tracking-tight">
                    {{ title }}
                </h1>
                <p class="mt-8 text-6xl font-light text-white/70">
                    {{ subtitle }}
                </p>
            </div>
        </Block>

        <!-- Footer -->
        <Block :col-span="12" :row-span="1" padding="1.5rem 2.5rem">
            <div class="flex h-full items-center justify-between text-3xl text-white/50">
                <span>{{ appScreen?.name ?? "Preview" }}</span>
                <span>Powered by Open Signage</span>
            </div>
        </Block>
    </Grid>
</template>
