<template>
    <div class="SharpSelect" :class="[{'SharpSelect--multiple':multiple}, `SharpSelect--${display}`]">
        <Multiselect
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
            @select="handleSelected"
            @open="$emit('open')"
            @close="$emit('close')"
            ref="multiselect"
        >
            <template v-if="hasClearButton" v-slot:caret>
                <button class="SharpSelect__clear-button" type="button" @mousedown.stop.prevent="remove()">
                    <svg class="SharpSelect__clear-button-icon"
                         aria-label="close" width="10" height="10" viewBox="0 0 10 10" fill-rule="evenodd">
                        <path d="M9.8 8.6L8.4 10 5 6.4 1.4 10 0 8.6 3.6 5 .1 1.4 1.5 0 5 3.6 8.6 0 10 1.4 6.4 5z"></path>
                    </svg>
                </button>
            </template>
            <template v-slot:tag="{ option, remove }">
                <span class="multiselect__tag" :key="option">
                    <span>{{ multiselectLabel(option) }}</span>
                    <i aria-hidden="true" tabindex="1" @keypress.enter.prevent="remove(option)" @mousedown.prevent.stop="remove(option)" class="multiselect__tag-icon"></i>
                </span>
            </template>
            <template v-slot:option>
                <slot name="option" />
            </template>
        </Multiselect>
        <template v-else>
            <div class="SharpSelect__group" :class="{'SharpSelect__group--block':!inline}">
                <template v-if="multiple">
                    <template v-for="option in options">
                        <div class="SharpSelect__item" :class="itemClasses" :key="option.id">
                            <Check
                                :value="isChecked(option)"
                                :text="optionsLabel[option.id]"
                                :read-only="readOnly"
                                @input="handleCheckboxChanged($event, option)"
                            />
                        </div>
                    </template>
                    <template v-if="showSelectAll">
                        <div class="SharpSelect__links mt-3">
                            <div class="row mx-n2">
                                <div class="col-auto px-2">
                                    <a href="#" @click.prevent="handleSelectAllClicked">{{ lang('form.select.select_all') }}</a>
                                </div>
                                <div class="col-auto px-2">
                                    <a href="#" @click.prevent="handleUnselectAllClicked">{{ lang('form.select.unselect_all') }}</a>
                                </div>
                            </div>
                        </div>
                    </template>
                </template>
                <template v-else>
                    <template v-for="(option, index) in options">
                        <div class="SharpSelect__item" :class="itemClasses" :key="option.id">
                            <input type="radio"
                                class="SharpRadio"
                                tabindex="0"
                                :id="`${uniqueIdentifier}${index}`"
                                :checked="isSelected(option)"
                                :value="option.id"
                                :disabled="readOnly"
                                :name="uniqueIdentifier"
                                @change="handleRadioChanged(option)"
                            >
                            <label class="SharpRadio__label" :for="`${uniqueIdentifier}${index}`">
                                <span class="SharpRadio__appearance"></span>
                                {{ optionsLabel[option.id] }}
                            </label>
                        </div>
                    </template>
                </template>
            </div>
        </template>
    </div>
</template>

<script>
    import { Multiselect } from 'sharp-ui';
    import Check from './Check.vue';
    import localize from '../../mixins/localize/Select';
    import { setDefaultValue } from "../../util";
    import { lang } from "sharp";

    export default {
        name: 'SharpSelect',

        mixins: [localize],

        components: {
            Multiselect,
            Check
        },

        props: {
            value: [Array, String, Number],
            uniqueIdentifier: String,
            options: {
                type: Array,
                required: true,
                default: ()=>[],
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
            showSelectAll: {
                type: Boolean,
                default: true,
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
        watch: {
            options() {
                this.init();
            }
        },
        computed: {
            multiselectOptions() {
                return this.options.map(o => o.id);
            },
            optionsLabel() {
                // if (this.display !== 'dropdown')
                //     return;

                return this.options.reduce((map, opt) => {
                    map[opt.id] = this.localizedOptionLabel(opt);
                    return map;
                }, {});
            },
            itemClasses() {
                return {
                    'SharpSelect__item--inline': this.inline,
                }
            },
            hasClearButton() {
                return this.clearable && !this.multiple && this.value != null;
            },
        },
        methods: {
            lang,
            isSelected(option, value = this.value) {
                if(option.id == null || value == null) {
                    return false;
                }
                return `${option.id}` === `${value}`;
            },
            isChecked(option) {
                return this.value?.some(value => this.isSelected(option, value));
            },
            update(value) {
                this.$emit('input', value);
                this.$emit('change', value);
            },
            remove() {
                this.$emit('input', null);
            },
            multiselectLabel(id) {
                return this.optionsLabel[id];
            },
            handleInput(val) {
                this.$emit('input', val);
            },
            async handleSelected() {
                await this.$nextTick();
                this.$emit('change', this.value);
            },
            handleCheckboxChanged(checked, option) {
                if (checked) {
                    this.update([...(this.value ?? []), option.id])
                }
                else {
                    this.update((this.value ?? []).filter(val => !this.isSelected(option, val)));
                }
            },
            handleRadioChanged(option) {
                this.update(option.id);
            },
            handleSelectAllClicked() {
                this.update(this.options.map(option => option.id));
            },
            handleUnselectAllClicked() {
                this.update([]);
            },
            setDefault() {
                if(!this.clearable && this.value == null && this.options.length > 0) {
                    console.log('set default');
                    this.$emit('input', this.options[0].id, { force:true });
                }
            },
            init() {
                setDefaultValue(this, this.setDefault, {
                    dependantAttributes: ['options'],
                });
            }
        },
        created() {
            this.init();
        }
    }
</script>
