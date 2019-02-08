import Vue from 'vue';
import VueRouter from 'vue-router';
import SharpEntityListPage from '../../components/pages/EntityListPage.vue';

import merge from 'lodash/merge';

import { MockI18n } from "../utils";
import { shallowMount } from '@vue/test-utils';

jest.mock('../../components/DynamicViewMixin');

describe('EntityListPage', () => {
    Vue.use(MockI18n);
    MockI18n.mockLangFunction();

    function createWrapper({ ...options }={}) {
        const wrapper = shallowMount(SharpEntityListPage, {
            extends: options,
            stubs: {
                'SharpDataList': `<div class="MOCKED_SharpDataList"> <slot name="item" :item="{}" /> </div>`,
                'SharpDataListRow': `<div class="MOCKED_SharpDataListRow"> <slot name="append" /> </div>`,
                'SharpDropdown':`<div class="MOCKED_SharpDropdown"> <slot /> </div>`,
                'SharpDropdownItem': `<div class="MOCKED_SharpDropdownItem"> <slot /> </div>`,
                'SharpCommandsDropdown': `<div class="MOCKED_SharpCommandsDropdown"> <slot /> </div>`,
                'SharpStateIcon': `<div class="MOCKED_SharpStateIcon"> <slot /> </div>`
            },
            mocks: {
                $route: {
                    params: {
                        id: 'spaceship'
                    },
                    query: {}
                },
                $router: {
                    push: jest.fn()
                },
            }
        });
        return wrapper;
    }

    function withDefaults(data) {
        return merge({
            config: {
                commands: {}
            }
        }, data);
    }

    test('can mount entity list', ()=>{
        const wrapper = createWrapper();
        wrapper.setData(withDefaults({
            containers: {
                title: {
                    key: 'title',
                    label: 'Titre'
                }
            },
            layout: [{
                key: 'title',
                size: 6,
                sizeXS: 12
            }],
            data: {
                items : [{ id: 1, title: 'Super title' }]
            },
            config: {
                filters: [],
                instanceIdAttribute: 'id'
            },
            authorizations:{
                view: true,
                update: true
            }
        }));
        wrapper.setData({ ready:true });
        expect(wrapper.html()).toMatchSnapshot();
    });

    describe('computed', () => {
        test('entityKey', () => {
            const wrapper = createWrapper();
            wrapper.vm.$route.params.id = 'spaceship';
            expect(wrapper.vm.entityKey).toEqual('spaceship');
        });

        test('hasMultiforms', () => {
            const wrapper = createWrapper();
            expect(wrapper.vm.hasMultiforms).toEqual(false);
            wrapper.setData({
                forms: {
                    custom: {}
                }
            });
            expect(wrapper.vm.hasMultiforms).toEqual(true);
        });

        test('apiParams', () => {
            const wrapper = createWrapper();
            wrapper.vm.$route.query = {
                search: 'search',
            };
            expect(wrapper.vm.apiParams).toEqual({
                search:'search',
            });
        });

        test('apiPath', () => {
            const wrapper = createWrapper({
                computed: {
                    entityKey:()=>'entity-key'
                }
            });
            expect(wrapper.vm.apiPath).toEqual('list/entity-key');
        });

        test('filterParams', () => {
            const wrapper = createWrapper();
            wrapper.setData({
                filtersValue: {
                    'age': 30,
                    'type': null
                }
            });
            expect(wrapper.vm.filterParams).toEqual({
                'filter_age': 30
            });
        });



        test('itemsCount', () => {
            const wrapper = createWrapper();
            wrapper.setData({
                data: { items: null }
            });
            expect(wrapper.vm.itemsCount).toEqual(0);
            wrapper.setData({
                data: { items: [{}] }
            });
            expect(wrapper.vm.itemsCount).toEqual(1);
        });

        test('filters', () => {
            const wrapper = createWrapper();
            wrapper.setData({
                config: {
                    filters: [{ key:'name' }]
                }
            });
            expect(wrapper.vm.filters).toEqual([{ key:'name' }]);
        });

        test('allowedEntityCommands', () => {
            const wrapper = createWrapper();
            wrapper.setData({
                config: {
                    commands: {}
                }
            });
            expect(wrapper.vm.allowedEntityCommands).toEqual([]);
            wrapper.setData({
                config: {
                    commands: {
                        entity: [
                            [{ key:'A', authorization:true }],
                            [{ key:'B', authorization:false }],
                        ]
                    }
                }
            });
            expect(wrapper.vm.allowedEntityCommands).toEqual([
                [{ key:'A', authorization:true }], []
            ]);
        });

        test('multiforms', () => {
            const wrapper = createWrapper();
            expect(wrapper.vm.multiforms).toBe(null);
            wrapper.setData({
                forms: {
                    custom: { key:'custom' }
                }
            });
            expect(wrapper.vm.multiforms).toEqual([{ key:'custom' }]);
        });

        test('canCreate', () => {
            const wrapper = createWrapper();
            wrapper.setData({
                authorizations: {}
            });
            expect(wrapper.vm.canCreate).toEqual(false);
            wrapper.setData({
                authorizations: {
                    create: true,
                }
            });
            expect(wrapper.vm.canCreate).toEqual(true);
        });

        test('canReorder', () => {
            const wrapper = createWrapper();
            const reorderableConfig = {
                config: { reorderable: true, },
                authorizations: { update: true },
                data: {
                    items: [{ id:1 }, { id: 2 }]
                }
            };

            wrapper.setData(reorderableConfig);
            expect(wrapper.vm.canReorder).toBe(true);

            wrapper.setData(merge(reorderableConfig, {
                config: { reorderable: false },
            }));
            expect(wrapper.vm.canReorder).toBe(false);

            wrapper.setData(merge(reorderableConfig, {
                authorizations: { update: false },
            }));
            expect(wrapper.vm.canReorder).toBe(false);

            wrapper.setData(merge(reorderableConfig, {
                data: {
                    items: [{ id:1 }]
                }
            }));
            expect(wrapper.vm.canReorder).toBe(false);
        });

        test('canSearch', () => {
            const wrapper = createWrapper();
            wrapper.setData({
                config: {}
            });
            expect(wrapper.vm.canSearch).toBe(false);
            wrapper.setData({
                config: {
                    searchable: true,
                }
            });
            expect(wrapper.vm.canSearch).toBe(true);
        });

        test('items', ()=>{
            const wrapper = createWrapper();
            wrapper.setData({
                data: {
                    items: [{ id:1 }]
                }
            });
            expect(wrapper.vm.items).toEqual([{ id:1 }]);
        });

        test('columns', ()=>{
            const wrapper = createWrapper();
            wrapper.setData({
                layout: [{ key:'name', size:4 }],
                containers: {
                    name: {
                        label: 'Name',
                    }
                }
            });
            expect(wrapper.vm.columns).toEqual([{ key:'name', label:'Name', size:4 }]);
        });

        test('paginated', ()=>{
            const wrapper = createWrapper();
            wrapper.setData({
                config: {}
            });
            expect(wrapper.vm.paginated).toEqual(false);
            wrapper.setData({
                config: {
                    paginated: true
                }
            });
            expect(wrapper.vm.paginated).toEqual(true);
        });

        test('totalCount', ()=>{
            const wrapper = createWrapper();
            wrapper.setData({
                data: {
                    totalCount: 10
                }
            });
            expect(wrapper.vm.totalCount).toBe(10);
        });

        test('pageSize', ()=>{
            const wrapper = createWrapper();
            wrapper.setData({
                data: {
                    pageSize: 5
                }
            });
            expect(wrapper.vm.pageSize).toBe(5);
        });
    });

    describe('methods',()=>{
        test('handleSearchChanged', ()=>{
            const wrapper = createWrapper();
            wrapper.vm.handleSearchChanged('search');
            expect(wrapper.vm.search).toEqual('search');
        });

        test('handleSearchSubmitted', ()=>{
            const wrapper = createWrapper();
            wrapper.setData({
                search: 'search'
            });
            wrapper.vm.handleSearchSubmitted();
            expect(wrapper.vm.$router.push).toHaveBeenCalledWith({ query: { search:'search' } })
        });

        test('handleFilterChanged', ()=>{
            const wrapper = createWrapper({
                computed: {
                    filterParams:()=>({ filter_name:'George' })
                }
            });
            wrapper.vm.handleFilterChanged('name', 'George');
            expect(wrapper.vm.filtersValue).toEqual({
                name: 'George'
            });
            expect(wrapper.vm.$router.push).toHaveBeenCalledWith({
                query: {
                    filter_name: 'George',
                },
            });
        });

        test('handleReorderButtonClicked', () => {
            const wrapper = createWrapper();
            const items = [{ id:1 }];
            wrapper.setData({
                data: {
                    items
                }
            });
            wrapper.vm.handleReorderButtonClicked();
            expect(wrapper.vm.reorderActive).toBe(true);
            expect(wrapper.vm.reorderedItems).toEqual([{ id:1 }]);

            wrapper.vm.handleReorderButtonClicked();
            expect(wrapper.vm.reorderActive).toBe(false);
            expect(wrapper.vm.reorderedItems).toEqual(null);
        });
    });
});