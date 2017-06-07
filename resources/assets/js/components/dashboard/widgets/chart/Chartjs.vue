<script>
    export default {
        name:'SharpChartjs',
        functional:true,
        props: {
            comp:Function,
            data:Object,
            options:Object
        },
        render(h, {props:{comp,data,options}}) {
            return h(comp.extend({
                mounted() {
                    this.renderChart(data,options);
                }
            }));
        }
    }
</script>