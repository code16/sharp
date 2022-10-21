<template>
    <div class="SharpActionBar">
        <div class="SharpActionBar__bar sticky-top" ref="bar">
            <div class="container">
                <div class="row align-items-center g-2 gx-md-4">
                    <div class="col SharpActionBar__col--left" style="min-width: 0">
                        <slot name="left"></slot>
                    </div>
                    <div class="col-auto SharpActionBar__col--right">
                        <slot name="right"></slot>
                    </div>
                </div>
            </div>
        </div>
        <template v-if="hasExtras">
            <div :class="{ 'container':container }">
                <div class="row">
                    <div class="col-sm">
                        <div class="SharpActionBar__extras">
                            <slot name="extras" />
                        </div>
                    </div>
                    <template v-if="$slots['extras-right']">
                        <div class="col-sm-auto">
                            <div class="SharpActionBar__extras">
                                <slot name="extras-right" />
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </template>
    </div>
</template>

<script>
    import { getNavbarHeight } from "../util";

    export default {
        name: 'SharpActionBar',
        props: {
            container: Boolean,
        },
        computed: {
            hasExtras() {
                return this.$slots.extras || this.$slots['extras-right'];
            },
        },
        methods: {
            layout(rect) {
                /** @see getNavbarHeight **/
                document.documentElement.style.setProperty('--navbar-height', `${rect.height}px`);
            },
        },
        mounted() {
            this.layout(this.$refs.bar.getBoundingClientRect());

            if(window.ResizeObserver) {
                (new ResizeObserver(entries => {
                    this.layout(entries[0].target.getBoundingClientRect());
                }))
                .observe(this.$refs.bar);
            }
        }
    }
</script>
