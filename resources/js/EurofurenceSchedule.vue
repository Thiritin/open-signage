<script setup>
import Time from "@/Projects/EF27/Components/CurrentTime.vue";
import TransmutationCircle from "@/Projects/EF27/Components/TransmutationCircle2.vue";
import MagicMist from "@/Projects/EF27/Components/MagicMist.vue";

import {computed, onMounted, onUnmounted, ref} from "vue";
import anime from 'animejs';
import moment from 'moment';
import XRegExp from 'xregexp';
import _ from 'lodash';

const props = defineProps({
    events: {
        type: Array,
        required: true
    },

    connews: {
        type: Array,
        required: true
    }
})

const table = ref();
const row0 = ref();
const time00 = ref();
const time01 = ref();
const sating0 = ref();
const title0 = ref();
const room0 = ref();
const note0 = ref();

const row1 = ref();
const time10 = ref();
const time11 = ref();
const sating1 = ref();
const title1 = ref();
const room1 = ref();
const note1 = ref();

const row2 = ref();
const time20 = ref();
const time21 = ref();
const sating2 = ref();
const title2 = ref();
const room2 = ref();
const note2 = ref();

const row3 = ref();
const time30 = ref();
const time31 = ref();
const sating3 = ref();
const title3 = ref();
const room3 = ref();
const note3 = ref();

let rows;

let title = ref("Curriculum");
//&Services -> split into two tracks ...
// Services are the opening times of: SEC CO REG FSL LS AS DD
// Curriculum are Events/Panels located Outside or in the Rooms: Blu XXX, CCH Hall [0-9]
const lecturesNote = ref();

import LogoSVG from '@/Projects/EF27/Assets/images/logoEF27e.svg';
import MaskSVG from '@/Projects/EF27/Assets/images/logoEF27Mask.svg';
import CurrentTime from "@/Components/CurrentTime.vue";
import TransmutationSVG from "@/Projects/EF27/Assets/images/transmutation5.svg";

const events = ref(props.events).value;   //events should be refreshed only once a while (e.g. 20 min on reload)
const connews = ref(props.connews).value;  //connews should be reloaded frequently (e.g. 1 per minute)

const seatingRegex = XRegExp("^(?<title>.*)( [‑–—‐−‐–—⸺|‖•‣] )(?<seating>(seat|door|entr).*)$", 'giys');//  [–-—]
const whitespaceRegex = XRegExp(" *(['&]+) *", 'gi'); //,\.-;:_!\"§$%&/\(\)\\=?´`'#+~\*^°|<>–—\[\]
const SplitRegex = XRegExp("[‑–—‐−‐–—⸺|‖•‣]", 'gi');

const now = function (e) {
    //time traveler function, used to simulate a con day-time in frontend -> for productive just remove/comment out from .year(....;
    return moment(e).year(2023).month(8).date(3).hour(12).minute(1);
};

function generateRandom(min, max) {
    return Math.random() * (max - min) + min;
}

const panelRooms = ["Blu Chicago", "Blu Dallas", "Blu Los Angeles", "Games – CCH Hall 6", "CCH Hall 7", "CCH Hall 8", "CCH Hall 9"];
const stageRooms = ["Theater Stage – CCH Hall 3", "Dance Stage – CCH Hall 4", "Open Stage – CCH Lobby"];

let dataCache = preparation(); //the data

function preparation() {

    let data = _.chain(events)
        .reduce(function (result, o) {
            //convert all remaining Text formatted Date-Time values to Moment objects
            o.day = moment(o.day, "YYYY-MM-DD");
            o.start_time = moment(o.day.format("YYYY-MM-DD") + 'T' + o.start_time + "+02:00");
            o.end_time = moment(o.day.format("YYYY-MM-DD") + 'T' + o.end_time + "+02:00");

            if (moment(o.end_time, "HH:mm:ss").isBefore(moment(o.start_time, "HH:mm:ss"))) {
                o.end_time.add(1, 'd');
            }
            o.delay = 0;

            if (now().isBetween(moment(o.start_time).subtract(12, 'h'), moment(o.end_time).add(15, 'm'))) {
                result.push(o);
            }
            return result;
        }, []).value();

    //kick out seating events and add the seating time to the original event
    for (let i = 0, a = data, n = a.length; i < n; i++) {
        let element = a[i];
        let parts = XRegExp.exec(element.title.trim(), seatingRegex);
        if (parts) {
            //now element is the seating event for an event -> next find the original event and add the seating time and remove the seating event
            for (let j = 0, a = data, m = a.length; j < m; j++) {
                let element2 = a[j];

                if (parts.groups.title.trim() === element2.title.trim()
                    && element.track === element2.track
                    && element.conference_room === element2.conference_room
                    && element.end_time.diff(element2.start_time, 'minutes') === 0
                ) {
                    //now element2 is the corresponding event to the seating event
                    //-> adding the additional seating time
                    element2.start_time_seating = element.start_time;

                    data.splice(i, 1);
                    i--, n = a.length; //TODO: next time refactor change to more elegant inverse loop so no index rebasing and length correction needed

                    break;
                }
            }
        }

        //additional compaction by kick out events/opening times which connect directly
        for (let j = 0, a = data, m = a.length; j < m; j++) {
            let element2 = a[j];

            if (element.title.trim() === element2.title.trim()
                && element.track === element2.track
                && element.conference_room === element2.conference_room
                && element.end_time.diff(element2.start_time, 'minutes') === 0
            ) {
                //now element2 is the connected event to the previous event
                //-> change the end_time
                element.end_time = element2.end_time;
                data.splice(j, 1);
                i--, n = a.length; //TODO: next time refactor change to more elegant inverse loop so no index rebasing and length correction needed
                break;
            }

        }

    }

    return _.chain(data)
        .map(function (o) {

            //mixin the announcements from conNews
            for (let j = 0, b = connews, n = b.length; j < n; j++) {
                let news = b[j];
                if (news.data && news.data.delay && news.data.event_id === o.event_id) {
                    //accumulated delay; multiple sequential announced delays are possibleaccumulated delay; multiple sequential announced delays are possible
                    o.delay = parseInt(o.delay) + parseInt(news.data.delay);
                    //precalculate delayed times to apply besides strikethrough original time
                    o.delayed_seating_time.add(o.delay, 'm');

                    let puffer = o.start_time.diff(o.start_time_seating, 'minutes') / 2;
                    if (o.delay > puffer) {
                        //only if the doors opening for seating delayed more than about 10-15min (half seating time),
                        //the corresponding event will truly affected by this delay.
                        o.delayed_start_time.add((o.delay - puffer), 'm');
                        o.delayed_end_time.add((o.delay - puffer), 'm');
                    }
                }
            }

            return o;
        })
        .filter(function (o, i, c) {
            if (typeof eventFilter === 'function') {
                //additional "external" function to filter generally per screen (.g. by place/room to show only the theater stage events on this screen)
                return eventFilter(o, i, c);
            }
            return true;
        })
        .map(function (o) {

                let conferenceRoomIdentifiers = XRegExp.split(o.conference_room, SplitRegex);
                o.conference_room = {original_identifier: o.conference_room.trim()};
                if (conferenceRoomIdentifiers.length === 2) {
                    o.conference_room.internal_identifier = XRegExp.replace(conferenceRoomIdentifiers[0], whitespaceRegex, '$1').trim();
                    o.conference_room.venue_identifier = XRegExp.replace(conferenceRoomIdentifiers[1], whitespaceRegex, '$1').trim();
                } else if (conferenceRoomIdentifiers.length === 1) {
                    o.conference_room.venue_identifier = XRegExp.replace(conferenceRoomIdentifiers[0], whitespaceRegex, '$1').trim();
                }

                let s = XRegExp.split(o.title, SplitRegex);
                s[0] = s[0].trim();
                s[0] = XRegExp.replace(s[0], whitespaceRegex, '$1');
                o.title = s[0];
                if (s.length > 1)
                    o.subtitle = s[1];

                o.title = XRegExp.replace(o.title.trim(), whitespaceRegex, '$1');
                if (o.subtitle) {
                    o.subtitle = XRegExp.replace(o.subtitle.trim(), whitespaceRegex, '$1');
                }

                return o;
            }
        )
        .orderBy(['start_time', 'end_time'], ['asc'])
        .reduce(function (result, o) {
            //reduce as groupBy to categorizes events into specific buckets
            if (panelRooms.indexOf(o.conference_room.original_identifier) != -1) {
                (result.panels || (result.panels = [])).push(o);
            } else if (o.track.trim().toLowerCase().includes("social")) {
                (result.panels || (result.panels = [])).push(o);
            } else if (o.track.trim().toLowerCase().includes("dance")) {
                (result.dances || (result.dances = [])).push(o);
            } else if (stageRooms.indexOf(o.conference_room.original_identifier) != -1) {
                (result.events || (result.events = [])).push(o);
            } else if ((o.title.match(/.*party.*staff.*/i))) {
                (result.events || (result.events = [])).push(o);
            } else if (o.conference_room.original_identifier.toLowerCase().includes("outdoor")) {
                (result.panels || (result.panels = [])).push(o);
            } else if (
                o.conference_room.venue_identifier.toLowerCase().includes("hall h")
                || o.conference_room.venue_identifier.toLowerCase().includes("foyer")
                || o.conference_room.venue_identifier.toLowerCase().includes("check-in")
            ) {
                (result.services || (result.services = [])).push(o);
            } else {
                (result.misc || (result.misc = [])).push(o);
            }

            return result;
        }, {}).value();
}


onMounted(() => {

    rows = [
        {
            row: row0.value,
            startTime: time00.value,
            endTime: time01.value,
            seatigNote: sating0.value,
            title: title0.value,
            room: room0.value,
            note: note0.value
        },
        {
            row: row1.value,
            startTime: time10.value,
            endTime: time11.value,
            seatigNote: sating1.value,
            title: title1.value,
            room: room1.value,
            note: note1.value
        },
        {
            row: row2.value,
            startTime: time20.value,
            endTime: time21.value,
            seatigNote: sating2.value,
            title: title2.value,
            room: room2.value,
            note: note2.value
        },
        {
            row: row3.value,
            startTime: time30.value,
            endTime: time31.value,
            seatigNote: sating3.value,
            title: title3.value,
            room: room3.value,
            note: note3.value
        },
    ];

    //Animate hourglass
    // anime({
    //   targets: hourGlass.value,
    //   duration: 1000,
    //   delay: 0,
    //   endDelay: 19000,
    //   rotate: 180,
    //   loop: true,
    //   easing: 'easeInOutExpo',
    //   autoplay: true
    // });

    loadContent(rows, dataCache.services, 0);

    let timeline = anime.timeline({
        loop: true,
        direction: 'alternate',
        easing: 'easeInElastic',
        autoplay: true
    }).add({
        targets: ".magic-text span",
        translateX: 0,
        translateY: 0,
        opacity: 1,
        scaleX: 1,
        duration: function () {
            return anime.random(250, 1000)
        },
        delay: function () {
            return anime.random(0, 250)
        },
        endDelay: 19000,
    }).add({
        targets: ".magic-text span",
        translateX: function (el) {
            let elementBounderys = el.getBoundingClientRect();
            let sectionBounderys = table.value.getBoundingClientRect();
            return anime.random(-elementBounderys.x + sectionBounderys.x, sectionBounderys.x + sectionBounderys.width - elementBounderys.x - elementBounderys.width - 25);
        },
        translateY: function (el) {
            let bounderys = el.getBoundingClientRect();
            let sectionBounderys = table.value.getBoundingClientRect();
            return anime.random(-bounderys.y + sectionBounderys.y, sectionBounderys.y + sectionBounderys.height - bounderys.y - bounderys.height - 25);
        },
        opacity: 0,
        scaleX: 0,
        duration: function () {
            return anime.random(250, 2000)
        },
        update: function (anim) {
            if (anim.progress === 100) {
                // anim.pause();
                console.log(anim.progress);
                loadContent(rows, dataCache.events, 0);
            }
        },
    });

})

onUnmounted(() => {
});


function loadContent(rows = [], data = [], offset = 0) {

    for (let i = 0, n = rows.length; i < n; i++) {
        rows[i].row.classList.add("invisible");
    }

    for (let i = offset, a = data, n = Math.min(a.length - offset, rows.length); i < n; i++) {
        let event = a[i];
        let row = rows[i % rows.length];

        fillHelper(row.startTime, (event.start_time.format("HH:mm")).split(""));
        fillHelper(row.endTime, (event.end_time.format("HH:mm")).split(""));
        fillHelper(row.title, (event.title).split(""));
        fillHelper(row.room, ("@ " + event.conference_room.venue_identifier).split(""));

        if (event.start_time_seating) {
            fillHelper(row.seatigNote, ("Doors open at ~" + event.start_time_seating.format("HH:mm")).split(""));
            row.seatigNote.classList.remove("invisible");
        } else {
            row.seatigNote.classList.add("invisible");
        }

        row.row.classList.remove("invisible");

    }

    function fillHelper(element, letters){
        element.innerHTML = '';
        for (let j = 0, m = letters.length; j < m; j++) {
            let span = document.createElement('span');
            span.innerText = letters[j];
            element.appendChild(span);
        }
    }

}

</script>


<template>

    <div class="absolute left-0 top-0 z-0 w-screen h-screen flex items-center justify-center text-center">

        <TransmutationCircle/>

        <MaskSVG style="z-index: 3; width: 700px; height: 700px;" class="absolute bottom-5 flex"/>
        <MagicMist/>
        <LogoSVG style="z-index: 5; width: 700px; height: 700px;" class="absolute flex bottom-5"/>

    </div>

    <Time/>

    <h1 class="text-center text-8xl top-1 mt-4 magicTextColor themeFont">{{ title }}</h1>

    <div ref="table" class="flex flex-col relative z-10 text-7xl min-w-full magicTextColor top-5 magic-text themeFontSecondary">
        <div ref="row0" class="flex flex-row flex-nowrap relative invisible py-8 items-start">
            <div class="relative flex flex-col flex-1 text-justify items-start">
                <div class="flex flex-row flex-nowrap relative items-baseline">
                    <div ref="time00"
                         class="pl-4 relative flex flex-row flex-nowrap flex-shrink-0 text-justify align-top w-digit-45">
                        00:00
                    </div>
                    <div class="px-4 flex flex-row flex-nowrap flex-shrink-0 text-center align-top w-digit-15">-</div>
                    <div ref="time01"
                         class="pr-4 relative flex flex-row flex-nowrap flex-shrink-0 text-justify align-top w-digit-45">
                        00:00
                    </div>
                </div>
                <div ref="sating0"
                     class="relative flex flex-row flex-nowrap flex-shrink-0 text-justify align-top text-5xl invisible">Doors open at ~00:00</div>
            </div>
            <div class="px-4 relative flex flex-col flex-auto text-justify items-start">
                <p ref="title0" class="relative flex flex-row flex-nowrap text-justify align-top">Fursuit Lounge</p>
                <p ref="room0" class="relative flex flex-row flex-nowrap text-justify align-top text-6xl">@ CCH</p>
            </div>
            <div ref="note0" class="pr-4 relative flex flex-col flex-1 text-justify items-start">
            </div>
        </div>
        <div ref="row1" class="flex flex-row flex-nowrap relative invisible py-8 items-start">
            <div class="relative flex flex-col flex-1 text-justify items-start">
                <div class="flex flex-row flex-nowrap relative items-baseline">
                    <div ref="time10"
                         class="pl-4 relative flex flex-row flex-nowrap flex-shrink-0 text-justify align-top w-digit-45">
                        00:00
                    </div>
                    <div class="px-4 flex flex-row flex-nowrap flex-shrink-0 text-center align-top w-digit-15">-</div>
                    <div ref="time11"
                         class="pr-4 relative flex flex-row flex-nowrap flex-shrink-0 text-justify align-top w-digit-45">
                        00:00
                    </div>
                </div>
                <div ref="sating1"
                     class="relative flex flex-row flex-nowrap flex-shrink-0 text-justify align-top text-5xl invisible">Doors open at ~00:00</div>
            </div>
            <div class="px-4 relative flex flex-col flex-auto text-justify items-start">
                <p ref="title1" class="relative flex flex-row flex-nowrap text-justify align-top">Fursuit Lounge</p>
                <p ref="room1" class="relative flex flex-row flex-nowrap text-justify align-top text-6xl">@ CCH</p>
            </div>
            <div ref="note1" class="pr-4 relative flex flex-col flex-1 text-justify items-start">
            </div>
        </div>
        <div ref="row2" class="flex flex-row flex-nowrap relative invisible py-8 items-start">
            <div class="relative flex flex-col flex-1 text-justify items-start">
                <div class="flex flex-row flex-nowrap relative items-baseline">
                    <div ref="time20"
                         class="pl-4 relative flex flex-row flex-nowrap flex-shrink-0 text-justify align-top w-digit-45">
                        00:00
                    </div>
                    <div class="px-4 flex flex-row flex-nowrap flex-shrink-0 text-center align-top w-digit-15">-</div>
                    <div ref="time21"
                         class="pr-4 relative flex flex-row flex-nowrap flex-shrink-0 text-justify align-top w-digit-45">
                        00:00
                    </div>
                </div>
                <div ref="sating2"
                     class="relative flex flex-row flex-nowrap flex-shrink-0 text-justify align-top text-5xl invisible">Doors open at ~00:00</div>
            </div>
            <div class="px-4 relative flex flex-col flex-auto text-justify items-start">
                <p ref="title2" class="relative flex flex-row flex-nowrap text-justify align-top">Fursuit Lounge</p>
                <p ref="room2" class="relative flex flex-row flex-nowrap text-justify align-top text-6xl">@ CCH</p>
            </div>
            <div ref="note2" class="pr-4 relative flex flex-col flex-1 text-justify items-start">
            </div>
        </div>
        <div ref="row3" class="flex flex-row flex-nowrap relative invisible py-8 items-start">
            <div class="relative flex flex-col flex-1 text-justify items-start">
                <div class="flex flex-row flex-nowrap relative items-baseline">
                    <div ref="time30"
                         class="pl-4 relative flex flex-row flex-nowrap flex-shrink-0 text-justify align-top w-digit-45">
                        00:00
                    </div>
                    <div class="px-4 flex flex-row flex-nowrap flex-shrink-0 text-center align-top w-digit-15">-</div>
                    <div ref="time31"
                         class="pr-4 relative flex flex-row flex-nowrap flex-shrink-0 text-justify align-top w-digit-45">
                        00:00
                    </div>
                </div>
                <div ref="sating3"
                     class="relative flex flex-row flex-nowrap flex-shrink-0 text-justify text-5xl invisible">Doors open at ~00:00</div>
            </div>
            <div class="px-4 relative flex flex-col flex-auto text-justify items-start">
                <p ref="title3" class="relative flex flex-row flex-nowrap text-justify align-top">Fursuit Lounge</p>
                <p ref="room3" class="relative flex flex-row flex-nowrap text-justify align-top text-6xl">@ CCH</p>
            </div>
            <div ref="note3" class="pr-4 relative flex flex-col flex-1 text-justify items-start">
            </div>
        </div>
    </div>

    <h1 ref="lecturesNote" class="absolute min-w-full text-center text-5xl bottom-1 mb-4 magicTextColor invisible">
        Please remember that lectures mostly start on time,<br/> so please come early to take your seat.</h1>

</template>


<style scoped>

.magicTextColor {
    color: rgba(155, 155, 255, 1);
    text-shadow: 0 0 7px rgba(155, 50, 255, 1),
    0 0 10px rgba(155, 50, 255, 1),
    0 0 21px rgba(150, 20, 200, 0.75),
    0 0 42px rgba(150, 20, 200, 0.5);
    user-select: none;
}

@keyframes rotation180 {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(180deg);
    }
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
    white-space: pre;
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
