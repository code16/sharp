<template>
    <div class="SharpActionView container">
        <component v-if="barComp" :is="barComp"></component>
        <slot></slot>
    </div>
</template>

<script>
    import ActionBars, { NameAssociation as relativeActionBar } from './action-bar/index';
    import EventBus from './EventBus';

    export default {
        name:'SharpActionView',
        components: ActionBars,

        provide() {
            return {
                actionsBus: new EventBus({name:'SharpActionsEventBus'}),
            }
        },
        data() {
            return {
                barComp: null,
            }
        },
        methods: {
        },
        computed: {
            viewComp() {
                return this.$slots.default[0].componentInstance;
            },
        },
        mounted() {
            console.log(this);
            console.log(relativeActionBar);

            if(this.viewComp) {
                this.barComp = relativeActionBar[this.viewComp.$options.name];
            }
        }
    }
</script>