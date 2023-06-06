import merge from 'lodash/merge';

import SharpDataList from '../src/components/DataList.vue';
import { shallowMount } from '@vue/test-utils';

describe('DataList', ()=>{

    function createWrapper({ ...options }={}) {
        return shallowMount(SharpDataList, {
            // language=Vue
            stubs: {
                DataListRow:
                    `<div class="MOCKED_SharpDataListRow" v-bind="$props">
                        <template v-for="column in columns">
                            <slot name="cell" :column="column" />
                        </template>
                    </div>`,
                Draggable:
                    `<div class="MOCKED_Draggable">
                        <slot />
                    </div>`,
            },
            ...options,
        });
    }

    function setReordering(wrapper, reordering) {
        const watcherSpy = jest.spyOn(wrapper.vm, 'handleReorderingChanged').mockImplementation();
        wrapper.setProps({
            reordering
        });
        watcherSpy.mockRestore();
    }

    function withDefaultMocks(options={}) {
        return merge({
            computed: {
                isEmpty: ()=>false,
            }
        }, options);
    }

    test('mount empty', ()=>{
        const wrapper = createWrapper(withDefaultMocks({
            computed: {
                isEmpty: ()=>true,
            },
            slots: {
                empty: 'Empty text',
            },
        }));
        expect(wrapper.html()).toMatchSnapshot();
    });

    test('mount items', ()=>{
        const wrapper = createWrapper(withDefaultMocks({
            scopedSlots: {
                item: '<DataListRow :row="props.item" />',
            }
        }));
        wrapper.setProps({
            items: [{ id:1 }],
        });
        expect(wrapper.html()).toMatchSnapshot();
    });

    test('mount header', ()=>{
        const wrapper = createWrapper(withDefaultMocks());
        const header = wrapper.find({ ref:'head' });
        const columns = [{ key:'col1', label:'col1' }, { key:'col2', label:'col2', sortable: true }];

        expect(header.html()).toMatchSnapshot('default');
        wrapper.setProps({
            columns: [...columns],
        });
        expect(header.html()).toMatchSnapshot('columns');
        wrapper.setProps({
            columns: [...columns],
            sort: 'col2',
        });
        expect(header.html()).toMatchSnapshot('sort selected');
        wrapper.setProps({
            columns: [...columns],
            sort: 'col2',
            dir: 'asc',
        });
        expect(header.html()).toMatchSnapshot('sort ascending');
    });

    test('mount items slot', ()=>{
        const wrapper = createWrapper(withDefaultMocks({
            scopedSlots: {
                item: `<div v-bind="props">{{ props.item.text }}</div>`,
            },
        }));
        wrapper.setProps({
            items: [{ id:1, text:'mockedText' }],
        });
        expect(wrapper.html()).toMatchSnapshot();
    });

    test('mount pagination', ()=>{
        const wrapper = createWrapper(withDefaultMocks({
            computed: {
                isEmpty: ()=>true,
                hasPagination: ()=>true
            },
        }));
        wrapper.setProps({
            totalCount: 10,
            pageSize: 3,
            page: 2,
        });
        expect(wrapper.html()).toMatchSnapshot();
    });

    describe('watch', ()=>{
        test('reordering', ()=>{
            const wrapper = createWrapper();
            wrapper.setMethods({
                handleReorderingChanged: jest.fn()
            });
            wrapper.setProps({
                reordering: true,
            });
            expect(wrapper.vm.handleReorderingChanged).toHaveBeenCalledWith(true);
        });
    });

    describe('computed', ()=>{
        test('hasPagination', ()=>{
            const wrapper = createWrapper();
            expect(wrapper.vm.hasPagination).toBe(false);
            wrapper.setProps({
                paginated: true,
            });
            expect(wrapper.vm.hasPagination).toBe(false);
            wrapper.setProps({
                paginated: true,
                totalCount: 10,
                pageSize: 3,
            });
            expect(wrapper.vm.hasPagination).toBe(true);
        });
        test('draggableOptions', ()=>{
            const wrapper = createWrapper();
            expect(wrapper.vm.draggableOptions).toEqual({
                disabled: true,
            });
            setReordering(wrapper, true);
            expect(wrapper.vm.draggableOptions).toEqual({
                disabled: false,
            });
        });
        test('currentItems', ()=>{
            const wrapper = createWrapper();
            wrapper.setProps({
                items: [{ label:'item' }],
            });
            expect(wrapper.vm.currentItems).toEqual([{ label:'item' }]);
            setReordering(wrapper, true);
            wrapper.setData({
                reorderedItems: [{ label: 'reorderedItem' }],
            });
            expect(wrapper.vm.currentItems).toEqual([{ label: 'reorderedItem' }]);
        });
        test('isEmpty', ()=>{
            const wrapper = createWrapper();
            expect(wrapper.vm.isEmpty).toBe(true);
            wrapper.setProps({
                items:[{ id:1 }],
            });
            expect(wrapper.vm.isEmpty).toBe(false);
        });
    });

    describe('methods', ()=>{
        test('handleItemsChanged', ()=>{
            const wrapper = createWrapper();
            wrapper.vm.handleItemsChanged('reorderedItems');
            expect(wrapper.vm.reorderedItems).toEqual('reorderedItems');
            expect(wrapper.emitted('change')[0]).toEqual(['reorderedItems']);
        });

        test('handleSortChanged', ()=>{
            let wrapper;
            wrapper = createWrapper();
            wrapper.vm.handleSortClicked('name');
            expect(wrapper.emitted('sort-change')[0]).toEqual([{ prop:'name', dir: 'asc' }]);

            wrapper = createWrapper();
            wrapper.setProps({
                sort: 'name',
                dir: 'asc',
            });
            wrapper.vm.handleSortClicked('name');
            expect(wrapper.emitted('sort-change')[0]).toEqual([{ prop:'name', dir: 'desc' }]);
        });

        test('handlePageChanged', ()=>{
            const wrapper = createWrapper();
            wrapper.vm.handlePageChanged(1);
            expect(wrapper.emitted('page-change')[0]).toEqual([1]);
        });

        test('handleReorderingChanged', ()=>{
            const wrapper = createWrapper();
            wrapper.setProps({
                items: [{ id:1 }]
            });
            wrapper.vm.handleReorderingChanged(true);
            expect(wrapper.vm.reorderedItems).toEqual([{ id:1 }]);
            wrapper.vm.handleReorderingChanged(false);
            expect(wrapper.vm.reorderedItems).toEqual(null);
        });
    });
});
