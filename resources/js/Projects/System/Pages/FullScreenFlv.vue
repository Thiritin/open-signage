<script setup>

import VueFlvPlayer from "@/Projects/System/Components/VueFlvPlayer.vue";

const props = defineProps({
    streamUrl: String,
    muted: Boolean
})
let mediaDataSource = {
    isLive: true,
}
</script>

<template>
    <div class="h-screen w-screen bg-black">
        <VueFlvPlayer :autoplay="true"
                      :controls="false"
                      :muted="muted"
                      class="w-screen h-screen"
                      ref="livePlayer"
                      :media-data-source="mediaDataSource"
                      :source="streamUrl + '&client=vlc'" type="flv"/>
    </div>
</template>

<style scoped>

</style>
