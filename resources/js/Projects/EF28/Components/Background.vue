<script setup>
import { computed, onMounted, onUnmounted, ref } from "vue";

const props = defineProps({
    color: {
        type: String,
        default: "#ff00ff",
    },
});

function clamp(n, min, max) {
    if (n > max)
        return max;
    else if (n < min)
        return min;
    else
        return n;
}

const gridLayer = ref();

function Line(canvas, context, x, y, color = '#ff00ff', vertical = false) {
    this.opacity = 255;
    this.blurOpacity = 255;
    this.gradient = context.createLinearGradient(0, 0, vertical ? 0 : canvas.width, vertical ? canvas.height : 0);
    this.gradient.addColorStop(0, '#00000000');
    this.gradient.addColorStop(1, '#00000000');

    this.render = () => {
        this.gradient.addColorStop(0.5, `${color}${this.opacity.toString(16).padStart(2, '0')}`)

        context.fillStyle = this.gradient;
        context.shadowColor = `${color}${this.blurOpacity.toString(16).padStart(2, '0')}`;
        context.shadowBlur = 15;
        context.fillRect(x, y, vertical ? 2 : canvas.width, vertical ? canvas.height : 2);
    }

    this.update = (newOpacity = 255) => {
        if (!canvas) return;

        const center = vertical ? canvas.width / 2 : canvas.height / 2;
        const mod = Math.abs(vertical ? center - x : center - y);

        if (center >= mod)
            newOpacity = Math.round(newOpacity * (1 - mod / center));

        this.opacity = clamp(newOpacity, 0, 255);
        this.blurOpacity = clamp(this.opacity * 5, 0, 255)
    }
}

function Background(options = {}) {
    this.lines = [];

    this.lineCountX =
        options.lineCountX && !isNaN(options.lineCountX)
            ? options.lineCountX
            : 16;
    this.lineCountY =
        options.lineCountY && !isNaN(options.lineCountY)
            ? options.lineCountY
            : 9;
    this.flickerIntensity =
        options.flickerIntensity && !isNaN(options.flickerIntensity)
            ? options.flickerIntensity
            : 1;
    this.flickerInterval =
        options.flickerInterval && !isNaN(options.flickerInterval)
            ? options.flickerInterval
            : 1;
    this.color =
        options.color ? options.color : '#ff00ff';

    this.setup = () => {
        this.canvas = gridLayer ? gridLayer.value : document.querySelector('#grid_layer');
        this.context = this.canvas.getContext("2d");
        this.lines = [];

        const slice = this.canvas.width / (this.lineCountX + 2);

        for (let x = 0; x < this.lineCountX + 1; x++) {
            this.lines.push(new Line(this.canvas, this.context, slice + x * slice + x, 0, this.color, true));
        }

        for (let y = 0; y < this.lineCountY; y++) {
            this.lines.push(new Line(this.canvas, this.context, 0, slice + y * slice + y, this.color));
        }
    }

    this.render = () => {
        this.lines.forEach((line) => {
            line.update(50);
            line.render();
        })
    }

    this.resize = () => {
        if (this.canvas) {
            this.canvas.width = window.innerWidth;
            this.canvas.height = window.innerHeight;

            this.setup();
        }
    }
}

const background = new Background({
    color: props.color
});

onMounted(() => {
    window.addEventListener("resize", background.resize);
    background.setup();
    background.resize();
    background.render();
});

onUnmounted(() => {
    window.removeEventListener("resize", background.resize);
});
</script>

<template>
    <canvas
        ref="gridLayer"
        id="grid_layer"
        class="absolute left-0 top-0 w-screen h-screen"
    ></canvas>
</template>

<style scoped></style>

<style></style>
