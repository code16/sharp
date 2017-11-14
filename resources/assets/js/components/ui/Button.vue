<template>
    <button class="SharpButton" :class="buttonClasses" v-on="$listeners"><slot></slot></button>
</template>

<script>
    export default {
        props: {
            type: String,
            outline: Boolean,
            danger: Boolean,
            small: Boolean
        },
        computed:{
            buttonClasses() {
                return [
                    this.type ? `SharpButton--${this.type}` : '', {
                        'SharpButton--secondary': this.outline,
                        'SharpButton--danger': this.danger,
                        'SharpButton--sm': this.small
                    }
                ]
            }
        }
    }
</script>