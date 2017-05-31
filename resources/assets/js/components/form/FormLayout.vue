<template>
    <div class="SharpFormLayout">
        <template v-if="layout.tabbed && layout.tabs.length>1">
            <b-tabs pills>
                <b-tab v-for="(tab,i) in tabs" :title="tab.titleComp.$el.outerHtml" :key="i">
                    <slot v-bind="tab"></slot>
                </b-tab>
            </b-tabs>
        </template>
        <template v-else>
            <div v-for="tab in layout.tabs">
                <slot v-bind="tab"></slot>
            </div>
        </template>
    </div>
</template>

<script>
    import bTabs from './vendor/bootstrap-vue/components/tabs'
    import bTab from './vendor/bootstrap-vue/components/tab'

    import Vue from 'vue';

    const Title = Vue.extend({
        template:`<span :class="{hasError:hasError}">{{title}}</span>`,
        props:['title', 'hasError']
    });

    export default {
        name:'SharpFormLayout',
        props : {
            layout: Object,
        },
        components: {
            bTab:{
                extends:bTab,
                provide() {
                    return {
                        $tab: this
                    }
                },
                methods: {
                    setError() {
                        this.$emit('error');
                    },
                    clearError() {
                        this.$emit('clear');
                    }
                },
                mounted() {
                    console.log(this);
                }
            },
            bTabs
        },
        computed: {
            tabs() {
                return this.layout.tabs.map(tab => {
                    tab.titleComp = new Title({ propsData: tab }).$mount();
                    return tab;
                })
            },
        }
    }
</script>