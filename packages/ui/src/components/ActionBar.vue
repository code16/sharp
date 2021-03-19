<template>
    <div class="SharpActionBar" v-if="ready">
        <div class="SharpActionBar__bar sticky-top" ref="bar">
            <div class="container">
                <div class="row mx-n2">
                    <div class="col-auto col-md left px-2 my-1 my-sm-0">
                        <slot name="left"></slot>
                    </div>
                    <div class="col col-md-auto right px-2 my-1 my-sm-0" :class="rightClass">
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
            rightClass: String,
        },
        methods: {
            layout(rect) {
                document.documentElement.style.setProperty('--navbar-height', `${rect.height}px`);
            },
        },
        mounted() {
            this.layout(this.$refs.bar.getBoundingClientRect());

            console.log(this.$refs.bar.getBoundingClientRect());

            if(window.ResizeObserver) {
                (new ResizeObserver(entries => {
                    this.layout(entries[0].target.getBoundingClientRect());
                }))
                .observe(this.$refs.bar);
            }
        }
    }
</script>
