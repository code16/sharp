import { mount } from '@vue/test-utils';
import ActionBar from '../src/components/TopBar.vue';

describe('action-bar', ()=>{
    test('can mount ActionBar', ()=>{
        expect(mount(ActionBar).html()).toMatchSnapshot();
    });
    test('can mount with slots ActionBar', ()=>{
        expect(mount(ActionBar, {
            slots: {
                left: '<div>LEFT CONTENT</div>',
                right: '<div>RIGHT CONTENT</div>',
                extras: '<div>EXTRAS CONTENT</div>'
            }
        }).html()).toMatchSnapshot();
    });
});
