<template>
    <div class="d-flex">
        <div class="row align-items-center gx-3">
            <template v-if="icon">
                <div class="col-auto">
                    <i class="fa fa-sm d-block text-primary" :class="icon" style="font-size: .75rem"></i>
                </div>
            </template>

            <div class="col">
                <div class="breadcrumb p-0 m-0">
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
