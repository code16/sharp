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
            },
            localVue,
            created() {
                jest.spyOn(this, 'init').mockImplementation();
                jest.spyOn(this.$store, 'dispatch').mockImplementation(()=>Promise.resolve());
            },
            store: new Vuex.Store({
                modules: {
                    'show': merge(showModule, storeModule),
                }
            }),
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
                                fields:[{}]
                            }]
                        }
                    ]
                }),
                formUrl: () => 'formUrl',
            },
            stubs: {
                'SharpGrid': {
                    template: `<div class="MOCKED_SharpGrid" v-bind="$attrs"><slot v-bind="{}" /></div>`,
                },
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
                                fields:[{}]
                            }]
                        }
                    ]
                }),
                formUrl: () => 'formUrl',
            },
            stubs: {
                'SharpGrid': {
                    template: `<div class="MOCKED_SharpGrid" v-bind="$attrs"><slot v-bind="{ key:'name' }" /></div>`,
                },
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