import FieldsLayout from '../src/components/ui/FieldsLayout.vue';

import { shallowMount } from '@vue/test-utils';

describe('fields-layout', () => {

    const FieldMock = { name:'Field', template:'<div class="FIELD_MOCK"></div>' };
    function createWrapper({ propsData } = {}) {
        return shallowMount(FieldsLayout, {
            propsData: {
                ...propsData,
            },
            // language=Vue
            stubs: {
                Grid:
                    `<div class="GRID_MOCK">
                        <slot :item-layout="rows[0][0]" />
                    </div>`,
                FieldsLayout:
                    // render first field from given layout
                    `<div class="FIELDS_LAYOUT_MOCK">
                        <slot :field-layout="$attrs.layout[0][0]" />
                    </div>`,
                Field: FieldMock,
            },
            scopedSlots: {
                default: '<Field :data="props.fieldLayout" />',
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

        expect(wrapper.find({ name: 'Field' }).vm.$attrs).toEqual({
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

        expect(wrapper.find({ name: 'Field' }).vm.$attrs).toEqual({
            data: { key: 'title' }
        });
    });

    test('fieldset visible', () => {
        const wrapper = createWrapper({
            propsData: {
                layout: [[{}]],
            },
        });

        wrapper.setProps({
            visible: {
                title: true,
                subtitle: false,
            },
        });

        expect(
            wrapper.vm.isFieldsetVisible({
                id: 'fieldset_1',
                legend: 'Fieldset 1',
                fields: [{ key: 'title' }, { key: 'subtitle' }],
            })
        ).toBe(true);

        wrapper.setProps({
            visible: {
                title: false,
                subtitle: false,
            },
        });

        expect(
            wrapper.vm.isFieldsetVisible({
                id: 'fieldset_1',
                legend: 'Fieldset 1',
                fields: [{ key: 'title' }, { key: 'subtitle' }],
            })
        ).toBe(false);
    });
});