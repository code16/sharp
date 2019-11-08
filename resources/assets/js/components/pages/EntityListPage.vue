<template>
    <div class="SharpEntityListPage">
        <div class="container">
            <template v-if="ready">
                <!-- Action bar is defined inside entity list component -->
                <SharpEntityList
                    :entity-key="entityKey"
                    module="entity-list"
                />
            </template>
        </div>
    </div>
</template>

<script>
    import isEqual from 'lodash/isEqual';
    import SharpEntityList from '../list/EntityList';
    import { mapGetters } from 'vuex';

    export default {
        name: 'SharpEntityListPage',
        components: {
            SharpEntityList,
        },
        data() {
            return {
                ready: false,
            }
        },
        watch: {
            'query': 'handleQueryChanged',
            '$route.query': 'init',
        },
        computed: {
            ...mapGetters('entity-list', [
                'query',
            ]),
            entityKey() {
                return this.$route.params.id;
            },
        },
        methods: {
            handleQueryChanged(query, oldQuery) {
                if(isEqual(query, oldQuery)) {
                    return;
                }
                this.$router.push({
                    query: { ...query }
                });
            },
            async init() {
                await this.$store.dispatch('entity-list/setQuery', { ...this.$route.query });
                this.ready = true;
            },
        },
        created() {
            this.init();
        },
    }
</script>