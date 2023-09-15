<template>
    <div class="SharpLoading" :class="classes" v-show="visible">
        <div class="d-block spinner-border" :class="spinnerClasses"></div>
    </div>
</template>


<script lang="ts">
    export default {
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
