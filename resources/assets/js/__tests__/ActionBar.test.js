import { mount } from '@vue/test-utils';
import ActionBar from '../components/action-bar/ActionBar';

describe('action-bar', ()=>{
    test('can mount non ready ActionBar', ()=>{
        expect(mount(ActionBar, { propsData: { ready: false } }).html()).toMatchSnapshot();
    });
    test('can mount ready ActionBar', ()=>{
        expect(mount(ActionBar, { propsData: { ready: true } }).html()).toMatchSnapshot();
    });
    test('can mount with slots ActionBar', ()=>{
        expect(mount(ActionBar, {
            propsData: { ready: true },
            slots: {
                left: '<div>LEFT CONTENT</div>',
                right: '<div>RIGHT CONTENT</div>',
                extras: '<div>EXTRAS CONTENT</div>'
            }
        }).html()).toMatchSnapshot();
    });
});