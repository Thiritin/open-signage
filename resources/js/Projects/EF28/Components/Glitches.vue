<script setup>
import { computed, onMounted, onUnmounted, ref } from "vue";

const glitchLayer = ref();

function randInt(max) {
    return Math.floor(Math.random() * max);
}

function GlitchController(options = {}) {
    this.setup = () => {
        this.canvas = glitchLayer ? glitchLayer.value : document.querySelector('#glitch_layer');

        if (this.canvas) {
            this.canvas.width = window.innerWidth;
            this.canvas.height = window.innerHeight;
        }

        this.context = this.canvas.getContext("2d");
    }

    this.duration = 2000;
    this.disabled = false;

    this.render = () => {
        setInterval(() => {
            this.context.clearRect(0, 0, this.canvas.width, this.canvas.height);

            if (this.disabled)
                return;

            for (let i = 0; i < (6 + randInt(6)); i++) {
                const width = 60 + randInt(120);

                this.context.fillStyle = `#ff00ff${(10 + randInt(100)).toString(16).padStart(2, '0')}`;
                this.context.shadowColor = "#ff00ff";
                this.context.shadowBlur = 8;
                this.context.fillRect(randInt(this.canvas.width), randInt(this.canvas.height), width, width / (1 + randInt(2)));
            }
        }, 250);

        setTimeout(() => {
            this.disabled = true;
        }, this.duration);
    }
}

const glitchController = new GlitchController();

onMounted(() => {
    window.addEventListener("resize", glitchController.setup);
    glitchController.setup();
    glitchController.render();
});
</script>

<template>
    <canvas
        ref="glitchLayer"
        id="glitch_layer"
        class="absolute left-0 top-0 w-screen h-screen"
    ></canvas>
</template>

<style scoped></style>

<style></style>
