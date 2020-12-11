<template>
    <div class="ShowSection" :class="classes">
        <component :is="wrapperElement">
            <template v-if="hasCollapse">
                <summary class="py-1 mb-1">
                    <h2 class="ShowSection__title mb-0">{{ section.title || ' ' }}</h2>
                </summary>
            </template>
            <template v-else-if="section.title">
                <h2 class="ShowSection__title mb-2">{{ section.title }}</h2>
            </template>
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

    export default {
        components: {
            Grid,
        },
        props: {
            section: Object,
            fieldsRowClass: Function,
            collapsable: Boolean,
            layout: {
                type: String,
                validator: layout => ['card', 'contents'].includes(layout),
                default: 'card',
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
            wrapperElement() {
                return this.hasCollapse ? 'details' : 'div';
            },
        }
    }
</script>
