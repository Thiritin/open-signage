// useScaleToFit.js
//
// A composable that computes a CSS transform scale so a fixed "virtual canvas"
// (width x height, in px) always fits the real viewport, regardless of the
// physical screen resolution. This is the core of the resolution-independent
// signage system: design once against a virtual canvas, render anywhere.
//
// Usage:
//   const target = ref(null)
//   const { scale, style } = useScaleToFit(target, { width: 1920, height: 1080, mode: 'contain' })
//
// The `style` computed is meant to be bound to the virtual-canvas element.

import { ref, computed, unref, onMounted, onBeforeUnmount, watch } from 'vue'

/**
 * @param {import('vue').Ref<HTMLElement|null>} targetRef - the canvas element to scale.
 * @param {Object} options
 * @param {number|import('vue').Ref<number>} options.width  - virtual canvas width in px.
 * @param {number|import('vue').Ref<number>} options.height - virtual canvas height in px.
 * @param {string|import('vue').Ref<string>} options.mode   - 'contain' | 'cover' | 'fit-width' | 'fit-height' | 'stretch'.
 * @returns {{ scale: import('vue').Ref<number>, scaleX: import('vue').Ref<number>, scaleY: import('vue').Ref<number>, style: import('vue').ComputedRef<Object> }}
 */
export function useScaleToFit(targetRef, options = {}) {
    // Independent X/Y scale factors. For uniform modes scaleX === scaleY.
    const scaleX = ref(1)
    const scaleY = ref(1)
    // Convenience uniform scale (equals scaleX for non-stretch modes).
    const scale = ref(1)

    let resizeObserver = null

    const getWidth = () => Number(unref(options.width)) || 1920
    const getHeight = () => Number(unref(options.height)) || 1080
    const getMode = () => unref(options.mode) || 'contain'

    function compute() {
        const width = getWidth()
        const height = getHeight()
        if (width <= 0 || height <= 0) return

        // Measure the real viewport. We prefer the documentElement client size
        // (the kiosk Chrome window) and fall back to window inner size.
        const root = document.documentElement
        const vw = (root && root.clientWidth) || window.innerWidth || 0
        const vh = (root && root.clientHeight) || window.innerHeight || 0
        if (vw <= 0 || vh <= 0) return

        const sw = vw / width   // scale needed to fill width
        const sh = vh / height  // scale needed to fill height

        let sx
        let sy

        switch (getMode()) {
            case 'cover':
                // Fill the entire viewport; canvas may be cropped. Uniform.
                sx = sy = Math.max(sw, sh)
                break
            case 'fit-width':
                // Match the viewport width; height scales uniformly with it.
                sx = sy = sw
                break
            case 'fit-height':
                // Match the viewport height; width scales uniformly with it.
                sx = sy = sh
                break
            case 'stretch':
                // Independently fill both axes (aspect ratio not preserved).
                sx = sw
                sy = sh
                break
            case 'contain':
            default:
                // Fit entirely within the viewport, never larger than the
                // screen, preserving aspect ratio. This is the safe default.
                sx = sy = Math.min(sw, sh)
                break
        }

        scaleX.value = sx
        scaleY.value = sy
        // Uniform reference scale (for callers that only need one number).
        scale.value = getMode() === 'stretch' ? Math.min(sx, sy) : sx
    }

    // requestAnimationFrame guards against measuring mid-layout.
    let rafId = null
    function scheduleCompute() {
        if (rafId != null) return
        rafId = requestAnimationFrame(() => {
            rafId = null
            compute()
        })
    }

    onMounted(() => {
        compute()

        // ResizeObserver on the document root catches viewport changes that a
        // plain 'resize' event might miss (e.g. zoom, devtools, container size).
        if (typeof ResizeObserver !== 'undefined') {
            resizeObserver = new ResizeObserver(scheduleCompute)
            resizeObserver.observe(document.documentElement)
        }

        // Fallback / additional safety net.
        window.addEventListener('resize', scheduleCompute, { passive: true })
        window.addEventListener('orientationchange', scheduleCompute, { passive: true })
    })

    // Recompute whenever the virtual dimensions or mode change.
    watch(
        () => [getWidth(), getHeight(), getMode()],
        scheduleCompute
    )

    onBeforeUnmount(() => {
        if (resizeObserver) {
            resizeObserver.disconnect()
            resizeObserver = null
        }
        window.removeEventListener('resize', scheduleCompute)
        window.removeEventListener('orientationchange', scheduleCompute)
        if (rafId != null) {
            cancelAnimationFrame(rafId)
            rafId = null
        }
    })

    // CSS to apply to the virtual canvas element. It is sized exactly to the
    // virtual dimensions and then transform-scaled from its top-left origin.
    const style = computed(() => {
        const width = getWidth()
        const height = getHeight()
        const transform =
            scaleX.value === scaleY.value
                ? `scale(${scaleX.value})`
                : `scale(${scaleX.value}, ${scaleY.value})`
        return {
            width: `${width}px`,
            height: `${height}px`,
            transform,
            transformOrigin: 'center center',
        }
    })

    return { scale, scaleX, scaleY, style }
}

export default useScaleToFit
