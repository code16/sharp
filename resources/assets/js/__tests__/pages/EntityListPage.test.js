import Vue from 'vue';
import SharpEntityListPage from '../../components/pages/EntityListPage.vue';

import {mockChildrenComponents} from "../utils/mockSFC";
import merge from 'lodash/merge';

// import moxios from 'moxios';
import { MockI18n } from "../utils";
// import { setter, mockProperty, unmockProperty } from "../utils/mock-utils";
// import { nextRequestFulfilled } from '../utils/moxios-utils';
// import HTMLElementsSerializer from '../utils/htmlElementsSnapshotSerializer';
import { shallowMount } from '@vue/test-utils';

jest.mock('../../components/DynamicViewMixin');

describe('EntityListPage', () => {
    Vue.use(MockI18n);
    MockI18n.mockLangFunction();


    function createWrapper({ routeQuery, ...options }={}) {
        const wrapper = shallowMount(SharpEntityListPage, {
            extends: options,
            data() {
                return {
                    ready: true
                }
            },
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
                    query: {
                        ...routeQuery
                    }
                }
            }
        });
        return wrapper;
    }

    function createData(data) {
        return merge({
            config: {
                commands: {}
            }
        }, data);
    }

    test('can mount entity list', ()=>{
        const wrapper = createWrapper({
            data:() => createData({
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
            })
        });

        expect(wrapper.html()).toMatchSnapshot();
    });
});