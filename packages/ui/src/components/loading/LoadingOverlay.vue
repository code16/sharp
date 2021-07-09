<template>
    <loading-transition :fade="fade">
        <div class="SharpLoading__overlay" :class="classes" v-show="visible">
            <Loading v-bind="$attrs" />
        </div>
    </loading-transition>
</template>

<script>
    import Loading from './Loading';
    import LoadingTransition from "./LoadingTransition";

    export default {
        components: {
            LoadingTransition,
            Loading,
        },
        props: {
            visible: {
                type: Boolean,
                default: true,
            },
            absolute: Boolean,
            fade: Boolean,
        },
        computed: {
            classes() {
                return {
                    'SharpLoading__overlay--absolute': this.absolute,
                }
            },
        }
    }
</script>
