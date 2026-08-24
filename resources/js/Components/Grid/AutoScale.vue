<script setup>
// AutoScale.vue
//
// Wraps a default slot in a fixed "virtual canvas" of width x height px and
// scales it to fit the real viewport using `useScaleToFit`. This is the
// guarantee that signage designs are resolution-independent and "never larger
// than the screen": author against a 1920x1080 canvas, render on any display.
//
// The outer wrapper fills the viewport (100vw x 100vh), hides overflow and
// centers the canvas. The inner canvas is exactly width x height px and is
// transform-scaled to fit.

import { ref } from 'vue'
import { useScaleToFit } from './useScaleToFit.js'

const props = defineProps({
    // Virtual canvas dimensions (px) the design is authored against.
    width: { type: Number, default: 1920 },
    height: { type: Number, default: 1080 },
    // Fit strategy: 'contain' | 'cover' | 'fit-width' | 'fit-height' | 'stretch'.
    mode: { type: String, default: 'contain' },
    // Background color of the viewport area (letterbox bars in 'contain').
    background: { type: String, default: 'transparent' },
})

const canvas = ref(null)

const { style } = useScaleToFit(canvas, {
    width: () => props.width,
    height: () => props.height,
    mode: () => props.mode,
})
</script>

<template>
    <div class="autoscale-viewport" :style="{ background }">
        <!-- Fixed virtual canvas; transform-scaled to fit the viewport. -->
        <div ref="canvas" class="autoscale-canvas" :style="style">
            <slot />
        </div>
    </div>
</template>

<style scoped>
.autoscale-viewport {
    position: fixed;
    inset: 0;
    width: 100vw;
    height: 100vh;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
}

.autoscale-canvas {
    /* width/height/transform are supplied by the bound :style. */
    position: relative;
    flex: 0 0 auto;
}
</style>
