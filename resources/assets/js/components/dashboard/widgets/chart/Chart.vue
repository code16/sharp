<template>
    <sharp-chartjs :comp="chartComp" :data="data" :options="options"></sharp-chartjs>
</template>

<script>
    import { Bar, Line, Pie } from 'vue-chartjs';
    import Chartjs from './Chartjs';

    const noop = ()=>{}

    export default {
        name:'SharpWidgetChart',

        components: {
            [Chartjs.name]:Chartjs
        },

        props: {
            display:String,
            title: String,
            datasets:Array,
            labels:Array
        },
        computed: {
            chartComp() {
                const map = {
                    bar:Bar, line:Line, pie:Pie
                };
                return map[this.display];
            },
            data() {
                return {
                    labels:this.labels,
                    datasets:this.datasets,
                }
            },
            options() {
                return {
                    title: {
                        display: !!this.title,
                        text: this.title
                    },
                    // prevent error if no background color provided
                    legendCallback: noop
                }
            }
        },
    }
</script>