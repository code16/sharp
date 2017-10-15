import Vue from 'vue';
import EntityList from '../components/list/EntityList.vue';
import Dropdown from '../components/dropdown/Dropdown.vue';
import DropdownItem from '../components/dropdown/DropdownItem.vue';
import StateIcon from '../components/list/StateIcon.vue';
import Modal from '../components/Modal.vue';

import * as consts from '../consts';

import { mockChildrenComponents } from "./utils/mockSFC";

import moxios from 'moxios';
import {MockInjections, MockI18n} from "./utils";
import { nextRequestFulfilled } from './utils/moxios-utils';
import HTMLElementsSerializer from './utils/htmlElementsSnapshotSerializer';

describe('entity-list', ()=>{
    Vue.use(MockI18n);
    MockI18n.mockLangFunction();

    Vue.component('sharp-entity-list', mockChildrenComponents(EntityList, {
        customs: {
            [Dropdown.name]: {
                template:
                    `<div class="MOCKED_SHARP_DROPDOWN" :class="{ '[[ isDisabled ]]':disabled }">
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

    moxios.delay = 10;

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

    it('can mount entity list', async ()=>{
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

    it('can mount "disabled all" entity list', async ()=>{
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

    it('can mount "disabled one" entity list', async ()=>{
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

    it('can mount "sortable" entity list', async ()=>{
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

    it('can mount "sortable selected" entity list', async ()=>{
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

    it('can mount "sortable selected ascending" entity list', async ()=>{
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

    it('can mount "empty" entity list', async ()=>{
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

    it('can mount "reordering" entity list', async ()=>{
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

    it('can mount "reordering reordered" entity list', async ()=>{
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

    it('can mount "with html" entity list', async ()=>{
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

    it('can mount "hide on XS" entity list', async ()=>{
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

    it('can mount "with commands" entity list', async ()=>{
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
                    commands:[{
                        type: "instance",
                        key: "validate",
                        label: "Should be visible in first title",
                        authorization: [1]
                    },{
                        type: "instance",
                        key: "delete",
                        label: "Should be visible in second title",
                        authorization: [2]
                    }],
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

    it('can mount "with state" entity list', async ()=>{
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

    it('can mount "with pagination" entity list', async ()=>{
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

    it('can mount "with form commands" entity list', async ()=>{
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
                    items : []
                },
                config:{
                    filters:[],
                    commands:[{
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
                    instanceIdAttribute: 'id'
                },
                authorizations:{
                    view: true,
                    update: true
                }
            }
        });

        expect.addSnapshotSerializer(HTMLElementsSerializer);
        expect(document.querySelectorAll('.MOCKED_SHARP_MODAL')).toMatchSnapshot();
    });

    it('filter params', async () => {
        let $entityList = await createVm();
        $entityList.filtersValue = {
            'age': 30,
            'type': null
        };
        expect($entityList.filterParams).toEqual({
            'filter_age': 30
        });
    });

    it('id attribute', async ()=>{
        let $entityList = await createVm();

        $entityList.config = {};
        expect($entityList.idAttr).toBeUndefined();

        Vue.set($entityList.config, 'instanceIdAttribute', 'id');
        expect($entityList.idAttr).toBe('id');
    });

    it('state attribute', async ()=>{
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

    it('filter by keys', async ()=>{
        let $entityList = await createVm();

        $entityList.config = {
            filters: [{ key: 'nationality' }, { key:'age' }]
        };

        expect($entityList.filterByKey).toEqual({
            nationality: { key: 'nationality' },
            age: { key:'age' }
        });
    });

    it('state by value', async ()=>{
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

    it('index by instance id', async ()=>{
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

    it('authorizations by instance id', async ()=>{
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


    it('commands by instance id', async ()=> {
        let $entityList = await createVm();

        $entityList.config = {
            commands: [{
                type: "entity",
                key: "synchronize",
            },
            {
                type: "instance",
                key: "validate",
                authorization: ['4']
            }],
            instanceIdAttribute: 'id'
        };
        $entityList.data = {
            items: [{id: '4'}, {id: '6'}]
        };

        expect($entityList.commandsByInstanceId).toEqual({
            '4': [{
                type: "instance",
                key: "validate",
                authorization: ['4']
            }]
        });
    });

    it('no instance commands', async ()=>{
        let $entityList = await createVm();

        $entityList.config = {
            commands: [
                {
                    type: "entity",
                    key: "synchronize",
                }
            ],
            instanceIdAttribute: 'id'
        };
        $entityList.data = {
            items: [{id: '4'}, {id: '6'}]
        };

        expect($entityList.noInstanceCommands).toBe(true);

        $entityList.config.commands.push({
            type: "instance",
            key: "validate",
            authorization: ['5']
        });

        expect($entityList.noInstanceCommands).toBe(true);

        Vue.set($entityList.config.commands[1],'authorization',['6']);

        expect($entityList.noInstanceCommands).toBe(false);
    });

    it('command forms', async ()=>{
        let $entityList = await createVm();

        $entityList.config = {
            commands: [{
                key: 'synchronize',
                form: {
                    fields: { title:{} },
                    data: {},
                    layout: [[ { key: 'title' }]]
                }
            }]
        };

        expect($entityList.commandForms).toEqual([{
            key: 'synchronize',
            fields: { title:{} },
            data: {},
            layout: { tabs: [{ columns: [{ fields: [[ { key: 'title' } ]] }] }] }
        }]);
    });

    it('mount', async ()=>{
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
                commands: [],
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
                commands: [{ key:'synchronize', authorization:[] }]
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

    it('setup action bar', async ()=>{
        let $entityList = await createVm();

        let setupEmitted = jest.fn();

        $entityList.actionsBus.$on('setup', setupEmitted);

        $entityList.config = {
            commands: [],
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
            commands: [
                { type:'instance' },
                { type:'entity', authorization: false },
                { type:'entity', authorization: true }
            ],
            searchable: true,
            reorderable: true
        };

        $entityList.authorizations = {
            create: true, update: false
        };
        $entityList.filtersValue = { age: 4 }
        $entityList.setupActionBar();
        expect(setupEmitted).toHaveBeenLastCalledWith(expect.objectContaining({
            filters: [{ key:'age' }],
            commands: [{type:'entity', authorization: true}],
            filtersValue: $entityList.filtersValue,
            searchable: true,
            showCreateButton: true,
            showReorderButton: false
        }));

        $entityList.authorizations = {
            create: true, update: true
        };
        $entityList.setupActionBar();
        expect(setupEmitted).toHaveBeenLastCalledWith(expect.objectContaining({
            showReorderButton: true
        }));
    });

    it('col classes', async () => {
        let { colClasses } = await createVm();

        let classes = colClasses({ sizeXS: 6, size:3 });
        expect(classes).toEqual(expect.arrayContaining(['col-6', 'col-md-3']));

        classes = colClasses({ sizeXS: 6, size:3, hideOnXS:true });
        expect(classes).toEqual(expect.arrayContaining(['col-6', 'col-md-3', 'd-none d-md-flex']));

        classes = colClasses({ sizeXS: 6, size:3}, { highlight: true });
        expect(classes).toEqual(expect.arrayContaining(['col-6', 'col-md-3', { highlight: true }]));
    });

    it('is state class', async () => {
        let { isStateClass } = await createVm();

        expect(isStateClass('orange')).toBe(false);
        expect(isStateClass('osharp_secondary')).toBe(false);
        expect(isStateClass('sharp_primary')).toBe(true);
    });

    it('state classes', async () => {
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

    it('state style', async () => {
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
            fill: 'green', stroke: 'green'
        });
        expect(stateStyle({ item: { visibility:'inactive' } })).toEqual('');

        expect(stateStyle({ value:'active' })).toEqual({
            fill: 'green', stroke: 'green'
        });
        expect(stateStyle({ value:'inactive' })).toEqual('');
    });

    it('has state authorization', async ()=>{
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

    it('filter value or default', async () => {
        let { filterValueOrDefault } = await createVm();
        expect(filterValueOrDefault(1)).toBe(1);
        expect(filterValueOrDefault(1, { default: 2 })).toBe(1);
        expect(filterValueOrDefault([1], { default: [2], multiple: true })).toEqual([1]);
        expect(filterValueOrDefault(null, { default: 3 })).toBe(3);
        expect(filterValueOrDefault(null, { default: [3], multiple: true })).toEqual([3]);
        expect(filterValueOrDefault(null, { })).toBeNull();
        expect(filterValueOrDefault(null, { multiple: true })).toEqual([]);
    });

    it('instance commands', async ()=>{
        let $entityList = await createVm();
        $entityList.config = {
            instanceIdAttribute: 'id',
            commands:[{ type:'instance', authorization: [3] }]
        };
        $entityList.data = {
            items: [{ id: 3 }]
        };
        let { instanceCommands } = $entityList;
        expect(instanceCommands({ id: 3 })).toEqual([{ type: 'instance', authorization:[3] }]);
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