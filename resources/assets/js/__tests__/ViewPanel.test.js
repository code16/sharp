import { mount } from '@vue/test-utils';
import ViewPanel from '../components/commands/CommandViewPanel.vue';

describe('view-panel', ()=>{
    let wrapper;
    beforeEach(()=>{
        wrapper = mount(ViewPanel);
    });

    test('mount ViewPanel', ()=>{
        expect(wrapper.html()).toMatchSnapshot();
    });

    test('mount "visible with content" ViewPanel', ()=>{
        wrapper.setProps({ content: 'SOME HTML MARKUP' });
        expect(wrapper.html()).toMatchSnapshot();
    });

    test('emit "change" to false on clicked background glasspane', ()=>{
        wrapper.setProps({ show: true });
        let glasspane = wrapper.find('.SharpViewPanel__glasspane');
        glasspane.trigger('click');
        expect(wrapper.emitted().close).toHaveLength(1);
    });

});