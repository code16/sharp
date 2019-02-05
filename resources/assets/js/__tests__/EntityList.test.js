import Vue from 'vue';
import EntityList from '../components/list/EntityList.vue';
import Dropdown from '../components/dropdown/Dropdown.vue';
import DropdownItem from '../components/dropdown/DropdownItem.vue';
import CommandsDropdown from '../components/list/CommandsDropdown.vue';
import StateIcon from '../components/list/StateIcon.vue';
import Modal from '../components/Modal.vue';

import {mockChildrenComponents, mockSFC} from "./utils/mockSFC";


import moxios from 'moxios';
import {MockInjections, MockI18n, wait } from "./utils";
import { setter, mockProperty, unmockProperty } from "./utils/mock-utils";
import { nextRequestFulfilled } from './utils/moxios-utils';
import HTMLElementsSerializer from './utils/htmlElementsSnapshotSerializer';
import { createWrapper } from '@vue/test-utils';


jest.mock('../helpers/querystring', ()=>({
    parse: jest.fn(),
    serialize: jest.fn(()=>'{{serialized}}')
}));

function mockComputed(vm, computedName, value) {
    Object.defineProperty(vm, computedName, {
        get:()=>value
    });
}

describe('entity-list', ()=>{
    Vue.use(MockI18n);
    MockI18n.mockLangFunction();

    Vue.component('sharp-entity-list', mockChildrenComponents(EntityList, {
        customs: {
            SharpCommandsDropdown: CommandsDropdown,
            [Dropdown.name]: {
                template: `<div class="MOCKED_SHARP_DROPDOWN" :class="{ '[[ isDisabled ]]':disabled }">
                            <div class="SLOT:text"><slot name="text"></slot></div>
                            <slot></slot>
                        </div>`
            },
            [DropdownItem.name]: {
                template: '<div class="MOCKED_SHARP_DROPDOWN_ITEM"><slot></slot></div>'
            },
            [Modal.name]: {
                template: '<div class="MOCKED_SHARP_MODAL"><slot></slot></div>'
            },
            [StateIcon.name]: {
                template: '<span class="MOCKED_STATE_ICON"></span>'
            },
            'Draggable': {
                template: '<div id="MOCKED_VUE_DRAGGALBE"><slot></slot></div>'
            },
        }
    }));

    const oldDelay = moxios.delay;
    beforeAll(() => moxios.delay = 10);
    afterAll(() => moxios.delay = oldDelay);

    beforeEach(()=>{
        document.body.innerHTML = `
            <div id="app">
                <sharp-entity-list entity-key="spaceship"></sharp-entity-list>
            </div>
        `;
    });
    afterEach(()=>{
        moxios.uninstall();
    });

    test('can mount entity list', async ()=>{
        await createVm();

        await nextRequestFulfilled({
            status: 200,
            response: {
                containers: {
                    title: {
                        key: 'title',
                        label: 'Titre'
                    }
                },
                layout:[{
                    key: 'title',
                    size: 6,
                    sizeXS: 12
                }],
                data:{
                    items : [{ id: 1, title: 'Super title' }]
                },
                config:{
                    filters:[],
                    instanceIdAttribute: 'id'
                },
                authorizations:{
                    view: true,
                    update: true
                }
            }
        });

        expect(document.body.innerHTML).toMatchSnapshot();
    });

    test('can mount "disabled all" entity list', async ()=>{
        let $entityList = await createVm();

        await nextRequestFulfilled({
            status: 200,
            response: {
                containers: {
                    title: {
                        key: 'title',
                        label: 'Titre'
                    }
                },
                layout:[{
                    key: 'title',
                    size: 6,
                    sizeXS: 12
                }],
                data:{
                    items : [{ id: 1, title: 'Should be disabled' }, { id: 2, title: 'Should be disabled' }]
                },
                config:{
                    filters:[],
                    instanceIdAttribute: 'id'
                },
                authorizations:{
                    view: false,
                    update: true
                }
            }
        });

        expect(document.body.innerHTML).toMatchSnapshot();
    });

    test('can mount "disabled one" entity list', async ()=>{
        await createVm();

        await nextRequestFulfilled({
            status: 200,
            response: {
                containers: {
                    title: {
                        key: 'title',
                        label: 'Titre'
                    }
                },
                layout:[{
                    key: 'title',
                    size: 6,
                    sizeXS: 12
                }],
                data:{
                    items : [{ id: 1, title: 'Super title' }, { id: 2, title: 'Should be disabled' }]
                },
                config:{
                    filters:[],
                    instanceIdAttribute: 'id'
                },
                authorizations:{
                    view: [1],
                    update: true
                }
            }
        });

        expect(document.body.innerHTML).toMatchSnapshot();
    });

    test('can mount "sortable" entity list', async ()=>{
        await createVm();

        await nextRequestFulfilled({
            status: 200,
            response: {
                containers: {
                    title: {
                        key: 'title',
                        label: 'Titre',
                        sortable: true
                    }
                },
                layout:[{
                    key: 'title',
                    size: 6,
                    sizeXS: 12
                }],
                data:{
                    items : [{ id: 1, title: 'Super title' }]
                },
                config:{
                    filters:[],
                    instanceIdAttribute: 'id'
                },
                authorizations:{
                    view: true,
                    update: true
                }
            }
        });

        expect(document.body.innerHTML).toMatchSnapshot();
    });

    test('can mount "sortable selected" entity list', async ()=>{
        await createVm();

        await nextRequestFulfilled({
            status: 200,
            response: {
                containers: {
                    title: {
                        key: 'title',
                        label: 'Titre',
                        sortable: true
                    }
                },
                layout:[{
                    key: 'title',
                    size: 6,
                    sizeXS: 12
                }],
                data:{
                    items : [{ id: 1, title: 'Super title' }]
                },
                config:{
                    filters:[],
                    instanceIdAttribute: 'id',
                    defaultSort: 'title'
                },
                authorizations:{
                    view: true,
                    update: true
                }
            }
        });

        expect(document.body.innerHTML).toMatchSnapshot();
    });

    test('can mount "sortable selected ascending" entity list', async ()=>{
        await createVm();

        await nextRequestFulfilled({
            status: 200,
            response: {
                containers: {
                    title: {
                        key: 'title',
                        label: 'Titre',
                        sortable: true
                    }
                },
                layout:[{
                    key: 'title',
                    size: 6,
                    sizeXS: 12
                }],
                data:{
                    items : [{ id: 1, title: 'Super title' }]
                },
                config:{
                    filters:[],
                    instanceIdAttribute: 'id',
                    defaultSort: 'title',
                    defaultSortDir: 'asc'
                },
                authorizations:{
                    view: true,
                    update: true
                }
            }
        });

        expect(document.body.innerHTML).toMatchSnapshot();
    });

    test('can mount "empty" entity list', async ()=>{
        await createVm();

        await nextRequestFulfilled({
            status: 200,
            response: {
                containers: {
                    title: {
                        key: 'title',
                        label: 'Titre'
                    }
                },
                layout:[{
                    key: 'title',
                    size: 6,
                    sizeXS: 12
                }],
                data:{
                    items : []
                },
                config:{
                    filters:[],
                    instanceIdAttribute: 'id'
                },
                authorizations:{
                    view: true,
                    update: true
                }
            }
        });

        expect(document.body.innerHTML).toMatchSnapshot();
    });

    test('can mount "reordering" entity list', async ()=>{
        let $entityList = await createVm();

        await nextRequestFulfilled({
            status: 200,
            response: {
                containers: {
                    title: {
                        key: 'title',
                        label: 'Titre'
                    }
                },
                layout:[{
                    key: 'title',
                    size: 6,
                    sizeXS: 12
                }],
                data:{
                    items : [{ id: 1, title: 'First title' }, { id: 2, title: 'Second title' }]
                },
                config:{
                    filters:[],
                    reorderable: true,
                    instanceIdAttribute: 'id'
                },
                authorizations:{
                    view: true,
                    update: true
                }
            }
        });

        $entityList.actionsBus.$emit('toggleReorder');

        await Vue.nextTick();

        expect(document.body.innerHTML).toMatchSnapshot();
    });

    test('can mount "reordering reordered" entity list', async ()=>{
        let $entityList = await createVm();

        await nextRequestFulfilled({
            status: 200,
            response: {
                containers: {
                    title: {
                        key: 'title',
                        label: 'Titre'
                    }
                },
                layout:[{
                    key: 'title',
                    size: 6,
                    sizeXS: 12
                }],
                data:{
                    items : [{ id: 1, title: 'First title' }, { id: 1, title: 'Second title' }]
                },
                config:{
                    filters:[],
                    reorderable: true,
                    instanceIdAttribute: 'id'
                },
                authorizations:{
                    view: true,
                    update: true
                }
            }
        });

        $entityList.actionsBus.$emit('toggleReorder');
        let [item1, item2] = $entityList.reorderedItems;
        $entityList.reorderedItems = [item2, item1];

        await Vue.nextTick();

        expect(document.body.innerHTML).toMatchSnapshot();
    });

    test('can mount "with html" entity list', async ()=>{
        await createVm();

        await nextRequestFulfilled({
            status: 200,
            response: {
                containers: {
                    title: {
                        key: 'title',
                        label: 'Titre',
                        html : true
                    }
                },
                layout:[{
                    key: 'title',
                    size: 6,
                    sizeXS: 12
                }],
                data:{
                    items : [{ id: 1, title: '<span>HTML content</span>'}]
                },
                config:{
                    filters:[],
                    instanceIdAttribute: 'id'
                },
                authorizations:{
                    view: true,
                    update: true
                }
            }
        });

        expect(document.body.innerHTML).toMatchSnapshot();
    });

    test('can mount "hide on XS" entity list', async ()=>{
        await createVm();

        await nextRequestFulfilled({
            status: 200,
            response: {
                containers: {
                    title: {
                        key: 'title',
                        label: 'Titre',
                    }
                },
                layout:[{
                    key: 'title',
                    size: 6,
                    sizeXS: 12
                }],
                data:{
                    items : [{ id: 1, title: 'Super title'}]
                },
                config:{
                    filters:[],
                    instanceIdAttribute: 'id'
                },
                authorizations:{
                    view: true,
                    update: true
                }
            }
        });

        expect(document.body.innerHTML).toMatchSnapshot();
    });

    test('can mount "with commands" entity list', async ()=>{
        await createVm();

        await nextRequestFulfilled({
            status: 200,
            response: {
                containers: {
                    title: {
                        key: 'title',
                        label: 'Titre',
                    }
                },
                layout:[{
                    key: 'title',
                    size: 6,
                    sizeXS: 12
                }],
                data:{
                    items : [
                        { id: 1, title: 'First title'},
                        { id: 2, title: 'Second title'},
                        { id: 3, title: 'Third title'} // third row should have placeholder command dropdown (no items)
                    ]
                },
                config:{
                    filters:[],
                    commands:{
                        instance: [
                            [{
                                type: "instance",
                                key: "validate",
                                label: "Should be visible in first title",
                                authorization: [1]
                            }, {
                                type: "instance",
                                key: "delete",
                                label: "Should be visible in second title",
                                authorization: [2]
                            }]
                        ],
                    },
                    instanceIdAttribute: 'id'
                },
                authorizations:{
                    view: true,
                    update: true
                }
            }
        });

        expect(document.body.innerHTML).toMatchSnapshot();
    });

    test('can mount "with state" entity list', async ()=>{
        await createVm();

        await nextRequestFulfilled({
            status: 200,
            response: {
                containers: {
                    title: {
                        key: 'title',
                        label: 'Titre',
                    }
                },
                layout:[{
                    key: 'title',
                    size: 6,
                    sizeXS: 12
                }],
                data:{
                    items : [
                        { id: 1, title: 'First title', visibility:'active' }, // should have state dropdown
                        { id: 2, title: 'Second title', visibility:'inactive'},
                    ]
                },
                config:{
                    filters:[],
                    state:{
                        attribute: 'visibility',
                        values: [
                            {
                                value: "active",
                                label: "Visible",
                                color: "green"
                            },
                            {
                                value: "inactive",
                                label: "Not visible",
                                color: "sharp_grey"
                            }
                        ],
                        authorization: [1]
                    },
                    instanceIdAttribute: 'id'
                },
                authorizations:{
                    view: true,
                    update: true
                }
            }
        });

        expect(document.body.innerHTML).toMatchSnapshot();
    });

    test('can mount "with pagination" entity list', async ()=>{
        await createVm();

        await nextRequestFulfilled({
            status: 200,
            response: {
                containers: {
                    title: {
                        key: 'title',
                        label: 'Titre',
                    }
                },
                layout:[{
                    key: 'title',
                    size: 6,
                    sizeXS: 12,
                }],
                data:{
                    items : [],
                    totalCount: 20,
                    pageSize: 10
                },
                config:{
                    filters:[],
                    paginated: true,
                    instanceIdAttribute: 'id'
                },
                authorizations:{
                    view: true,
                    update: true
                }
            }
        });

        expect(document.body.innerHTML).toMatchSnapshot();
    });

    test('can mount "with form commands" entity list', async ()=>{
        const $entityList = await createVm();

        await nextRequestFulfilled({
            status: 200,
            response: {
                containers: {
                    title: {
                        key: 'title',
                        label: 'Titre',
                    }
                },
                layout:[{
                    key: 'title',
                    size: 6,
                    sizeXS: 12
                }],
                data:{
                    items : []
                },
                config:{
                    filters:[],
                    commands:{
                        instance: [
                            [{
                                type: "instance",
                                key: "validate",
                                label: "Should be visible in first title",
                                authorization: [],
                                form: {}
                            },{
                                type: "instance",
                                key: "delete",
                                label: "Should be visible in second title",
                                authorization: [],
                                form: {}
                            }],
                        ],
                    },
                    instanceIdAttribute: 'id'
                },
                authorizations:{
                    view: true,
                    update: true
                }
            }
        });

        $entityList.$set($entityList.showFormModal, 'validate', true);
        await Vue.nextTick();

        expect.addSnapshotSerializer(HTMLElementsSerializer);
        expect(document.querySelectorAll('.MOCKED_SHARP_MODAL')).toMatchSnapshot();
    });

    test('filter params', async () => {
        let $entityList = await createVm();
        $entityList.filtersValue = {
            'age': 30,
            'type': null
        };
        expect($entityList.filterParams).toEqual({
            'filter_age': 30
        });
    });

    test('id attribute', async ()=>{
        let $entityList = await createVm();

        $entityList.config = {};
        expect($entityList.idAttr).toBeUndefined();

        Vue.set($entityList.config, 'instanceIdAttribute', 'id');
        expect($entityList.idAttr).toBe('id');
    });

    test('state attribute', async ()=>{
        let $entityList = await createVm();

        $entityList.config = {};
        expect($entityList.stateAttr).toBeFalsy();

        $entityList.config = {
            state: {}
        };
        expect($entityList.stateAttr).toBeFalsy();


        Vue.set($entityList.config.state, 'attribute', 'visibility');
        expect($entityList.stateAttr).toBe('visibility');
    });

    test('filter by keys', async ()=>{
        let $entityList = await createVm();

        $entityList.config = {
            filters: [{ key: 'nationality' }, { key:'age' }]
        };

        expect($entityList.filterByKey).toEqual({
            nationality: { key: 'nationality' },
            age: { key:'age' }
        });
    });

    test('state by value', async ()=>{
        let $entityList = await createVm();

        $entityList.config = {

        };

        expect($entityList.stateByValue).toBeFalsy();


        $entityList.config = {
            state: {
                values: [{ value: 'active' }, { value: 'inactive' }]
            }
        };

        expect($entityList.stateByValue).toEqual({
            active: { value: 'active' },
            inactive: { value: 'inactive' }
        });
    });

    test('index by instance id', async ()=>{
        let $entityList = await createVm();

        $entityList.config = {
            instanceIdAttribute: 'id'
        };
        $entityList.data = {
            items: [{ id: '4' },{ id: '6' }]
        };

        expect($entityList.indexByInstanceId).toEqual({
            '4': 0,
            '6': 1
        });
    });

    test('authorizations by instance id', async ()=>{
        let $entityList = await createVm();

        $entityList.config = {
            instanceIdAttribute: 'id'
        };
        $entityList.authorizations = {
            view: ['4'], update: ['6']
        };
        $entityList.data = {
            items: [{ id: '4' },{ id: '6' }]
        };

        expect($entityList.authorizationsByInstanceId).toEqual({
            '4': { view: true, update: false },
            '6': { view: false, update: true }
        });

        $entityList.authorizations = {
            view: false, update: true
        };

        expect($entityList.authorizationsByInstanceId).toEqual({
            '4': { view: false, update: true },
            '6': { view: false, update: true }
        });
    });


    test('commands by instance id', async ()=> {
        let $entityList = await createVm();

        $entityList.config = {
            commands: {
                entity: [
                    [{
                        type: "entity",
                        key: "synchronize",
                    }]
                ],
                instance: [
                    [{
                        type: "instance",
                        key: "validate",
                        authorization: ['4']
                    }]
                ],
            },
            instanceIdAttribute: 'id'
        };
        $entityList.data = {
            items: [{id: '4'}, {id: '6'}]
        };

        expect($entityList.commandsByInstanceId).toEqual({
            '4': [[{
                type: "instance",
                key: "validate",
                authorization: ['4']
            }]],
            '6': [[]]
        });
    });

    test('command forms', async ()=>{
        let $entityList = await createVm();

        $entityList.config = {
            commands: {
                entity: [
                    [{
                        key: 'synchronize',
                        form: {
                            fields: { title:{} },
                            data: {},
                            layout: [[ { key: 'title' }]]
                        }
                    }]
                ],
                instance: [
                    [{ key: 'command2', form: { } }]
                ]
            }
        };

        expect($entityList.commandForms).toEqual([
            {
                key: 'synchronize',
                fields: { title:{} },
                data: {},
                layout: { tabs: [{ columns: [{ fields: [[ { key: 'title' } ]] }] }] }
            },
            expect.objectContaining({ key: 'command2' })
        ]);
    });

    test('mount', async ()=>{
        let $entityList = await createVm();

        history.replaceState = jest.fn();

        $entityList.mount({
            containers: { title: {} },
            layout: [{ key: 'title'}],
            data: {
                items: [{ title: 'AAA' }],
                page: 1
            },
            config: {
                filters: [{ key:'age', 'default':4 }],
                defaultSortDir: 'desc',
                defaultSort: 'title'
            },
            authorizations: {
                view: false, update: false
            }
        });

        expect(history.replaceState).toHaveBeenCalledWith(expect.anything(),null);

        expect($entityList.$data).toMatchObject({
            containers: { title: {} },
            layout: [{ key: 'title'}],
            data: {
                items: [{ title: 'AAA' }],
                page: 1
            },
            config: {
                filters: [{ key:'age', 'default':4 }],
                defaultSortDir: 'desc',
                defaultSort: 'title',
                commands: {},
            },
            authorizations: {
                view: false, update: false
            },
            reorderedItems: [{ title: 'AAA' }],
            page: 1,
            sortDir: 'desc',
            sortedBy: 'title',
            sortDirs: {
                title: 'desc'
            },
            filtersValue: {
                age: 4
            },
            ready: true
        });

        $entityList.sortDir = 'asc';
        $entityList.sortedBy = 'name';
        $entityList.mount({
            containers: { title: {} },
            layout: [{ key: 'title'}],
            data: {
                items: [{ title: 'AAA' }],
                page: 1
            },
            config: {
                filters: [{ key:'age', 'default':5 }, { key:'date', multiple: true }],
                defaultSortDir: 'desc',
                defaultSort: 'title',
                commands: {
                    instance: [
                        [{ key:'synchronize', authorization:[] }]
                    ],
                },
            },
            authorizations: {
                view: false, update: false
            }
        });

        expect($entityList.$data).toMatchObject({
            sortDir: 'asc',
            sortedBy: 'name',
            filtersValue: {
                age: 4,
                date: []
            }
        });
    });

    test('setup action bar', async ()=>{
        let $entityList = await createVm();

        let setupEmitted = jest.fn();

        $entityList.actionsBus.$on('setup', setupEmitted);

        $entityList.config = {
            commands: {},
            reorderable: false,
            searchable: false
        };
        $entityList.authorizations = {
            create: false, update: false
        };

        $entityList.data = {
            totalCount: 3
        };
        $entityList.setupActionBar();
        expect(setupEmitted).toHaveBeenLastCalledWith(expect.objectContaining({
            itemsCount: 3
        }));

        $entityList.data = {
            items: [{}]
        };
        $entityList.setupActionBar();
        expect(setupEmitted).toHaveBeenLastCalledWith(expect.objectContaining({
            itemsCount: 1,
        }));

        expect(setupEmitted).toHaveBeenLastCalledWith(expect.objectContaining({
            searchable: false,
            showCreateButton: false,
            showReorderButton: false
        }));

        $entityList.config = {
            filters: [{ key:'age' }],
            commands: {
                instance: [
                    [{ type:'instance' }]
                ],
                entity: [
                    [{type: 'entity', authorization: false}], [{type: 'entity', authorization: true}]
                ],
            },
            searchable: true,
            reorderable: true
        };

        $entityList.authorizations = {
            create: true, update: false
        };
        $entityList.filtersValue = { age: 4 };
        $entityList.setupActionBar();
        expect(setupEmitted).toHaveBeenLastCalledWith(expect.objectContaining({
            filters: [{ key:'age' }],
            commands: [
                [], [{type:'entity', authorization: true}]
            ],
            filtersValue: $entityList.filtersValue,
            searchable: true,
            showCreateButton: true,
            showReorderButton: false
        }));

        $entityList.authorizations = {
            create: true, update: true
        };
        $entityList.data.items = [{}, {}];
        $entityList.setupActionBar();
        expect(setupEmitted).toHaveBeenLastCalledWith(expect.objectContaining({
            showReorderButton: true
        }));
    });

    test('col classes', async () => {
        let { colClasses } = await createVm();

        let classes = colClasses({ sizeXS: 6, size:3 });
        expect(classes).toEqual(expect.arrayContaining(['col-6', 'col-md-3']));

        classes = colClasses({ sizeXS: 6, size:3, hideOnXS:true });
        expect(classes).toEqual(expect.arrayContaining(['col-6', 'col-md-3', 'd-none d-md-flex']));

        classes = colClasses({ sizeXS: 6, size:3}, { highlight: true });
        expect(classes).toEqual(expect.arrayContaining(['col-6', 'col-md-3', { highlight: true }]));
    });

    test('is state class', async () => {
        let { isStateClass } = await createVm();

        expect(isStateClass('orange')).toBe(false);
        expect(isStateClass('osharp_secondary')).toBe(false);
        expect(isStateClass('sharp_primary')).toBe(true);
    });

    test('state classes', async () => {
        let $entityList = await createVm();

        $entityList.config = {
            state: {
                attribute: 'visibility',
                values: [{
                    value: "active",
                    label: "Visible",
                    color: "green"
                },
                {
                    value: "inactive",
                    label: "Not visible",
                    color: "sharp_grey"
                }]
            }
        };
        let { stateClasses } = $entityList;
        expect(stateClasses({ item: { visibility:'active' } })).toEqual([]);
        expect(stateClasses({ item: { visibility:'inactive' } })).toEqual(['sharp_grey']);

        expect(stateClasses({ value:'active' })).toEqual([]);
        expect(stateClasses({ value:'inactive' })).toEqual(['sharp_grey']);
    });

    test('state style', async () => {
        let $entityList = await createVm();

        $entityList.config = {
            state: {
                attribute: 'visibility',
                values: [
                    {
                        value: "active",
                        label: "Visible",
                        color: "green"
                    },
                    {
                        value: "inactive",
                        label: "Not visible",
                        color: "sharp_grey"
                    }
                ]
            }
        };
        let { stateStyle } = $entityList;
        expect(stateStyle({ item: { visibility:'active' } })).toEqual({
            background: 'green'
        });
        expect(stateStyle({ item: { visibility:'inactive' } })).toEqual('');

        expect(stateStyle({ value:'active' })).toEqual({
            background: 'green'
        });
        expect(stateStyle({ value:'inactive' })).toEqual('');
    });

    test('has state authorization', async ()=>{
        let $entityList= await createVm();
        let { hasStateAuthorization } = $entityList;

        $entityList.config = {
            instanceIdAttribute: 'id',
            state: {}
        };
        $entityList.config.state.authorization = false;
        expect(hasStateAuthorization({ id: 1 })).toBe(false);

        $entityList.config.state.authorization = true;
        expect(hasStateAuthorization({ id: 1 })).toBe(true);

        $entityList.config.state.authorization = [3];
        expect(hasStateAuthorization({ id: 1 })).toBe(false);
        expect(hasStateAuthorization({ id: 3 })).toBe(true);
    });

    test('filter value or default', async () => {
        let { filterValueOrDefault } = await createVm();
        expect(filterValueOrDefault(1)).toBe(1);
        expect(filterValueOrDefault(1, { default: 2 })).toBe(1);
        expect(filterValueOrDefault([1], { default: [2], multiple: true })).toEqual([1]);
        expect(filterValueOrDefault(null, { default: 3 })).toBe(3);
        expect(filterValueOrDefault(null, { default: [3], multiple: true })).toEqual([3]);
        expect(filterValueOrDefault(null, { })).toBeNull();
        expect(filterValueOrDefault(null, { multiple: true })).toEqual([]);
    });

    test('instance commands', async ()=>{
        let $entityList = await createVm();
        $entityList.config = {
            instanceIdAttribute: 'id',
            commands: {
                instance: [[{ type:'instance', authorization: [3] }]]
            },
        };
        $entityList.data = {
            items: [{ id: 3 }]
        };
        let { instanceCommands } = $entityList;
        expect(instanceCommands({ id: 3 })).toEqual([
            [{ type: 'instance', authorization:[3] }]
        ]);
    });

    test('row has link', async () => {
        let $entityList = await createVm();
        $entityList.config = {
            instanceIdAttribute: 'id',
        };

        // mock computed
        mockComputed($entityList,'authorizationsByInstanceId', {
            3: { view: false, update: true },
            4: { view: true, update: false }
        });

        // row with link must have 'view' authorization defined
        expect($entityList.rowHasLink({ id: 3 })).toBe(false);
        expect($entityList.rowHasLink({ id: 4 })).toBe(true);
    });

    test('row link', async () => {
        let $entityList = await createVm();
        $entityList.config = {
            instanceIdAttribute: 'id'
        };
        expect($entityList.rowLink({ id: 3 })).toBe('/sharp/form/spaceship/3');
    });

    test('get authorizations', async () => {
        let $entityList = await createVm();
        $entityList.authorizations = {
            view: true, update: false
        };
        expect($entityList.getAuthorizations({ type:'view', id: 3 })).toBe(true);
        expect($entityList.getAuthorizations({ type:'update', id: 7 })).toBe(false);
        $entityList.authorizations.view = [
            1,3,5
        ];
        expect($entityList.getAuthorizations({ type:'view', id: 4 })).toBe(false);
        expect($entityList.getAuthorizations({ type:'view', id: 5 })).toBe(true);
    });

    test('page changed', async () => {
        let $entityList = await createVm();
        $entityList.update = jest.fn();
        $entityList.pageChanged(2);
        expect($entityList.page).toBe(2);
        expect($entityList.update).toHaveBeenCalledWith({ resetPage: false });
    });

    test('sort toggle', async () => {
        let $entityList = await createVm();
        $entityList.sortDir = null;
        $entityList.sortedBy = null;

        $entityList.update = jest.fn();

        $entityList.sortToggle('col1');
        expect($entityList.sortDir).toBe('asc');
        expect($entityList.sortedBy).toBe('col1');
        expect($entityList.page).toBe(1);
        expect($entityList.update).toHaveBeenCalled();

        $entityList.sortToggle('col1');
        expect($entityList.sortDir).toBe('desc');
    });

    test('set state', async () => {
        let $entityList = await createVm();
        $entityList.config = {
            instanceIdAttribute: 'id',
            state: { attribute: 'stateAttr' }
        };

        const items = {};
        $entityList.actionRefresh = jest.fn();
        mockComputed($entityList, 'apiPath', '{{apiPath}}');

        $entityList.setState({ id: 3 }, { value:'active' });
        let { request } = await nextRequestFulfilled({
            status: 200,
            response: {
                action: 'refresh',
                items
            }
        });
        expect($entityList.actionRefresh).toHaveBeenCalledWith(items);
        expect(request.config).toMatchObject({
            method:'post',
            data: JSON.stringify({
                attribute: 'stateAttr',
                value: 'active'
            }),
            url: '{{apiPath}}/state/3'
        });

        $entityList.actionReload = jest.fn();
        $entityList.setState({ id: 3 }, { value:'active' });
        await nextRequestFulfilled({
            status: 200,
            response: {
                action: 'reload'
            }
        });
        expect($entityList.actionReload).toHaveBeenCalled();

        const showMainModalEmmitted = jest.fn();
        $entityList.actionsBus.$on('showMainModal', showMainModalEmmitted);
        $entityList.setState({ id: 3 }, { value:'active' });
        await nextRequestFulfilled({
            status: 422,
            response: {
                message: 'Error message'
            }
        });
        expect(showMainModalEmmitted).toHaveBeenCalledWith({
            title: expect.any(String),
            text: 'Error message',
            isError: true,
            okCloseOnly: true
        })
    });

    test('update', async ()=> {
        let $entityList = await createVm();

        $entityList.page = 3;
        $entityList.updateData = jest.fn();
        $entityList.updateHistory = jest.fn();

        $entityList.update();
        expect($entityList.page).toBe(1);
        expect($entityList.updateData).toHaveBeenCalled();
        expect($entityList.updateHistory).toHaveBeenCalled();

        $entityList.page = 3;

        $entityList.update({ resetPage: false });
        expect($entityList.page).toBe(3);
    });

    test('update data', async ()=>{
        let $entityList = await createVm();

        const data = {};
        $entityList.setupActionBar = jest.fn();
        $entityList.get = jest.fn(()=>Promise.resolve({
            data: {
                data
            }
        }));
        $entityList.updateData();
        await wait(20);
        expect($entityList.get).toHaveBeenCalled();
        expect($entityList.data).toBe(data);
        expect($entityList.setupActionBar).toHaveBeenCalled();
    });

    test('command endpoint', async () => {
        let $entityList = await createVm();
        $entityList.config = {
            instanceIdAttribute: 'id'
        };
        mockComputed($entityList, 'apiPath', '{{apiPath}}');
        expect($entityList.commandEndpoint('updateAll')).toBe('{{apiPath}}/command/updateAll');
        expect($entityList.commandEndpoint('updateAll', { id: 3 })).toBe('{{apiPath}}/command/updateAll/3');
    });

    test('send command', async () => {
        let $entityList = await createVm();

        $entityList.commandEndpoint = jest.fn(()=>'{{commandEndpoint}}');
        $entityList.handleCommandResponse = jest.fn();

        mockComputed($entityList, 'apiParams', { param1: true });
        $entityList.sendCommand({
            key: 'updateAll'
        });
        const data = {
            myData: 1
        };
        const response = await nextRequestFulfilled({
            status: 200,
            response: new Blob([JSON.stringify(data)], { type:'application/json' })
        });
        expect(response.request.config).toMatchObject({
            method: 'post',
            url: '{{commandEndpoint}}',
            data: JSON.stringify({ query:{ param1: true } }),
            responseType: 'blob'
        });

        expect($entityList.handleCommandResponse).toHaveBeenCalledWith(response);
    });

    test('send command with form', async () => {
        let $entityList = await createVm();
        const wrapper = createWrapper($entityList);
        const commandForm = () => wrapper.find({ name:'SharpForm' });

        $entityList.mount({
            data: { items:[] },
            config: {
                commands:{
                    entity:[
                        [{
                            type: "entity",
                            key: "update",
                            authorization: [],
                            form: {}
                        }]
                    ],
                    instance:[
                        [{
                            type: "instance",
                            key: "openForm",
                            authorization: [],
                            form: {}
                        }]
                    ],
                },
                instanceIdAttribute: 'id'
            }
        });

        $entityList.getCommandFormData = jest.fn(()=>Promise.resolve('form data'));

        $entityList.sendCommand({
            key: 'update',
            form: {}
        }, null);

        expect($entityList.getCommandFormData).not.toHaveBeenCalled();
        expect($entityList.currentFormData).toEqual({});
        expect($entityList.showFormModal).toEqual({ update:true });
        expect($entityList.selectedInstance).toBe(null);

        await Vue.nextTick();
        expect(commandForm().props()).toMatchObject({
            entityKey: 'update',
            props: { data:{} }
        });

        $entityList.showFormModal = {};
        await $entityList.sendCommand({
            key: 'openForm',
            form: {},
            fetch_initial_data: true
        }, 'instance 2');

        expect($entityList.getCommandFormData).toHaveBeenCalledWith('openForm',  'instance 2');
        expect($entityList.currentFormData).toBe('form data');
        expect($entityList.showFormModal).toEqual({ openForm:true });
        expect($entityList.selectedInstance).toBe('instance 2');

        await Vue.nextTick();
        expect(commandForm().props()).toMatchObject({
            entityKey: 'openForm',
            props: { data:'form data' }
        });
    });

    test('download command', async ()=> {
        let $entityList = await createVm();
        URL.createObjectURL = jest.fn(()=>'blob:1234');

        $entityList.commandEndpoint = jest.fn(()=>'{{commandEndpoint}}');
        jest.spyOn($entityList, 'actionDownload');

        mockComputed($entityList, 'apiParams', {});

        $entityList.sendCommand({
            key: 'download'
        });

        await nextRequestFulfilled({
            status: 200,
            response: new Blob(['<<file content>>'], { type:'application/pdf' }),
            headers: { ['Content-Disposition']: 'attachment; filename="file.pdf"' }
        });

        expect($entityList.actionDownload).toHaveBeenCalled();
        let dlLink = $entityList.$el.lastChild;
        expect(dlLink.tagName).toEqual('A');
        expect(dlLink.download).toEqual('file.pdf');
        expect(dlLink.href).toEqual('blob:1234');
    });

    test('handle command response', async ()=> {
        let $entityList = await createVm();
        $entityList.actionRefresh = jest.fn();
        $entityList.actionReload = jest.fn();
        $entityList.actionDownload = jest.fn();

        const handleCommandResponseJSON = async data => {
            await $entityList.handleCommandResponse({ data:new Blob([JSON.stringify(data)], { type:'application/json' }) });
        };

        const items = [];
        await handleCommandResponseJSON({ action: 'refresh', items });
        expect($entityList.actionRefresh).toHaveBeenCalledWith(items);

        await handleCommandResponseJSON({ action: 'reload' });
        expect($entityList.actionReload).toHaveBeenCalled();

        let showMainModalEmitted = jest.fn();
        $entityList.actionsBus.$on('showMainModal', showMainModalEmitted);

        await handleCommandResponseJSON({ action: 'info', message: 'My message' });
        expect(showMainModalEmitted).toHaveBeenCalledWith({
            title: expect.any(String),
            text: 'My message',
            okCloseOnly: true
        });

        await handleCommandResponseJSON({ action: 'view', html:'<p></p>' });
        expect($entityList.showViewPanel).toBe(true);
        expect($entityList.viewPanelContent).toBe('<p></p>');

        await $entityList.handleCommandResponse({ data:new Blob([], { type:'application/pdf' }) });
        expect($entityList.actionDownload).toHaveBeenCalled();
    });

    test('post command form', async () => {
        let $entityList = await createVm();
        let submitEmitted = jest.fn();
        const modalEvent = { preventDefault:jest.fn() };

        $entityList.showFormModal = {};
        $entityList.commandEndpoint = jest.fn(()=>'{{commandEndpoint}}');
        $entityList.actionsBus.$on('submit', submitEmitted);
        $entityList.selectedInstance = {};
        const apiParams = {};
        mockComputed($entityList,'apiParams',apiParams);

        $entityList.$set = jest.fn();
        $entityList.postCommandForm('sendInfos', modalEvent);
        expect($entityList.commandEndpoint).toHaveBeenCalledWith('sendInfos', $entityList.selectedInstance);
        expect(submitEmitted).toHaveBeenCalledTimes(1);
        expect(submitEmitted).toHaveBeenCalledWith({
            entityKey: 'sendInfos',
            endpoint: '{{commandEndpoint}}',
            dataFormatter: expect.any(Function),
            postConfig: {
                responseType: 'blob'
            }
        });

        let { dataFormatter } = submitEmitted.mock.calls[0][0];
        expect(dataFormatter({
            data: {
                someData: true
            }
        })).toEqual({
            query:apiParams,
            data:{ someData:true }
        });

        expect(modalEvent.preventDefault).toHaveBeenCalled();
        expect($entityList.$set).toHaveBeenCalledWith($entityList.showFormModal, 'sendInfos', true);
    });

    test('command form submitted', async () => {
        let $entityList = await createVm();
        $entityList.selectedInstance = {};
        $entityList.handleCommandResponse = jest.fn();
        $entityList.$set = jest.fn();
        $entityList.showFormModal = {};

        const data = {};
        await $entityList.commandFormSubmitted('command1', data);
        expect($entityList.selectedInstance).toBeNull();
        expect($entityList.handleCommandResponse).toHaveBeenCalledWith(data);
        expect($entityList.$set).toHaveBeenCalledWith($entityList.showFormModal, 'command1', false);
    });

    test('action reload', async () => {
        let $entityList = await createVm();
        $entityList.updateData = jest.fn();

        $entityList.actionReload();
        expect($entityList.updateData).toHaveBeenCalled();
    });

    test('action refresh', async () => {
        let $entityList = await createVm();
        $entityList.config = {
            instanceIdAttribute: 'id'
        };
        $entityList.$set = jest.fn();
        $entityList.data = { items: [{ id: 3}] };
        mockComputed($entityList, 'indexByInsstanceId', { 3: 0 });

        $entityList.actionRefresh([{
            id: 3
        }]);
        expect($entityList.$set).toHaveBeenCalledWith($entityList.data.items, 0, { id: 3 });
    });

    test('update history', async () => {
        let $entityList = await createVm();

        history.pushState = jest.fn();
        const apiParams = {};
        mockComputed($entityList,'apiParams',apiParams);

        $entityList.updateHistory();

        expect(history.pushState).toHaveBeenCalledWith(apiParams, null, '{{serialized}}');
    });

    test('bind params', async () => {
        let $entityList = await createVm();
        let searchChangedEmitted = jest.fn();

        $entityList.page = 2;
        $entityList.sortedBy = 'col3';
        $entityList.sortDir = 'desc';

        const filterByKey = {
            age: {
                key: 'age'
            },
            job: {
                key: 'job',
                multiple: true
            },
            wrong : {
                key: 'wrong'
            }
        };
        mockComputed($entityList,'filterByKey',filterByKey);

        const filterValueOrDefault = Symbol('filter value or default');
        $entityList.filterValueOrDefault = jest.fn(()=>filterValueOrDefault);

        $entityList.actionsBus.$on('searchChanged', searchChangedEmitted);

        $entityList.bindParams({ search: 'aaa', filter_age: 3, filter_job: 'teacher', _filter_wrong: 'wrong' });
        expect($entityList.page).toBe(2);
        expect($entityList.sortedBy).toBe('col3');
        expect($entityList.sortDir).toBe('desc');

        expect(searchChangedEmitted).toHaveBeenCalledWith('aaa', { isInput: false });
        expect($entityList.filterValueOrDefault).toHaveBeenCalledWith(3, filterByKey.age);
        expect($entityList.filterValueOrDefault).toHaveBeenCalledWith(['teacher'], filterByKey.job);
        expect($entityList.filterValueOrDefault).not.toHaveBeenCalledWith('wrong', filterByKey.wrong);

        expect($entityList.filtersValue).toEqual({
            age: filterValueOrDefault,
            job: filterValueOrDefault
        });

        $entityList.bindParams({ page: 1, sort: 'col1', dir: 'asc' });
        expect($entityList.page).toBe(1);
        expect($entityList.sortedBy).toBe('col1');
        expect($entityList.sortDir).toBe('asc');
    });

    test('search changed (action)', async () => {
        let $entityList = await createVm();
        $entityList.update = jest.fn();
        $entityList.actionsBus.$emit('searchChanged', 'new search');
        expect($entityList.search).toBe('new search');
        expect($entityList.update).toHaveBeenCalled();

        $entityList.update.mockClear();
        $entityList.actionsBus.$emit('searchChanged', 'new new search', { isInput: false });
        expect($entityList.update).not.toHaveBeenCalled();
    });

    test('filter changed (action)', async () => {
        let $entityList = await createVm();

        Object.defineProperty($entityList, 'filterByKey', {
            value: {
                age: { key: 'age '}
            }
        });

        $entityList.update = jest.fn();
        $entityList.actionsBus.$emit('filterChanged', 'age', 3);
        expect($entityList.filtersValue).toEqual({ age: 3 });
        expect($entityList.update).toHaveBeenCalled();
    });

    test('command (action)', async () => {
        let $entityList = await createVm();
        $entityList.sendCommand = jest.fn();
        $entityList.actionsBus.$emit('command');
        expect($entityList.sendCommand).toHaveBeenCalled();
    });

    test('create (action)', async () => {
        let $entityList = await createVm();
        mockProperty(location, 'href');
        $entityList.actionsBus.$emit('create');
        expect(setter(location, 'href')).toHaveBeenCalledWith('/sharp/form/spaceship');
        unmockProperty(location, 'href');
    });

    test('toggle reorder (action)', async () => {
        let $entityList = await createVm();
        $entityList.reorderActive = false;
        $entityList.data = {
            items: [{ id: 1 }, { id: 2 }]
        };
        $entityList.reorderedItems = [{ id: 2 }, { id: 1 }];
        $entityList.actionsBus.$emit('toggleReorder');
        expect($entityList.reorderActive).toBe(true);

        $entityList.actionsBus.$emit('toggleReorder');
        expect($entityList.reorderActive).toBe(false);
        expect($entityList.reorderedItems).toEqual($entityList.data.items);


    });
    test('toggle reorder - apply (action)', async () => {
        let $entityList = await createVm();
        $entityList.reorderActive = true;
        $entityList.config = {
            instanceIdAttribute: 'id'
        };
        $entityList.data = {
            items: [{ id: 1 }, { id: 2 }]
        };
        $entityList.reorderedItems = [{ id: 2 }, { id: 1 }];
        $entityList.$set = jest.fn();

        mockComputed($entityList, 'apiPath', '{{apiPath}}');
        $entityList.actionsBus.$emit('toggleReorder', { apply: true });
        let { request } = await nextRequestFulfilled({
            status: 200
        });
        expect(request.config).toMatchObject({
            method: 'post',
            data: JSON.stringify({
                instances: [2, 1]
            })
        });
        expect($entityList.$set).toHaveBeenCalledTimes(1);
        expect($entityList.$set).toHaveBeenCalledWith($entityList.data, 'items', expect.any(Array));
        expect($entityList.$set.mock.calls[0][2]).toEqual($entityList.reorderedItems);
        expect($entityList.reorderActive).toBe(false);
    });

    test('created', async () => {
        let $entityList = await createVm();
        $entityList.get = jest.fn(()=>Promise.resolve());

        $entityList.verify=jest.fn();
        $entityList.bindParams=jest.fn();
        $entityList.setupActionBar=jest.fn();
        $entityList.updateData=jest.fn();

        EntityList.created.call($entityList);
        await wait(20);
        expect($entityList.verify).toHaveBeenCalled();
        expect($entityList.bindParams).toHaveBeenCalled();
        expect($entityList.setupActionBar).toHaveBeenCalled();

        $entityList.updateData.mockClear();
        const state = {};
        window.onpopstate({ state });
        expect($entityList.bindParams).toHaveBeenCalledWith(state);
        expect($entityList.updateData).toHaveBeenCalled();
    });
});

async function createVm(customOptions={}) {

    const vm = new Vue({
        el: '#app',
        mixins: [MockInjections, customOptions],

        props: ['independant', 'props'],

        'extends': {
            data:()=>({
                instanceId: null,
                ignoreAuthorizations: null
            })
        },

        created() {
            let { axiosInstance } = this._provided;
            moxios.install(axiosInstance);
            moxios.uninstall = moxios.uninstall.bind(moxios, axiosInstance);
        }
    });

    await Vue.nextTick();

    return vm.$children[0];
}
