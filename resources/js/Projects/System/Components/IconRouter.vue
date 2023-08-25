<script setup>
import {computed, defineAsyncComponent} from "vue";

defineOptions({
    inheritAttrs: false
})

const props = defineProps({
    icon: {
        type: String,
        default: 'arrow'
    },
    rotation: {
        type: Number,
        default: 0
    },
    mirror: {
        type: Boolean,
        default: false
    },
    path: {
        type: String,
        required: true,
        default: 'System'
    },
})
let icon = computed(() => {
    return resolveComponent(props.icon)
})

function resolveComponent(name) {
    return defineAsyncComponent(() => import(`../../${props.path}/Components/Icons/${name}.svg`))
}

</script>

<template>
    <component v-bind="$attrs" :is="icon" :style="`transform: ${mirror ? 'scaleX(-1)' : ''} rotate(${rotation}deg)`" />
</template>

<style scoped>

</style>
