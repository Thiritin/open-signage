<script setup>
import {defineAsyncComponent, ref, watch, computed, onMounted, resolveComponent} from "vue";
import ScreenIdentification from "@/Pages/ScreenIdentification.vue";
import Centered from "@/Layouts/Centered.vue";

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
    screen: {
        type: Object,
        required: true
    },
});

const pages = ref(props.initialPages);
const announcements = ref(props.initialAnnouncements);
const schedule = ref(props.initialSchedule);

Echo.channel('ScreenAll')
    .listen('.announcement.update', e => {
        announcements.value = e.announcements;
    })
    .listen('.schedule.update', (e) => {
        schedule.value = e.schedule;
    })

Echo.channel('Screen.' + props.screen.id)
    .listen('.page.update', (e) => {
        pages.value = e.pages;
        layouts = mapLayouts(mappedPages);
        activePageIndex.value = (activePageIndex.value + 1) % pages.value.length;
    });


const mappedPages = computed(() => {
    let item = pages.value;
    return item.map((page, index) => {
        return {
            ...page,
            index: index,
            resolvedComponent: defineAsyncComponent(() => import(`./Pages/${page.component}.vue`))
        }
    });
})

function mapLayouts(mappedPages) {
    let layouts = [];
    mappedPages.value.forEach((page) => {
        if (!layouts.includes(page.layout)) {
            layouts.push(page.layout);
        }
    });
    return layouts.map((layout) => {
        return {
            name: layout,
            resolvedLayout: defineAsyncComponent(() => import(`./Layouts/${layout}.vue`))
        }
    });
}

let layouts = mapLayouts(mappedPages);

const activePageIndex = ref(0);

const activePage = computed(() => {
    return pages.value[activePageIndex.value];
});

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
                :announcements="announcements"
                :page="mappedPages[activePageIndex]"
                :is="layouts.find(item => item.name === mappedPages[activePageIndex].layout).resolvedLayout"></component>
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
