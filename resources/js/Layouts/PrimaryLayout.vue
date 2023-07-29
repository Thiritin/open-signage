<script setup>
import {onMounted, useAttrs} from "vue";

const props = defineProps(['page'])
defineOptions({
    inheritAttrs: false
})
import HeaderLogo from "@/Layouts/HeaderLogo.vue";
let attrs = useAttrs();
attrs = {
    ...attrs,
    ...props.page.props
}

</script>

<template>
    <div class="h-screen overflow-auto bg-primary flex flex-col flex-grow">
        <!-- Header Menu -->
        <HeaderLogo :title="page.title"/>
        <!-- Main Content -->
        <component class="px-8" :is="page.resolvedComponent" v-bind="attrs"></component>
    </div>
</template>

<style>

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
