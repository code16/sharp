<template>
    <div class="form-check">
        <label class="form-check-label">
            <input class="form-check-input" type="checkbox" @change="handleCheck" :checked="value"> {{text}}
        </label>
    </div>
</template>

<script>
    export default {
        name: 'SharpCheck',
        props: {
            value:Boolean,
            text:String
        },
        methods: {
            handleCheck(e) {
                console.log(e);
                this.$emit('input', e.target.checked);
            }
        }
    }
</script>