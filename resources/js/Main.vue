<script setup>
import {defineAsyncComponent, ref, watch, computed, onMounted, resolveComponent, onUnmounted, unref} from "vue";
import None from "@/Projects/System/Layouts/None.vue";
import Error from "@/Projects/System/Pages/Error.vue";
import moment from "moment";
import _ from "lodash";

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

const currentTime = ref(moment());
const pages = ref(props.initialPages);
const announcements = ref(props.initialAnnouncements);
const schedule = ref(props.initialSchedule);
const appScreen = ref(props.initialScreen);
const artworks = ref(props.initialArtworks);
const isConnected = ref(true);
const connectionError = ref("");
const version = ref(props.initialScreen.version)
const pageSwitchTimer = ref(null)

const ping = () => {
    window
        .axios
        .post(route('screens.ping', {
            screen: props.initialScreen.id,
            shared_secret: new URLSearchParams(window.location.search).get('shared_secret'),
            version: version.value
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
    const checkInterval = setInterval(() => {
        currentTime.value = moment()
    }, 1000);
    onUnmounted(() => {
        clearInterval(pingInterval);
        clearInterval(checkInterval);
    });
});

Echo.channel('ScreenAll')
    .listen('.announcement.update', e => {
        announcements.value = e.announcements;
        version.value++;
    })
    .listen('.schedule.update', (e) => {
        schedule.value = e.schedule;
        version.value++;
    })

Echo.channel('Screen.' + props.initialScreen.id)
    .listen('.screen.refresh', (e) => {
        window.location.reload();
    })
    .listen('.artwork.update', (e) => {
        artworks.value = e.artworks;
    })
    .listen('.page.update', (e) => {
        activePageIndex.value = 0;
        version.value++;
        pages.value = e.pages;
        appScreen.value = e.screen;
        layouts = mapLayouts(mappedPages);
        activePageIndex.value = activePageIndex.value + 1 % pages.value.length;
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

const mappedPages = computed(() => {
    return pages.value.map((page, index) => {
        return {
            ...page,
            index: index,
            resolvedComponent: defineAsyncComponent(() => import(`./Projects/${page.path}/Pages/${page.component}.vue`))
        }
    })
})

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

const activeLayout = computed(() => {
    if (activePage.value === undefined) return None;

    let layout = layouts.find(e => e.component === activePage.value.layout.component && e.path === activePage.value.layout.path)
    return layout.resolvedLayout ?? None;
});

const activePageComponent = computed(() => {
    if (activePage.value === undefined) return Error;

    let page = mappedPages.value.find(e => e.index === activePageIndex.value)
    return page?.resolvedComponent ?? Error;
});

const rooms = computed(() => {
    return appScreen.value.rooms.filter(room => {
        return (
            // If room.pivot.starts_at exists, check if the current time is greater than or equal to it
            (!room.pivot.starts_at || currentTime.value.isSameOrAfter(moment(room.pivot.starts_at))) &&

            // If room.pivot.ends_at exists, check if the current time is less than or equal to it
            (!room.pivot.ends_at || currentTime.value.isSameOrBefore(moment(room.pivot.ends_at)))
        );
    });
});

function nextPage() {
    activePageIndex.value = (activePageIndex.value + 1) % pages.value.length;
}

watch(activePageIndex, (value, oldValue) => {
    if (value === oldValue) return;
    if (pages.value.length <= 1) return;

    // If current page does not match timing requirements skip to next page
    if ((activePage.value.starts_at && new Date(activePage.value.starts_at).getTime() > new Date().getTime()) || (activePage.value.ends_at && new Date(activePage.value.ends_at).getTime() < new Date().getTime())) {
        console.log("Current page does not match timing requirements, skipping to next page")
        activePageIndex.value = (value + 1) % pages.value.length;
    }

    console.log("Clearing Timeout")
    clearTimeout(pageSwitchTimer.value)

    let validPageFound = false;
    let skipPages = 0;
    let nextPageIndex;

    while (!validPageFound) {
        console.log("Checking page " + (value + skipPages) % pages.value.length);
        skipPages++;

        nextPageIndex = (value + skipPages) % (pages.value.length);
        console.log("Next page index: " + nextPageIndex)
        const nextPage = pages.value[nextPageIndex] ?? null;

        const currentTime = new Date().getTime();
        const startsAtTime = nextPage.starts_at ? new Date(nextPage.starts_at).getTime() : null;
        const endsAtTime = nextPage.ends_at ? new Date(nextPage.ends_at).getTime() : null;

        if (startsAtTime && endsAtTime) {
            if (currentTime >= startsAtTime && currentTime <= endsAtTime) {
                console.log("Page between both start and end time found")
                validPageFound = true;
            }
        } else if (startsAtTime) {
            // Only starts_at is set
            if (currentTime >= startsAtTime) {
                console.log("Page after start time found")
                validPageFound = true;
            }
        } else if (endsAtTime) {
            // Only ends_at is set
            if (currentTime <= endsAtTime) {
                console.log("Page before end time found")
                validPageFound = true;
            }
        } else {
            console.log("Page without time found")
            validPageFound = true;
        }
    }

    console.log("Setting Timeout")
    pageSwitchTimer.value = setTimeout(() => {
        nextPageIndex = nextPageIndex % (pages.value.length);
        console.log("Switching to page " + nextPageIndex);
        activePageIndex.value = nextPageIndex;
    }, (activePage.value?.duration ?? pages.value[0].duration) * 1000);
}, {immediate: true});

</script>

<template>
    <div v-if="isConnected === false"
         class="bg-black z-50 absolute top-0 left p-1 px-4 font-bold text-white rounded-br">Reconnecting...
        ({{ connectionError }})
    </div>
    <Transition>
        <component
            :connected="isConnected"
            v-show="activePageComponent"
            :appScreen="appScreen"
            :rooms="rooms"
            :schedule="schedule"
            :artworks="artworks"
            :announcements="announcements"
            :page="mappedPages[activePageIndex] ?? {resolvedComponent: Error}"
            :is="activeLayout"></component>
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
