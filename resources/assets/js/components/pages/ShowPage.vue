<template>
    <div class="SharpShowPage">
        <div class="container">
            <template v-if="ready">
                <SharpActionBarShow
                    :can-edit="canEdit"
                    :form-url="formUrl"
                    @command="handleEntityCommandRequested"
                />

                <SharpEntityList
                    v-if="fields.pilots"
                    :entity-key="fields.pilots.entityListKey"
                    :show-create-button="fields.pilots.showCreateButton"
                    :show-reorder-button="fields.pilots.showReorderButton"
                    :show-search-field="fields.pilots.showSearchField"
                    module="show/entity-lists/pilot"
                    inline
                />
            </template>
        </div>
    </div>
</template>

<script>
    import EntityListModule from "../../store/modules/entity-list";
    import { BASE_URL } from "../../consts";
    import { mapState, mapGetters } from 'vuex';
    import SharpActionBarShow from "../action-bar/ActionBarShow";
    import SharpEntityList from '../list/EntityList';
    import SharpGrid from "../Grid";

    export default {
        components: {
            SharpActionBarShow,
            SharpEntityList,
            SharpGrid,
        },

        data() {
            return {
                ready: false,
            }
        },

        computed: {
            ...mapState('show', {
                entityKey: state => state.entityKey,
                instanceId: state => state.instanceId,
            }),
            ...mapGetters('show', [
                'canEdit',
                'fields'
            ]),
            formUrl() {
                return `${BASE_URL}/form/${this.entityKey}/${this.instanceId}`;
            },
        },

        methods: {
            async init() {
                await this.$store.dispatch('show/setEntityKey', this.$route.params.entityKey);
                await this.$store.dispatch('show/setInstanceId', this.$route.params.instanceId);
                await this.$store.dispatch('show/get');

                Object.values(this.fields).forEach(field => {
                    if(field.type === 'entityList') {
                        this.$store.registerModule(['show', 'entity-lists', field.entityListKey], EntityListModule);
                    }
                });

                this.ready = true;
            }
        },

        created() {
            this.init();
        },
    }
</script>