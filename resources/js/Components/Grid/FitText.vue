<script setup>
// FitText.vue
//
// Shrinks its slot content (via CSS transform: scale) so it NEVER overflows its
// container in either dimension. This is the "fit-to-content" leg of the system:
// the virtual canvas keeps the layout fixed, the grid keeps tiles bounded, and
// FitText keeps content inside each tile no matter how big it naturally is.
//
// Algorithm: measure the container box and the content's natural box, then
// scale = min(max, containerW/contentW, containerH/contentH), clamped to >= min.
// transform-origin is top-left so the scaled content stays anchored.
//
// Recomputes on container resize (ResizeObserver) and content change
// (MutationObserver), and measures inside requestAnimationFrame after layout.

import { ref, onMounted, onBeforeUnmount } from 'vue'

const props = defineProps({
    // Maximum scale (never enlarge content beyond this).
    max: { type: Number, default: 1 },
    // Minimum scale (floor so content never vanishes entirely).
    min: { type: Number, default: 0.1 },
})

const container = ref(null) // the clipping box we must fit within
const content = ref(null)   // the natural-size wrapper we scale
const scale = ref(1)

let resizeObserver = null
let mutationObserver = null
let rafId = null

function measure() {
    rafId = null
    const c = container.value
    const inner = content.value
    if (!c || !inner) return

    const cw = c.clientWidth
    const ch = c.clientHeight

    // Natural (unscaled) content size. scrollWidth/Height reflect the content
    // box; we temporarily ignore the current transform by reading offset size,
    // which is layout size before transform is applied.
    const contentW = inner.offsetWidth
    const contentH = inner.offsetHeight

    // Guard against divide-by-zero / not-yet-laid-out elements.
    if (cw <= 0 || ch <= 0 || contentW <= 0 || contentH <= 0) {
        return
    }

    let next = Math.min(props.max, cw / contentW, ch / contentH)
    if (!Number.isFinite(next)) next = props.max
    next = Math.max(props.min, next)
    scale.value = next
}

function scheduleMeasure() {
    if (rafId != null) return
    rafId = requestAnimationFrame(measure)
}

onMounted(() => {
    scheduleMeasure()

    if (typeof ResizeObserver !== 'undefined') {
        resizeObserver = new ResizeObserver(scheduleMeasure)
        if (container.value) resizeObserver.observe(container.value)
        // Also observe the content so intrinsic size changes are caught.
        if (content.value) resizeObserver.observe(content.value)
    }

    if (typeof MutationObserver !== 'undefined' && content.value) {
        mutationObserver = new MutationObserver(scheduleMeasure)
        mutationObserver.observe(content.value, {
            childList: true,
            subtree: true,
            characterData: true,
            attributes: true,
        })
    }

    window.addEventListener('resize', scheduleMeasure, { passive: true })
})

onBeforeUnmount(() => {
    if (resizeObserver) {
        resizeObserver.disconnect()
        resizeObserver = null
    }
    if (mutationObserver) {
        mutationObserver.disconnect()
        mutationObserver = null
    }
    window.removeEventListener('resize', scheduleMeasure)
    if (rafId != null) {
        cancelAnimationFrame(rafId)
        rafId = null
    }
})
</script>

<template>
    <div ref="container" class="fittext-container">
        <!-- The content is measured at its natural size, then scaled. -->
        <div
            ref="content"
            class="fittext-content"
            :style="{ transform: `scale(${scale})`, transformOrigin: 'top left' }"
        >
            <slot />
        </div>
    </div>
</template>

<style scoped>
.fittext-container {
    width: 100%;
    height: 100%;
    overflow: hidden;
    position: relative;
}

.fittext-content {
    /* Lay out at natural width so we can measure the true content size, then
       shrink via transform. inline-block hugs the content box. */
    display: inline-block;
    white-space: normal;
    position: absolute;
    top: 0;
    left: 0;
}
</style>
