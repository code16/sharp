<template>
    <sharp-action-bar>
        <template slot="left">
            <span>{{ itemsCount }} {{ l('action_bar.list.items_count') }}</span>
        </template>
        <template slot="right">
            <sharp-text :value="search" @keyup.enter.native="emitAction('searchChanged',$event.target.value)"></sharp-text>
        </template>
    </sharp-action-bar>
</template>

<script>
    import ActionBar from './ActionBar';
    import ActionBarMixin from './ActionBarMixin';
    import { Localization, ActionEvents } from '../../mixins';

    import Text from '../form/fields/Text';

    export default {
        name: 'SharpActionBarList',
        components : {
            [ActionBar.name]: ActionBar,
            [Text.name]: Text
        },

        mixins: [ActionBarMixin, ActionEvents, Localization],

        data() {
            return {
                itemsCount: 0,
                search:''
            }
        },
        actions: {
            setup(config) {
                let { itemsCount } = config;
                this.itemsCount = itemsCount;
            },
            searchChanged(input) {
                this.search = input;
            }
        }
    }
</script>