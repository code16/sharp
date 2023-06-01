<template>
    <div class="d-flex">
        <div class="input-group input-group-sm w-auto" style="min-height: 1.75rem">
            <template v-if="icon">
                <div class="input-group-text">
                    <i class="fa fa-sm" :class="icon" style="opacity: .75; font-size: .75rem"></i>
                </div>
            </template>

            <div class="form-control">
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
