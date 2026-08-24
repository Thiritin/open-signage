<script setup>
// Starter "None" layout.
//
// Like the Grid layout it provides the 1920x1080 AutoScale virtual canvas (so
// designs stay resolution-independent) but with no themed background chrome —
// the design owns the whole frame.
import { computed, reactive, useAttrs } from "vue";
import { AutoScale } from "@/Components/Grid";

const props = defineProps(["page"]);

defineOptions({ inheritAttrs: false });

const attrs = reactive(useAttrs());

const usableAttributes = computed(() => ({
    ...attrs,
    page: props.page,
    ...props.page.props,
}));
</script>

<template>
    <AutoScale :width="1920" :height="1080" mode="contain" background="#0f172a">
        <div class="canvas">
            <Transition mode="out-in">
                <component
                    :is="page.resolvedComponent"
                    v-bind="usableAttributes"
                />
            </Transition>
        </div>
    </AutoScale>
</template>

<style scoped>
.canvas {
    position: relative;
    width: 100%;
    height: 100%;
    color: #fff;
}

.v-enter-active,
.v-leave-active {
    transition: opacity 0.6s ease;
}
.v-enter-from,
.v-leave-to {
    opacity: 0;
}
</style>
