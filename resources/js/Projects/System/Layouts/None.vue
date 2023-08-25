<script setup>
import {computed, onMounted, reactive, useAttrs} from "vue";

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
    <div class="h-screen overflow-hidden bg-primary flex flex-col flex-grow">
        <Transition mode="out-in">
            <component :is="page.resolvedComponent" v-bind="usableAttributes"></component>
        </Transition>
    </div>
</template>

<style>
.v-enter-active {
    transition: opacity 1s ease-in;
}

.v-leave-active {
    transition: opacity 0.5s ease-out;
}

.v-enter-from,
.v-leave-to {
    opacity: 0;
}
</style>
