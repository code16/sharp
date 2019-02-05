<template>
    <div class="SharpActionBar" v-if="ready">
        <div class="SharpActionBar__bar">
            <div class="container">
                <div class="row">
                    <div class="col left pr-0 pr-sm-3">
                        <slot name="left"></slot>
                    </div>
                    <div class="col right pl-0 pl-sm-3">
                        <slot name="right"></slot>
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="row">
                <div class="col">
                    <div class="SharpActionBar__extras">
                        <slot name="extras" />
                    </div>
                </div>
                <template v-if="$slots['extras-right']">
                    <div class="col-auto">
                        <div class="SharpActionBar__extras">
                            <slot name="extras-right" />
                        </div>
                    </div>
                </template>
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