<template>
    <loading-transition :fade="fade">
        <div class="SharpLoading" :class="classes" v-show="visible">
            <div class="d-block spinner-border" :class="spinnerClasses"></div>
        </div>
    </loading-transition>
</template>


<script lang="ts">
    import LoadingTransition from "./LoadingTransition.vue";

    export default {
        components: {
            LoadingTransition,
        },
        props: {
            visible: {
                type: Boolean,
                default: true,
            },
            small: Boolean,
            light: Boolean,
            fade: Boolean,
        },
        computed: {
            classes() {
                return {
                    'SharpLoading--light': this.light,
                }
            },
            spinnerClasses() {
                return {
                    'spinner-border-sm': this.small,
                }
            },
        },
    };
</script>
