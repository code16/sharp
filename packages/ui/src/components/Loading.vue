<template>
    <div  class="SharpLoading" :class="classes" v-show="visible">
        <div class="SharpLoading__content">
            <svg class="SharpLoading__svg" viewBox="-75 -75 150 150">
                <circle cx="0" cy="0" r="37.5" />
            </svg>
        </div>
    </div>
</template>


<script>
    export default {
        name: 'SharpLoading',
        props: {
            visible: Boolean,
            small: Boolean,
        },
        computed: {
            classes() {
                return {
                    'SharpLoading--small': this.small,
                }
            }
        },
    }
</script>