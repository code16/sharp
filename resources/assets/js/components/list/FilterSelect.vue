<template>
    <span class="SharpFilterSelect"
          :class="{
          'SharpFilterSelect--open':opened,
          'SharpFilterSelect--empty':empty,
          'SharpFilterSelect--multiple':multiple}" tabindex="0">
        <span class="SharpFilterSelect__text" @click="showMultiselect">
            {{name}}<span v-if="!empty" style="font-weight:normal">&nbsp;&nbsp;</span>
        </span>
        <sharp-select class="SharpFilterSelect__select"
                      :value="value"
                      :options="values"
                      :multiple="multiple"
                      :clearable="!required"
                      :inline="false"
                      :unique-identifier="filterKey"
                      :placeholder="fixZeroValuePlaceholder"
                      ref="select"
                      @input="handleSelect"
                      @open="opened=true"
                      @close="opened=false">
        </sharp-select>
    </span>
</template>

<script>
    import Dropdown from '../dropdown/Dropdown';
    import Select from '../form/fields/Select';

    import { AutoScroll } from '../../mixins';


    export default {
        name: 'SharpFilterSelect',
        components: {
            [Dropdown.name]: Dropdown,
            [Select.name]: Select
        },
        props: {
            filterKey: {
                type: String,
                required: true
            },
            name : {
                type: String,
                required: true
            },
            values: {
                type: Array,
                required: true
            },
            value: {
                type: [String, Number, Array],
            },
            multiple: Boolean,
            required: Boolean
        },
        data() {
            return {
                opened: false
            }
        },
        computed: {
            empty() {
                return this.value == null || this.multiple && !this.value.length;
            },
            fixZeroValuePlaceholder() {
                return !this.multiple ? (this.values.find(option => option.id===0)||{}).label : '';
            }
        },
        methods: {
            handleSelect(value) {
                this.$emit('input', value);
            },
            showMultiselect() {
                let { select:{ $refs: { multiselect } } } = this.$refs;
                multiselect.activate();
            }
        }
    }
</script>