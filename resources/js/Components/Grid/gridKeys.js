// gridKeys.js
//
// Shared provide/inject keys for the Grid system. Kept in a tiny module so both
// Grid.vue (provider) and Block.vue (consumer) reference the exact same symbol.

// The resolved column count of the nearest Grid ancestor (a Ref<number>).
// Blocks use this to clamp their colSpan so a tile never overflows a row.
export const GRID_COLUMNS_KEY = Symbol('grid-columns')
