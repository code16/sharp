<template>
    <component
        :is="component"
        class="SharpSelect"
        :class="classes"
        :labels="optionsLabel"
        v-bind="[$props, $attrs]"
        @input="handleInput"
        ref="component"
    >
    </component>
</template>

<script>
    import DropdownSelect from "./select/DropdownSelect.vue";
    import Checkboxes from "./select/Checkboxes.vue";
    import Radios from "./select/Radios.vue";
    import localize from '../../mixins/localize/Select';
    import { setDefaultValue } from "../../util";

    export default {
        name: 'SharpSelect',

        mixins: [localize],

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
            root: Boolean,
        },
        watch: {
            options() {
                this.init();
            }
        },
        computed: {
            classes() {
                return [
                    `SharpSelect--${this.display}`,
                    {
                        'SharpSelect--multiple': this.multiple,
                    }
                ];
            },
            component() {
                if(this.display === 'dropdown') {
                    return DropdownSelect;
                }
                return this.multiple
                    ? Checkboxes
                    : Radios;
            },
            optionsLabel() {
                return this.options.reduce((map, opt) => {
                    map[opt.id] = this.localizedOptionLabel(opt);
                    return map;
                }, {});
            },
        },
        methods: {
            handleInput(value, { error } = {}) {
                this.$emit('input', value, { error });
            },
            setDefault() {
                if(!this.clearable
                    && !this.multiple
                    && this.value == null
                    && this.options.length > 0
                ) {
                    this.$emit('input', this.options[0].id, { force:true });
                }
            },
            init() {
                setDefaultValue(this, this.setDefault, {
                    dependantAttributes: ['options'],
                });
            },
            /**
             * @public
             */
            blur() {
                this.$refs.component.$refs?.multiselect.deactivate();
            }
        },
        created() {
            this.init();
        }
    }
</script>
