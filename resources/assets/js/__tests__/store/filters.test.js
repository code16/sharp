import Vue from 'vue';
import filters from '../../store/modules/filters';
import { SET_FILTERS, SET_FILTER_VALUE } from "../../store/modules/filters";

describe('store filters', () => {
    test('state match snapshot', ()=>{
        expect(filters.state).toMatchSnapshot();
    });

    describe('mutations', () => {
        test('SET_FILTERS', () => {
            const state = {};
            const testFilters = [];
            filters.mutations[SET_FILTERS](state, testFilters);

            expect(state.filters).toBe(testFilters);
        });

        test('SET_FILTER_VALUE', () => {
            const state = { values: {} };
            filters.mutations[SET_FILTER_VALUE](state, { key:'prop', value:'value' });

            expect(state.values.prop).toBe('value');
        });
    });

    describe('getters', () => {
        test('value', ()=>{
            const state = {
                values: {
                    prop: 'value'
                }
            };
            expect(filters.getters.value(state)('prop')).toBe('value');
        });

        test('filters', ()=>{
            expect(filters.getters.filters({ filter: null })).toEqual([]);
            expect(filters.getters.filters({ filters: [{}] })).toEqual([{}]);
        });

        test('defaultValue', ()=>{
            expect(filters.getters.defaultValue()(null)).toBeUndefined();
            expect(filters.getters.defaultValue()({ default:'default' })).toEqual('default');
        });

        test('filterQueryKey', ()=>{
            expect(filters.getters.filterQueryKey()('key')).toBe('filter_key');
        });

        test('getQueryParams', ()=>{
            const state = {
            };
            const getters = {
                filterQueryKey: jest.fn(key => `TEST_${key}`)
            };
            expect(filters.getters.getQueryParams(state, getters)({ })).toEqual({});

            expect(filters.getters.getQueryParams(state, getters)({
                type: 'aaa',
                name: 'bbb',
            })).toEqual({
                'TEST_type': 'aaa', 'TEST_name': 'bbb'
            });
        });

        test('getValuesFromQuery', ()=>{
            expect(filters.getters.getValuesFromQuery()(null)).toEqual({ });
            expect(filters.getters.getValuesFromQuery()({ filter_type: 'aaa', filter_name: 'bbb', custom: 'ccc' }))
                .toEqual({
                    type:'aaa', name: 'bbb'
                })
        });

        test('resolveFilterValue', ()=>{
            const state = {};
            const getters = {
                defaultValue: jest.fn(()=>'defaultValue')
            };
            const resolveFilterValue = (...args) => filters.getters.resolveFilterValue(state, getters)(...args);

            expect(resolveFilterValue({ filter: { key:'filter' }, value: undefined })).toEqual('defaultValue');
            expect(getters.defaultValue).toHaveBeenCalledWith({ key:'filter' });
            expect(resolveFilterValue({ filter: {}, value: null })).toEqual('defaultValue');

            expect(resolveFilterValue({
                filter:{ multiple: true }, value: 3
            })).toEqual([3]);

            expect(resolveFilterValue({
                filter: {}, value: 'test'
            })).toEqual('test');

            expect(resolveFilterValue({
                filter: { multiple: true }, value: [3]
            })).toEqual([3]);
        });

        test('nextValues', ()=>{
            const state = {
                values: {
                    type: 'aa'
                }
            };

            expect(filters.getters.nextValues(state)({ filter: { key:'filter' }, value: 1 }))
                .toEqual({
                    type: 'aa',
                    filter: 1
                });
            expect(filters.getters.nextValues(state)({ filter: { key:'filter', master: true }, value: 1 }))
                .toEqual({
                    type: null,
                    filter: 1
                });
        });

        test('nextQuery', ()=>{
            const getters = {
                getQueryParams: jest.fn(()=>'query params'),
                nextValues: jest.fn(()=>'next values')
            };

            expect(filters.getters.nextQuery(null, getters)({ filter:{ key:'filter' }, value:1 })).toEqual('query params');
            expect(getters.getQueryParams).toHaveBeenCalledWith('next values');
            expect(getters.nextValues).toHaveBeenCalledWith({ filter:{ key:'filter' }, value:1 });
        });
    });

    describe('actions', () => {
        test('update', async () => {
            const commit = jest.fn();
            const dispatch = jest.fn();
            const testFilters = [{ key:'prop1' }, { key:'prop2' }];
            const testValues = { prop1:'aaa', prop2:'bbb' };

            filters.actions.update({ commit, dispatch }, { filters: testFilters, values: testValues });

            expect(commit).toHaveBeenCalledWith(SET_FILTERS, testFilters);
            expect(dispatch).toHaveBeenCalledWith('setFilterValue', { filter: { key:'prop1' }, value: 'aaa' });
            expect(dispatch).toHaveBeenCalledWith('setFilterValue', { filter: { key:'prop2' }, value: 'bbb' });

            dispatch.mockClear();

            expect(() => {
                filters.actions.update({ commit, dispatch }, { filters: null, values: null });
            }).not.toThrow();

            expect(commit).toHaveBeenCalledWith(SET_FILTERS, null);
            expect(dispatch).not.toHaveBeenCalled();
        });

        test('setFilterValue', ()=>{
            const commit = jest.fn();
            const getters = {
                resolveFilterValue: jest.fn(()=>'resolvedValue')
            };
            const filter = { key:'filter' };
            const value = 'value';

            filters.actions.setFilterValue({ commit, getters }, { filter, value });

            expect(commit).toHaveBeenCalledWith(SET_FILTER_VALUE, { key: 'filter', value: 'resolvedValue' });
            expect(getters.resolveFilterValue).toHaveBeenCalledWith({ filter, value });
        });
    });
});