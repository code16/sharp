<template>
    <div>
        <button class="btn d-inline-flex btn-sm btn-outline-light border-0" @click="open">
            <!-- heroicons: solid/20/search -->
            <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
            </svg>
        </button>

        <Modal
            :visible.sync="modalVisible"
            :no-close-on-backdrop="false"
            :no-enforce-focus="false"
            hide-footer
            body-class="pb-5"
            @shown="$refs.input.focus()"
        >
            <template v-slot:title>
                {{ lang('action_bar.list.search.placeholder') }}
            </template>

            <div class="position-relative">
                <input type="text" class="form-control pe-4.5" :placeholder="placeholder" v-model="query" ref="input" @input="handleInput">
                <template v-if="loading">
                    <Loading class="position-absolute top-50 translate-middle-y" style="right: .5rem" small />
                </template>
            </div>

            <template v-if="visibleResultSets.length && query">
                <div class="mt-4.5 mb-n4.5">
                    <template v-for="resultSet in visibleResultSets">
                        <section class="mb-4.5">
                            <template v-if="resultSet.label">
                                <h6 class="d-flex mb-3">
                                    <template v-if="resultSet.icon">
                                        <i class="fa fa-fw me-2" :class="resultSet.icon"></i>
                                    </template>
                                    {{ resultSet.label }}
                                </h6>
                            </template>
                            <template v-if="(resultSet.validationErrors || []).length">
                                <div class="text-danger fs-7">
                                    <template v-for="error in resultSet.validationErrors">
                                        <div>{{ error }}</div>
                                    </template>
                                </div>
                            </template>
                            <template v-if="(resultSet.results || []).length">
                                <div class="list-group">
                                    <template v-for="result in resultSet.results">
                                        <a :href="result.link" class="list-group-item fs-7 list-group-item-action">
                                            <div v-html="highlight(result.label)"></div>
                                            <template v-if="result.detail">
                                                <div class="fs-8 text-muted" v-html="highlight(result.detail)"></div>
                                            </template>
                                        </a>
                                    </template>
                                </div>
                            </template>
                            <template v-else-if="!(resultSet.validationErrors || []).length">
                                <div class="text-muted fs-7">
                                    {{ resultSet.emptyStateLabel || lang('entity_list.empty_text') }}
                                </div>
                            </template>
                        </section>
                    </template>
                </div>
            </template>
        </Modal>
    </div>
</template>

<script>
    import { Modal, Loading } from "sharp-ui";
    import { lang } from "sharp";
    import debounce from "lodash/debounce";
    import { getSearchResults } from "../api";

    export default {
        components: {
            Modal,
            Loading,
        },
        props: {
            placeholder: String,
        },
        data() {
            return {
                query: '',
                resultSets: null,
                loading: false,
                modalVisible: false,
            }
        },
        computed: {
            visibleResultSets() {
                return (this.resultSets ?? [])
                    .filter(resultSet =>
                        resultSet.results?.length ||
                        resultSet.showWhenEmpty ||
                        resultSet.validationErrors?.length
                    );
            },
        },
        methods: {
            lang,
            async getResults(query) {
                if(!query?.length) {
                    return;
                }
                const loadingTimeout = setTimeout(() => {
                    this.loading = true;
                }, 200);
                try {
                    this.resultSets = await getSearchResults({ query });
                } finally {
                    clearTimeout(loadingTimeout);
                    this.loading = false;
                }
            },
            open() {
                this.modalVisible = true;
                this.query = '';
                this.resultSets = null;
            },
            handleInput() {
                this.debouncedGetResults(this.query);
            },
            highlight(text) {
                if(this.query?.length) {
                    return text.replace(new RegExp(this.query, 'gi'), match => {
                        return `<mark class="px-0">${match}</mark>`;
                    });
                }
                return text;
            },
            handleWindowKeydown(event) {
                const isContentEditable = event.target.isContentEditable || /^(?:input|textarea|select)$/i.test(event.target.tagName);

                if(event.key?.toLowerCase() === 'k' && (event.metaKey || event.ctrlKey) // Cmd+k
                    || !isContentEditable && event.key === '/'
                ) {
                    event.preventDefault();
                    this.open();
                }
            },
        },
        created() {
            this.debouncedGetResults = debounce(this.getResults, 200);
        },
        mounted() {
            window.addEventListener('keydown', this.handleWindowKeydown);
        },
        destroyed() {
            window.removeEventListener('keydown', this.handleWindowKeydown);
        }
    }
</script>


