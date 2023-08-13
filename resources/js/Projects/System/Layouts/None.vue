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
        ...props.page.props
    }
})

</script>

<template>
    <div class="h-screen overflow-hidden bg-primary flex flex-col flex-grow">
        <component :is="page.resolvedComponent" v-bind="usableAttributes"></component>
    </div>
</template>
