<template>
    <nav class="SharpLeftNav SharpLeftNav--collapseable" :class="`SharpLeftNav--${state}`"
         role="navigation" aria-label="Menu Sharp" @click="collapsed && (collapsed=false)">
        <slot></slot>
        <div class="SharpLeftNav__collapse" @click.stop="collapsed = !collapsed">
            <a class="SharpLeftNav__collapse-link" href="#" @click.prevent>
                <svg class="SharpLeftNav__collapse-arrow" width="8" height="12" viewBox="0 0 8 12" fill-rule="evenodd">
                    <path d="M7.5 10.6L2.8 6l4.7-4.6L6.1 0 0 6l6.1 6z"></path>
                </svg>
            </a>
        </div>
        <div class="hidden-md-down" ref="testViewport"></div>
    </nav>
</template>

<script>
    export default {
        name: 'SharpLeftNav',
        data() {
            return {
                collapsed: false,
                state: 'expanded'
            }
        },
        watch: {
            collapsed: {
                immediate: true,
                handler(val) {
                    this.state = val ? 'collapsing' : 'expanding';
                    setTimeout(_=>{
                        this.state= val ? 'collapsed' : 'expanded';
                        this.$root.$emit('setClass', 'leftNav--opened', !this.collapsed);
                    }, 250);
                }
            }
        },
        computed: {
            viewportSmall() {
                let { offsetWidth, offsetHeight } = this.$refs.testViewport;
                return !offsetWidth && !offsetHeight;
            }
        },
        mounted() {
            this.collapsed = this.viewportSmall;
        }
    }
</script>