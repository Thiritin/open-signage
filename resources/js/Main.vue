<script setup>
import {defineAsyncComponent, ref, watch, computed, onMounted, resolveComponent} from "vue";

const props = defineProps({
    initialPages: {
        type: Array,
        required: true
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
        if(e.pages.length !== pages.value.length) {
            activePageIndex.value = 0;
        }
        pages.value = e.pages;
        layouts = mapLayouts(mappedPages);
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
    setTimeout(() => {
        activePageIndex.value = (value + 1) % pages.value.length;
    }, (activePage.value.duration ?? pages.value[0].duration) * 1000);
}, {immediate: true});

</script>

<template>
    <KeepAlive>
        <component
            v-show="mappedPages[activePageIndex].index === activePageIndex"
            :screen="screen"
            :schedule="schedule"
            :announcements="announcements"
            :page="mappedPages[activePageIndex]"
            :is="layouts.find(item => item.name === mappedPages[activePageIndex].layout).resolvedLayout"></component>
    </KeepAlive>
</template>

<style>

body {
    overflow: hidden;
}

.v-enter-active,
.v-leave-active {
    transition: opacity 0.5s ease;
}

.v-enter-from,
.v-leave-to {
    opacity: 0;
}
</style>
