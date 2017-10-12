import Vue from 'vue';
import EntityList from '../components/list/EntityList.vue';
import Dropdown from '../components/dropdown/Dropdown.vue';
import Modal from '../components/Modal.vue';

import * as consts from '../consts';

import { mockChildrenComponents } from "./utils/mockSFC";

import { mockProperty, unmockProperty, setter } from "./utils/mock-utils";

import moxios from 'moxios';
import {MockInjections, MockI18n} from "./utils";
import { nextRequestFulfilled } from './utils/moxios-utils';


import mocks from './__mocks__/entityList';


describe('entity-list', ()=>{
    Vue.use(MockI18n);
    MockI18n.mockLangFunction();

    Vue.component('sharp-entity-list', mockChildrenComponents(EntityList, {
        customs: {
            [Dropdown.name]: {
                template: '<div class="MOCKED_SHARP_DROPDOWN"><slot></slot></div>'
            },
            'Draggable': {
                template: '<div id="MOCKED_VUE_DRAGGALBE"><slot></slot></div>'
            },
            [Modal.name]: {
                template: '<div class="MOCKED_SHARP_MODAL"><slot></slot></div>'
            }
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
            response: mocks.basicSingleContainerFullAuthorizations
        });

        expect(document.body.innerHTML).toMatchSnapshot();
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