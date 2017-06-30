<script>
    import Multiselect from 'vue-multiselect';
    import DropdownArrow from './dropdown/Arrow';

    import { lang } from '../mixins/Localization';


    export default {
        name:'SharpMultiselect',
        functional:true,
        render(h, { data, children, props }) {

            if(!props.placeholder) {
                data.attrs.placeholder = lang('form.multiselect.placeholder');
            }

            return h(Multiselect, data, [
                h(DropdownArrow, { 'class': 'multiselect__select' ,slot:'carret' }),
                h('template', { slot:'maxElements'}, lang('form.multiselect.max_text')),
                ...(children||[])
            ])
        }
    }
</script>