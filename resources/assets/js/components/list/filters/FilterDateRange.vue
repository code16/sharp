<template>
    <div class="SharpFilterDateRange" :class="{
        'SharpFilterDateRange--empty': empty,
    }">
        <SharpFilterControl :label="label" @click="handleClicked">
            <SharpDateRange
                class="SharpFilterDateRange__field"
                :value="value"
                ref="range"
                @input="handleInput"
            />
        </SharpFilterControl>
    </div>
</template>

<script>
    import SharpFilterControl from '../FilterControl';
    import SharpDateRange from '../../form/fields/date-range/DateRange';

    export default {
        name: 'SharpFilterDateRange',

        components: {
            SharpDateRange,
            SharpFilterControl,
        },

        props: {
            value: {
                required: true,
            },
            label: String,
        },

        computed: {
            empty() {
                return !this.value;
            },
        },

        methods: {
            handleClicked() {
                this.$refs.range.$refs.picker.focus();
            },
            handleInput(range) {
                this.$emit('input', range);
            }
        }
    }
</script>