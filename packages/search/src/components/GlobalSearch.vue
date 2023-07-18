<template>
    <div>
        <button class="btn d-inline-flex btn-sm btn-outline-light border-0" @click="modalVisible=true">
            <!-- heroicons: solid/20/search -->
            <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
            </svg>
        </button>

        <Modal :visible.sync="modalVisible" hide-footer @shown="$refs.input.focus()">
            <template v-slot:title>
                {{ lang('action_bar.list.search.placeholder') }}
            </template>

            <div class="position-relative mb-4">
                <input type="text" class="form-control pe-4.5" placeholder="Search..." v-model="query" ref="input" @input="handleInput">
                <template v-if="loading">
                    <Loading class="position-absolute top-50 translate-middle-y" style="right: .5rem" small />
                </template>
            </div>

            <template v-if="error">
                <div class="alert alert-danger" role="alert">
                    {{ error }}
                </div>
            </template>
            <template v-else-if="results && results.length && query">
                <div class="mt-4.5">
                    <template v-for="group in results">
                        <section class="mb-4.5">
                            <template v-if="group.label">
                                <h6 class="d-flex mb-3">
                                    <template v-if="group.icon">
                                        <i class="fa fa-fw me-2" :class="group.icon"></i>
                                    </template>
                                    {{ group.label }}
                                </h6>
                            </template>
                            <template v-if="group.results && group.results.length">
                                <div class="list-group">
                                    <template v-for="result in group.results">
                                        <a :href="result.link" class="list-group-item fs-7 list-group-item-action">
                                            <div v-html="highlight(result.label)"></div>
                                            <div class="fs-8 text-muted" v-html="result.detail"></div>
                                        </a>
                                    </template>
                                </div>
                            </template>
                            <template v-else>
                                <div class="text-muted fs-7">
                                    {{ lang('entity_list.empty_text') }}
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
        data() {
            return {
                modalVisible: false,
                query: '',
                results: null,
                error: null,
                loading: false,
            }
        },
        methods: {
            lang,
            async getResults(query) {
                const loadingTimeout = setTimeout(() => {
                    this.loading = true;
                }, 200);
                try {
                    this.results = await getSearchResults({ query });
                } catch(error) {
                    if(error.response?.status === 422) {
                        this.error = error.response.data;
                    } else {
                        this.error = lang('modals.error.message');
                    }
                } finally {
                    clearTimeout(loadingTimeout);
                    this.loading = false;
                }
            },
            handleInput() {
                if(this.query?.length) {
                    this.debouncedGetResults(this.query);
                }
            },
            highlight(text) {
                if(this.query?.length) {
                    return text.replace(new RegExp(this.query, 'gi'), match => {
                        return `<mark class="px-0">${match}</mark>`;
                    });
                }
                return text;
            },
            handleWindowKeyup(e) {
                if(e.key === '/' && !e.ctrlKey && !e.metaKey) {
                    if (e.target.isContentEditable || /^(?:input|textarea|select)$/i.test(e.target.tagName)) {
                        return;
                    }
                    e.preventDefault();
                    this.modalVisible = true;
                }
            },
        },
        created() {
            this.debouncedGetResults = debounce(this.getResults, 200);
        },
        mounted() {
            document.addEventListener("keyup", this.handleWindowKeyup);
        },
        destroyed() {
            document.removeEventListener("keyup", this.handleWindowKeyup);
        }
    }
</script>


