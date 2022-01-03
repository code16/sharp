import dashboard, { UPDATE} from '../../src/store/dashboard';

jest.mock('sharp');

describe('store dashboard', ()=>{
    test('state matches snapshot', ()=>{
        expect(dashboard.state).toMatchSnapshot();
    });

    describe('mutations', ()=>{
        test('UPDATE', ()=>{
            const state = {};
            const data = {};
            const widgets = {};
            const layout = {};
            const config = {};
            dashboard.mutations[UPDATE](state, {
                data, widgets, layout, config
            });
            expect(state.data).toBe(data);
            expect(state.widgets).toBe(widgets);
            expect(state.layout).toBe(layout);
            expect(state.config).toBe(config);
        });
    });
});
