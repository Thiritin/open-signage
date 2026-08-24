# Developing designs with Open Signage

Open Signage is a **framework for building digital‑signage designs**. You write
your screens as Vue 3 components in a *project*, preview them instantly in the
browser with mock data, and the platform handles playlists, scheduling, live
updates and kiosk delivery.

This guide gets you from a fresh clone to iterating on your own design as fast
as possible.

---

## 1. Quick start

```bash
git clone https://github.com/thiritin/open-signage.git
cd open-signage

composer install
npm install

cp .env.example .env
php artisan key:generate

# Create the schema and seed a working demo (a "demo" screen, a Starter
# playlist, rooms, schedule and announcements):
php artisan migrate --seed

# Two terminals (or use Laravel Sail):
php artisan serve      # http://localhost:8000
npm run dev            # Vite dev server with hot reload
```

Now open:

| URL | What you get |
| --- | --- |
| `http://localhost:8000/preview` | **Design preview** – render any design with mock data, no DB needed |
| `http://localhost:8000/screens/demo` | The seeded demo screen running the real playlist pipeline |
| `http://localhost:8000/admin` | Filament admin (login `me@thiritin.com` / `password`) |

> The active project is selected with `VITE_PROJECT_PATH` in `.env`
> (default: `Starter`). It is baked in at build time, so change it and restart
> Vite to switch projects.

---

## 2. The preview workflow (test designs without any setup)

The fastest way to iterate on a design is the **preview route**. It renders a
single design component standalone, fed with realistic **mock data** from
`resources/js/mock/index.js` — **no database, no playlist, no screen, no
websocket server required.**

```
/preview                         # first design, default layout
/preview/RoomGrid                # preview a specific page component
/preview/ScheduleBoard?layout=Grid
/preview/Welcome?layout=Grid&bare=1   # bare=1 hides the dev toolbar (clean screenshots)
```

The preview screen has a small **dev toolbar** to switch page, switch layout,
change the canvas resolution (1080p / 4K / portrait / 720p) and toggle a grid
overlay. It is **dev‑only** (gated behind `APP_DEBUG`/`local`).

Edit your `.vue` file → Vite hot‑reloads → see it instantly. Tweak the mock data
in `resources/js/mock/index.js` to exercise edge cases.

---

## 3. Anatomy of a project

A project is a folder under `resources/js/Projects/<Name>/`:

```
resources/js/Projects/Starter/
├── app.css          # global styles (imported automatically for the active project)
├── theme.js         # Tailwind theme (colors etc.) – merged into tailwind.config.js
├── Layouts/         # frames that wrap pages (background, scaling, transitions)
│   ├── Grid.vue
│   └── None.vue
├── Pages/           # the actual designs
│   ├── Welcome.vue
│   ├── ScheduleBoard.vue
│   ├── RoomGrid.vue
│   └── Announcements.vue
└── Components/      # shared bits used by your pages
    └── Clock.vue
```

**Every page receives the same screen data as props**, forwarded by its layout:

| Prop | Type | Description |
| --- | --- | --- |
| `appScreen` | Object | The screen (name, slug, orientation, `rooms`, …) |
| `rooms` | Array | Rooms assigned to the screen (each with a `pivot`) |
| `schedule` | Array | Schedule entries (`title`, `room`, `starts_at`, `ends_at`, `delay`, …) |
| `announcements` | Array | Active announcements |
| `artworks` | Array | Artwork gallery items |
| `connected` | Boolean | Websocket connection state |

…plus any **page‑specific props** you declare in the page's *schema* (editable in
the admin and stored per playlist item) — e.g. `title`, `subtitle`.

To register a new page/layout in the database (so it can be added to playlists),
mirror `database/seeders/StarterSeeder.php`.

---

## 4. The grid system (auto‑fit + scale‑to‑fit)

Signage screens come in every resolution. Instead of hand‑tuning a design for
1080p, **author against a fixed virtual canvas and let the framework scale it to
fit any screen** — and never larger than the screen. Three primitives in
`resources/js/Components/Grid/` make this work:

- **`AutoScale`** – wraps your design in a fixed virtual canvas (default
  `1920×1080`) and uniformly scales it to fit the viewport (`contain`). This is
  the "never larger than the screen" guarantee. The Starter layouts already do
  this for you.
- **`Grid`** – a CSS‑grid container that *fills* its parent. Every track is
  `minmax(0, 1fr)`, so adding more blocks just makes each smaller — the grid
  **auto‑extends but never overflows**. Use `auto-fit` to arrange N blocks into a
  near‑square grid automatically.
- **`Block`** – a tile that spans `colSpan × rowSpan` cells, with optional
  `title`, `fit`, and `align`.
- **`FitText`** – shrinks content so it never overflows its tile (used via the
  `fit` prop on `Block`, or directly).

```vue
<script setup>
import { AutoScale, Grid, Block } from "@/Components/Grid";
defineProps({ rooms: { type: Array, default: () => [] } });
</script>

<template>
  <!-- The Starter layouts already provide AutoScale; shown here for clarity. -->
  <AutoScale :width="1920" :height="1080" mode="contain">
    <Grid auto-fit gap="1.5rem">
      <Block v-for="r in rooms" :key="r.id" fit align="center">
        {{ r.name }}
      </Block>
    </Grid>
  </AutoScale>
</template>
```

See `resources/js/Components/Grid/README.md` for the full prop reference and
`resources/js/Projects/Starter/Pages/RoomGrid.vue` for a complete example.

---

## 5. Creating your own project

1. Copy `resources/js/Projects/Starter` to `resources/js/Projects/MyProject`.
2. Set `VITE_PROJECT_PATH="MyProject"` in `.env` and restart Vite.
3. Build your pages/layouts under `Pages/` and `Layouts/`, previewing with
   `/preview`.
4. Add a seeder (copy `StarterSeeder.php`) to register your pages/layouts and a
   demo playlist, so designs become selectable in the admin and on real screens.

Keep event‑ or customer‑specific projects in **their own repository** and drop
the folder into `resources/js/Projects/` — the framework stays generic.
