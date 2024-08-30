<script setup>
import {computed, defineAsyncComponent, onMounted, onUnmounted, ref, toRaw, unref} from "vue";
import anime from 'animejs';

const props = defineProps({
    title: {
        type: String,
        default: "Event Rooms"
    },
    schedule: {
        type: Array,
        default: []
    },
    rooms: {
        type: Array,
        default: []
    },
    screen: {
        type: Object,
        default: []
    },
    pageSwitchingTimer: {
        type: Number,
        default: 15000
    },
    isThemeFont: {
        type: Boolean,
        default: true
    }
})

const currentTime = ref(DateTime.now());
const currentPageIndex = ref(0);

onMounted(() => {
    const interval = setInterval(() => {
        currentTime.value = DateTime.now();
    }, 5000)
    const pageSwitcher = setInterval(() => {
        currentPageIndex.value = (currentPageIndex.value + 1) % roomPages.value.length;
    }, props.pageSwitchingTimer)
    onUnmounted(() => {
        clearInterval(interval);
        clearInterval(pageSwitcher);
    })
})

String.prototype.truncate = String.prototype.truncate ||
    function ( n, useWordBoundary ){
        if (this.length <= n) { return this; }
        const subString = this.slice(0, n-1); // the original check
        return (useWordBoundary
            ? subString.slice(0, subString.lastIndexOf(" "))
            : subString) + " …";
};

function getNextEventForRoom(room,timeObject) {
        return _.cloneDeep(props.schedule).filter(event => {
            return event.room_id === room.id;
        }).filter(event => {
            return timeObject <= DateTime.fromISO(event.ends_at).plus({minutes: event.delay}) && !event.title.toLowerCase().includes("seating")
        }).map((event) => {
            let eventCopy = toRaw(event);
            // console.log(event.title);
            eventCopy.title = eventCopy.title ? eventCopy.title
                .replace("Dealers' Den & Art Show", "")
                .replace("Dealers' Den", "")
                .replace("Art Show", "")
                .replace("Fursuit Badge", "")
                .replace("Registration", "")
                .replace("Constore", "")
                .replace("Fursuit Badge", "")
                .replace("Fursuit Lounge", "")
                .replace("Artists' Lounge", "")
                .replace("Locker Service", "")
                .replace("The Electric Lounge Sessions", "")
                // .replace(/^[ ‑–—‐−‐–—⸺|‖•‣]+/g, "")
                // .replace("room.name", "")
                .replace(/^[\W]+/g, "")
        : eventCopy.title;
            eventCopy.title = eventCopy.title.split(" – ")[0].truncate(30, true);
            return eventCopy;//event.title.replace(room.name);
        }).shift();
}

const populatedRooms = computed(() => {
    return _.cloneDeep(props.rooms).map(room => {
        room.nextEvent = getNextEventForRoom(room,currentTime.value);
        return room;
    });
});

function chunkArray(array, chunkSize) {
    const result = [];
    for (let i = 0; i < array.length; i += chunkSize) {
        result.push(array.slice(i, i + chunkSize));
    }
    return result;
}

const roomPages = computed(() => {
    return chunkArray(populatedRooms.value, 3);
});

const currentSlide = computed(() => {
    return roomPages.value[currentPageIndex.value];
});

import LogoSVG from '@/Projects/EF28/Assets/images/logoEF27e.svg';
import straightSVG from '@/Projects/EF28/Assets/images/straight.svg';
import rightSVG from '@/Projects/EF28/Assets/images/right.svg';
import wheelchairSVG from '@/Projects/EF28/Assets/images/wheelchair.svg';
import MaskSVG from "@/Projects/EF28/Assets/images/logoEF27Mask.svg";
import IconRouter from "@/Projects/System/Components/IconRouter.vue";
import {DateTime} from "luxon";
import HourTime from "@/Components/HourTime.vue";
import _ from "lodash";





function spanify(element){

    const textes = element.querySelectorAll('div.anim');
    for (let i = 0, n = textes.length; i < n; i++) {
        let letters = textes[i].innerText.split("");
        textes[i].innerHTML = '';
        for (let j = 0, m = letters.length; j < m; j++) {
            let span = document.createElement('span');
            span.innerText = letters[j];
            textes[i].appendChild(span);
        }
    }

}

function onBeforeEnter(el) {
    spanify(el);
}

function onEnter(node, done) {
    // call the done callback to indicate transition end
    // optional if used in combination with CSS

    anime({
        targets: node.querySelectorAll(".anim span"),
        loop: 1,
        direction: 'reverse',
        easing: 'cubicBezier(.5, .05, .1, .3)',
        autoplay: false,
        complete: function(anim) {
            done();
        },
        translateX: function (el) {
            let elementBounderys = el.getBoundingClientRect();
            let sectionBounderys = node.getBoundingClientRect();
            // sectionBounderys.x = (sectionBounderys.x + sectionBounderys.width)/2 - 0.681*sectionBounderys.width/2;
            // sectionBounderys.width = 0.681*sectionBounderys.width;
            return anime.random(-elementBounderys.x + sectionBounderys.x, sectionBounderys.x + sectionBounderys.width - elementBounderys.x - elementBounderys.width);
        },
        translateY: function (el) {
            let elementBounderys = el.getBoundingClientRect();
            let sectionBounderys = node.getBoundingClientRect();
            // sectionBounderys.y = (sectionBounderys.x + sectionBounderys.height)/2 - 0.681*sectionBounderys.height/2;
            // sectionBounderys.height = 0.681*sectionBounderys.height;
            return anime.random(-elementBounderys.y + sectionBounderys.y, sectionBounderys.y + sectionBounderys.height - elementBounderys.y - elementBounderys.height);
        },
        opacity: 0.25,
        scaleX: 0,
        duration: function () {
            return anime.random(250, 1725)
        },
        delay: function () {
            return anime.random(0, 500)
        }
    }).play();

}

 function onBeforeLeave(el) {
     spanify(el);
 }

// called when the leave transition starts.
// use this to start the leaving animation.
function onLeave(node, done) {
    // call the done callback to indicate transition end
    // optional if used in combination with CSS
    anime({
        targets: node.querySelectorAll(".anim span"),
        loop: 1,
        direction: 'normal',
        easing: 'cubicBezier(.5, .05, .1, .3)',
        autoplay: false,
        complete: function(anim) {
            done();
        },
        translateX: function (el) {
            let elementBounderys = el.getBoundingClientRect();
            let sectionBounderys = node.getBoundingClientRect();
            // sectionBounderys.x = (sectionBounderys.x + sectionBounderys.width)/2 - 0.681*sectionBounderys.width/2;
            // sectionBounderys.width = 0.681*sectionBounderys.width;
            return anime.random(-elementBounderys.x + sectionBounderys.x, sectionBounderys.x + sectionBounderys.width - elementBounderys.x - elementBounderys.width);
        },
        translateY: function (el) {
            let elementBounderys = el.getBoundingClientRect();
            let sectionBounderys = node.getBoundingClientRect();
            // sectionBounderys.y = (sectionBounderys.x + sectionBounderys.height)/2 - 0.681*sectionBounderys.height/2;
            // sectionBounderys.height = 0.681*sectionBounderys.height;
            return anime.random(-elementBounderys.y + sectionBounderys.y, sectionBounderys.y + sectionBounderys.height - elementBounderys.y - elementBounderys.height);
        },
        opacity: 0,
        scaleX: 0,
        duration: function () {
            return anime.random(250, 1725)
        },
        delay: function () {
            return anime.random(0, 500)
        }
    }).play();
}

</script>

<template>

    <div class="flex absolute z-30 h-[100vh] w-[100vw] justify-items-center overflow-hidden">

    <!--    <h1 class="relative z-30 text-center text-8xl top-1 mt-4 magicTextColor themeFont">{{ title }}</h1>-->
        <Transition
                    appear
                    @before-enter="onBeforeEnter"
                    @enter="onEnter"
                    @before-leave="onBeforeLeave"
                    @leave="onLeave"
                    :css="false">

            <div :key="currentPageIndex" class="flex flex-col absolute z-30 h-[100vh] w-[100vw] p-8 space-y-8 justify-items-center overflow-hidden">

    <!--                <TransitionGroup name="list">-->
                        <div v-for="item in currentSlide"
                             :key="item.id" class="flex flex-col magicTextColor magic-text" :class="[isThemeFont ? 'themeFont' : 'themeFontSecondary']">
                            <div class="flex text-[9vw] text-justify anim">
                                {{ item.name }}
                            </div>

                            <div class="flex flex-row text-[4vw] items-baseline">

                                <div v-if="item.nextEvent" class="flex flex-row items-baseline">
                                    <div v-if="item.nextEvent && DateTime.fromISO(item.nextEvent.starts_at) < DateTime.local()"
                                         class="flex text-left magicTextColorGreen anim">
                                        OPEN
                                    </div>
                                    <div v-else class="flex text-left magicTextColorRed anim">
                                        CLOSED
                                    </div>
                                </div>

                                <div
                                    v-if="item.nextEvent && DateTime.fromISO(item.nextEvent.starts_at) < DateTime.local() && item.nextEvent.title"
                                    class="flex text-left anim">
                                    {{ item.nextEvent.title }}
                                </div>
                                <div v-else-if="item.nextEvent && item.nextEvent.title" class="flex text-left anim">
                                    Next: {{ item.nextEvent.title }}
                                </div>

                            </div>
                        </div>
    <!--                </TransitionGroup>-->

            </div>
        </Transition>
    </div>

</template>

<style scoped>

.v-enter-active,
.v-leave-active {
    transition: opacity 5s ease;
}

.v-enter-from,
.v-leave-to {
    opacity: 0;
}

.list-enter-active,
.list-leave-active {
    transition: all 5s ease;
}
.list-enter-from,
.list-leave-to {
    opacity: 0;
}

</style>


<style>

body {
    overflow: hidden;
    @apply bg-primary
}

.magic-text {
    position: relative;
    user-select: none;
    /*
    font-family: 'primaryThemeFont', sans-serif;
    white-space: pre;
    */
}

.magic-text span {
    position: relative;
    white-space: pre;
    display: inline-block;
    cursor: pointer;
    opacity: 1;
}

.w-digit-15 {
    width: 1.5ch;
}

.w-digit-15 span {
    width: 1ch;
}

.w-digit-2 {
    width: 2ch;
}

.w-digit-2 span {
    width: 1ch;
}

.w-digit-45 {
    width: 4.5ch;
}

.w-digit-45 span {
    width: 1ch;
}

.w-digit-5 {
    width: 5ch;
}

.w-digit-5 span {
    width: 1ch;
}

</style>
