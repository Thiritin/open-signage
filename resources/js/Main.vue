<script setup>
import {defineAsyncComponent, ref, watch, computed, onMounted, resolveComponent, onUnmounted} from "vue";

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
const isConnected = ref(true);
const connectionError = ref("");

const ping = () => {
    window
        .axios
        .post(route('screens.ping', {
            screen: props.initialScreen.id,
            shared_secret: new URLSearchParams(window.location.search).get('shared_secret')
        }))
        .then((response) => isConnected.value = true)
        .catch((error) => {
            isConnected.value = false;
            connectionError.value = "Ping failed"
        });
};

onMounted(() => {
    ping();
    const pingInterval = setInterval(ping, 60000);
    onUnmounted(() => {
        clearInterval(pingInterval);
    });
});

Echo.channel('ScreenAll')
    .listen('.announcement.update', e => {
        announcements.value = e.announcements;
    })
    .listen('.schedule.update', (e) => {
        schedule.value = e.schedule;
    })

Echo.channel('Screen.' + props.initialScreen.id)
    .listen('.screen.refresh', (e) => {
        window.location.reload();
    })
    .listen('.artwork.update', (e) => {
        artworks.value = e.artworks;
    })
    .listen('.page.update', (e) => {
        pages.value = e.pages;
        screen.value = e.screen;
        layouts = mapLayouts(mappedPages);
        activePageIndex.value = (activePageIndex.value + 1) % pages.value.length;
    });

window.Echo.connector.pusher.connection.bind('connecting', (payload) => {
    isConnected.value = false
    connectionError.value = "Socket reconnecting"
});

window.Echo.connector.pusher.connection.bind('connected', (payload) => {
    isConnected.value = true
});

window.Echo.connector.pusher.connection.bind('unavailable', (payload) => {
    isConnected.value = false
    connectionError.value = "Socket failed"
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
    <div v-if="isConnected === false"
         class="bg-red-600 z-50 absolute top-0 left p-1 px-4 font-bold text-white rounded-br">Reconnecting... ({{ connectionError }})
    </div>
    <Transition>
        <KeepAlive>
            <component
                :connected="isConnected"
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
