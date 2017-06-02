<template>
    <div class="SharpActionBar row">
        <div class="col left">
            <slot name="left"></slot>
        </div>
        <div class="col right">
            <slot name="right"></slot>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'SharpActionBar',
    }
</script>