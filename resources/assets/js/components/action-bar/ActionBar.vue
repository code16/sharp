<template>
    <div class="SharpActionBar" v-if="ready">
        <div class="SharpActionBar__bar">
            <div class="container">
                <div class="row">
                    <div class="col left">
                        <slot name="left"></slot>
                    </div>
                    <div class="col right">
                        <slot name="right"></slot>
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="SharpActionBar__extras">
                <slot name="extras"></slot>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'SharpActionBar',
        props: {
            ready: Boolean
        }
    }
</script>