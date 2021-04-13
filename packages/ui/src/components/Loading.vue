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
        props: {
            visible: {
                type: Boolean,
                default: true,
            },
            small: Boolean,
            medium: Boolean,
        },
        computed: {
            classes() {
                return {
                    'SharpLoading--small': this.small,
                    'SharpLoading--medium': this.medium,
                }
            }
        },
    }
</script>
