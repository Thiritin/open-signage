<script setup>
import {computed, onMounted, onUnmounted, ref, unref} from "vue";
import anime from 'animejs';
import chunkArray from "@/chunkArray.js";

const props = defineProps({
    title: {
        type: String,
        default: "Event Rooms"
    },
    schedule: {
        type: Array,
        default: []
    },
    appScreen: {
        type: Array,
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
        currentPageIndex.value = (currentPageIndex.value + 1) % schedulePages.value.length;
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

const filteredEvents = computed(() => {
    return _.cloneDeep(props.schedule).filter(event => {
        return currentTime.value > DateTime.fromISO(event.starts_at).minus({hours: 12}) && currentTime.value <= DateTime.fromISO(event.ends_at).plus({minutes: event.delay}).plus({minutes: 10}) && !event.title.toLowerCase().includes("seating")
    }).map((event, index) => {
        // console.log(event.title);

        if((event.room.name.includes(event.title) || event.title.includes(event.room.name) )){
            if(event.room.name !== event.room.venue_name){
                event.room.name = event.room.venue_name;
            }
        }

        if(event.title.length > 30){
            let parts = event.title.split(" – ");
            if(parts.length > 1){
                if(event.room.name.includes(parts[0]) || parts[0].includes(event.room.name)){
                    parts.shift();
                    event.title = parts.join(" – ").replace(/^[\W]+/g, "").replace(/[\W]+$/g, "");
                } else {
                    parts.pop();
                    event.title = parts.join(" – ").replace(/^[\W]+/g, "").replace(/[\W]+$/g, "");
                }
            }
        }

        event.title = event.title.truncate(30, true);


        // event.title = event.title ? event.title
        // .replace("Dealers' Den & Art Show", "")
        // .replace("Dealers' Den", "")
        // .replace("Art Show", "")
        // .replace("Fursuit Badge", "")
        // .replace("Registration", "")
        // .replace("Constore", "")
        // .replace("Fursuit Badge", "")
        // .replace("Fursuit Lounge", "")
        // .replace("Artists' Lounge", "")
        // .replace("Locker Service", "")
        // .replace("The Electric Lounge Sessions", "")
        // .replace(/^[ ‑–—‐−‐–—⸺|‖•‣]+/g, "")
        // .replace("room.name", "")
        // .replace(/^[\W]+/g, "")
        // : event.title;
        // event.title = event.title.split(" – ")[0];

        return event;//event.title.replace(room.name);
    });
})

const schedulePages = computed(() => {
    return chunkArray(filteredEvents.value, 5);
});

const currentSlide = computed(() => {
    return schedulePages.value[currentPageIndex.value];
});

import LogoSVG from '@/Projects/EF27/Assets/images/logoEF27e.svg';
import straightSVG from '@/Projects/EF27/Assets/images/straight.svg';
import rightSVG from '@/Projects/EF27/Assets/images/right.svg';
import wheelchairSVG from '@/Projects/EF27/Assets/images/wheelchair.svg';
import MaskSVG from "@/Projects/EF27/Assets/images/logoEF27Mask.svg";
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
                <div v-for="item in currentSlide" :key="item.id" class="flex flex-row magicTextColor magic-text items-start items-baseline" :class="[isThemeFont ? 'themeFont' : 'themeFontSecondary']">

                        <div class="relative flex flex-col text-justify items-start">
                            <div class="relative flex flex-row flex-shrink-0 flex-nowrap items-baseline text-justify text-[3.5vw] w-[11ch]">
                                <div class="relative flex flex-row flex-nowrap text-justify align-top anim">
                                    {{ DateTime.fromISO(item.starts_at).toFormat("HH:mm") }} –
                                </div>
                                <div class="relative flex flex-row flex-nowrap text-justify align-top anim">
                                    {{ DateTime.fromISO(item.ends_at).toFormat("HH:mm") }}
                                </div>
                            </div>
                            <div v-if="item.delay" class="flex flex-row items-baseline text-[3vw]">
                                <div v-if="item.delay < 15" class="flex text-left anim">slightly delayed</div>
                                <div v-else class="flex text-left magicTextColorRed anim">{{ item.delay }}min. delayed</div>
                            </div>
                        </div>

                        <div class="relative flex flex-col flex-auto text-justify items-start">
                            <div class="relative flex flex-row flex-nowrap text-justify align-top text-[4.5vw] anim">
                                {{ item.title }}
                            </div>
                            <div class="relative flex flex-row flex-nowrap text-justify align-top text-[3vw] anim">
                                {{ item.room.name !== item.room.venue_name ? item.room.name + " ( " + item.room.venue_name + " )" : item.room.name}}
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

.w-digit-120 {
    width: 12ch;
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
