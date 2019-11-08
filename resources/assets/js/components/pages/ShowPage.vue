<template>
    <div class="SharpShowPage">
        <div class="container">
            <template v-if="ready">
                <SharpActionBarShow
                    :can-edit="canEdit"
                    :form-url="formUrl"
                    @command="handleEntityCommandRequested"
                />

                <template v-for="section in layout.sections">
                    <SharpGrid :rows="[section.columns]">
                        <template slot-scope="fieldsLayout">
                            <SharpGrid :rows="fieldsLayout.fields">
                                <template slot-scope="fieldLayout">
                                    <SharpShowField
                                        :options="fieldOptions(fieldLayout)"
                                        :value="fieldValue(fieldLayout)"
                                    />
                                </template>
                            </SharpGrid>
                        </template>
                    </SharpGrid>
                </template>

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
    import SharpShowField from '../show/Field';

    export default {
        components: {
            SharpActionBarShow,
            SharpEntityList,
            SharpGrid,
            SharpShowField,
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
                'fields',
                'layout',
                'data',
            ]),

            formUrl() {
                return `${BASE_URL}/form/${this.entityKey}/${this.instanceId}`;
            },
        },

        methods: {
            fieldOptions(layout) {
                console.log(layout);
                return this.fields[layout.key];
            },
            fieldValue(layout) {
                return this.data[layout.key];
            },
            handleEntityCommandRequested() {

            },
            async init() {
                await this.$store.dispatch('show/setEntityKey', this.$route.params.entityKey);
                await this.$store.dispatch('show/setInstanceId', this.$route.params.instanceId);
                await this.$store.dispatch('show/get');

                this.ready = true;
            }
        },

        created() {
            this.init();
        },
    }
</script>