# Open Signage — Grid System

An Alamos FE2–style auto-fitting **GRID / BLOCK** system for composing
full-screen digital-signage designs that **always fit the screen and never
overflow**, regardless of the physical display resolution.

## Design philosophy

Three independent mechanisms stack to make overflow impossible:

1. **Virtual canvas (`AutoScale`)** — You author every design against a fixed
   virtual canvas (default `1920 × 1080`). `AutoScale` measures the real
   viewport and applies a single CSS `transform: scale()` so the canvas fits the
   screen. Layout math never depends on the actual resolution — design once,
   render on a 4K wall or a 720p panel identically.

2. **Auto-fit grid (`Grid` + `Block`)** — A CSS grid where every track is
   `minmax(0, 1fr)`. Columns and rows always share the available space equally,
   so the grid **can never exceed its container**. Adding more blocks simply
   makes each block smaller. With `auto-fit`, N blocks are arranged into a
   near-square grid automatically.

3. **Fit-to-content (`FitText`)** — Inside a block, content is measured and
   shrunk (`transform: scale()`) so it never overflows its cell in either
   dimension — no matter how long the text or how big the element.

> Virtual canvas + auto-fit grid + fit-to-content = **never overflows.**

## Full example

```vue
<script setup>
import { AutoScale, Grid, Block } from '@/Components/Grid'
</script>

<template>
  <AutoScale :width="1920" :height="1080" mode="contain" background="#0b1020">
    <Grid :columns="12" gap="1.5rem" padding="2rem">
      <Block :col-span="8" :row-span="2" title="Now" fit>
        <div class="text-white text-9xl font-bold">Main Hall — Keynote</div>
      </Block>
      <Block :col-span="4" title="Up Next">
        <p class="text-white text-4xl">Workshop A · 14:30</p>
      </Block>
      <Block :col-span="12" align="center">
        <span class="text-white/70 text-3xl">Welcome to the conference</span>
      </Block>
    </Grid>
  </AutoScale>
</template>
```

### Auto-fit mode (near-square grid from a list)

```vue
<AutoScale>
  <Grid auto-fit gap="1rem" padding="1.5rem">
    <Block v-for="r in rooms" :key="r.id" :title="r.name" fit>
      {{ r.status }}
    </Block>
  </Grid>
</AutoScale>
```

`auto-fit` ignores `columns` and arranges the N blocks into
`cols = ceil(sqrt(N))` × `rows = ceil(N / cols)`, sizing every block equally.

## Component reference

### `AutoScale`

Wraps a slot in a fixed virtual canvas and scales it to fit the viewport.

| Prop         | Type     | Default         | Description |
|--------------|----------|-----------------|-------------|
| `width`      | `Number` | `1920`          | Virtual canvas width (px) the design is authored against. |
| `height`     | `Number` | `1080`          | Virtual canvas height (px). |
| `mode`       | `String` | `'contain'`     | Fit strategy (see below). |
| `background` | `String` | `'transparent'` | Viewport background (letterbox bars in `contain`). |

**`mode` values:**

- `contain` — fit entirely within the viewport, preserve aspect ratio, never
  larger than the screen (`min(vw/w, vh/h)`). **Default / safest.**
- `cover` — fill the whole viewport, preserve aspect ratio, may crop (`max(...)`).
- `fit-width` — match viewport width; height scales with it.
- `fit-height` — match viewport height; width scales with it.
- `stretch` — fill both axes independently (aspect ratio not preserved).

### `Grid`

CSS-grid container that fills its parent (`100% × 100%`).

| Prop      | Type            | Default    | Description |
|-----------|-----------------|------------|-------------|
| `columns` | `Number`        | `12`       | Column count (ignored when `auto-fit`). |
| `rows`    | `Number｜String` | `'auto'`   | `'auto'` grows rows with content; a number fixes equal rows. |
| `gap`     | `String`        | `'1.5rem'` | Gap between tiles (any CSS length). |
| `autoFit` | `Boolean`       | `false`    | Auto-arrange children into a near-square grid; ignores `columns`. |
| `padding` | `String`        | `'0'`      | Inner padding of the grid container. |

Provides its resolved column count to descendant `Block`s for span clamping.

### `Block`

A single grid tile.

| Prop      | Type     | Default    | Description |
|-----------|----------|------------|-------------|
| `colSpan` | `Number` | `1`        | Columns to span (clamped to the Grid's column count). |
| `rowSpan` | `Number` | `1`        | Rows to span. |
| `title`   | `String` | `''`       | Header text above the content (overridden by `#header`). |
| `padding` | `String` | `'1.5rem'` | Inner padding of the tile. |
| `fit`     | `Boolean`| `false`    | Wrap content in `FitText` so it auto-shrinks to fit. |
| `align`   | `String` | `'stretch'`| Content alignment: `stretch｜start｜center｜end`. |

**Slots:** default (content), `#header` (overrides `title`).

The default surface is `rounded-2xl bg-white/5` — override by adding your own
background/rounding classes on the `<Block>` usage.

### `FitText`

Shrinks its slot content so it never overflows its container.

| Prop  | Type     | Default | Description |
|-------|----------|---------|-------------|
| `max` | `Number` | `1`     | Maximum scale (never enlarges beyond this). |
| `min` | `Number` | `0.1`   | Minimum scale floor. |

Recomputes on container resize (`ResizeObserver`) and content change
(`MutationObserver`). `scale = min(max, containerW/contentW, containerH/contentH)`,
clamped to `min`.

### `useScaleToFit(targetRef, { width, height, mode })`

Low-level composable powering `AutoScale`. Returns `{ scale, scaleX, scaleY, style }`
— reactive scale factors plus a computed `style` object (`width`, `height`,
`transform`, `transformOrigin`) to bind to the canvas element. Each option may be
a raw value or a getter/ref (reactive). Cleans up its observers on unmount.
