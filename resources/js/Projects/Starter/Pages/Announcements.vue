<script setup>
// Announcements — renders via the "None" layout (no themed chrome), so it owns
// its own background. Tiles every active announcement into an `auto-fit` grid
// and uses <FitText> so longer announcements scale down to fit their tile.
import { computed } from "vue";
import { DateTime } from "luxon";
import { Grid, Block } from "@/Components/Grid";

const props = defineProps({
    title: { type: String, default: "Announcements" },
    announcements: { type: Array, default: () => [] },
});

const items = computed(() => {
    const now = DateTime.now();
    return (props.announcements ?? []).filter((a) => {
        if (!a) return false;
        const startOk = !a.starts_at || DateTime.fromISO(a.starts_at) <= now;
        const endOk = !a.ends_at || DateTime.fromISO(a.ends_at) >= now;
        return startOk && endOk;
    });
});
</script>

<template>
    <div class="flex h-full w-full flex-col bg-primary-900 p-14">
        <h1 class="mb-8 shrink-0 text-7xl font-black tracking-tight text-white">
            {{ title }}
        </h1>

        <div class="min-h-0 flex-1">
            <Grid auto-fit gap="1.5rem">
                <Block
                    v-for="(item, i) in items"
                    :key="item.id ?? i"
                    class="surface"
                    title=""
                    fit
                    align="start"
                    padding="2.5rem"
                >
                    <template #header>
                        <div class="text-5xl font-bold text-accent-400">
                            {{ item.title }}
                        </div>
                    </template>
                    <div
                        class="prose-invert text-4xl font-light leading-snug text-white/80"
                        v-html="item.content"
                    ></div>
                </Block>

                <Block v-if="items.length === 0" align="center" fit>
                    <span class="text-5xl text-white/50">No announcements</span>
                </Block>
            </Grid>
        </div>
    </div>
</template>
