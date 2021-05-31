<template>
    <div class="d-flex">
        <div class="input-group input-group-sm w-auto">
            <template v-if="currentEntity">
                <div class="input-group-text bg-white">
                    <div class="row gx-2">
                        <template v-if="currentEntity.icon">
                            <div class="col-auto">
                                <i class="fa fa-sm" :class="currentEntity.icon" style="opacity: .75; font-size: .75rem"></i>
                            </div>
                        </template>
                        <template v-if="currentEntity.label">
                            <div class="col-auto">
                                {{ currentEntity.label }}
                            </div>
                        </template>
                    </div>
                </div>
            </template>

            <div class="form-control d-flex align-items-center">
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
            currentEntity() {
                return this.$store.state.currentEntity;
            },
        },
        methods: {
            isActive(i) {
                return i === this.items.length - 1;
            },
        }
    }
</script>
