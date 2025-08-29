<script setup>
import { onMounted, onUnmounted, ref } from "vue";

let h00 = ref();
let h01 = ref();
let h10 = ref();
let h11 = ref();
let m00 = ref();
let m01 = ref();
let m10 = ref();
let m11 = ref();

let time = getTime();

const props = defineProps({
    hourglass: {
        type: Boolean,
        default: false,
    },
});

onMounted(() => {
    m00.value.textContent = m01.value.textContent = time.minutes[1];
    m10.value.textContent = m11.value.textContent = time.minutes[0];
    h00.value.textContent = h01.value.textContent = time.hours[1];
    h10.value.textContent = h11.value.textContent = time.hours[0];

    let timer = setInterval(() => {
        let newTime = getTime();
        if (time.minutes[1] !== newTime.minutes[1]) {
            let opacity00 = window
                .getComputedStyle(m00.value)
                .getPropertyValue("opacity");
            if (opacity00 == 0) {
                m00.value.textContent = newTime.minutes[1];
                m00.value.classList.add("fade-in");
                m00.value.classList.remove("fade-out");
            } else if (opacity00 == 1) {
                m00.value.classList.add("fade-out");
                m00.value.classList.remove("fade-in");
            }

            let opacity01 = window
                .getComputedStyle(m01.value)
                .getPropertyValue("opacity");
            if (opacity01 == 0) {
                m01.value.textContent = newTime.minutes[1];
                m01.value.classList.add("fade-in");
                m01.value.classList.remove("fade-out");
            } else if (opacity01 == 1) {
                m01.value.classList.add("fade-out");
                m01.value.classList.remove("fade-in");
            }
        }

        if (time.minutes[0] !== newTime.minutes[0]) {
            let opacity10 = window
                .getComputedStyle(m10.value)
                .getPropertyValue("opacity");
            if (opacity10 == 0) {
                m10.value.textContent = newTime.minutes[0];
                m10.value.classList.add("fade-in");
                m10.value.classList.remove("fade-out");
            } else if (opacity10 == 1) {
                m10.value.classList.add("fade-out");
                m10.value.classList.remove("fade-in");
            }

            let opacity11 = window
                .getComputedStyle(m11.value)
                .getPropertyValue("opacity");
            if (opacity11 == 0) {
                m11.value.textContent = newTime.minutes[0];
                m11.value.classList.add("fade-in");
                m11.value.classList.remove("fade-out");
            } else if (opacity11 == 1) {
                m11.value.classList.add("fade-out");
                m11.value.classList.remove("fade-in");
            }
        }

        if (time.hours[1] !== newTime.hours[1]) {
            let opacity00 = window
                .getComputedStyle(h00.value)
                .getPropertyValue("opacity");
            if (opacity00 == 0) {
                h00.value.textContent = newTime.hours[1];
                h00.value.classList.add("fade-in");
                h00.value.classList.remove("fade-out");
            } else if (opacity00 == 1) {
                h00.value.classList.add("fade-out");
                h00.value.classList.remove("fade-in");
            }

            let opacity01 = window
                .getComputedStyle(h01.value)
                .getPropertyValue("opacity");
            if (opacity01 == 0) {
                h01.value.textContent = newTime.hours[1];
                h01.value.classList.add("fade-in");
                h01.value.classList.remove("fade-out");
            } else if (opacity01 == 1) {
                h01.value.classList.add("fade-out");
                h01.value.classList.remove("fade-in");
            }
        }

        if (time.hours[0] !== newTime.hours[0]) {
            let opacity10 = window
                .getComputedStyle(h10.value)
                .getPropertyValue("opacity");
            if (opacity10 == 0) {
                h10.value.textContent = newTime.hours[0];
                h10.value.classList.add("fade-in");
                h10.value.classList.remove("fade-out");
            } else if (opacity10 == 1) {
                h10.value.classList.add("fade-out");
                h10.value.classList.remove("fade-in");
            }

            let opacity11 = window
                .getComputedStyle(h11.value)
                .getPropertyValue("opacity");
            if (opacity11 == 0) {
                h11.value.textContent = newTime.hours[0];
                h11.value.classList.add("fade-in");
                h11.value.classList.remove("fade-out");
            } else if (opacity11 == 1) {
                h11.value.classList.add("fade-out");
                h11.value.classList.remove("fade-in");
            }
        }

        time = newTime;
    }, 1000);
    onUnmounted(() => {
        clearInterval(timer);
    });
});

function getTime() {
    const date = new Date();
    const hours = String(date.getHours()).padStart(2, "0").split("");
    const minutes = String(date.getMinutes()).padStart(2, "0").split("");
    const seconds = String(date.getSeconds()).padStart(2, "0").split("");
    return { hours: hours, minutes: minutes, seconds: seconds };
}
</script>

<template>
    <div
        class="absolute z-10 top-4 right-5 text-7xl flex flex-row items-end text-white themeFont"
    >
        <div
            class="flex flex-col text-right justify-end items-end clockContainer"
        >
            <div id="line0" class="flex digitline">
                <span ref="h10" class="fade-in">0</span>
                <span ref="h00" class="fade-in">0</span>
                <span style="visibility: visible; width: 0.5ch">:</span>
                <span ref="m10" class="fade-in">0</span>
                <span ref="m00" class="fade-in">0</span>
            </div>

            <div id="line1" class="flex digitline">
                <span ref="h11" class="fade-out">0</span>
                <span ref="h01" class="fade-out">0</span>
                <span style="visibility: hidden; width: 0.5ch">:</span>
                <span ref="m11" class="fade-out">0</span>
                <span ref="m01" class="fade-out">0</span>
            </div>
        </div>

        <!--        <div v-if="hourglass" id="HourGlass" class="flex ml-5">-->
        <!--            <HourGlassSVG style="height: 0.9em; transform: scaleY(-1);"/>-->
        <!--        </div>-->
    </div>
</template>

<style scoped>
.clockContainer {
    width: 5ch;
    height: 1em;
    user-select: none;
}

.fade-in {
    animation: in 1s normal forwards linear;
}

@keyframes in {
    from {
        opacity: 0;
        filter: "blur(20px)";
    }
    to {
        opacity: 1;
        filter: "blur(0px)";
    }
}

.fade-out {
    animation: out 1s normal forwards linear;
}

@keyframes out {
    from {
        opacity: 1;
        filter: "blur(0px)";
    }
    to {
        opacity: 0;
        filter: "blur(20px)";
    }
}
</style>

<style>
.digitline {
    position: absolute;
}

.digitline span {
    display: inline-block;
    width: 1ch;
}
</style>
