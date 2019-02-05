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

            data.attrs.showPointer = false;

            let carretSlot = slots().caret;

            return h({
                'extends': Multiselect,
                computed: {
                    isSingleLabelVisible() {
                        // vue-multiselect #851
                        return this.singleValue === 0 || Multiselect.computed.isSingleLabelVisible.call(this);
                    },
                },
                mounted() {
                    this.$el.addEventListener('blur', () => this.deactivate());
                }
            }, data, [
                carretSlot
                    ? h('template',{ slot:'caret' },carretSlot)
                    : h(DropdownArrow, { 'class': 'multiselect__select', slot:'caret' }),
                h('template', { slot:'maxElements'}, lang('form.multiselect.max_text')),
                ...children,
            ])
        }
    }
</script>