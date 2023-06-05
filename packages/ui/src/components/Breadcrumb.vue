<template>
    <div class="d-flex">
        <div class="d-flex align-items-center">
            <template v-if="icon">
                <div class="me-2 pe-1">
                    <i class="fa fa-sm d-block text-primary opacity-75 fs-8" :class="icon"></i>
                </div>
            </template>

            <div class="breadcrumb align-items-center p-0 m-0">
                <template v-for="(item, i) in items">
                    <div class="breadcrumb-item" :class="{ 'active': isActive(i) }">
                        <template v-if="isActive(i)">
                            <span>{{ item.name }}</span>
                        </template>
                        <template v-else>
                            <a :href="item.url">{{ item.name }}</a>
                        </template>
                    </div>
                </template>
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
        computed: {
            icon() {
                return this.$store.state.currentEntity?.icon;
            },
        },
        methods: {
            isActive(i) {
                return i === this.items.length - 1;
            },
        }
    }
</script>
