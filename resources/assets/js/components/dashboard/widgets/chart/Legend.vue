<template>
    <div class="SharpLegend">
        <div class="SharpLegend__list row">
            <div v-for="dataset in datasets" class="SharpLegend__item col-sm">
                <span class="SharpLegend__color" :style="{background:dataset.color}"></span>
                <span class="SharpLegend__text">{{dataset.label}}</span>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name:'SharpLegend',
        props: {
            datasets: Array
        }
    }
</script>