<template>
    <div class="ShowSection" :class="classes">
        <component :is="wrapperElement">
            <div class="row">
                <template v-if="hasCollapse || section.title">
                    <div class="col">
                        <template v-if="hasCollapse">
                            <summary class="ShowSection__header ShowSection__header--collapsable">
                                <h2 class="ShowSection__title d-inline-block mb-0">{{ section.title || ' ' }}</h2>
                            </summary>
                        </template>
                        <template v-else-if="section.title">
                            <div class="ShowSection__header">
                                <h2 class="ShowSection__title">{{ section.title }}</h2>
                            </div>
                        </template>
                    </div>
                </template>
                <template v-if="hasCommands">
                    <div class="col-auto">
                        <CommandsDropdown :commands="commands" @select="handleCommandSelected">
                            <template v-slot:text>
                                {{ lang('entity_list.commands.instance.label') }}
                            </template>
                        </CommandsDropdown>
                    </div>
                </template>
            </div>

            <div class="ShowSection__content">
                <Grid class="ShowSection__grid"
                    :rows="[section.columns]"
                    :col-class="() => 'ShowSection__col'"
                    v-slot="{ itemLayout:column }"
                >
                    <Grid class="ShowPage__fields-grid"
                        :rows="column.fields"
                        :row-class="fieldsRowClass"
                        v-slot="{ itemLayout:fieldLayout }"
                    >
                        <slot :field-layout="fieldLayout" />
                    </Grid>
                </Grid>
            </div>
        </component>
    </div>
</template>

<script>
    import { Grid } from "sharp-ui";
    import { CommandsDropdown } from 'sharp-commands';
    import { lang } from "sharp";

    export default {
        components: {
            Grid,
            CommandsDropdown,
        },
        props: {
            section: Object,
            fieldsRowClass: Function,
            collapsable: Boolean,
            layout: {
                type: String,
                validator: layout => ['card', 'contents'].includes(layout),
                default: 'card',
            },
            commands: Array,
        },
        computed: {
            classes() {
                return [
                    `ShowSection--layout-${this.layout}`,
                ]
            },
            hasCollapse() {
                return this.collapsable;
            },
            hasCommands() {
                return this.commands?.flat().length > 0;
            },
            wrapperElement() {
                return this.hasCollapse ? 'details' : 'div';
            },
        },
        methods: {
            lang,
            handleCommandSelected(command) {
                this.$emit('command', command);
            },
        },
    }
</script>
