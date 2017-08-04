<template>
    <sharp-chartjs :comp="chartComp" :data="data" :options="options"
                   :styles="{}" :cssClasses="classes">
    </sharp-chartjs>
</template>

<script>
    // Removed because Vue duplication
    import { Bar, Line, Pie } from 'vue-chartjs';
    import Chartjs from './Chartjs';

    const noop = ()=>{};

    export default {
        name:'SharpWidgetChart',

        components: {
            [Chartjs.name]:Chartjs
        },

        props: {
            display:String,
            title: String,
            value: Object
        },
        computed: {
            classes() {
                return `SharpWidgetChart SharpWidgetChart--${this.display}`;
            },
            chartComp() {
                const map = {
                    bar:Bar, line:Line, pie:Pie
                };
                return map[this.display];
            },
            options() {
                return {
                    title: {
                        display: !!this.title,
                        text: this.title
                    },
                    maintainAspectRatio:false,
                    legendCallback: noop
                }
            },
            data() {
                let { datasets } = this.value;
                //debugger;
                return {
                    ...this.value,
                    datasets: [
                        ...datasets.map(set=>({
                            ...set, backgroundColor: set.color, data: set.values || set.data
                        }))
                    ]
                }
            }
        },
        mounted() {

        }
    }
</script>