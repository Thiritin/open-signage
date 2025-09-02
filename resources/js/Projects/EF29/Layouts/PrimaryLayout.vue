<script setup>
import {computed, onMounted, onUpdated, reactive, useAttrs, watch} from "vue";

const props = defineProps(['page'])

defineOptions({
    inheritAttrs: false
})

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
        <!-- background -->
        <div style="position: absolute; left: 0; top: 0; width: 100vw; height: 100vh; z-index: -1;">
            <img src="../Assets/images/background.png" class="background_foreground" style="position: absolute; left: 0; top: 0; width: 100vw; height: 100vh; z-index: -1;" />
        </div>

        <!--    <Time hourglass="true"/>-->

        <div class="h-screen overflow-auto bg-transparent flex flex-col flex-grow">
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
