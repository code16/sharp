<script>
    import Multiselect from 'vue-multiselect';

    import { __ } from "@/util/i18n";
    import { multiselectUpdateScroll } from "../util";

    export default {
        name: 'SharpMultiselect',
        template: '<div><slot /></div>', // todo
        // render(h, { data, children=[],  props }) {
        //
        //     if(!props.placeholder) {
        //         data.attrs.placeholder = __('sharp::form.multiselect.placeholder');
        //     }
        //
        //     data.class = ['form-control', data.class]
        //
        //     data.attrs.showPointer = false;
        //
        //     if(props.disabled) {
        //         data.attrs.tabindex = -1;
        //     }
        //
        //     return h({
        //         'extends': Multiselect,
        //         watch: {
        //             isOpen(open) {
        //                 open && multiselectUpdateScroll(this);
        //             }
        //         },
        //         computed: {
        //             isSingleLabelVisible() {
        //                 // vue-multiselect #851
        //                 return this.singleValue === 0 || Multiselect.computed.isSingleLabelVisible.call(this);
        //             },
        //         },
        //         mounted() {
        //             this.$el.addEventListener('blur', () => this.deactivate());
        //         }
        //     }, data, [
        //         h('template', { slot:'maxElements'}, __('sharp::form.multiselect.max_text')),
        //         ...children,
        //     ])
        // }
    }
</script>
