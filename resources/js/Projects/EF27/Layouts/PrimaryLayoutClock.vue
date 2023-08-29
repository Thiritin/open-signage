<script setup>
import {computed, onMounted, onUpdated, reactive, useAttrs, watch} from "vue";

const props = defineProps(['page'])
defineOptions({
    inheritAttrs: false
})

import Time from "@/Projects/EF27/Components/CurrentTime.vue";
import TransmutationCircle from "@/Projects/EF27/Components/TransmutationCircle.vue";
import MagicMist from "@/Projects/EF27/Components/MagicMist.vue";
import MaskSVG from "@/Projects/EF27/Assets/images/logoEF27Mask.svg";
import LogoSVG from "@/Projects/EF27/Assets/images/logoEF27e.svg";


let attrs = reactive(useAttrs());

const usableAttributes = computed(() => {
    return {
        ...attrs,
        page: props.page,
        ...props.page.props
    }
})

</script>

<template>
    <div>
        <!-- animated background -->
        <div class="absolute left-0 top-0 z-0 w-screen h-screen flex items-center justify-center text-center">
            <TransmutationCircle/>

            <MaskSVG style="z-index: 3; width: 700px; height: 700px;" class="absolute bottom-5 flex"/>
            <MagicMist color="purple"/>
            <LogoSVG style="z-index: 5; width: 700px; height: 700px; opacity: 0.75" class="absolute flex bottom-5"/>
        </div>

        <Time hourglass="false"/>

        <div class="h-screen overflow-auto bg-primary flex flex-col flex-grow">
            <!-- Main Content -->
            <Transition mode="out-in">
                <component :is="page.resolvedComponent" v-bind="usableAttributes"></component>
            </Transition>
        </div>
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
