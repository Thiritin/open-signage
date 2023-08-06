<script setup>
import {defineAsyncComponent, ref, watch, computed, onMounted, resolveComponent} from "vue";

const props = defineProps({
    initialPages: {
        type: Array,
        default: () => []
    },
    initialSchedule: {
        type: Array,
        required: true
    },
    initialAnnouncements: {
        type: Array,
        required: true,
        default: () => []
    },
    initialScreen: {
        type: Object,
        required: true
    },
    initialArtworks: {
        type: Array,
        required: true
    },
});

const pages = ref(props.initialPages);
const announcements = ref(props.initialAnnouncements);
const schedule = ref(props.initialSchedule);
const screen = ref(props.initialScreen);
const artworks = ref(props.initialArtworks);

Echo.channel('ScreenAll')
    .listen('.announcement.update', e => {
        announcements.value = e.announcements;
    })
    .listen('.schedule.update', (e) => {
        schedule.value = e.schedule;
    })

Echo.channel('Screen.' + props.initialScreen.id)
    .listen('.page.update', (e) => {
        pages.value = e.pages;
        screen.value = e.screen;
        layouts = mapLayouts(mappedPages);
        activePageIndex.value = (activePageIndex.value + 1) % pages.value.length;
    });


const mappedPages = computed(() => pages.value.map((page, index) => {
        return {
            ...page,
            index: index,
            resolvedComponent: defineAsyncComponent(() => import(`./Projects/${page.path}/Pages/${page.component}.vue`))
        }
    }))

function mapLayouts(mappedPages) {
    let layouts = [];
    mappedPages.value.forEach((page) => {
        if (!layouts.find(e => e.component === page.layout.component && e.path === page.layout.path)) {
            layouts.push({
                component: page.layout.component,
                path: page.layout.path
            });
        }
    });
    return layouts.map((layout) => {
        return {
            component: layout.component,
            path: layout.path,
            resolvedLayout: defineAsyncComponent(() => import(`./Projects/${layout.path}/Layouts/${layout.component}.vue`))
        }
    });
}

let layouts = mapLayouts(mappedPages);

const activePageIndex = ref(0);

const activePage = computed(() => pages.value[activePageIndex.value]);

watch(activePageIndex, (value) => {
    if (pages.value.length === 0) return;
    setTimeout(() => {
        activePageIndex.value = (value + 1) % pages.value.length;
    }, (activePage.value.duration ?? pages.value[0].duration) * 1000);
}, {immediate: true});

</script>

<template>
    <Transition>
        <KeepAlive>
            <component
                v-show="mappedPages[activePageIndex].index === activePageIndex"
                :screen="screen"
                :schedule="schedule"
                :artworks="artworks"
                :announcements="announcements"
                :page="mappedPages[activePageIndex]"
                :is="layouts.find(item => item.component === mappedPages[activePageIndex].layout.component && item.path === mappedPages[activePageIndex].layout.path).resolvedLayout"></component>
        </KeepAlive>
    </Transition>
</template>

<style>

body {
    overflow: hidden;
    @apply bg-primary-800
}

.v-enter-active {
    transition: opacity 1s ease-in;
}

.v-leave-active {
    transition: opacity 0.5s ease-out;
}

.v-enter-from,
.v-leave-to {
    opacity: 0;
}
</style>
