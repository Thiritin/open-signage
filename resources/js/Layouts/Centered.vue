<script setup>
import {onMounted, useAttrs} from "vue";

const props = defineProps(['page'])
defineOptions({
    inheritAttrs: false
})
import HeaderLogo from "@/Components/HeaderLogo.vue";
let attrs = useAttrs();
attrs = {
    ...attrs,
    ...props.page.props
}
</script>

<template>
    <div class="h-screen overflow-hidden justify-center items-center bg-primary flex flex-col flex-grow">
        <component class="pt-8 px-8" :is="page.resolvedComponent" v-bind="attrs"></component>
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
</style>
