<script setup>
import { computed, defineAsyncComponent, ref } from "vue";
import NoneFallback from "@/Projects/System/Layouts/None.vue";
import {
    mockScreen,
    mockRooms,
    mockSchedule,
    mockAnnouncements,
    mockArtworks,
} from "@/mock";

/**
 * Standalone design preview page.
 *
 * Renders ANY signage design Vue component with realistic mock data, with no
 * database, no screens/playlists and no websocket (Reverb/Echo) server. It
 * deliberately does NOT import Main.vue's logic (ping/Echo/etc).
 *
 * Props mirror what PreviewController passes.
 */
const props = defineProps({
    project: { type: String, required: true },
    pages: { type: Array, default: () => [] },
    layouts: { type: Array, default: () => [] },
    projects: { type: Array, default: () => [] },
    selectedPage: { type: String, default: null },
    selectedLayout: { type: String, default: "None" },
});

// ---- Mock data passed to the design (top-level props, exactly like prod) ----
const appScreen = mockScreen();
const rooms = mockRooms();
const schedule = mockSchedule();
const announcements = mockAnnouncements();
const artworks = mockArtworks();

const mockProps = {
    connected: true,
    appScreen,
    rooms,
    schedule,
    artworks,
    announcements,
};

// ---- Fake `page` object, identical in shape to production's ----
const importError = ref(null);

const resolvedComponent = defineAsyncComponent(() =>
    import(`./Projects/${props.project}/Pages/${props.selectedPage}.vue`).catch(
        (err) => {
            importError.value = err;
            return { template: "<div></div>" };
        }
    )
);

const page = computed(() => ({
    resolvedComponent,
    layout: { component: props.selectedLayout, path: props.project },
    path: props.project,
    component: props.selectedPage,
    props: {},
    duration: null,
    title: null,
    starts_at: null,
    ends_at: null,
    index: 0,
}));

// ---- Resolve the layout (fall back to System/None) ----
const resolvedLayout = props.selectedLayout
    ? defineAsyncComponent({
          loader: () =>
              import(
                  `./Projects/${props.project}/Layouts/${props.selectedLayout}.vue`
              ),
          errorComponent: NoneFallback,
      })
    : NoneFallback;

// ---- Dev toolbar state ----
const params = new URLSearchParams(window.location.search);
const bare = params.get("bare") === "1";
const toolbarVisible = ref(!bare);

const resolutions = [
    { label: "1920 x 1080 (FHD)", w: 1920, h: 1080 },
    { label: "3840 x 2160 (4K)", w: 3840, h: 2160 },
    { label: "1080 x 1920 (Portrait)", w: 1080, h: 1920 },
    { label: "1280 x 720 (HD)", w: 1280, h: 720 },
    { label: "Fit window", w: 0, h: 0 },
];
const selectedResolution = ref(resolutions[0]);
const showGrid = ref(false);

// Scale the fixed-resolution canvas down to fit the viewport.
const scale = ref(1);
const stageStyle = computed(() => {
    const r = selectedResolution.value;
    if (!r.w || !r.h) {
        return { width: "100vw", height: "100vh" };
    }
    return {
        width: r.w + "px",
        height: r.h + "px",
        transform: `scale(${scale.value})`,
        transformOrigin: "top left",
    };
});

function recomputeScale() {
    const r = selectedResolution.value;
    if (!r.w || !r.h) {
        scale.value = 1;
        return;
    }
    const sw = window.innerWidth / r.w;
    const sh = window.innerHeight / r.h;
    scale.value = Math.min(sw, sh, 1);
}

if (typeof window !== "undefined") {
    window.addEventListener("resize", recomputeScale);
    recomputeScale();
}

function onResolutionChange() {
    recomputeScale();
}

// ---- Navigation (plain URLs to avoid Ziggy timing issues) ----
const pageChoice = ref(props.selectedPage);
const layoutChoice = ref(props.selectedLayout);

function navigate() {
    const p = encodeURIComponent(pageChoice.value);
    const l = encodeURIComponent(layoutChoice.value);
    window.location.href = `/preview/${p}?layout=${l}`;
}
</script>

<template>
    <div class="preview-root">
        <!-- DEV TOOLBAR -->
        <div
            v-if="toolbarVisible"
            class="preview-toolbar"
        >
            <strong class="preview-title">Design Preview</strong>

            <label class="preview-field">
                <span>Page</span>
                <select v-model="pageChoice" @change="navigate">
                    <option v-for="p in pages" :key="p" :value="p">{{ p }}</option>
                </select>
            </label>

            <label class="preview-field">
                <span>Layout</span>
                <select v-model="layoutChoice" @change="navigate">
                    <option v-for="l in layouts" :key="l" :value="l">{{ l }}</option>
                </select>
            </label>

            <label class="preview-field">
                <span>Resolution</span>
                <select v-model="selectedResolution" @change="onResolutionChange">
                    <option
                        v-for="r in resolutions"
                        :key="r.label"
                        :value="r"
                    >
                        {{ r.label }}
                    </option>
                </select>
            </label>

            <label
                class="preview-field"
                :title="'Project switching requires changing VITE_PROJECT_PATH in .env and rebuilding. This is informational only.'"
            >
                <span>Project</span>
                <select :value="project" disabled>
                    <option v-for="pr in projects" :key="pr" :value="pr">
                        {{ pr }}
                    </option>
                </select>
            </label>

            <label class="preview-check">
                <input type="checkbox" v-model="showGrid" />
                <span>Grid</span>
            </label>

            <span class="preview-meta">
                scale {{ Math.round(scale * 100) }}%
            </span>

            <button class="preview-hide" @click="toolbarVisible = false" title="Hide toolbar (add ?bare=1 to start hidden)">
                Hide ✕
            </button>
        </div>

        <!-- STAGE -->
        <div class="preview-stage-wrap">
            <div class="preview-stage" :style="stageStyle">
                <div v-if="importError" class="preview-error">
                    <h2>Failed to load design</h2>
                    <p>
                        Could not import
                        <code>Projects/{{ project }}/Pages/{{ selectedPage }}.vue</code>.
                    </p>
                    <pre>{{ String(importError) }}</pre>
                </div>
                <Suspense v-else>
                    <component
                        :is="resolvedLayout"
                        :page="page"
                        v-bind="mockProps"
                    />
                    <template #fallback>
                        <div class="preview-loading">Loading design…</div>
                    </template>
                </Suspense>

                <!-- Grid overlay -->
                <div v-if="showGrid" class="preview-grid"></div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.preview-root {
    width: 100vw;
    height: 100vh;
    overflow: hidden;
    background: #111;
}

.preview-toolbar {
    position: fixed;
    top: 8px;
    left: 8px;
    z-index: 99999;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 10px;
    padding: 6px 10px;
    background: rgba(20, 20, 20, 0.82);
    color: #fff;
    font-family: ui-sans-serif, system-ui, sans-serif;
    font-size: 12px;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.4);
    backdrop-filter: blur(4px);
    max-width: calc(100vw - 16px);
}

.preview-title {
    font-weight: 700;
    letter-spacing: 0.02em;
}

.preview-field {
    display: flex;
    align-items: center;
    gap: 4px;
}

.preview-field > span {
    opacity: 0.7;
}

.preview-toolbar select,
.preview-toolbar button {
    background: #2a2a2a;
    color: #fff;
    border: 1px solid #444;
    border-radius: 4px;
    padding: 2px 4px;
    font-size: 12px;
}

.preview-toolbar select:disabled {
    opacity: 0.5;
}

.preview-check {
    display: flex;
    align-items: center;
    gap: 4px;
    cursor: pointer;
}

.preview-meta {
    opacity: 0.6;
}

.preview-hide {
    cursor: pointer;
}

.preview-stage-wrap {
    width: 100vw;
    height: 100vh;
    overflow: hidden;
}

.preview-stage {
    position: relative;
    overflow: hidden;
    background: #000;
}

.preview-grid {
    position: absolute;
    inset: 0;
    pointer-events: none;
    z-index: 9999;
    background-image: linear-gradient(
            to right,
            rgba(255, 255, 255, 0.15) 1px,
            transparent 1px
        ),
        linear-gradient(
            to bottom,
            rgba(255, 255, 255, 0.15) 1px,
            transparent 1px
        );
    background-size: 96px 96px;
}

.preview-error {
    color: #fff;
    padding: 40px;
    font-family: ui-sans-serif, system-ui, sans-serif;
}

.preview-error pre {
    white-space: pre-wrap;
    color: #f87171;
    font-size: 12px;
}

.preview-loading {
    color: #fff;
    padding: 40px;
    font-family: ui-sans-serif, system-ui, sans-serif;
}
</style>
