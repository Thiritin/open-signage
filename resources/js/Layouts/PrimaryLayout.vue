<script setup>
defineProps(['page'])
defineOptions({
    inheritAttrs: false
})
import HeaderLogo from "@/Layouts/HeaderLogo.vue";
</script>

<template>
    <div class="p-8 xl:p-16 min-h-screen bg-primary">
        <!-- Header Menu -->
        <HeaderLogo :title="page.title"/>
        <!-- Main Content -->
        <div class="pt-12">
            <Transition name="bounce" mode="out-in">
                <component :is="page.resolvedComponent" v-bind="$attrs"></component>
            </Transition>
        </div>
    </div>
</template>

<style>
@font-face {
    font-family: 'primaryThemeFont';
    font-style: normal;
    src: local('Hansief'), url(../../assets/fonts/Hansief.otf) format('truetype');
}

.themeColor {
    color: #1c253c;
}

.themeColorSecondary {
    color: #6dd2d1;
}

.themeColorCompsite {
    color: #feff99;
}

.bounce-enter-active {
    animation: bounce-in 0.5s;
}

.bounce-leave-active {
    animation: bounce-in 0.5s reverse;
}

@keyframes bounce-in {
    0% {
        transform: scale(0);
    }
    50% {
        transform: scale(1.25);
    }
    100% {
        transform: scale(1);
    }
}
</style>
