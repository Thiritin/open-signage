<template>
    <video :width="width" :height="height" class="flv-player" v-bind="$attrs" ref="flvPlayer"></video>
</template>

<script>
import flvjs from 'flv.js';
export default {
    name: 'VueFlvPlayer',
    props: {
        source: {
            type: String,
            required: true,
        },
        type: {
            type: String,
            default: 'flv',
            required: true,
        },
        width: {
            type: Number,
            default: 800,
        },
        height: {
            type: Number,
            default: 600,
        },
        mediaDataSource: {
            type: Object,
            default: () => {},
        },
        config: {
            type: Object,
            default: () => {},
        },
    },
    mixins: [],
    components: {},
    data() {
        return {
            isSupported: null,
            flvPlayer: null,
        };
    },
    computed: {},
    watch: {
        source(val) {
            if (val) {
                this.init();
            }
        },
    },
    methods: {
        constructor: function (mediaDataSource, config) {
            this.flvPlayer.constructor(mediaDataSource, config);
        },
        init() {
            if (this.isSupported && this.source && this.type) {
                let videoElement = this.$refs.flvPlayer;
                this.flvPlayer = flvjs.createPlayer(
                    { url: this.source, type: this.type, ...this.mediaDataSource },
                    this.config
                );
                this.flvPlayer.attachMediaElement(videoElement);
                this.load();
            }
        },
        on(event, listener) {
            this.flvPlayer.on(event, listener);
        },
        off(event, listener) {
            this.flvPlayer.off(event, listener);
        },
        load() {
            this.flvPlayer.load();
        },
        unload() {
            this.flvPlayer.unload();
        },
        play() {
            this.flvPlayer.play();
        },
        pause() {
            this.flvPlayer.pause();
        },
        reset() {
            this.pause();
            this.unload();
        },
        destroy() {
            this.flvPlayer.destroy();
        },
    },
    created() {
        this.isSupported = flvjs.isSupported();
    },
    mounted() {
        this.init();
    },
    beforeCreate() {},
    beforeMount() {},
    beforeUpdate() {},
    updated() {},
    beforeDestroy() {},
    destroyed() {
        if (this.flvPlayer) {
            this.pause();
            this.unload();
            this.destroy();
            this.flvPlayer = null;
        }
    },
    activated() {},
};
</script>

<style>
</style>
