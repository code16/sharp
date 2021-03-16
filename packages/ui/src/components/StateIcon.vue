<template>
    <span class="StateIcon" :style="style"></span>
</template>

<script>
    export default {
        name: 'SharpStateIcon',
        props: {
            color: {
                required: true,
                type: String,
            },
        },
        computed: {
            style() {
                return {
                    'background': this.color
                }
            }
        },
    }
</script>
