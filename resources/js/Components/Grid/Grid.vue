<script setup>
// Grid.vue
//
// A CSS-grid container that FILLS its parent (100% x 100%). It is the layout
// backbone of the signage system. The critical trick is `minmax(0, 1fr)` for
// every track: rows and columns share the available space equally and the grid
// NEVER exceeds its container. Adding more blocks simply makes each smaller —
// "auto extends, never larger than the screen".
//
// Two modes:
//   * Explicit: `columns` columns (default 12), rows 'auto' or a fixed count.
//   * autoFit:  ignores `columns` and arranges the N children into a near-square
//               grid (cols = ceil(sqrt(N)), rows = ceil(N/cols)), each equal.
//
// The resolved column count is provided to descendant Blocks (via provide) so
// they can clamp their colSpan and never overflow a row.

import { computed, provide, useSlots } from 'vue'
import { GRID_COLUMNS_KEY } from './gridKeys.js'

const props = defineProps({
    // Number of columns when autoFit is false.
    columns: { type: Number, default: 12 },
    // 'auto' lets rows grow with content (grid-auto-rows), or fix a row count.
    rows: { type: [Number, String], default: 'auto' },
    // Gap between tiles (any CSS length).
    gap: { type: String, default: '1.5rem' },
    // When true, ignore `columns` and auto-arrange children into a square-ish grid.
    autoFit: { type: Boolean, default: false },
    // Inner padding of the grid container.
    padding: { type: String, default: '0' },
})

const slots = useSlots()

// Count the rendered top-level VNodes of the default slot. We flatten obvious
// container fragments (v-for / template) so `<Block v-for>` is counted per item.
function countSlotChildren() {
    const nodes = slots.default ? slots.default() : []
    let count = 0
    const visit = (list) => {
        for (const vnode of list) {
            if (vnode == null) continue
            // Comment nodes (e.g. v-if placeholders) have Symbol type — skip.
            if (typeof vnode.type === 'symbol') {
                // Fragment: its children is an array of vnodes (v-for output).
                if (Array.isArray(vnode.children)) {
                    visit(vnode.children)
                }
                continue
            }
            // Skip plain whitespace text nodes.
            if (typeof vnode.children === 'string' && vnode.children.trim() === '') {
                continue
            }
            count++
        }
    }
    visit(nodes)
    return count
}

// Reactive child count (re-evaluated when the slot re-renders).
const childCount = computed(() => countSlotChildren())

// autoFit geometry: near-square arrangement of childCount items.
const autoCols = computed(() => {
    const n = childCount.value
    return n > 0 ? Math.ceil(Math.sqrt(n)) : 1
})
const autoRows = computed(() => {
    const n = childCount.value
    const cols = autoCols.value
    return n > 0 ? Math.ceil(n / cols) : 1
})

// The effective column count, provided to Blocks for span-clamping.
const resolvedColumns = computed(() =>
    props.autoFit ? autoCols.value : props.columns
)
provide(GRID_COLUMNS_KEY, resolvedColumns)

const gridStyle = computed(() => {
    const base = {
        display: 'grid',
        width: '100%',
        height: '100%',
        gap: props.gap,
        padding: props.padding,
        boxSizing: 'border-box',
    }

    if (props.autoFit) {
        // Equal square-ish cells; every child sized equally.
        return {
            ...base,
            gridTemplateColumns: `repeat(${autoCols.value}, minmax(0, 1fr))`,
            gridTemplateRows: `repeat(${autoRows.value}, minmax(0, 1fr))`,
        }
    }

    // Explicit columns. minmax(0, 1fr) keeps tracks equal and prevents overflow.
    const out = {
        ...base,
        gridTemplateColumns: `repeat(${props.columns}, minmax(0, 1fr))`,
    }

    if (props.rows === 'auto' || props.rows == null) {
        // Rows grow as needed but each shares space equally.
        out.gridAutoRows = 'minmax(0, 1fr)'
    } else {
        // Fixed number of equal rows that fill the container height.
        out.gridTemplateRows = `repeat(${props.rows}, minmax(0, 1fr))`
    }

    return out
})
</script>

<template>
    <div class="grid-container" :style="gridStyle">
        <slot />
    </div>
</template>

<style scoped>
.grid-container {
    /* min-height/width:0 lets the grid shrink inside flex/grid parents. */
    min-width: 0;
    min-height: 0;
}
</style>
