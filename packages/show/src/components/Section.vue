<template>
    <div class="ShowSection" :class="classes">
        <div class="row">
            <template v-if="hasCollapse || section.title">
                <div class="col">
                    <SectionTitle :section="section" :collapsable="hasCollapse" :collapsed="collapsed" />
                </div>
            </template>
            <template v-if="hasCommands && !collapsed">
                <div class="col-auto align-self-end mb-2">
                    <CommandsDropdown :commands="commands" @select="handleCommandSelected">
                        <template v-slot:text>
                            {{ lang('entity_list.commands.instance.label') }}
                        </template>
                    </CommandsDropdown>
                </div>
            </template>
        </div>

        <template v-if="!collapsed">
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
        </template>
    </div>
</template>

<script>
    import { Grid, SectionTitle } from "sharp-ui";
    import { CommandsDropdown } from 'sharp-commands';
    import { lang } from "sharp";

    export default {
        components: {
            Grid,
            CommandsDropdown,
            SectionTitle
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
        data() {
            return {
                collapsed: this.collapsable,
            }
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
            handleDetailsToggle(e) {
                this.collapsed = !e.target.open;
            },
        },
    }
</script>
