<template>
    <div>
        <template v-if="collapsable">
            <details :open="!collapsed" @toggle="$emit('toggle', $event.target.open)">
                <summary class="section__header section__header--collapsable">
                    <h2 class="ShowSection__title d-inline-block mb-0">{{ section.title || ' ' }}</h2>
                </summary>
            </details>
        </template>
        <template v-else-if="section.title">
            <div class="section__header">
                <h2 class="section__title">{{ section.title }}</h2>
            </div>
        </template>
    </div>
</template>

<script>
    export default {
        props: {
            section: Object,
            collapsed: Boolean,
            collapsable: Boolean,
        }
    }
</script>
