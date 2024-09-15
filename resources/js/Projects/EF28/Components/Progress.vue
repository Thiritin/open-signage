<script setup>
import { computed, onMounted, onUnmounted, ref } from "vue";
import anime from "animejs";
import Chevron from '../Assets/images/chevron_right.svg';

onMounted(() => {
    anime.timeline({
        targets: document.querySelectorAll(".progressbar > .progress"),
        loop: true,
        easing: 'linear',
        translateX: function (el) {
            return el.getBoundingClientRect().width;
        },
        duration: function () {
            return 1000;
        },
    }).add({
        targets: document.querySelectorAll(".progressbar > .progress#progress_1"),
        loop: true,
        easing: 'linear',
        opacity: 1,
        duration: function () {
            return 1000;
        },
    }, 0).add({
        targets: document.querySelectorAll(".progressbar > .progress#progress_5"),
        loop: true,
        easing: 'linear',
        direction: "normal",
        opacity: 0,
        duration: function () {
            return 1000;
        },
    }, 0)
    .play();
});
</script>

<template>
    <div class="progressbar">
        <Chevron class="progress" id="progress_1"></Chevron>
        <Chevron class="progress" id="progress_2"></Chevron>
        <Chevron class="progress" id="progress_3"></Chevron>
        <Chevron class="progress" id="progress_4"></Chevron>
        <Chevron class="progress" id="progress_5"></Chevron>
    </div>
</template>

<style scoped>
.progressbar {
    display: flex;
    flex-flow: row;
    flex: 1 0 auto;
    margin: 0 1.5em;
}

.progress {
    width: 1em;
    height: 2.25em;
}

.progress * {
    fill: #ff7eff;
}

.progress#progress_1 {
    opacity: 0;
}
</style>

<style></style>
