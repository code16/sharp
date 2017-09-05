<script>
    import Multiselect from 'vue-multiselect';
    import DropdownArrow from './dropdown/Arrow';

    import { lang } from '../mixins/Localization';


    export default {
        name:'SharpMultiselect',
        functional:true,
        render(h, { data, children=[], slots ,props }) {

            if(!props.placeholder) {
                data.attrs.placeholder = lang('form.multiselect.placeholder');
            }

            let carretSlot = slots().carret;

            return h({
                'extends':Multiselect,
                mounted() {
                    this.$el.addEventListener('blur', () => this.deactivate());
                    console.log(data, this.$refs);
                }
            }, data, [
                carretSlot
                    ? h('template',{ slot:'carret' },carretSlot)
                    : h(DropdownArrow, { 'class': 'multiselect__select', slot:'carret' }),
                h('template', { slot:'maxElements'}, lang('form.multiselect.max_text')),
                ...children,
            ])
        }
    }
</script>