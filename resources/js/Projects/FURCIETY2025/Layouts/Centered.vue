<script setup>
import {onMounted, useAttrs} from "vue";

const props = defineProps(['page'])
defineOptions({
    inheritAttrs: false
})
let attrs = useAttrs();
attrs = {
    ...attrs,
    page: props.page,
    ...props.page.props
}
</script>

<template>
    <div class="h-screen overflow-hidden justify-center items-center bg-primary flex flex-col flex-grow">
        <component class="pt-8 px-8" :is="page.resolvedComponent" v-bind="attrs"></component>
    </div>
</template>
