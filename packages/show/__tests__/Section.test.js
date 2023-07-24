import { shallowMount } from '@vue/test-utils';
import Section from "../src/components/Section.vue";

describe('show section', () => {
    function createWrapper(options) {
        return shallowMount(Section, {
            // language=Vue
            stubs: {
                Grid:
                    `<div class="MOCKED_SharpGrid" :class="rowClass && rowClass()">
                        <slot :item-layout="rows[0][0]" />
                    </div>`,
            },
            ...options,
        })
    }

    test('can mount', () => {
        const wrapper = createWrapper({
            propsData: {
                section: {
                    title: 'Section title',
                    columns: [{
                        fields: [[{ key:'name' }]]
                    }]
                },
                fieldsRowClass: () => 'row-class'
            }
        });
        expect(wrapper.html()).toMatchSnapshot();
    });

    test('can mount: collapsable + contents layout', () => {
        const wrapper = createWrapper({
            propsData: {
                section: {
                    title: 'Section title',
                    collapsable: true,
                    columns: [{
                        fields: [[{ key:'name' }]]
                    }]
                },
                collapsable: true,
                layout: 'contents',
            }
        });
        expect(wrapper.html()).toMatchSnapshot();
    });
});
