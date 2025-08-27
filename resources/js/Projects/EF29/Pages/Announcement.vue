<script setup>
const preserveClasses = [
    "text-3xl",
    "text-4xl",
    "text-5xl",
    "text-6xl",
    "text-7xl",
    "text-8xl",
    "text-9xl",
    "text-10xl",
    "text-11xl",
    "text-12xl",
    "text-13xl",
    "text-14xl",
]

defineProps({
    title: {
        type: String,
        default: "Announcement"
    },
    text: {
        type: String,
        default: "Welp!\n\nLooks like there's no announcement right now."
    },
    centerContent: {
        type: Boolean,
        default: false
    },
    headerSize: {
        type: String,
        default: "text-12xl"
    },
    textSize: {
        type: String,
        default: "text-9xl"
    },
})
</script>

<template>
    <div class="h-full flex flex-col justify-center items-center z-50 bgImage bg-no-repeat bg-cover bg-center text-primary-200" :class="{'text-center': centerContent}">
        <div class="p-16" :class="{'bg-white bg-opacity-80 max-w-7xl mx-auto min-h-full': useContainer}">
            <h1 class="themeFont text-[128pt] mb-12 text-center" :class="headerSize">{{ title }}</h1>
            <p class="font-semibold themeFontSecondary leading-normal mx-auto whitespace-pre-wrap text-[88pt] text-center" :class="textSize">
                <div class="textscreen" v-html="text"></div>
            </p>
        </div>
    </div>
</template>

<style>
figcaption {
    display: none;
}

.textscreen img {
    padding:60px;
    margin:auto;
}
</style>
