<template>
    <button class="SharpClearButton btn" :class="classes" type="button" v-on="$listeners">
        <svg class="SharpClearButton__icon"
            aria-label="close" width="10" height="10" viewBox="0 0 10 10" fill-rule="evenodd">
            <path d="M9.8 8.6L8.4 10 5 6.4 1.4 10 0 8.6 3.6 5 .1 1.4 1.5 0 5 3.6 8.6 0 10 1.4 6.4 5z"></path>
        </svg>
    </button>
</template>

<script>
    export default {
        props: {
            formControl: {
                type: Boolean,
                default: true,
            }
        },
        computed: {
            classes() {
                return {
                    'SharpClearButton--form-control': this.formControl,
                }
            },
        },
        methods: {
            click() {
                this.$el.click();
            }
        }
    }
</script>
