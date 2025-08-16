<script setup>

import {computed, onMounted, onUnmounted, ref} from "vue";
import { animate, utils } from 'animejs';

import TransmutationSVG from '@/Projects/EF27/Assets/images/transmutation5.svg';

let animation;
onMounted(() => {
    //Animate Transmutation circle path
    let pathEls = document.querySelectorAll('.transmutation path');
    for (let i = 0; i < pathEls.length; i++) {
        let pathEl = pathEls[i];
        // let offset = anime.setDashoffset(pathEl);
        pathEl.setAttribute('stroke-dashoffset', offset);
        animation = animate({
            targets: pathEl,
            // strokeDashoffset: [offset, 0],
            duration: utils.random(10000, 17000),
            delay: utils.random(0, 3000),
            loop: true,
            direction: 'alternate',
            easing: 'easeInOutSine',
            autoplay: true
        });

    }
})

onUnmounted(() => {
    animation.reset();
});

</script>


<template>

    <div class="absolute z-0 flex w-screen h-screen justify-center items-center text-center">
        <TransmutationSVG style="z-index: 2; bottom: 200px;" class="h-[61.8vh] w-[61.8vw] absolute transmutation flex"/>
    </div>

</template>


<style scoped>

.transmutation {
    animation: moveCircle 20s normal forwards linear, rotation360 240s infinite linear;
}

@keyframes moveCircle {
    from {
        opacity: 0;
    /* top: -700px; */
    }
    to {
        opacity: 0.3;
    /* top: 10px; */
    }
}

@keyframes rotation180 {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(180deg);
    }
}

@keyframes rotation360 {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(359deg);
    }
}

</style>
