import merge from 'lodash/merge';

import Vuex from 'vuex';
import SharpEntityListPage from '../../components/pages/EntityListPage.vue';
import entityListModule from '../../store/modules/entity-list';

import { shallowMount, createLocalVue } from '@vue/test-utils';

jest.mock('../../mixins/Localization');
jest.mock('../../components/DynamicViewMixin');
jest.mock('../../store/modules/entity-list');
jest.mock('../../consts', () => ({
    BASE_URL: 'BASE_URL'
}));

describe('EntityListPage', () => {
    function createWrapper({ storeModule={}, ...options }={}) {
        const localVue = createLocalVue();
        localVue.use(Vuex);
        const wrapper = shallowMount(SharpEntityListPage, {
            stubs: {
                'SharpDataList': `<div class="MOCKED_SharpDataList"> <slot name="item" :item="{}" /> </div>`,
                'SharpDataListRow': `<div class="MOCKED_SharpDataListRow" :url="url"> <slot name="append" /> </div>`,
                'SharpDropdown':`<div class="MOCKED_SharpDropdown"> <slot name="text"/> <slot /> </div>`,
                'SharpCommandsDropdown': `<div class="MOCKED_SharpCommandsDropdown"> <slot name="text" /> <slot /> </div>`
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
            },
            created() {
                jest.spyOn(this, 'init').mockImplementation();
            },
            store: new Vuex.Store({
                modules: {
                    'entity-list': merge(entityListModule, storeModule),
                }
            }),
            localVue,
            ...options,
        });
        wrapper.vm.$store.dispatch = jest.fn(()=>Promise.resolve());
        return wrapper;
    }

    function withDefaults(data) {
        return merge({
            layout: [],
            containers: {},
            data: {},
            config: {
                commands: {}
            },
            authorizations: {},
        }, data);
    }

    test('mount', ()=>{
        const wrapper = createWrapper();
        wrapper.setMethods({
            instanceFormUrl:()=>'instanceFormUrl',
        });
        wrapper.setData(withDefaults({}));
        wrapper.setData({ ready:true });
        expect(wrapper.html()).toMatchSnapshot();
    });

    test('mount with row actions', ()=>{
        const wrapper = createWrapper({
            computed: {
                hasActionsColumn: ()=>true,
            }
        });
        wrapper.setMethods({
            instanceFormUrl: ()=>'instanceFormUrl',
            instanceHasState: ()=>true,
            instanceHasCommands: ()=>true,
            instanceStateIconColor: ()=>'instanceStateIconColor',
            instanceStateLabel: ()=>'instanceStateLabel'
        });
        wrapper.setData(withDefaults({
            config: {
                state: {
                    values: [{ value:1, label:'state 1', color: '#000' }]
                }
            }
        }));
        wrapper.setData({ ready:true });
        expect(wrapper.html()).toMatchSnapshot();
    });

    describe('computed', () => {
        test('entityKey', () => {
            const wrapper = createWrapper({
                storeModule: {
                    state: {
                        entityKey: 'spaceship',
                    },
                },
            });
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
                    filterNextQuery:() => jest.fn(()=>({ filter:'nextQuery' })),
                }
            });
            wrapper.vm.handleFilterChanged({ key:'name' }, 'George');
            expect(wrapper.vm.filterNextQuery).toHaveBeenCalledWith({ filter:{ key:'name' }, value:'George' });
            expect(wrapper.vm.$router.push).toHaveBeenCalledWith({
                query: {
                    filter: 'nextQuery',
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

        test('handleReorderSubmitted', async () => {
            const wrapper = createWrapper();
            wrapper.vm.$route.params.id = 'spaceship';
            wrapper.setData({
                reorderedItems: [{ id:1 }],
                data: {}
            });
            wrapper.setMethods({
                instanceId: ()=>'id'
            });
            await wrapper.vm.handleReorderSubmitted();
            expect(wrapper.vm.$store.dispatch).toHaveBeenCalledWith('entity-list/reorder', { instances:['id'] });
            expect(wrapper.vm.data.items).toEqual([{ id:1 }]);
            expect(wrapper.vm.reorderedItems).toEqual(null);
            expect(wrapper.vm.reorderActive).toEqual(false);
        });

        test('handleEntityCommandRequested', () => {
            const wrapper = createWrapper();
            wrapper.setMethods({
                handleCommandRequested: jest.fn(),
                commandEndpoint: jest.fn(()=>'commandEndpoint'),
            });
            wrapper.vm.handleEntityCommandRequested({ key:'sync' });
            expect(wrapper.vm.handleCommandRequested).toHaveBeenCalledWith({ key:'sync' }, {
                endpoint: 'commandEndpoint',
            });
            expect(wrapper.vm.commandEndpoint).toHaveBeenCalledWith('sync');
        });

        test('handleCreateButtonClicked', () => {
            const wrapper = createWrapper();
            const locationHrefSpy = jest.spyOn(window.location, 'href', 'set');
            wrapper.setMethods({
                formUrl: jest.fn(()=>'formUrl')
            });
            wrapper.vm.handleCreateButtonClicked();
            expect(locationHrefSpy).toHaveBeenCalledWith('formUrl');

            locationHrefSpy.mockClear();
            wrapper.vm.handleCreateButtonClicked({ key:'form' });
            expect(wrapper.vm.formUrl).toHaveBeenCalledWith({ formKey:'form' });
            expect(locationHrefSpy).toHaveBeenCalledWith('formUrl');
        });

        test('instanceId', () => {
            const wrapper = createWrapper();
            wrapper.setData({
                config: {
                }
            });
            expect(wrapper.vm.instanceId({ id:3 })).toEqual(3);
            wrapper.setData({
                config: {
                    instanceIdAttribute: 'key'
                }
            });
            expect(wrapper.vm.instanceId({ key:3 })).toEqual(3);
        });

        test('instanceState', () => {
            const wrapper = createWrapper();
            wrapper.setMethods({
                instanceHasState: jest.fn(()=>false)
            });
            expect(wrapper.vm.instanceState({})).toEqual(null);
            wrapper.setMethods({
                instanceHasState: jest.fn(()=>true)
            });
            wrapper.setData({
                config: {
                    state: {
                    }
                }
            });
            expect(wrapper.vm.instanceState({ state:'verified' })).toEqual('verified');
            wrapper.setData({
                config: {
                    state: {
                        attribute: 'stateKey'
                    }
                }
            });
            expect(wrapper.vm.instanceState({ stateKey:'validated' })).toEqual('validated');
        });

        test('instanceHasState', () => {
            const wrapper = createWrapper();
            wrapper.setData({
                config: {
                }
            });
            expect(wrapper.vm.instanceHasState({})).toEqual(false);
            wrapper.setData({
                config: {
                    state: {
                        attribute: 'state',
                        values: [],
                    }
                }
            });
            expect(wrapper.vm.instanceHasState({})).toEqual(true);
        });

        test('instanceHasComands', () => {
            const wrapper = createWrapper();
            wrapper.setMethods({
                instanceCommands: jest.fn(() => [[]])
            });
            expect(wrapper.vm.instanceHasCommands({})).toEqual(false);
            wrapper.setMethods({
                instanceCommands: jest.fn(() => [[{ id:1 }], [{ id:5 }]])
            });
            expect(wrapper.vm.instanceHasCommands({})).toEqual(true);
        });

        test('instanceHasStateAuthorization', () => {
            const wrapper = createWrapper();
            wrapper.setMethods({
                instanceHasState: jest.fn(()=>false)
            });
            expect(wrapper.vm.instanceHasStateAuthorization({})).toEqual(false);
            wrapper.setMethods({
                instanceHasState: jest.fn(()=>true),
                instanceId: jest.fn(()=>1),
            });
            wrapper.setData({
                config: {
                    state: {
                    }
                }
            });
            expect(wrapper.vm.instanceHasStateAuthorization({})).toEqual(false);
            wrapper.setData({
                config: {
                    state: {
                        authorization: true
                    }
                }
            });
            expect(wrapper.vm.instanceHasStateAuthorization({})).toEqual(true);
            wrapper.setData({
                config: {
                    state: {
                        authorization: [2, 3]
                    }
                }
            });
            expect(wrapper.vm.instanceHasStateAuthorization({})).toEqual(false);
            wrapper.setData({
                config: {
                    state: {
                        authorization: [1, 2]
                    }
                }
            });
            expect(wrapper.vm.instanceHasStateAuthorization({})).toEqual(true);
        });

        test('instanceCommands', () => {
            const wrapper = createWrapper();
            wrapper.setMethods({
                instanceId: jest.fn(()=>1),
            });
            wrapper.setData({
                config: {
                    commands: {
                    }
                }
            });
            expect(wrapper.vm.instanceCommands({})).toEqual([]);
            wrapper.setData({
                config: {
                    commands: {
                        instance: [[{ authorization:[2, 3] }], [{ authorization:[1,2] }]]
                    }
                }
            });
            expect(wrapper.vm.instanceCommands({})).toEqual([[], [{ authorization:[1,2] }]]);
        });

        test('instanceForm', () => {
            const wrapper = createWrapper({
                computed: {
                    multiforms: ()=>[{ instances:[3,4] }, { instances:[1,2] }]
                }
            });
            wrapper.setMethods({
                instanceId: jest.fn(()=>1)
            });
            expect(wrapper.vm.instanceForm({})).toEqual({ instances:[1,2] });
        });

        test('instanceFormUrl', () => {
            let wrapper;
            wrapper = createWrapper({
                computed: {
                    hasMultiforms: ()=>false,
                }
            });
            wrapper.setMethods({
                instanceId: jest.fn(()=>1),
                formUrl: jest.fn(()=>'formUrl'),
                instanceHasViewAuthorization: jest.fn(()=>true),
            });
            expect(wrapper.vm.instanceFormUrl({})).toEqual('formUrl');
            expect(wrapper.vm.formUrl).toHaveBeenCalledWith({ instanceId:1 });

            wrapper.setMethods({
                instanceHasViewAuthorization: jest.fn(()=>false),
            });
            expect(wrapper.vm.instanceFormUrl({})).toBe(null);

            wrapper = createWrapper({
                computed: {
                    hasMultiforms: ()=>true
                }
            });
            wrapper.setMethods({
                instanceId: jest.fn(()=>1),
                formUrl: jest.fn(()=>'formUrl'),
                instanceHasViewAuthorization: jest.fn(()=>true),
            });
            wrapper.setMethods({
                instanceForm: jest.fn(()=>null),
            });
            expect(wrapper.vm.instanceFormUrl({})).toEqual('formUrl');
            expect(wrapper.vm.formUrl).toHaveBeenCalledWith({ formKey:undefined, instanceId:1 });

            wrapper.setMethods({
                instanceForm: jest.fn(()=>({ key:'form' })),
            });
            expect(wrapper.vm.instanceFormUrl({})).toEqual('formUrl');
            expect(wrapper.vm.formUrl).toHaveBeenCalledWith({ formKey:'form', instanceId:1 });
        });

        test('instanceHasViewAuthorization', ()=>{
            const wrapper = createWrapper();
            wrapper.setMethods({
                instanceId: jest.fn(()=>1)
            });
            wrapper.setData({
                authorizations: {
                }
            });
            expect(wrapper.vm.instanceHasViewAuthorization({})).toEqual(false);
            wrapper.setData({
                authorizations: {
                    view: true,
                }
            });
            expect(wrapper.vm.instanceHasViewAuthorization({})).toEqual(true);
            wrapper.setData({
                authorizations: {
                    view: [2],
                }
            });
            expect(wrapper.vm.instanceHasViewAuthorization({})).toEqual(false);
            wrapper.setData({
                authorizations: {
                    view: [2, 1],
                }
            });
            expect(wrapper.vm.instanceHasViewAuthorization({})).toEqual(true);
        });

        test('filterByKey', () => {
            const wrapper = createWrapper();
            wrapper.setData({
                config: {
                }
            });
            expect(wrapper.vm.filterByKey('name')).toBeUndefined();
            wrapper.setData({
                config: {
                    filters:[{ key:'age' }, { key:'name' }]
                }
            });
            expect(wrapper.vm.filterByKey('name')).toEqual({ key:'name' });
        });

        test('formUrl', () => {
            const wrapper = createWrapper({
                computed: {
                    entityKey:()=>'entityKey'
                }
            });
            expect(wrapper.vm.formUrl()).toEqual('BASE_URL/form/entityKey');
            expect(wrapper.vm.formUrl({ instanceId:'instanceId' })).toEqual('BASE_URL/form/entityKey/instanceId');
            expect(wrapper.vm.formUrl({ formKey:'formKey', instanceId:'instanceId' })).toEqual('BASE_URL/form/entityKey:formKey/instanceId');
        });

        test('handleCommandRequested', ()=>{
            const wrapper = createWrapper();
            wrapper.setMethods({
                sendCommand: jest.fn()
            });
            wrapper.vm.handleCommandRequested('command', { endpoint:'endpoint' });
            expect(wrapper.vm.sendCommand).toHaveBeenCalledWith('command', expect.objectContaining({
                postCommand: expect.any(Function),
                postForm: expect.any(Function),
                getFormData: expect.any(Function),
            }));
        });

        test('commandEndpoint', ()=>{
            const wrapper = createWrapper({
                computed: {
                    apiPath:()=>'apiPath'
                }
            });
            expect(wrapper.vm.commandEndpoint('commandKey')).toEqual('apiPath/command/commandKey');
            expect(wrapper.vm.commandEndpoint('commandKey', 'instanceId')).toEqual('apiPath/command/commandKey/instanceId');
        });

        test('init', ()=>{
            const wrapper = createWrapper();
            wrapper.setData(withDefaults());
            wrapper.vm.$route.params.id = 'entityKey';
            wrapper.vm.init.mockRestore();
            wrapper.vm.init();
            expect(wrapper.vm.$store.dispatch).toHaveBeenCalledWith('entity-list/setEntityKey', 'entityKey');
        });
    });
});