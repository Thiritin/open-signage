<script setup>
// Starter "Grid" layout.
//
// Establishes the resolution-independent virtual canvas (1920x1080) via
// AutoScale and paints a themed background. Any page rendered through this
// layout is authored against a fixed 1920x1080 box and uniformly scaled to fit
// the real screen — never larger than the screen. Pages then compose their
// content with the <Grid>/<Block> primitives.
import { computed, reactive, useAttrs } from "vue";
import { AutoScale } from "@/Components/Grid";

const props = defineProps(["page"]);

defineOptions({ inheritAttrs: false });

const attrs = reactive(useAttrs());

// Mirror the shared layout contract: forward all screen data + the page's own
// configured props down into the design component.
const usableAttributes = computed(() => ({
    ...attrs,
    page: props.page,
    ...props.page.props,
}));
</script>

<template>
    <AutoScale :width="1920" :height="1080" mode="contain" background="#0f172a">
        <!-- Themed canvas backdrop -->
        <div class="canvas">
            <div class="canvas-bg"></div>
            <Transition mode="out-in">
                <component
                    :is="page.resolvedComponent"
                    v-bind="usableAttributes"
                />
            </Transition>
        </div>
    </AutoScale>
</template>

<style scoped>
.canvas {
    position: relative;
    width: 100%;
    height: 100%;
    color: #fff;
}

.canvas-bg {
    position: absolute;
    inset: 0;
    background:
        radial-gradient(
            1200px 800px at 15% -10%,
            rgba(99, 102, 241, 0.35),
            transparent 60%
        ),
        radial-gradient(
            1000px 700px at 110% 120%,
            rgba(34, 211, 238, 0.22),
            transparent 55%
        ),
        #0f172a;
    z-index: 0;
}

.canvas > :not(.canvas-bg) {
    position: relative;
    z-index: 1;
    width: 100%;
    height: 100%;
}

.v-enter-active,
.v-leave-active {
    transition: opacity 0.6s ease;
}
.v-enter-from,
.v-leave-to {
    opacity: 0;
}
</style>
