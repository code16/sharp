<template>
    <div class="SharpSelect" :class="{'SharpSelect--multiple':multiple}">
        <sharp-multiselect
                v-if="display==='dropdown'"
                :value="value"
                :searchable="false"
                :options="multiselectOptions"
                :multiple="multiple"
                :hide-selected="multiple"
                :close-on-select="!multiple"
                :custom-label="multiselectLabel"
                :placeholder="placeholder"
                :disabled="readOnly"
                :max="maxSelected"
                @input="handleInput" ref="multiselect">
            <template v-if="!multiple && value!=null">
                <div slot="carret" @mousedown.stop.prevent="remove()" class="SharpSelect__remove-btn close"></div>
            </template>
        </sharp-multiselect>
        <div v-else>
            <template v-if="multiple">
                <sharp-check v-for="option in options" :value="checked(option.id)"
                             @input="c=>handleCheckboxChanged(c,option.id)"
                             :text="option.label" :disabled="readOnly" :key="option.id">
                </sharp-check>
            </template>
            <div v-else class="SharpSelect__radio-button-group" :class="{'SharpSelect__radio-button-group--block':!inline}">
                <component :is="inline?'span':'div'" v-for="option in options" :key="option.id">
                    <input class="SharpRadio" type="radio" :checked="value==option.id" tabindex="0" :disabled="readOnly">
                    <label class="SharpRadio__label" @click="handleRadioClicked(option.id)">
                        <span class="SharpRadio__appearance"></span>
                        {{option.label}}
                    </label>
                </component>
            </div>
        </div>
    </div>
</template>

<script>
    import Multiselect from '../../Multiselect';
    import SharpCheck from './Check.vue';

    export default {
        name: 'SharpSelect',

        components: {
            [Multiselect.name]: Multiselect,
            SharpCheck
        },

        props: {
            value: [Array, String, Number],

            options: {
                type: Array,
                required: true
            },
            multiple: {
                type: Boolean,
                default: false
            },
            display: {
                type: String,
                default: 'dropdown'
            },
            clearable: {
                type: Boolean,
                default: false
            },
            placeholder: {
                type: String,
                default: '-'
            },
            maxText: String,
            maxSelected: Number,
            readOnly: Boolean,

            inline: {
                type: Boolean,
                default: true
            },
            disableFocus: Boolean
        },

        data() {
            return {
                checkboxes: this.value
            }
        },
        computed: {
            multiselectOptions() {
                return this.options.map(o => o.id);
            },
            optionsLabel() {
                if (this.display !== 'dropdown')
                    return;

                return this.options.reduce((map, opt) => {
                    map[opt.id] = opt.label;
                    return map;
                }, {});
            }
        },
        methods: {
            remove() {
                this.$emit('input', null);
            },
            multiselectLabel(id) {
                return this.optionsLabel[id];
            },
            handleInput(val) {
                this.$emit('input', val);
            },
            checked(optId) {
                return this.value.indexOf(optId) !== -1;
            },
            handleCheckboxChanged(checked, optId) {
                let newValue = this.value;
                if (checked)
                    newValue.push(optId);
                else
                    newValue = this.value.filter(val => val !== optId);
                this.$emit('input', newValue);
            },
            handleRadioClicked(optId) {
                this.$emit('input', optId);
            }
        },
        mounted(){console.log(this)}
    }
</script>