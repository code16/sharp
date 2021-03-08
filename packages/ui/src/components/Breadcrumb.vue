<template>
    <div class="d-flex">
        <div class="input-group input-group-sm">
            <div class="input-group-text">

            </div>
            <div class="form-control">
                <div class="breadcrumb p-0 m-0">
                    <template v-for="(item, i) in items">
                        <div class="breadcrumb-item" :class="{ 'active': isActive(i) }">
                            <template v-if="isActive(i)">
                                <span>{{ itemLabel(item) }}</span>
                            </template>
                            <template v-else>
                                <a :href="item.url">{{ itemLabel(item) }}</a>
                            </template>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            /**
             * @typedef BreadcrumbItem
             * @property {string} name
             * @property {string} url
             * @type {Array.<BreadcrumbItem>}
             */
            items: Array
        },
        methods: {
            itemLabel(item) {
                return item.name;
            },
            isActive(i) {
                return i === this.items.length - 1;
            },
        }
    }
</script>
