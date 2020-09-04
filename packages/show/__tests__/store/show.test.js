import Vuex from 'vuex';
import * as api from '../../src/api';
import show from '../../src/store/show';
import { createLocalVue } from '@vue/test-utils';


describe('store show', () => {
    test('state match snapshot', () => {
        expect(show.state).toMatchSnapshot();
    });

    test('canChangeState', () => {
        expect(show.getters.canChangeState(null, {
            config: {},
        })).toBe(false);
        expect(show.getters.canChangeState(null, {
            config: {
                state: {
                    authorization: false,
                }
            },
        })).toBe(false);
        expect(show.getters.canChangeState(null, {
            config: {
                state: {
                    authorization: true,
                }
            },
        })).toBe(true);
    });

    test('authorizedCommands', () => {
        expect(show.getters.authorizedCommands(null, {
            config: {},
        })).toEqual([]);
        expect(show.getters.authorizedCommands(null, {
            config: {
                commands: {
                    instance: [
                        [{ authorization: true }], [{ authorization:false }]
                    ]
                }
            },
        })).toEqual([[{ authorization: true }], []]);
    });

    test('instanceState', () => {
        expect(show.getters.instanceState(null, {
            config: {},
        })).toEqual(null);
        expect(show.getters.instanceState(null, {
            config: {
                state: {
                    attribute: 'state',
                    values: [{
                        value: 'currentState'
                    }]
                }
            },
            data: {
                state: 'currentState',
            }
        })).toEqual({ value:'currentState' });
    });

    test('stateValues', () => {
        expect(show.getters.stateValues(null, {
            config: {},
        })).toEqual(null);
        expect(show.getters.stateValues(null, {
            config: {
                state: {
                    values: [{
                        value: 'currentState'
                    }]
                }
            },
        })).toEqual([{ value:'currentState' }]);
    });

    test('get integration', async () => {
        const localVue = createLocalVue();
        localVue.use(Vuex);
        const store = new Vuex.Store(show);

        jest.spyOn(api, 'getShowView').mockImplementation(() => Promise.resolve({
            config: Symbol('config'),
            fields: Symbol('fields'),
            data: Symbol('data'),
            layout: Symbol('layout'),
            breadcrumb: Symbol('breadcrumb'),
            authorizations: Symbol('authorizations')
        }));

        await store.dispatch('setEntityKey', 'entityKey');
        await store.dispatch('setInstanceId', 'instanceId');
        await store.dispatch('get');

        expect(api.getShowView).toHaveBeenCalledWith({
            entityKey: 'entityKey',
            instanceId: 'instanceId',
        });

        const { config, fields, data, layout, breadcrumb, authorizations, } = store.getters;
        expect({
            config,
            fields,
            data,
            layout,
            breadcrumb,
            authorizations,
        }).toMatchSnapshot();
    });
});