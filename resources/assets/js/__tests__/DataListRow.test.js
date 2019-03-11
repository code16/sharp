import merge from 'lodash/merge';

import SharpDataListRow from '../components/list/DataListRow.vue';
import { shallowMount } from '@vue/test-utils';


describe('DataListRow', ()=>{

    function createWrapper({ ...options }={}) {
        return shallowMount(SharpDataListRow, {
            ...options
        });
    }

    function withDefaultMocks(options) {
        return merge({
            computed: {
                hasLink: ()=>false
            },
            methods: {
                colClasses: ()=>'colClasses'
            },
        }, options);
    }

    test('mount', ()=>{
        const wrapper = createWrapper(withDefaultMocks());
        expect(wrapper.html()).toMatchSnapshot();
    });

    test('mount header', ()=>{
        const wrapper = createWrapper(withDefaultMocks());
        wrapper.setProps({
            header: true
        });
        expect(wrapper.html()).toMatchSnapshot();
        wrapper.setProps({
            columns: [{ key:'label' }],
            row: { label: 'label' }
        });
        expect(wrapper.html()).toMatchSnapshot('columns');
    });

    test('mount with url', ()=>{
        const wrapper = createWrapper(withDefaultMocks({
            computed: {
                hasLink: ()=>true
            }
        }));
        wrapper.setProps({
            url: 'http://example.org'
        });
        expect(wrapper.html()).toMatchSnapshot();
    });

    test('mount append', ()=>{
        const wrapper = createWrapper(withDefaultMocks({
            slots: {
                append: 'mockedAppend'
            }
        }));
        expect(wrapper.html()).toMatchSnapshot();
    });

    test('mount columns', ()=>{
        const wrapper = createWrapper(withDefaultMocks());
        wrapper.setProps({
            columns: [{ key:'name' }, { key:'age', html: true }],
            row: { name:'John', age:'<span>3</span>' }
        });
        expect(wrapper.html()).toMatchSnapshot();
    });

    test('mount cell slot', ()=>{
        const wrapper = createWrapper(withDefaultMocks({
            scopedSlots: {
                cell: '<div v-bind="props">{{ props.column.key }}:{{ props.row.name }}</div>'
            }
        }));
        wrapper.setProps({
            columns: [{ key:'name' }],
            row: { name:'John' },
        });
        expect(wrapper.html()).toMatchSnapshot();
    });

    describe('computed', ()=>{
        test('hasUrl', ()=>{
            const wrapper = createWrapper();
            expect(wrapper.vm.hasLink).toBe(false);
            wrapper.setProps({
                url: 'http://example.org',
            });
            expect(wrapper.vm.hasLink).toBe(true);
        });
    });

    describe('methods', ()=>{
        test('colClasses', ()=>{
            const wrapper = createWrapper();
            expect(wrapper.vm.colClasses({ size:6, sizeXS:12 }))
                .toEqual(expect.arrayContaining([
                    'col-12',
                    'col-md-6',
                ]));
            expect(wrapper.vm.colClasses({ hideOnXS:true }))
                .toContain('d-none d-md-flex');
        });
    });
});