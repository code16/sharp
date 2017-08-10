<template>
    <div v-if="inline" class="SharpLoading" :class="{'SharpLoading--small':small}" v-show="show">
        <svg class="SharpLoading__svg" viewBox="-75 -75 150 150">
            <circle cx="0" cy="0" r="37.5" />
        </svg>
    </div>
    <div v-else class="SharpLoading__overlay" v-show="show">
        <div class="SharpLoading" :class="{'SharpLoading--small':small}">
            <svg class="SharpLoading__svg" viewBox="-75 -75 150 150">
                <circle cx="0" cy="0" r="37.5" />
            </svg>
        </div>
    </div>
</template>



<script>
    import Vue from 'vue';
    export default {
        name: 'SharpLoading',
        props: {
            visible: Boolean,
            inline: Boolean,
            small: Boolean
        },
        data() {
            return {
                show: this.visible
            }
        },
        watch: {
            visible(v) {
                this.show = v;
            }
        },
        created() {
            this.$on('show', _ => this.show = true);
            this.$on('hide', _ => this.show = false);
        }
    }
</script>