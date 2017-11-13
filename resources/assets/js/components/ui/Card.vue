<template>
    <div class="SharpModule" :class="cardClasses">
        <div class="SharpModule__inner">
            <div class="SharpModule__content">
                <slot></slot>
            </div>
            <template v-if="hasClose">
                <button class="SharpModule__close-button" type="button" @click="$emit('close-click')">
                    <svg class="SharpModule__close-icon"
                         aria-label="close" width="10" height="10" viewBox="0 0 10 10" fill-rule="evenodd">
                        <path d="M9.8 8.6L8.4 10 5 6.4 1.4 10 0 8.6 3.6 5 .1 1.4 1.5 0 5 3.6 8.6 0 10 1.4 6.4 5z"></path>
                    </svg>
                </button>
            </template>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'SharpCard',
        props: {
            light:Boolean,
            hasClose:Boolean
        },
        computed: {
            cardClasses() {
                return {
                    'SharpModule--light':this.light
                }
            }
        }
    }
</script>