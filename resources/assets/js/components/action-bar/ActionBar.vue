<template>
    <div class="SharpActionBar">
        <div class="row">
            <div class="col left">
                <slot name="left"></slot>
            </div>
            <div class="col right">
                <slot name="right"></slot>
            </div>
        </div>
        <div class="SharpActionBar__extras">
            <slot name="extras"></slot>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'SharpActionBar',
    }
</script>