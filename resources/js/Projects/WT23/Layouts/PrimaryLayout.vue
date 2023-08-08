<script setup>
import {computed, onMounted, onUpdated, reactive, useAttrs, watch} from "vue";

const props = defineProps(['page'])
defineOptions({
    inheritAttrs: false
})
import HeaderLogo from "@/Projects/WT23/Components/HeaderLogo.vue";

let attrs = reactive(useAttrs());

const usableAttributes = computed(() => {
    return {
        ...attrs,
        ...props.page.props
    }
})

</script>

<template>
    <div class="h-screen overflow-auto bg-primary flex flex-col flex-grow">
        <!-- Header Menu -->
        <HeaderLogo :title="page.title"/>
        <!-- Main Content -->
        <component :is="page.resolvedComponent" v-bind="usableAttributes"></component>
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
