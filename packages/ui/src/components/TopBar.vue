<template>
    <div class="SharpTopBar sticky-top" ref="bar">
        <div class="container">
            <div class="row align-items-center g-2 gx-md-4">
                <div class="col" style="min-width: 0">
                    <template v-if="title">
                        <div class="d-none d-md-block">
                            <div class="h5 mb-0 text-truncate" :class="{ 'opacity-0': !showTitle }" style="transition: opacity .2s ease-in-out">
                                {{ title }}
                            </div>
                        </div>
                    </template>
                </div>
                <div class="col-auto">
                    <slot name="right"></slot>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import { getNavbarHeight } from "../util";
    import throttle from "lodash/throttle";

    export default {
        name: 'SharpActionBar',
        props: {
            container: Boolean,
        },
        data() {
            return {
                title: null,
                showTitle: false,
            }
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
            handleScroll() {
                this.title = document.querySelector('h1[data-top-bar-title]')?.innerText;
                this.showTitle = window.scrollY > 150;
            },
        },
        async mounted() {
            this.layout(this.$refs.bar.getBoundingClientRect());

            if(window.ResizeObserver) {
                (new ResizeObserver(entries => {
                    this.layout(entries[0].target.getBoundingClientRect());
                }))
                .observe(this.$refs.bar);
            }
            window.addEventListener('scroll', throttle(this.handleScroll, 100));
        }
    }
</script>
