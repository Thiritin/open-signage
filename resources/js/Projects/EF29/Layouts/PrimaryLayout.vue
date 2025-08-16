<script setup>
import {computed, onMounted, onUpdated, reactive, useAttrs, watch} from "vue";

const props = defineProps(['page'])

defineOptions({
    inheritAttrs: false
})

import Background from '../Components/Background.vue';
import BackgroundImage from '../Assets/images/background.svg';

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
            <Background />
            <BackgroundImage class="cyberpunk_background" />
            <!-- <div class="spark" id="spark1"></div>
            <div class="spark" id="spark2"></div>
            <div class="spark" id="spark3"></div>
            <svg version="1.1" viewBox="0 0 3840 2160" xml:space="preserve" class="sparksvg" id="path1">
                <path fill="none" d="m332.22 816.94v-58.781l63.632-64.29-53.21-51.454 0.7502-41.697-41.56-43.18-0.10994-19.509 61.421-0.72935 54.87-54.944v-108.5l97.407-96.231-0.42812 1.8617"/>
            </svg>
            <svg version="1.1" viewBox="0 0 3840 2160" xml:space="preserve" class="sparksvg" id="path2">
                <path fill="none" d="m3633.4 1356.1-38.901-65.62v-107.83l-174.26 0.5981-60.855-65.611v-149.6"/>
            </svg>
            <svg version="1.1" viewBox="0 0 3840 2160" xml:space="preserve" class="sparksvg" id="path3">
                <path fill="none" d="m3634.5 461.22-43.86 70.331v106.07h-172.89l-60.899 72.178v143.4"/>
            </svg> -->
        </div>

        <!--    <Time hourglass="true"/>-->

        <div class="h-screen overflow-auto bg-primary flex flex-col flex-grow">
            <!-- Main Content -->
            <Transition mode="out-in">
                <component :is="page.resolvedComponent" v-bind="usableAttributes"></component>
            </Transition>
        </div>
    </div>
</template>

<style>
.spark {
    position: absolute;
    left: -0.4rem;
    top: -0.4rem;
    right: 0;
    bottom: 0;
    min-width: 0.75rem;
    min-height: 0.75rem;
    width: 0.75rem;
    height: 0.75rem;
    border-radius: 100%;
    background: #ff00ff30;
    box-shadow: 0 0 1rem #ff00ff;
}

.sparksvg {
    position: absolute;
    left: 0;
    top: 0;
    right: 0;
    bottom: 0;
    width: 100vw;
    height: 100vh;
}

.cyberpunk_background {
    position: absolute;
}

.cyberpunk_background * {
    fill: #291429;
    filter: drop-shadow(0 0 1rem #ff00ff50);
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
