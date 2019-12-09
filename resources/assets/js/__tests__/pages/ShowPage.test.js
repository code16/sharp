import merge from 'lodash/merge';
import Vuex from 'vuex';
import ShowPage from "../../components/pages/ShowPage";
import { shallowMount, createLocalVue } from '@vue/test-utils';
import showModule from "../../store/modules/show";

jest.mock('../../store/modules/show');
jest.mock('../../consts', () => ({
    BASE_URL: 'BASE_URL'
}));


describe('show page', () => {
    function createWrapper({ storeModule, ...options } = {}) {
        const localVue = createLocalVue();
        localVue.use(Vuex);
        const wrapper = shallowMount(ShowPage, {
            extends: {
                computed: {
                    breadcrumb: () => [],
                    config: () => ({}),
                },
                methods: {
                    fieldOptions: () => ({}),
                    fieldValue: () => ({}),
                },
            },
            localVue,
            created() {
                jest.spyOn(this, 'init').mockImplementation(() => { });
            },
            store: new Vuex.Store({
                modules: {
                    'show': merge(showModule, storeModule),
                }
            }),
            stubs: {
                'SharpGrid': {
                    template: `<div class="MOCKED_SharpGrid" v-bind="$attrs"><slot v-bind="{ key:'name' }" /></div>`,
                },
            },
            ...options,
        });
        return wrapper;
    }

    test('can mount', () => {
        expect(createWrapper().html()).toMatchSnapshot();
    });

    test('can mount with fields', () => {
        const wrapper = createWrapper({
            computed: {
                layout: () => ({
                    sections: [
                        {
                            title: 'Section title',
                            columns: []
                        }
                    ]
                }),
                formUrl: () => 'formUrl',
            }
        });
        wrapper.setData({ ready: true });
        expect(wrapper.html()).toMatchSnapshot();
    });

    test('formUrl', () => {
        const wrapper = createWrapper({
            storeModule: {
                state: {
                    entityKey: 'entityKey',
                    instanceId: 'instanceId',
                }
            }
        });
        expect(wrapper.vm.formUrl).toEqual('/BASE_URL/form/entityKey/instanceId?x-access-from=ui');
    });
});