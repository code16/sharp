<script>
    import Widgets, { NameAssociation as widgetCompName } from './widgets/index';

    export default {
        name:'SharpWidget',
        components:Widgets,

        props: {
            widgetType:String,
            widgetProps:Object,
            value:Object
        },

        render(h) {
            h(widgetCompName[this.widgetType], {
                props: { ...this.widgetProps, value:this.value }
            })
        }
    }
</script>
