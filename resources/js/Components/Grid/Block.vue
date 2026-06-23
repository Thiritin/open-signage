<script setup>
// Block.vue
//
// A single grid tile/cell. It places itself in the parent Grid via
// `grid-column: span colSpan` / `grid-row: span rowSpan`, and renders a subtle,
// neutral, overridable tile surface. Optionally fits its content with FitText
// so a tile's content never overflows the cell.
//
// Span clamping: if a parent Grid provided its resolved column count, colSpan is
// clamped to it so a tile can never be wider than the grid's row.

import { computed, inject } from 'vue'
import FitText from './FitText.vue'
import { GRID_COLUMNS_KEY } from './gridKeys.js'

const props = defineProps({
    // How many columns this tile spans.
    colSpan: { type: Number, default: 1 },
    // How many rows this tile spans.
    rowSpan: { type: Number, default: 1 },
    // Optional header text rendered above the content (overridden by #header).
    title: { type: String, default: '' },
    // Inner padding of the tile.
    padding: { type: String, default: '1.5rem' },
    // When true, wrap the default slot in FitText so content auto-shrinks.
    fit: { type: Boolean, default: false },
    // Content alignment inside the tile: 'stretch' | 'start' | 'center' | 'end'.
    align: { type: String, default: 'stretch' },
})

// Provided by the nearest Grid (a Ref<number>), or undefined if standalone.
const gridColumns = inject(GRID_COLUMNS_KEY, null)

// Clamp colSpan to [1, columns] when a column count is available.
const effectiveColSpan = computed(() => {
    const span = Math.max(1, props.colSpan)
    const max = gridColumns && gridColumns.value ? gridColumns.value : null
    return max ? Math.min(span, max) : span
})

const effectiveRowSpan = computed(() => Math.max(1, props.rowSpan))

const tileStyle = computed(() => ({
    gridColumn: `span ${effectiveColSpan.value}`,
    gridRow: `span ${effectiveRowSpan.value}`,
    padding: props.padding,
}))

// Map align prop to flexbox alignment for the content column.
const contentAlign = computed(() => {
    switch (props.align) {
        case 'start':
            return { justifyContent: 'flex-start', alignItems: 'flex-start' }
        case 'center':
            return { justifyContent: 'center', alignItems: 'center' }
        case 'end':
            return { justifyContent: 'flex-end', alignItems: 'flex-end' }
        case 'stretch':
        default:
            return { justifyContent: 'flex-start', alignItems: 'stretch' }
    }
})
</script>

<template>
    <!-- Neutral, overridable surface. Override bg/rounding via class on usage. -->
    <div class="block-tile rounded-2xl bg-white/5" :style="tileStyle">
        <!-- Header: #header slot wins, else `title` prop if present. -->
        <header v-if="$slots.header || title" class="block-header">
            <slot name="header">{{ title }}</slot>
        </header>

        <!-- Content region fills remaining space. -->
        <div class="block-content" :style="contentAlign">
            <FitText v-if="fit">
                <slot />
            </FitText>
            <template v-else>
                <slot />
            </template>
        </div>
    </div>
</template>

<style scoped>
.block-tile {
    display: flex;
    flex-direction: column;
    /* min-* lets the tile shrink within minmax(0,1fr) tracks. */
    min-width: 0;
    min-height: 0;
    overflow: hidden;
    box-sizing: border-box;
}

.block-header {
    flex: 0 0 auto;
    margin-bottom: 0.75rem;
    font-weight: 600;
    line-height: 1.1;
}

.block-content {
    flex: 1 1 auto;
    display: flex;
    flex-direction: column;
    min-width: 0;
    min-height: 0;
    /* When fit is used, FitText fills this box and measures against it. */
    overflow: hidden;
}
</style>
