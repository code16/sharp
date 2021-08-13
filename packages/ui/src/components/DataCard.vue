<template>
    <a class="card h-100 text-body text-decoration-none" :href="url" style="font-size: .875rem">
        <template v-if="$slots.actions">
            <div class="card-header border-0 bg-white p-2 pb-0 mb-n2">
                <div class="row">
                    <div class="col"></div>
                    <div class="col-auto">
                        <slot name="actions" />
                    </div>
                </div>
            </div>
        </template>
        <div class="card-body">
            <div class="row gx-3 gy-4">
                <template v-for="column in columns">
                    <template v-if="data[column.key]">
                        <div class="col-12" :class="[`col-md-${column.size}`]">
                            <template v-if="column.label">
                                <div class="form-label" style="font-size: .875em">{{ column.label }}</div>
                            </template>
                            <div v-html="data[column.key]"></div>
                        </div>
                    </template>
                </template>
            </div>
        </div>
    </a>
</template>

<script>
    export default {
        props: {
            columns: Array,
            data: Object,
            url: String,
        },
    }
</script>
