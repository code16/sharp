<template>
    <div class="card position-relative" style="min-height: 100px">
        <div class="card-body d-flex align-items-center justify-content-center">
            <div>Upload...</div>
            <input
                type="file"
                class="position-absolute w-100 h-100"
                style="opacity:0; left:0;top: 0; cursor: pointer;"
                multiple
                @change="handleChanged"
            >
        </div>
    </div>
</template>

<script>
    export default {
        props: {},
        methods: {
            handleChanged(e) {
                this.$emit('change', e);
            }
        }
    }
</script>
