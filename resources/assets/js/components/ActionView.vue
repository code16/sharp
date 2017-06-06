<template>
    <div class="SharpActionView container">
        <component v-if="barComp" :is="barComp"></component>
        <slot></slot>
    </div>
</template>

<script>
    import ActionBars, { NameAssociation as actionBarCompName } from './action-bar/index';
    import EventBus from './EventBus';


    export default {
        name:'SharpActionView',
        components: ActionBars,

        provide() {
            return {
                actionsBus: new EventBus({name:'SharpActionsEventBus'}),
            }
        },

        props: {
            context:{
                type:String,
                required:true
            }
        },

        data() {
            return {
            }
        },
        computed: {
            barComp() {
                return actionBarCompName[this.context];
            }
        },
        methods: {
        },
    }
</script>