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
                :allow-empty="clearable"
                @input="handleInput"
                @open="$emit('open')"
                @close="$emit('close')"
                ref="multiselect">
            <template v-if="clearable && !multiple && value!=null">
                <button slot="carret" class="SharpSelect__clear-button" type="button" @mousedown.stop.prevent="remove()">
                    <svg class="SharpSelect__clear-button-icon"
                         aria-label="close" width="10" height="10" viewBox="0 0 10 10" fill-rule="evenodd">
                        <path d="M9.8 8.6L8.4 10 5 6.4 1.4 10 0 8.6 3.6 5 .1 1.4 1.5 0 5 3.6 8.6 0 10 1.4 6.4 5z"></path>
                    </svg>
                </button>
            </template>
            <slot name="option" slot="option"></slot>
        </sharp-multiselect>
        <div v-else>
            <template v-if="multiple">
                <sharp-check v-for="option in options" :value="checked(option.id)"
                             @input="c=>handleCheckboxChanged(c,option.id)"
                             :text="option.label" :read-only="readOnly" :key="option.id">
                </sharp-check>
            </template>
            <div v-else class="SharpSelect__radio-button-group" :class="{'SharpSelect__radio-button-group--block':!inline}">
                <component :is="inline?'span':'div'" v-for="(option, index) in options" :key="option.id">
                    <input type="radio" :id="`${uniqueIdentifier}${index}`" class="SharpRadio"
                           :checked="value===option.id" tabindex="0" :disabled="readOnly"
                           :name="uniqueIdentifier" :value="option.id"
                           @change="handleRadioChanged(option.id)">
                    <label class="SharpRadio__label" :for="`${uniqueIdentifier}${index}`">
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
            uniqueIdentifier: String,
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
            maxSelected: Number,
            readOnly: Boolean,

            inline: {
                type: Boolean,
                default: true
            },
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
            handleRadioChanged(optId) {
                this.$emit('input', optId);
            }
        },
    }
</script>