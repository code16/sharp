import Vuex from "vuex";
import merge from 'lodash/merge';
import { createLocalVue, shallowMount } from '@vue/test-utils';
import EntityList from '../src/components/fields/entity-list/EntityList.vue';
import showModule from "../src/store/show";
import { createStub } from "@sharp/test-utils";

jest.mock('sharp');


describe('show entity list field', () => {

    function createWrapper({ storeModule, ...options } = {}) {
        const localVue = createLocalVue();
        localVue.use(Vuex);

        return shallowMount(EntityList, {
            ...options,
            propsData: {
                fieldKey: 'fieldKey',
                label: 'label',
                ...options.propsData,
            },
            created() {
                jest.spyOn(this.$store, 'dispatch').mockImplementation(() => Promise.resolve());
            },
            // language=Vue
            stubs: {
                EntityList: createStub({
                    template: `<entitylist-stub v-bind="$attrs">
                        <slot name="action-bar" :props="{ ready:true }" :listeners="{ onchanged:()=>{} }" />
                        <slot name="append-head" :props="{ commands:[[{}]] }" :listeners="{ command:()=>{} }" />
                    </entitylist-stub>`,
                }),
            },
            store: new Vuex.Store({
                modules: {
                    'show': merge({}, showModule, storeModule),
                }
            }),
            localVue,
        });
    }

    test('mount', () => {
        expect(createWrapper().html()).toMatchSnapshot();
    });

    test('mount collapsed', () => {
        const wrapper = createWrapper({
            propsData: {
                collapsable: true,
            },
        })
        expect(wrapper.html()).toMatchSnapshot();
    });

    test('isVisible', () => {
        let wrapper = null;

        /** default */
        wrapper = createWrapper();
        expect(wrapper.vm.isVisible).toBeUndefined();

        /** has empty visible enabled */
        wrapper = createWrapper({
            propsData: {
                emptyVisible: true,
            }
        });
        expect(wrapper.vm.isVisible).toBe(true);

        /** has empty list */
        wrapper = createWrapper({
            data:() => ({
                list: { data: { list: { items:[] } } }
            })
        });
        expect(wrapper.vm.isVisible).toBe(false);

        /** has empty visible enabled with list */
        wrapper = createWrapper({
            propsData: {
                emptyVisible: true,
            },
            data:() => ({
                list: { data: { list: { items:[] } } }
            })
        });
        expect(wrapper.vm.isVisible).toBe(true);

        /** has items */
        wrapper = createWrapper({
            data:() => ({
                list: { data: { list: { items:[{ id:1 }] } } }
            })
        });
        expect(wrapper.vm.isVisible).toBe(true);

        /** has create button */
        wrapper = createWrapper({
            propsData: {
                showCreateButton: true,
            },
            data:() => ({
                list: { data: { list: { items:[] } }, authorizations: { create:true } }
            })
        });
        expect(wrapper.vm.isVisible).toBe(true);

        /** has filter active */
        wrapper = createWrapper({
            data:() => ({
                list: { data: { list: { items:[] } } }
            }),
            computed: {
                filters: () => [
                    { key:'name' }
                ],
                filtersValues: () => ({
                    name: 'aaa',
                }),
            }
        });
        expect(wrapper.vm.isVisible).toBe(true);

        /** has search active */
        wrapper = createWrapper({
            data:() => ({
                list: { data: { list: { items:[] } } }
            }),
            computed: {
                query: () => ({
                    search: 'aaa',
                }),
            }
        });
        expect(wrapper.vm.isVisible).toBe(true);
    })

    test('visibleFilters', () => {
        const wrapper = createWrapper({
            computed: {
                filters: () => [{ id:1, key:'type' }]
            }
        });
        expect(wrapper.vm.visibleFilters).toEqual([{ id:1, key:'type' }]);

        wrapper.setProps({
            hiddenFilters: {
                type: 3,
            }
        });
        expect(wrapper.vm.visibleFilters).toEqual([]);

        wrapper.setProps({
            hiddenFilters: {
                type: null,
            }
        });
        expect(wrapper.vm.visibleFilters).toEqual([]);
    });
});
