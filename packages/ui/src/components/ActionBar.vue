<template>
    <div class="SharpActionBar" v-if="ready">
        <div class="SharpActionBar__bar">
            <div class="container">
                <div class="row mx-n2">
                    <div class="col left px-2 my-1 my-sm-0">
                        <slot name="left"></slot>
                    </div>
                    <div class="col right px-2 my-1 my-sm-0" :class="rightClass">
                        <slot name="right"></slot>
                    </div>
                </div>
            </div>
        </div>
        <div :class="{ 'container':container }">
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
            ready: {
                type: Boolean,
                default: true,
            },
            container: Boolean,
            rightClass: Boolean,
        }
    }
</script>