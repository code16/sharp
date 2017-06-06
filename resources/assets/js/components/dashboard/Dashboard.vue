<template>
    <div class="SharpDashboard">
        <template v-if="ready">
            <sharp-tabbed-layout :layout="layout">
                <template scope="tab">
                    <sharp-grid :rows="[tab.columns]">
                        <template scope="column">
                            <sharp-grid :rows="column.widgets">
                                <template scope="widgetLayout">
                                    <sharp-widget :widget-type="widgets[widgetLayout.key].type"
                                                  :widget-props="widgets[widgetLayout.key]"
                                                  :value="data[widgetLayout.key]">
                                    </sharp-widget>
                                </template>
                            </sharp-grid>
                        </template>
                    </sharp-grid>
                </template>
            </sharp-tabbed-layout>
        </template>
    </div>
</template>

<script>
    import TabbedLayout from '../TabbedLayout';
    import Grid from '../Grid';
    import Widget from './Widget';
    import DynamicView from '../DynamicViewMixin';

    import { testableDashboard } from '../../mixins/index';

    export default {
        name:'SharpDashboard',
        extends: DynamicView,

        mixins: [testableDashboard],

        components: {
            [TabbedLayout.name]:TabbedLayout,
            [Grid.name]:Grid,
            [Widget.name]:Widget
        },
        data() {
            return {
                widgets: null
            }
        },
        computed: {
            apiPath() {
                //TODO
            }
        },
        methods: {
            mount({layout, data, widgets}) {
                this.layout = layout;
                this.data = data || {};
                this.widgets = widgets;
            }
        },
        created() {
            this.get();
        }
    }
</script>