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
            <input type="text" class="form-control" placeholder="Search..." v-model="query" ref="input" @input="handleInput">

            <template v-if="results && results.length">
                <div class="mt-4">
                    <template v-for="group in results">
                        <section class="mb-4">
                            <h6>
                                <template v-if="group.icon">
                                    <i class="fa" :class="group.icon"></i>
                                </template>
                                {{ group.label }}
                            </h6>
                            <div class="list-group">
                                <template v-for="result in group.results">
                                    <a :href="result.link" class="list-group-item list-group-item-action">
                                        {{ result.label }}
                                        <div class="fs-8">
                                            {{ results.detail }}
                                        </div>
                                    </a>
                                </template>
                            </div>
                        </section>
                    </template>
                </div>
            </template>
        </Modal>
    </div>
</template>

<script>
    import { Modal } from "sharp-ui";
    import { lang } from "sharp";
    import debounce from "lodash/debounce";
    import { getSearchResults } from "../api";

    export default {
        components: {
            Modal
        },
        data() {
            return {
                modalVisible: false,
                query: '',
                results: null,
            }
        },
        methods: {
            lang,
            async getResults(query) {
                this.results = await getSearchResults({ query });
            },
            handleInput() {
                if(this.query.length > 2) {
                    this.debouncedGetResults(this.query);
                }
            },
        },
        created() {
            this.debouncedGetResults = debounce(this.getResults, 200);
        },
    }
</script>


