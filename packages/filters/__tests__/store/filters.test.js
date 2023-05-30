import filters, {SET_VALUES, SET_FILTERS} from '../../src/store/filters';
import * as querystringUtils from "sharp/util/querystring";

describe('store filters', () => {
    beforeEach(() => {
        jest.restoreAllMocks()
    });

    test('state match snapshot', ()=>{
        expect(filters.state()).toMatchSnapshot();
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

        test('rootFilters', ()=>{
            expect(filters.getters.rootFilters({ })).toEqual([]);
            expect(filters.getters.rootFilters({ filters: { _root: [{}] } })).toEqual([{}]);
        });

        test('filterQueryKey', ()=>{
            expect(filters.getters.filterQueryKey()('key')).toBe('filter_key');
        });

        test('getQueryParams', ()=>{
            const state = {
                filters: {
                    _page: [
                        { key:'type' },
                        { key:'name' },
                    ]
                }
            };
            const getters = {
                filterQueryKey: jest.fn(key => `filter_${key}`),
                serializeValue: jest.fn(({ filter, value }) => value),
            };
            expect(filters.getters.getQueryParams(state, getters)({ })).toEqual({});

            expect(filters.getters.getQueryParams(state, getters)({
                type: 'aaa',
                name: 'bbb',
            })).toEqual({
                'filter_type': 'aaa', 'filter_name': 'bbb'
            });
            expect(getters.serializeValue).toHaveBeenCalledWith({ filter:{ key:'type' }, value:'aaa' });
            expect(getters.serializeValue).toHaveBeenCalledWith({ filter:{ key:'name' }, value:'bbb' });
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
            const getters = {};
            const resolveFilterValue = (...args) => filters.getters.resolveFilterValue(state, getters)(...args);

            expect(resolveFilterValue({ filter: { key:'filter', default:'defaultValue' }, value: undefined })).toEqual('defaultValue');
            expect(resolveFilterValue({ filter: { default:'defaultValue' }, value: null })).toEqual('defaultValue');

            expect(resolveFilterValue({
                filter:{ multiple: true }, value: 3
            })).toEqual([3]);

            expect(resolveFilterValue({
                filter: {}, value: 'test'
            })).toEqual('test');

            expect(resolveFilterValue({
                filter: { multiple: true }, value: [3]
            })).toEqual([3]);

            jest.spyOn(querystringUtils, 'parseRange')
                .mockImplementation(() => 'parsedRange');

            expect(resolveFilterValue({
                filter: { type: 'daterange' }, value: '2019-06-21..2019-06-24',
            })).toEqual('parsedRange');
            expect(querystringUtils.parseRange)
                .toHaveBeenCalledWith('2019-06-21..2019-06-24');
        });

        test('serializeValue', ()=>{
            const state = {};
            const getters = {
            };
            expect(filters.getters.serializeValue(state, getters)({
                filter: {},
                value: 'val'
            })).toEqual('val');

            jest.spyOn(querystringUtils, 'serializeRange')
                .mockImplementation(() => 'serializedRange');

            expect(filters.getters.serializeValue(state, getters)({
                filter: {
                    type: 'daterange',
                },
                value: {
                    start: 'start',
                    end: 'end',
                }
            })).toEqual('serializedRange');
            expect(querystringUtils.serializeRange).toHaveBeenCalledWith({ start:'start', end: 'end' });
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
        test('update', () => {
            const state = {};
            const commit = jest.fn();
            const dispatch = jest.fn();
            const getters = {
                resolveFilterValue: jest.fn(()=>'resolvedValue')
            };
            const testFilters = [{ key:'prop1' }, { key:'prop2' }];
            const testValues = { prop1:'aaa', prop2:'bbb' };

            filters.actions.update({ state, commit, dispatch, getters }, { filters: testFilters, values: testValues });

            expect(commit).toHaveBeenCalledWith(SET_FILTERS, testFilters);
            expect(commit).toHaveBeenCalledWith(SET_VALUES, {
                prop1: 'resolvedValue',
                prop2: 'resolvedValue'
            });

            dispatch.mockClear();

            expect(() => {
                filters.actions.update({ state, commit, dispatch, getters }, { filters: null, values: null });
            }).not.toThrow();

            expect(commit).toHaveBeenCalledWith(SET_FILTERS, null);
            expect(commit).toHaveBeenCalledWith(SET_VALUES, {});
        });
    });
});
