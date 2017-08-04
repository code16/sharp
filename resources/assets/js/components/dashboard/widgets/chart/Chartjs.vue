<script>
    import { ignoreWarns } from '../../../../util';

    export default {
        name:'SharpChartjs',
        functional:true,

        render(h, {props,props:{comp,data,options}}) {
            return h(comp.extend({
                mounted() {
                    this.renderChart(data,options);
                }
            }), {
                props
            });
        }
    }
</script>