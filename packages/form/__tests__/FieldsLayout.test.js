import FieldsLayout from '../src/components/ui/FieldsLayout.vue';

import { shallowMount } from '@vue/test-utils';

describe('fields-layout', () => {
    const FieldMock = { template:'<div id="FIELD_MOCK"></div>' };
    function createWrapper({ propsData }) {
        return shallowMount(FieldsLayout, {
            propsData: {
                ...propsData,
            },
            stubs: {
                Grid:
                    `<div id="MOCKED_GRID">
                        <slot v-bind="rows[0][0]"></slot>
                    </div>`,
                FieldsLayout: true,
            },
            scopedSlots: {
                default: '<Field :data="props" />',
            },
        });
    }

    test('can mount fields layout', () => {
        const wrapper = createWrapper({
            propsData: {
                layout: [
                    [{ key:'title' }]
                ]
            }
        });

        expect(wrapper.html()).toMatchSnapshot();
    });

    test('can mount "fieldset" fields layout', () => {
        const wrapper = createWrapper({
            propsData: {
                layout: [
                    [{
                        legend: 'Fieldset', fields: [[
                            { key: 'title' }
                        ]]
                    }]
                ],
                visible: {
                    title: true
                }
            }
        });

        expect(wrapper.html()).toMatchSnapshot();
    });

    test('can mount "hidden fieldset" fields layout', () => {
        const wrapper = createWrapper({
            propsData: {
                layout: [
                    [{
                        legend: 'Fieldset', fields: [
                            [{ key: 'title' }]
                        ]
                    }]
                ],
                visible: {
                    title: false
                }
            }
        });

        expect(wrapper.html()).toMatchSnapshot();
    });

    test('expose correct props', () => {
        const wrapper = createWrapper({
            propsData: {
                layout: [
                    [{ key:'list' }]
                ]
            }
        });

        expect(wrapper.find(FieldMock).vm.$attrs).toEqual({
            data: { key: 'list' }
        })
    });

    test('expose correct fieldset props', () => {
        const wrapper = createWrapper({
            propsData: {
                layout: [
                    [{
                        legend: 'Fieldset', fields: [
                            [{ key: 'title' }]
                        ]
                    }]
                ],
            }
        });

        expect(wrapper.find(FieldMock).vm.$attrs).toEqual({
            data: { key: 'title' }
        });
    });

    test('fieldset visible', () => {
        const wrapper = createWrapper({
            propsData: {
                layout: [
                    [{
                        legend: 'Fieldset 1',
                        id: 'fieldset_1',
                        fields: [
                            [{ key: 'title' }, { key: 'subtitle' }],[{ key: 'name' }]
                        ]
                    }]
                ],
                visible: {
                    title: true, subtitle: true, name: false,
                }
            }
        });

        expect(wrapper.vm.fieldsetMap).toEqual({
            'fieldset_1':[{ key: 'title' }, { key: 'subtitle' },{ key: 'name' }]
        });

        expect(wrapper.vm.isFieldsetVisible({ id: 'fieldset_1'})).toBe(true);
    });

    test('fieldset invisible', () => {
        const wrapper = createWrapper({
            propsData: {
                layout: [
                    [{
                        legend: 'Fieldset 1',
                        id: 'fieldset_1',
                        fields: [
                            [{ key: 'title' }, { key: 'subtitle' }],[{ key: 'name' }]
                        ]
                    }]
                ],
                visible: {
                    title: false, subtitle: false, name: false,
                }
            }
        });

        expect(wrapper.vm.fieldsetMap).toEqual({
            'fieldset_1':[{ key: 'title' }, { key: 'subtitle' },{ key: 'name' }]
        });

        expect(wrapper.vm.isFieldsetVisible({ id: 'fieldset_1'})).toBe(false);
    })

});