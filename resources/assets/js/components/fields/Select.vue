<template>
    <div class="SharpSelect">
        <multiselect v-if="display==='dropdown'"
                     :value="value"
                     :searchable="false"
                     :options="multiselectOptions"
                     :multiple="multiple"
                     :hide-selected="multiple"
                     :close-on-select="!multiple"
                     :custom-label="multiselectLabel"
                     :taggable="taggable"
                     :placeholder="placeholder"
                     @input="handleInput" ref="multiselect">
            <template v-if="!multiple && value!=null">
                <div slot="carret" @mousedown.stop.prevent="remove()" class="SharpSelect__remove-btn close"></div>
            </template>
        </multiselect>
        <div v-else>
            <template v-if="multiple">
                <sharp-check v-for="option in options" :value="checked(option.id)" @input="c=>handleCheckboxChanged(c,option.id)"
                             :text="option.label" :key="option.id">
                </sharp-check>
            </template>
            <template v-else>
                <div class="form-check" v-for="option in options">
                    <label class="form-check-label">
                        <input class="form-check-input" type="radio" :checked="value===option.id" @click="handleRadioClicked(option.id)"> {{option.label}}
                    </label>
                </div>
            </template>
        </div>
    </div>
</template>

<script>
    import Multiselect from 'vue-multiselect';
    import SharpCheck from './Check.vue';

    export default {
        name:'SharpSelect',
        components: {
            Multiselect,
            SharpCheck
        },

        props: {
            value: [Array,String, Number],

            options: {
                type:Array,
                required:true
            },
            multiple: {
                type:Boolean,
                default: false
            },
            display: {
                type:String,
                default:'dropdown'
            },
            clearable: {
                type:Boolean,
                default: false
            },
            taggable: {
                type: Boolean,
                default: false
            },
            placeholder: {
                type:String,
                default: '-'
            }
        },

        data() {
            return {
                checkboxes: this.value
            }
        },
        computed: {
            multiselectOptions() {
                return this.options.map(o=>o.id);
            },
            optionsLabel() {
                if(this.display !== 'dropdown')
                    return;

                return this.options.reduce((map,opt) => {
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
                if(checked)
                    newValue.push(optId);
                else
                    newValue = this.value.filter(val => val !== optId);
                this.$emit('input',newValue);
            },
            handleRadioClicked(optId) {
                this.$emit('input', optId);
            }
        }
    }
</script>