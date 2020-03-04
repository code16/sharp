import merge from 'lodash/merge';
import Vuex from 'vuex';
import { shallowMount, createLocalVue } from '@vue/test-utils';
import ShowPage from "../src/components/pages/ShowPage";
import showModule from "../src/store/show";
import { formUrl } from "sharp";

jest.mock('../src/store/show');
jest.mock('sharp/consts', () => ({
    BASE_URL: 'BASE_URL'
}));
jest.mock('sharp');


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
            },
            created() {
                jest.spyOn(this, 'init').mockImplementation();
                jest.spyOn(this.$store, 'dispatch').mockImplementation(()=>Promise.resolve());
            },
            store: new Vuex.Store({
                modules: {
                    'show': merge(showModule, storeModule),
                }
            }),
            // language=Vue
            stubs: {
                Grid:
                    `<div class="MOCKED_SharpGrid" v-bind="$props">
                        <slot v-bind="rows[0][0]" />
                    </div>`,
            },
            mocks: {
                $route: { params: { } },
            },
            localVue,
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
                            columns: [{
                                fields: [[{}]]
                            }]
                        }
                    ]
                }),
                formUrl: () => 'formUrl',
            },
        });

        wrapper.setMethods({
            fieldOptions: () => ({}),
            fieldValue: () => ({}),
        });
        wrapper.setData({ ready: true });
        expect(wrapper.html()).toMatchSnapshot();
    });

    test('can mount with unknown field', () => {
        const wrapper = createWrapper({
            computed: {
                layout: () => ({
                    sections: [
                        {
                            title: 'Section title',
                            columns: [{
                                fields: [[{ key:'name' }]]
                            }]
                        }
                    ]
                }),
                formUrl: () => 'formUrl',
            },
        });

        wrapper.setMethods({
            fieldOptions: () => null,
        });
        wrapper.setData({ ready: true });
        expect(wrapper.html()).toMatchSnapshot();
    });

    test('can mount with no container section', () => {
        const wrapper = createWrapper({
            computed: {
                layout: () => ({
                    sections: [
                        {
                            title: 'Section title',
                            columns: [{
                                fields:[{}]
                            }]
                        }
                    ]
                }),
                formUrl: () => 'formUrl',
            },
        });
        wrapper.setMethods({
            fieldOptions: () => ({ type:'entityList' }),
        });
        wrapper.setData({ ready: true });
        expect(wrapper.html()).toMatchSnapshot();
    });

    test('formUrl', () => {
        formUrl.mockReturnValue('formUrl');
        const wrapper = createWrapper({
            storeModule: {
                state: {
                    entityKey: 'entityKey',
                    instanceId: 'instanceId',
                }
            }
        });
        expect(wrapper.vm.formUrl).toEqual('formUrl');
        expect(formUrl).toHaveBeenCalledWith({
            entityKey: 'entityKey',
            instanceId: 'instanceId',
        });
    });

    test('fieldOptions', () => {
        let wrapper;
        const consoleErrorSpy = jest.spyOn(console, 'error').mockImplementation();

        wrapper = createWrapper({
            computed: {
                fields: () => ({
                    name: 'options'
                })
            },
        });
        expect(wrapper.vm.fieldOptions({ key:'name' })).toBe('options');

        wrapper = createWrapper({
            computed: {
                fields: () => ({}),
            }
        });
        consoleErrorSpy.mockClear();
        expect(wrapper.vm.fieldOptions('name')).toBeUndefined();
        expect(console.error).toHaveBeenCalled();

        wrapper = createWrapper({
            computed: {
                fields: () => null,
            }
        });
        consoleErrorSpy.mockClear();
        expect(wrapper.vm.fieldOptions('name')).toBe(null);
        expect(console.error).toHaveBeenCalled();
    });

    test('fieldValue', () => {
        let wrapper;

        wrapper = createWrapper({
            computed: {
                data: () => ({
                    name: 'value'
                })
            },
        });
        expect(wrapper.vm.fieldValue({ key:'name' })).toBe('value');

        wrapper = createWrapper({
            computed: {
                data: () => ({})
            }
        });
        expect(wrapper.vm.fieldValue({ key:'name' })).toBeUndefined();
        expect(console.error).toHaveBeenCalled();

        wrapper = createWrapper({
            computed: {
                fields: () => null,
            }
        });
        expect(wrapper.vm.fieldValue({ key:'name' })).toBe(null);
        expect(console.error).toHaveBeenCalled();
    });

    test('change state + command action integration', async () => {
        const wrapper = createWrapper();

        wrapper.vm.init.mockClear();
        wrapper.vm.$store.dispatch.mockReturnValueOnce(Promise.resolve({
            action: 'refresh',
        }));
        await wrapper.vm.handleStateChanged({});
        expect(wrapper.vm.init).toHaveBeenCalled();
    });
});